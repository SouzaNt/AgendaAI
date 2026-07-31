<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-user-shield text-primary me-2"></i> Grupos de Acesso & Matriz de Permissões por Tela</h3>
        <p class="text-muted mb-0">Controle granular de acessos por tela (Visualizar, Consultar, Cadastrar, Editar, Deletar).</p>
    </div>

    <button class="btn btn-primary-custom rounded-pill px-4" onclick="abrirModalGrupo()">
        <i class="fa-solid fa-plus me-1"></i> Cadastrar Novo Grupo
    </button>
</div>

<div class="card card-glass border-0 p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Grupo</th>
                    <th>Telas Acessíveis</th>
                    <th>Ações Permitidas</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grupos as $g): ?>
                <?php $p = $g['permissoes'] ?? []; ?>
                <tr>
                    <td><strong>#<?= $g['id'] ?></strong></td>
                    <td><strong class="text-primary"><?= htmlspecialchars($g['nome']) ?></strong></td>
                    <td>
                        <?php 
                        if ($g['nome'] === 'Administrador') {
                            echo '<span class="badge bg-success">Acesso Total (Todas as Telas)</span>';
                        } else {
                            $telas = [];
                            foreach ($p as $telaKey => $acoes) {
                                if (!empty($acoes['visualizar'])) {
                                    $telas[] = ucfirst($telaKey);
                                }
                            }
                            echo !empty($telas) ? implode(', ', $telas) : '<span class="badge bg-secondary">Nenhuma Tela</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($g['nome'] === 'Administrador'): ?>
                            <span class="badge bg-primary">Full Control (Visualizar, Consultar, Cadastrar, Editar, Deletar)</span>
                        <?php else: ?>
                            <span class="badge bg-info text-dark">Personalizado</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary rounded-pill me-1" onclick='editarGrupo(<?= json_encode($g) ?>)'>
                            <i class="fa-solid fa-pen-to-square"></i> Editar Permissões
                        </button>
                        <?php if ($g['nome'] !== 'Administrador'): ?>
                            <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="deletarGrupo(<?= $g['id'] ?>)">
                                <i class="fa-solid fa-trash-can"></i> Excluir
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal CRUD Grupo com Matriz de Permissões por Tela -->
<div class="modal fade" id="modal-grupo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content card-glass border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="modal-grupo-titulo">Gerenciar Grupo de Acesso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-grupo">
                <input type="hidden" id="g-id" value="">
                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nome do Grupo de Acesso *</label>
                        <input type="text" class="form-control form-control-lg" id="g-nome" required placeholder="Ex: Professor / Agendador">
                    </div>

                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-lock me-2 text-primary"></i> Matriz de Permissões Granulares por Tela</h6>
                    <p class="text-muted small mb-3">Defina exatamente quais ações (Visualizar, Consultar, Cadastrar, Editar, Deletar) este perfil possui em cada módulo do sistema.</p>

                    <div class="table-responsive border rounded-3">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th class="text-start">Módulo / Tela do Sistema</th>
                                    <th>👁️ Visualizar (Acessar)</th>
                                    <th>🔍 Consultar (Ver dados)</th>
                                    <th>➕ Cadastrar (Novo)</th>
                                    <th>✏️ Editar (Alterar)</th>
                                    <th>❌ Deletar (Excluir)</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-matriz-permissoes">
                                <?php 
                                $modulos = [
                                    'dashboard' => 'Painel BI / Dashboard',
                                    'agenda' => 'Módulo Agenda & Reservas',
                                    'aprovacoes' => 'Fila de Aprovação de Agendamentos',
                                    'recursos' => 'Gestão de Equipamentos / Recursos',
                                    'salas' => 'Gestão de Salas de Aula',
                                    'instituicoes' => 'Gestão de Instituições & Unidades',
                                    'usuarios' => 'Gestão de Funcionários & Perfis',
                                    'localizacao' => 'Base Geográfica (Cidades/Bairros)',
                                    'relatorios' => 'Relatórios BI & Exportação PDF',
                                    'auditoria' => 'Logs & Auditoria do Sistema',
                                    'configuracoes' => 'Configurações Gerais do Sistema'
                                ];
                                foreach ($modulos as $key => $rotulo):
                                ?>
                                <tr>
                                    <td><strong><?= $rotulo ?></strong> <small class="text-muted d-block">(<?= $key ?>)</small></td>
                                    <td class="text-center"><input class="form-check-input perm-chk" type="checkbox" data-tela="<?= $key ?>" data-acao="visualizar"></td>
                                    <td class="text-center"><input class="form-check-input perm-chk" type="checkbox" data-tela="<?= $key ?>" data-acao="consultar"></td>
                                    <td class="text-center"><input class="form-check-input perm-chk" type="checkbox" data-tela="<?= $key ?>" data-acao="cadastrar"></td>
                                    <td class="text-center"><input class="form-check-input perm-chk" type="checkbox" data-tela="<?= $key ?>" data-acao="editar"></td>
                                    <td class="text-center"><input class="form-check-input perm-chk" type="checkbox" data-tela="<?= $key ?>" data-acao="deletar"></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-5">Salvar Permissões do Grupo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalGrupo() {
    $('#g-id').val('');
    $('#g-nome').val('');
    $('#modal-grupo-titulo').text('Cadastrar Novo Grupo de Acesso');
    $('.perm-chk').prop('checked', false);
    
    // Padrão amigável: marcar agenda com permissão total para novos grupos
    $(`.perm-chk[data-tela="agenda"]`).prop('checked', true);
    
    $('#modal-grupo').modal('show');
}

function editarGrupo(grupo) {
    $('#g-id').val(grupo.id);
    $('#g-nome').val(grupo.nome);
    $('#modal-grupo-titulo').text('Editar Grupo: ' + grupo.nome);
    $('.perm-chk').prop('checked', false);

    const permissoes = grupo.permissoes || {};
    
    // Preenche a matriz de checkboxes conforme o objeto salvo
    Object.keys(permissoes).forEach(tela => {
        const acoes = permissoes[tela];
        if (typeof acoes === 'object') {
            Object.keys(acoes).forEach(acao => {
                if (acoes[acao]) {
                    $(`.perm-chk[data-tela="${tela}"][data-acao="${acao}"]`).prop('checked', true);
                }
            });
        }
    });

    $('#modal-grupo').modal('show');
}

$('#form-grupo').on('submit', function(e) {
    e.preventDefault();

    const permissoesObj = {};
    $('.perm-chk').each(function() {
        const tela = $(this).data('tela');
        const acao = $(this).data('acao');
        const checked = $(this).is(':checked');

        if (!permissoesObj[tela]) {
            permissoesObj[tela] = {};
        }
        permissoesObj[tela][acao] = checked;
    });

    sendAjaxRequest(BASE_URL + '/api/grupos/store', {
        id: $('#g-id').val() || null,
        nome: $('#g-nome').val(),
        permissoes: permissoesObj
    });
});

function deletarGrupo(id) {
    bootbox.confirm({
        title: "Excluir Grupo",
        message: "Tem certeza que deseja remover este grupo?",
        buttons: { confirm: { label: 'Excluir', className: 'btn-danger' } },
        callback: function(result) {
            if (result) sendAjaxRequest(BASE_URL + '/api/grupos/delete', { id: id });
        }
    });
}
</script>
