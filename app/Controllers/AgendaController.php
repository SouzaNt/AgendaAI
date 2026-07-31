<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/Notification.php';
require_once ROOT_PATH . '/app/Models/AgendamentoModel.php';
require_once ROOT_PATH . '/app/Models/RecursoModel.php';
require_once ROOT_PATH . '/app/Models/SalaModel.php';
require_once ROOT_PATH . '/app/Models/InstituicaoModel.php';
require_once ROOT_PATH . '/app/Models/TipoRecursoModel.php';
require_once ROOT_PATH . '/app/Models/ConfiguracaoModel.php';
require_once ROOT_PATH . '/app/Models/FeedbackModel.php';

class AgendaController extends Controller {

    public function index() {
        if (!Auth::check()) {
            $this->redirect('login');
        }

        $instituicoes = InstituicaoModel::getAll();
        $recursos = RecursoModel::getAll();
        $salas = SalaModel::getAll();
        $tipos = TipoRecursoModel::getAll();
        $config = ConfiguracaoModel::getConfig();
        $user = Auth::user();

        // Filtrar instituições vinculadas ao usuário se não for Administrador total
        $instituicoesUsuario = $instituicoes;
        if (!empty($user['instituicoes_vinculadas']) && is_array($user['instituicoes_vinculadas']) && ($user['grupo_nome'] ?? '') !== 'Administrador') {
            $instituicoesUsuario = array_values(array_filter($instituicoes, function ($i) use ($user) {
                return in_array($i['id'], $user['instituicoes_vinculadas']);
            }));
        }

        $data = [
            'instituicoes' => $instituicoesUsuario,
            'recursos' => $recursos,
            'tipos' => $tipos,
            'salas' => $salas,
            'config' => $config,
            'somenteUmaInstituicao' => (count($instituicoesUsuario) === 1),
            'instituicaoUnica' => count($instituicoesUsuario) === 1 ? $instituicoesUsuario[0] : null
        ];

        $this->render('agenda/index', $data);
    }

    public function aprovacoesView() {
        if (!Auth::check()) {
            $this->redirect('login');
        }

        $agendamentos = AgendamentoModel::getAll();
        $config = ConfiguracaoModel::getConfig();

        usort($agendamentos, function ($a, $b) {
            return strtotime($b['created_at'] ?? '0') - strtotime($a['created_at'] ?? '0');
        });

        $pendentes = array_values(array_filter($agendamentos, function ($a) {
            return $a['status'] === 'Pendente';
        }));

        $historico = array_values(array_filter($agendamentos, function ($a) {
            return $a['status'] !== 'Pendente';
        }));

        $this->render('agenda/aprovacoes', [
            'agendamentosPendentes' => $pendentes,
            'historicoAgendamentos' => $historico,
            'config' => $config
        ]);
    }

    public function getEvents() {
        if (!Auth::check()) {
            return $this->json([]);
        }

        $user = Auth::user();
        $isAdmin = ($user['grupo_nome'] ?? '') === 'Administrador';
        $currentUserId = $user['id'];
        $instituicaoId = $_GET['instituicao_id'] ?? null;
        $agendamentos = AgendamentoModel::getAll();

        $events = [];
        foreach ($agendamentos as $ag) {
            if ($instituicaoId && (string)$ag['instituicao_id'] !== (string)$instituicaoId) {
                continue;
            }

            $isOwner = (string)($ag['usuario_id'] ?? '') === (string)$currentUserId;

            // Formatação rica dos Itens Reservados
            $itensObjetos = [];
            if (!empty($ag['recursos_ids']) && is_array($ag['recursos_ids'])) {
                foreach ($ag['recursos_ids'] as $rId) {
                    $rec = RecursoModel::getById($rId);
                    if ($rec) {
                        $itensObjetos[] = [
                            'tipo' => 'recurso',
                            'nome' => $rec['nome'],
                            'patrimonio' => $rec['patrimonio'] ?? ''
                        ];
                    }
                }
            }
            if (!empty($ag['salas_ids']) && is_array($ag['salas_ids'])) {
                foreach ($ag['salas_ids'] as $sId) {
                    $sala = SalaModel::getById($sId);
                    if ($sala) {
                        $itensObjetos[] = [
                            'tipo' => 'sala',
                            'nome' => $sala['nome'],
                            'patrimonio' => ''
                        ];
                    }
                }
            }

            $itensNomesOnly = array_column($itensObjetos, 'nome');
            $itensStr = !empty($itensNomesOnly) ? implode(', ', $itensNomesOnly) : 'Recurso/Sala';

            $extendedProps = $ag;
            $extendedProps['is_owner'] = $isOwner;
            $extendedProps['is_admin'] = $isAdmin;
            $extendedProps['itens_objetos'] = $itensObjetos;
            $extendedProps['itens_str'] = $itensStr;

            $title = ($ag['prioridade_emergencia'] ? '⚡ ' : '') . $ag['usuario_nome'] . ' - ' . $itensStr;

            $color = '#3b82f6'; // Azul
            if ($ag['status'] === 'Pendente') $color = '#f59e0b';
            if (strpos($ag['status'], 'Cancelado') !== false) $color = '#ef4444';

            $events[] = [
                'id' => $ag['id'],
                'title' => $title,
                'start' => date('c', strtotime($ag['data_inicio'])),
                'end' => date('c', strtotime($ag['data_fim'])),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => $extendedProps
            ];
        }

        return $this->json($events);
    }

    public function store() {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        }

        $data = $this->getPostData();
        $user = Auth::user();
        $config = ConfiguracaoModel::getConfig();

        $dataInicio = trim($data['data_inicio'] ?? '');
        $dataFim = trim($data['data_fim'] ?? '');
        $tipoUso = trim($data['tipo_uso'] ?? 'Unidade');
        $motivo = trim($data['motivo'] ?? '');
        $recursosIds = isset($data['recursos_ids']) && is_array($data['recursos_ids']) ? array_map('intval', $data['recursos_ids']) : [];
        $salasIds = isset($data['salas_ids']) && is_array($data['salas_ids']) ? array_map('intval', $data['salas_ids']) : [];
        $instituicaoId = (int)($data['instituicao_id'] ?? 1);
        $observacoes = trim($data['observacoes'] ?? '');
        $isEmergencia = !empty($data['prioridade_emergencia']);

        // Validações
        if (empty($dataInicio) || empty($dataFim)) {
            return $this->json(['success' => false, 'message' => 'Informe a data/horário de início e término.']);
        }

        if ($tipoUso === 'Externo' && empty($motivo)) {
            return $this->json(['success' => false, 'message' => 'Para Uso Externo, o preenchimento da justificativa é obrigatório.']);
        }

        if (empty($recursosIds) && empty($salasIds)) {
            return $this->json(['success' => false, 'message' => 'Selecione ao menos um equipamento ou sala.']);
        }

        // Validação de Recursos em Manutenção
        foreach ($recursosIds as $recId) {
            $rec = RecursoModel::getById($recId);
            if ($rec && ($rec['estado'] ?? '') === 'Não Funcionando') {
                return $this->json([
                    'success' => false, 
                    'message' => "O recurso '{$rec['nome']}' está em MANUTENÇÃO e bloqueado para novos agendamentos."
                ]);
            }
        }

        // Checagem de Conflitos e Tolerância
        $conflitos = AgendamentoModel::checkConflict($dataInicio, $dataFim, $recursosIds, $salasIds);

        if (!empty($conflitos)) {
            if ($isEmergencia) {
                // Sobreposição de Emergência ativada
                AgendamentoModel::processEmergencyOverride($conflitos, $user['nome']);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Existe choque de horário ou intervalo de tolerância insuficiente com agendamentos existentes.',
                    'conflitos' => $conflitos
                ]);
            }
        }

        // Status Inicial: Agendamento Direto = Aprovado ou Pendente
        $agendamentoDireto = isset($config['agendamento_direto']) ? (bool)$config['agendamento_direto'] : false;
        $statusInicial = $agendamentoDireto ? 'Aprovado' : 'Pendente';

        $novoAgendamento = [
            'usuario_id' => $user['id'],
            'usuario_nome' => $user['nome'],
            'tipo_uso' => $tipoUso,
            'motivo' => $motivo,
            'data_inicio' => date('Y-m-d H:i:s', strtotime($dataInicio)),
            'data_fim' => date('Y-m-d H:i:s', strtotime($dataFim)),
            'recursos_ids' => $recursosIds,
            'salas_ids' => $salasIds,
            'instituicao_id' => $instituicaoId,
            'observacoes' => $observacoes,
            'prioridade_emergencia' => $isEmergencia,
            'status' => $statusInicial,
            'motivo_cancelamento' => null
        ];

        $created = AgendamentoModel::create($novoAgendamento);

        if ($created) {
            // Notificações
            if ($statusInicial === 'Pendente') {
                Notification::create(
                    1, 
                    'Novo Agendamento Pendente de Aprovação', 
                    "O usuário {$user['nome']} solicitou agendamento #{$created['id']}. Requer análise.",
                    'warning'
                );
            }

            // Simulação de E-mail
            Notification::sendEmailSimulated(
                $user['email'],
                $user['nome'],
                "Solicitação de Agendamento #{$created['id']} - AgendaAI",
                "<p>Sua reserva foi cadastrada com sucesso! Status atual: <strong>{$statusInicial}</strong>.</p>"
            );

            return $this->json([
                'success' => true, 
                'message' => $statusInicial === 'Aprovado' ? 'Agendamento confirmado com sucesso!' : 'Agendamento cadastrado e enviado para aprovação!',
                'agendamento' => $created
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Erro ao salvar agendamento.']);
    }

    /**
     * Agendamento em Massa (Múltiplas Datas)
     */
    public function storeMassa() {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        }

        $data = $this->getPostData();
        $user = Auth::user();
        $config = ConfiguracaoModel::getConfig();

        if (empty($config['agendamento_massa_habilitado'])) {
            return $this->json(['success' => false, 'message' => 'A funcionalidade de Agendamento em Massa está desativada no sistema.']);
        }

        $datasSelecionadas = isset($data['datas']) && is_array($data['datas']) ? $data['datas'] : [];
        $horarioInicio = trim($data['horario_inicio'] ?? '');
        $horarioFim = trim($data['horario_fim'] ?? '');
        $recursosIds = isset($data['recursos_ids']) && is_array($data['recursos_ids']) ? array_map('intval', $data['recursos_ids']) : [];
        $salasIds = isset($data['salas_ids']) && is_array($data['salas_ids']) ? array_map('intval', $data['salas_ids']) : [];
        $tipoUso = trim($data['tipo_uso'] ?? 'Unidade');
        $motivo = trim($data['motivo'] ?? '');
        $instituicaoId = (int)($data['instituicao_id'] ?? 1);

        if (empty($datasSelecionadas) || empty($horarioInicio) || empty($horarioFim)) {
            return $this->json(['success' => false, 'message' => 'Selecione os dias e os horários de início/término.']);
        }

        $limiteDias = (int)($config['limite_dias_agendamento_massa'] ?? 30);
        if (count($datasSelecionadas) > $limiteDias) {
            return $this->json(['success' => false, 'message' => "O limite máximo configurado para agendamentos em massa é de {$limiteDias} dias."]);
        }

        $agendamentosCriados = [];
        $datasParciaisComConflito = [];

        foreach ($datasSelecionadas as $dataStr) {
            $dtInicioStr = "{$dataStr} {$horarioInicio}:00";
            $dtFimStr = "{$dataStr} {$horarioFim}:00";

            $conflitos = AgendamentoModel::checkConflict($dtInicioStr, $dtFimStr, $recursosIds, $salasIds);

            if (!empty($conflitos)) {
                $datasParciaisComConflito[] = [
                    'data' => date('d/m/Y', strtotime($dataStr)),
                    'conflitos' => $conflitos
                ];
                continue; // Pula este dia ou o usuário poderá ajustar
            }

            $agendamentoDireto = isset($config['agendamento_direto']) ? (bool)$config['agendamento_direto'] : false;
            $statusInicial = $agendamentoDireto ? 'Aprovado' : 'Pendente';

            $novoAg = [
                'usuario_id' => $user['id'],
                'usuario_nome' => $user['nome'],
                'tipo_uso' => $tipoUso,
                'motivo' => $motivo,
                'data_inicio' => $dtInicioStr,
                'data_fim' => $dtFimStr,
                'recursos_ids' => $recursosIds,
                'salas_ids' => $salasIds,
                'instituicao_id' => $instituicaoId,
                'observacoes' => "Agendamento em Massa (Dia {$dataStr})",
                'prioridade_emergencia' => false,
                'status' => $statusInicial,
                'motivo_cancelamento' => null
            ];

            $created = AgendamentoModel::create($novoAg);
            if ($created) {
                $agendamentosCriados[] = $created;
            }
        }

        return $this->json([
            'success' => true,
            'total_criados' => count($agendamentosCriados),
            'dias_com_conflito' => $datasParciaisComConflito,
            'message' => "Processamento em massa finalizado: " . count($agendamentosCriados) . " dias reservados com sucesso."
        ]);
    }

    public function aprovar() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();
        $id = $data['id'] ?? null;
        $status = $data['status'] ?? 'Aprovado'; // Aprovado ou Recusado

        if (!$id) return $this->json(['success' => false, 'message' => 'ID inválido.']);

        $ag = AgendamentoModel::getById($id);
        if (!$ag) return $this->json(['success' => false, 'message' => 'Agendamento não encontrado.']);

        $updated = AgendamentoModel::update($id, ['status' => $status]);

        if ($updated) {
            Notification::create(
                $ag['usuario_id'],
                "Agendamento #{$id} " . ($status === 'Aprovado' ? 'Aprovado ✅' : 'Recusado ❌'),
                "Seu agendamento para " . date('d/m/Y H:i', strtotime($ag['data_inicio'])) . " foi marcado como {$status}.",
                $status === 'Aprovado' ? 'success' : 'danger'
            );

            return $this->json(['success' => true, 'message' => "Agendamento #{$id} alterado para {$status}."]);
        }

        return $this->json(['success' => false, 'message' => 'Erro ao atualizar agendamento.']);
    }

    public function cancelar() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();
        $id = $data['id'] ?? null;
        $motivo = trim($data['motivo_cancelamento'] ?? '');
        $config = ConfiguracaoModel::getConfig();

        if (!$id) return $this->json(['success' => false, 'message' => 'ID de agendamento inválido.']);

        $ag = AgendamentoModel::getById($id);
        if (!$ag) return $this->json(['success' => false, 'message' => 'Agendamento não encontrado.']);

        // Verificação do Prazo Limite para Cancelamento
        $prazoHoras = (int)($config['prazo_cancelamento_horas'] ?? 2);
        $limiteTs = strtotime($ag['data_inicio']) - ($prazoHoras * 3600);
        
        if (time() > $limiteTs && (Auth::user()['grupo_nome'] ?? '') !== 'Administrador') {
            return $this->json([
                'success' => false, 
                'message' => "O prazo limite para cancelamento antecedente ({$prazoHoras}h antes do início) foi excedido."
            ]);
        }

        // Justificativa Obrigatória
        $justificativaObrigatoria = isset($config['justificativa_cancelamento_obrigatoria']) ? (bool)$config['justificativa_cancelamento_obrigatoria'] : true;
        if ($justificativaObrigatoria && empty($motivo)) {
            return $this->json(['success' => false, 'message' => 'Por favor, informe a justificativa do cancelamento.']);
        }

        $updated = AgendamentoModel::update($id, [
            'status' => 'Cancelado',
            'motivo_cancelamento' => $motivo
        ]);

        if ($updated) {
            return $this->json(['success' => true, 'message' => 'Agendamento cancelado com sucesso.']);
        }

        return $this->json(['success' => false, 'message' => 'Erro ao cancelar agendamento.']);
    }

    public function salvarFeedback() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $agendamentoId = (int)($data['agendamento_id'] ?? 0);
        $recursoId = !empty($data['recurso_id']) ? (int)$data['recurso_id'] : null;
        $comentario = trim($data['comentario'] ?? '');
        $alunoUtilizador = trim($data['aluno_utilizador'] ?? '');

        if (!$agendamentoId) return $this->json(['success' => false, 'message' => 'Agendamento inválido.']);

        // Upload de Imagem Opcional
        $imagemPath = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            if (!is_dir(UPLOAD_PATH)) {
                @mkdir(UPLOAD_PATH, 0777, true);
            }
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $fileName = 'feedback_' . time() . '_' . uniqid() . '.' . $ext;
            $destination = UPLOAD_PATH . '/' . $fileName;
            if (move_uploaded_filename($_FILES['imagem']['tmp_name'], $destination)) {
                $imagemPath = 'storage/uploads/' . $fileName;
            }
        }

        $created = FeedbackModel::create([
            'agendamento_id' => $agendamentoId,
            'recurso_id' => $recursoId,
            'comentario' => $comentario,
            'aluno_utilizador' => $alunoUtilizador,
            'imagem_path' => $imagemPath,
            'data_registro' => date('Y-m-d H:i:s')
        ]);

        return $this->json(['success' => true, 'message' => 'Feedback registrado com sucesso!', 'feedback' => $created]);
    }

    public function checkDisponibilidade() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $dataInicio = trim($data['data_inicio'] ?? '');
        $dataFim = trim($data['data_fim'] ?? '');

        if (empty($dataInicio) || empty($dataFim)) {
            return $this->json(['success' => false, 'message' => 'Data e horários são obrigatórios.']);
        }

        $recursos = RecursoModel::getAll();
        $salas = SalaModel::getAll();

        $disponibilidadeRecursos = [];
        $disponibilidadeSalas = [];
        $motivosIndisponibilidade = [];

        foreach ($recursos as $r) {
            $rId = (int)$r['id'];
            if (($r['estado'] ?? '') === 'Não Funcionando') {
                $disponibilidadeRecursos[$rId] = false;
                $motivosIndisponibilidade['recurso_' . $rId] = 'Em Manutenção';
                continue;
            }

            $conflitos = AgendamentoModel::checkConflict($dataInicio, $dataFim, [$rId], []);
            if (!empty($conflitos)) {
                $disponibilidadeRecursos[$rId] = false;
                $ag = $conflitos[0]['agendamento'];
                $motivosIndisponibilidade['recurso_' . $rId] = "Reservado por " . htmlspecialchars($ag['usuario_nome']);
            } else {
                $disponibilidadeRecursos[$rId] = true;
            }
        }

        foreach ($salas as $s) {
            $sId = (int)$s['id'];
            $conflitos = AgendamentoModel::checkConflict($dataInicio, $dataFim, [], [$sId]);
            if (!empty($conflitos)) {
                $disponibilidadeSalas[$sId] = false;
                $ag = $conflitos[0]['agendamento'];
                $motivosIndisponibilidade['sala_' . $sId] = "Reservada por " . htmlspecialchars($ag['usuario_nome']);
            } else {
                $disponibilidadeSalas[$sId] = true;
            }
        }

        return $this->json([
            'success' => true,
            'disponibilidade_recursos' => $disponibilidadeRecursos,
            'disponibilidade_salas' => $disponibilidadeSalas,
            'motivos_indisponibilidade' => $motivosIndisponibilidade
        ]);
    }
}
