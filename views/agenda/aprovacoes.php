<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-clipboard-check text-warning me-2"></i> Módulo de Aprovação de Agendamentos</h3>
        <p class="text-muted mb-0">Centralize a análise, aprovação ou recusa de solicitações por instituição/unidade.</p>
    </div>

    <span class="badge bg-warning text-dark fs-6 px-4 py-2 rounded-pill shadow-sm">
        <i class="fa-solid fa-clock-rotate-left me-1"></i> <span id="badge-count-pendentes"><?= count($agendamentosPendentes) ?></span> Solicitação(ões) Pendente(s)
    </span>
</div>

<!-- Banner com Status do Agendamento Direto -->
<?php if (!empty($config['agendamento_direto'])): ?>
    <div class="alert alert-info border-0 rounded-4 mb-4 shadow-sm">
        <i class="fa-solid fa-circle-info me-2"></i> <strong>Configuração Atual:</strong> O <em>Agendamento Direto</em> está <strong>ATIVADO</strong>. As novas reservas são confirmadas automaticamente, mas você ainda pode revisar e gerenciar qualquer pedido por esta tela.
    </div>
<?php else: ?>
    <div class="alert alert-warning border-0 rounded-4 mb-4 shadow-sm">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Configuração Atual:</strong> O <em>Agendamento Direto</em> está <strong>DESATIVADO</strong>. Todas as solicitações requerem análise prévia e aprovação manual nesta tela.
    </div>
<?php endif; ?>

<!-- Filtro de Unidade / Instituição -->
<div class="card card-glass border-0 p-3 mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-3">
            <label for="filtro-inst-aprovacoes" class="form-label fw-semibold mb-md-0">
                <i class="fa-solid fa-building-columns text-primary me-2"></i> Filtrar por Unidade:
            </label>
        </div>
        <div class="col-12 col-md-9">
            <select class="form-select select2" id="filtro-inst-aprovacoes" onchange="filtrarAprovacoesPorInstituicao()">
                <option value="">Todas as Instituições / Unidades</option>
                <?php if (!empty($instituicoes)): ?>
                    <?php foreach ($instituicoes as $inst): ?>
                        <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nome']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
</div>

<!-- Abas: Fila de Análise vs Histórico -->
<ul class="nav nav-pills mb-4 gap-2">
    <li class="nav-item">
        <button class="nav-link active rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-pendentes">
            Fila de Análise (Pendentes)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-historico">
            Histórico Geral de Decisões
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- ABA 1: SOLICITAÇÕES PENDENTES -->
    <div class="tab-pane fade show active" id="tab-pendentes">
        <?php if (empty($agendamentosPendentes)): ?>
            <div class="card card-glass border-0 p-5 text-center">
                <div class="stat-icon emerald mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h4 class="fw-bold text-success">Fila Limpa!</h4>
                <p class="text-muted mb-0">Não há solicitações pendentes de análise no momento.</p>
            </div>
        <?php else: ?>
            <div class="card card-glass border-0 p-5 text-center d-none mb-4" id="msg-nenhum-pendente-filtro">
                <div class="stat-icon blue mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                    <i class="fa-solid fa-filter"></i>
                </div>
                <h4 class="fw-bold text-primary">Nenhuma Solicitação Pendente para esta Unidade</h4>
                <p class="text-muted mb-0">Não há pedidos aguardando análise referente à instituição selecionada.</p>
            </div>

            <div class="row g-4" id="grid-pendentes-aprovacao">
                <?php foreach ($agendamentosPendentes as $ag): ?>
                <?php 
                    $instObj = JsonDatabase::findById('instituicoes', $ag['instituicao_id']);
                    $instNome = $instObj['nome'] ?? 'Unidade Geral';
                ?>
                <div class="col-12 col-lg-6 card-aprovacao-item" data-instituicao-id="<?= $ag['instituicao_id'] ?>">
                    <div class="card card-glass border-0 h-100 p-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-warning text-dark mb-1 me-1">Pendente de Aprovação</span>
                                <h5 class="fw-bold mb-0">Solicitação #<?= $ag['id'] ?></h5>
                            </div>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($ag['created_at'] ?? $ag['data_inicio'])) ?></small>
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle p-2">
                                <i class="fa-solid fa-building-columns me-1"></i> <?= htmlspecialchars($instNome) ?>
                            </span>
                        </div>

                        <div class="mb-3">
                            <div class="fw-bold text-primary fs-6 mb-1">
                                <i class="fa-solid fa-user me-2"></i> <?= htmlspecialchars($ag['usuario_nome']) ?>
                            </div>
                            <div class="text-muted small">
                                <i class="fa-solid fa-tag me-2"></i> Tipo de Uso: 
                                <span class="badge <?= $ag['tipo_uso'] === 'Externo' ? 'bg-danger' : 'bg-primary' ?>"><?= htmlspecialchars($ag['tipo_uso']) ?></span>
                            </div>
                        </div>

                        <!-- Detalhes do Período -->
                        <div class="bg-body-tertiary p-3 rounded-3 mb-3">
                            <div class="row g-2 text-center">
                                <div class="col-6 border-end">
                                    <small class="text-muted d-block">Data / Início</small>
                                    <strong class="text-success"><?= date('d/m/Y H:i', strtotime($ag['data_inicio'])) ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Término</small>
                                    <strong class="text-danger"><?= date('d/m/Y H:i', strtotime($ag['data_fim'])) ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Itens Solicitados -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted mb-1">Itens Requisitados:</label>
                            <ul class="mb-0 ps-3 small fw-semibold">
                                <?php 
                                    if (!empty($ag['recursos_ids']) && is_array($ag['recursos_ids'])) {
                                        foreach ($ag['recursos_ids'] as $rId) {
                                            $rec = RecursoModel::getById($rId);
                                            echo '<li>💻 ' . htmlspecialchars($rec['nome'] ?? 'Recurso #' . $rId) . '</li>';
                                        }
                                    }
                                    if (!empty($ag['salas_ids']) && is_array($ag['salas_ids'])) {
                                        foreach ($ag['salas_ids'] as $sId) {
                                            $sala = SalaModel::getById($sId);
                                            echo '<li>🏫 ' . htmlspecialchars($sala['nome'] ?? 'Sala #' . $sId) . '</li>';
                                        }
                                    }
                                ?>
                            </ul>
                        </div>

                        <?php if (!empty($ag['motivo'])): ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted mb-0">Motivo / Justificativa:</label>
                                <p class="mb-0 small text-body bg-body-tertiary p-2 rounded border border-secondary-subtle">"<?= htmlspecialchars($ag['motivo']) ?>"</p>
                            </div>
                        <?php endif; ?>

                        <!-- Botões de Ação -->
                        <div class="d-flex gap-2 mt-auto pt-3 border-top">
                            <button class="btn btn-success flex-fill rounded-pill py-2" onclick="aprovarAgendamento(<?= $ag['id'] ?>, 'Aprovado')">
                                <i class="fa-solid fa-check me-1"></i> Aprovar Reserva
                            </button>
                            <button class="btn btn-outline-danger flex-fill rounded-pill py-2" onclick="recusarAgendamento(<?= $ag['id'] ?>)">
                                <i class="fa-solid fa-xmark me-1"></i> Recusar
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ABA 2: HISTÓRICO GERAL DE DECISÕES -->
    <div class="tab-pane fade" id="tab-historico">
        <div class="card card-glass border-0 p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable" id="tabela-historico-aprovacoes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Requisitante</th>
                            <th>Unidade / Instituição</th>
                            <th>Data / Período</th>
                            <th>Status</th>
                            <th>Tipo de Uso</th>
                            <th>Motivo / Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historicoAgendamentos as $hAg): ?>
                        <?php 
                            $hInstObj = JsonDatabase::findById('instituicoes', $hAg['instituicao_id']);
                            $hInstNome = $hInstObj['nome'] ?? 'Unidade Geral';
                        ?>
                        <tr>
                            <td><strong>#<?= $hAg['id'] ?></strong></td>
                            <td><strong class="text-primary"><?= htmlspecialchars($hAg['usuario_nome']) ?></strong></td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <i class="fa-solid fa-building-columns me-1"></i> <?= htmlspecialchars($hInstNome) ?>
                                </span>
                            </td>
                            <td>
                                <small class="d-block fw-semibold"><?= date('d/m/Y H:i', strtotime($hAg['data_inicio'])) ?></small>
                                <small class="text-muted">até <?= date('H:i', strtotime($hAg['data_fim'])) ?></small>
                            </td>
                            <td>
                                <?php if ($hAg['status'] === 'Aprovado'): ?>
                                    <span class="badge bg-success">Aprovado</span>
                                <?php elseif (strpos($hAg['status'], 'Cancelado') !== false || $hAg['status'] === 'Recusado'): ?>
                                    <span class="badge bg-danger"><?= htmlspecialchars($hAg['status']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($hAg['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $hAg['tipo_uso'] === 'Externo' ? 'bg-danger' : 'bg-primary' ?>"><?= htmlspecialchars($hAg['tipo_uso']) ?></span>
                            </td>
                            <td>
                                <small class="d-block text-truncate" style="max-width: 250px;">
                                    <?= htmlspecialchars($hAg['motivo'] ?? $hAg['motivo_cancelamento'] ?? '-') ?>
                                </small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function filtrarAprovacoesPorInstituicao() {
    const instId = $('#filtro-inst-aprovacoes').val();
    let visivelCount = 0;

    $('.card-aprovacao-item').each(function() {
        const itemInst = $(this).data('instituicao-id');
        if (!instId || String(itemInst) === String(instId)) {
            $(this).removeClass('d-none');
            visivelCount++;
        } else {
            $(this).addClass('d-none');
        }
    });

    $('#badge-count-pendentes').text(visivelCount);

    if (visivelCount === 0 && $('.card-aprovacao-item').length > 0) {
        $('#msg-nenhum-pendente-filtro').removeClass('d-none');
    } else {
        $('#msg-nenhum-pendente-filtro').addClass('d-none');
    }

    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabela-historico-aprovacoes')) {
        const table = $('#tabela-historico-aprovacoes').DataTable();
        if (instId) {
            const selectedText = $('#filtro-inst-aprovacoes option:selected').text().trim();
            table.column(2).search(selectedText).draw();
        } else {
            table.column(2).search('').draw();
        }
    }
}

function aprovarAgendamento(id, status) {
    sendAjaxRequest(BASE_URL + '/api/agenda/aprovar', { id: id, status: status });
}

function recusarAgendamento(id) {
    bootbox.prompt({
        title: "Recusar Agendamento #" + id,
        message: "Por favor, informe a justificativa da recusa para notificar o requisitante:",
        inputType: 'textarea',
        placeholder: "Informe o motivo...",
        buttons: { confirm: { label: 'Confirmar Recusa', className: 'btn-danger' } },
        callback: function (result) {
            if (result !== null) {
                sendAjaxRequest(BASE_URL + '/api/agenda/aprovar', { id: id, status: 'Recusado: ' + result });
            }
        }
    });
}
</script>
