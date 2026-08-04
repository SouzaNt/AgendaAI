<?php
$usuario = $_SESSION['usuario'] ?? null;
$primeiroNome = strtok($usuario['nome'] ?? 'Usuário', ' ');
?>

<!-- Hero Banner Corporativo Profissional -->
<div class="card card-glass border-0 mb-4 overflow-hidden position-relative hero-dashboard-card" style="background: linear-gradient(135deg, #004b87 0%, #00284d 100%); color: #ffffff;">
    <div class="position-absolute end-0 bottom-0 opacity-10 p-3 d-none d-md-block pointer-events-none">
        <i class="fa-solid fa-layer-group" style="font-size: 14rem; margin-right: -2rem; margin-bottom: -3rem;"></i>
    </div>
    
    <div class="card-body p-4 p-lg-5 position-relative z-1">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-circle-dot text-success me-1"></i> SISTEMA OPERACIONAL SENAC
                    </span>
                    <span class="text-white-50 small"><i class="fa-solid fa-shield-halved me-1"></i> Ambiente Seguro</span>
                </div>
                
                <h2 class="fw-bold mb-2 text-white" style="font-size: 1.85rem; letter-spacing: -0.5px;">
                    Olá, <?= htmlspecialchars($primeiroNome) ?>! <span class="fs-4 fw-normal text-white-50">Bem-vindo ao Painel de Controle</span>
                </h2>
                
                <p class="text-white-50 mb-4" style="max-width: 620px; font-size: 0.95rem; line-height: 1.5;">
                    Gerencie com precisão o inventário de equipamentos, acompanhe o fluxo de aprovações de agendamentos e controle a disponibilidade das unidades Senac em tempo real.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= BASE_URL ?>/agenda" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm hover-lift">
                        <i class="fa-solid fa-calendar-plus me-2"></i> Nova Reserva
                    </a>
                    <a href="<?= BASE_URL ?>/recursos" class="btn btn-outline-light rounded-pill px-4 hover-lift">
                        <i class="fa-solid fa-laptop me-2"></i> Gerenciar Equipamentos
                    </a>
                    <?php if ($totalPendentes > 0): ?>
                    <a href="<?= BASE_URL ?>/aprovacoes" class="btn btn-danger text-white fw-bold rounded-pill px-3 animate-pulse">
                        <i class="fa-solid fa-bell me-1"></i> <?= $totalPendentes ?> Pendência(s)
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4 text-lg-end d-none d-lg-block">
                <div class="card card-glass border-0 bg-white bg-opacity-10 p-3 rounded-4 text-center">
                    <div class="small text-white-50 text-uppercase fw-bold mb-1">Disponibilidade Operacional</div>
                    <div class="display-6 fw-bold text-warning mb-1"><?= $taxaOperacional ?>%</div>
                    <small class="text-white-50 d-block mb-2">Equipamentos em pleno funcionamento</small>
                    <div class="progress rounded-pill bg-white bg-opacity-20" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $taxaOperacional ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scorecards de KPIs Principais -->
<div class="row g-3 g-md-4 mb-4">
    <!-- Stat 1: Total Reservas -->
    <div class="col-6 col-xl-3">
        <div class="card-glass stat-widget p-3 p-md-4 h-100">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Total de Agendamentos</span>
                <h3 class="fw-bold mb-0 text-body"><?= number_format($totalAgendamentos, 0, ',', '.') ?></h3>
                <small class="text-success fw-semibold"><i class="fa-solid fa-arrow-trend-up me-1"></i><?= $totalAprovados ?> Confirmados</small>
            </div>
            <div class="stat-icon blue">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Stat 2: Aprovações Pendentes -->
    <div class="col-6 col-xl-3">
        <div class="card-glass stat-widget p-3 p-md-4 h-100 border-start border-warning border-4">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Aprovações Pendentes</span>
                <h3 class="fw-bold mb-0 text-warning"><?= $totalPendentes ?></h3>
                <small class="<?= $totalPendentes > 0 ? 'text-warning fw-bold' : 'text-muted' ?>">
                    <?= $totalPendentes > 0 ? '⚠️ Aguardando decisão' : '✓ Fila zerada' ?>
                </small>
            </div>
            <div class="stat-icon amber">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
    </div>

    <!-- Stat 3: Recursos Cadastrados -->
    <div class="col-6 col-xl-3">
        <div class="card-glass stat-widget p-3 p-md-4 h-100">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Total de Equipamentos</span>
                <h3 class="fw-bold mb-0 text-body"><?= $totalRecursos ?></h3>
                <small class="text-muted"><i class="fa-solid fa-door-open me-1"></i><?= $totalSalas ?> Salas Cadastradas</small>
            </div>
            <div class="stat-icon emerald">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>
    </div>

    <!-- Stat 4: Recursos em Manutenção -->
    <div class="col-6 col-xl-3">
        <div class="card-glass stat-widget p-3 p-md-4 h-100 border-start border-danger border-4">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Em Manutenção</span>
                <h3 class="fw-bold mb-0 text-danger"><?= count($recursosManutencaoList) ?></h3>
                <small class="<?= count($recursosManutencaoList) > 0 ? 'text-danger fw-bold' : 'text-success' ?>">
                    <?= count($recursosManutencaoList) > 0 ? '🚫 Bloqueados para uso' : '✓ 100% Liberados' ?>
                </small>
            </div>
            <div class="stat-icon rose">
                <i class="fa-solid fa-wrench"></i>
            </div>
        </div>
    </div>
</div>

<!-- Hub de Ações Rápidas -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <h6 class="fw-bold text-muted text-uppercase mb-2" style="font-size: 0.78rem; letter-spacing: 0.8px;">
            <i class="fa-solid fa-bolt text-warning me-1"></i> Atalhos de Gestão Rápida
        </h6>
    </div>

    <div class="col-6 col-md-3">
        <a href="<?= BASE_URL ?>/agenda" class="card card-glass border-0 text-decoration-none p-3 h-100 hover-lift text-body">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary fs-4">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <strong class="d-block mb-0 text-primary">Módulo Agenda</strong>
                    <small class="text-muted">Consultar e Agendar</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="<?= BASE_URL ?>/recursos" class="card card-glass border-0 text-decoration-none p-3 h-100 hover-lift text-body">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success fs-4">
                    <i class="fa-solid fa-laptop"></i>
                </div>
                <div>
                    <strong class="d-block mb-0 text-success">Equipamentos</strong>
                    <small class="text-muted">Gestão & Patrimônio</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="<?= BASE_URL ?>/salas" class="card card-glass border-0 text-decoration-none p-3 h-100 hover-lift text-body">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-warning bg-opacity-10 text-warning fs-4">
                    <i class="fa-solid fa-door-open"></i>
                </div>
                <div>
                    <strong class="d-block mb-0 text-warning">Salas de Aula</strong>
                    <small class="text-muted">Laboratórios & Auditórios</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="<?= BASE_URL ?>/relatorios" class="card card-glass border-0 text-decoration-none p-3 h-100 hover-lift text-body">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-info bg-opacity-10 text-info fs-4">
                    <i class="fa-solid fa-chart-column"></i>
                </div>
                <div>
                    <strong class="d-block mb-0 text-info">Relatórios BI</strong>
                    <small class="text-muted">Indicadores & PDF</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Grid Principal Operacional -->
<div class="row g-4 mb-4">
    <!-- Fila de Aprovações Pendentes -->
    <div class="col-12 col-lg-7">
        <div class="card card-glass border-0 h-100 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-4 px-4 pb-2">
                <div>
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-clipboard-check text-warning fs-4"></i> Soluções & Aprovações Pendentes
                    </h5>
                    <small class="text-muted">Solicitações que aguardam validação de autorização</small>
                </div>
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-bold">
                    <?= count($agendamentosPendentes) ?> na Fila
                </span>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($agendamentosPendentes)): ?>
                    <div class="text-center py-5 text-muted">
                        <div class="stat-icon emerald mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                        <h6 class="fw-bold text-success mb-1">Nenhuma solicitação pendente!</h6>
                        <p class="mb-0 small text-muted">Todas as solicitações de empréstimo e reserva já foram processadas.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light opacity-75">
                                <tr>
                                    <th>#ID</th>
                                    <th>Solicitante</th>
                                    <th>Período</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Ações Rápida</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agendamentosPendentes as $ag): ?>
                                <tr id="linha-aprovacao-<?= $ag['id'] ?>">
                                    <td><strong class="text-primary">#<?= $ag['id'] ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                <?= strtoupper(substr($ag['usuario_nome'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <strong class="d-block text-truncate" style="max-width: 140px;"><?= htmlspecialchars($ag['usuario_nome'] ?? 'Usuário') ?></strong>
                                                <small class="text-muted d-block" style="font-size: 0.72rem;"><?= htmlspecialchars($ag['motivo'] ?? 'Reserva') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="d-block fw-semibold text-body"><?= date('d/m/Y H:i', strtotime($ag['data_inicio'])) ?></small>
                                        <small class="text-muted" style="font-size: 0.75rem;">até <?= date('d/m/Y H:i', strtotime($ag['data_fim'])) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?= ($ag['tipo_uso'] ?? '') === 'Externo' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' ?> rounded-pill px-2">
                                            <?= htmlspecialchars($ag['tipo_uso'] ?? 'Unidade') ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-success rounded-start-pill px-3" onclick="aprovarAgendamentoDashboard(<?= $ag['id'] ?>, 'Aprovado')" title="Aprovar Solicitação">
                                                <i class="fa-solid fa-check me-1"></i> Aprovar
                                            </button>
                                            <button type="button" class="btn btn-outline-danger rounded-end-pill px-2" onclick="aprovarAgendamentoDashboard(<?= $ag['id'] ?>, 'Recusado')" title="Recusar Solicitação">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-transparent border-0 text-center pb-3">
                <a href="<?= BASE_URL ?>/aprovacoes" class="btn btn-sm btn-link text-decoration-none fw-semibold text-primary">
                    Ver Fila Completa de Aprovações <i class="fa-solid fa-arrow-right me-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recursos em Manutenção & Agendamentos do Dia -->
    <div class="col-12 col-lg-5 d-flex flex-column gap-4">
        <!-- Widget: Recursos em Manutenção -->
        <div class="card card-glass border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-4 px-4">
                <h5 class="fw-bold mb-0 text-danger d-flex align-items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Central de Manutenção
                </h5>
                <span class="badge bg-danger rounded-pill px-3 py-1"><?= count($recursosManutencaoList) ?> Inativo(s)</span>
            </div>

            <div class="card-body p-4">
                <?php if (empty($recursosManutencaoList)): ?>
                    <div class="text-center py-3 text-muted">
                        <i class="fa-solid fa-circle-check text-success fs-3 mb-2 d-block"></i>
                        <p class="mb-0 small fw-semibold">Todos os equipamentos estão funcionando e liberados para uso.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush gap-2">
                        <?php foreach ($recursosManutencaoList as $recM): ?>
                        <div class="list-group-item bg-body-tertiary rounded-3 border-0 p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <strong class="d-block text-primary mb-1"><?= htmlspecialchars($recM['nome']) ?></strong>
                                <small class="text-muted d-block" style="font-size: 0.78rem;">
                                    <i class="fa-solid fa-barcode me-1"></i> <?= htmlspecialchars($recM['patrimonio']) ?>
                                </small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="desbloquearRecursoDashboard(<?= $recM['id'] ?>)">
                                <i class="fa-solid fa-wrench me-1"></i> Liberar
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Widget: Reservas de Hoje -->
        <div class="card card-glass border-0 shadow-sm flex-grow-1">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-4 px-4">
                <h5 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock"></i> Reservas Marcadas para Hoje
                </h5>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold"><?= count($agendamentosHoje) ?> Evento(s)</span>
            </div>

            <div class="card-body p-4">
                <?php if (empty($agendamentosHoje)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-calendar-xmark fs-2 mb-2 d-block opacity-50"></i>
                        <p class="mb-0 small">Nenhum empréstimo agendado para o dia de hoje.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush gap-2">
                        <?php foreach ($agendamentosHoje as $agH): ?>
                        <div class="list-group-item bg-transparent border-bottom px-0 py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="text-primary d-block"><?= htmlspecialchars($agH['usuario_nome']) ?></strong>
                                    <small class="text-secondary d-block"><?= htmlspecialchars($agH['motivo']) ?></small>
                                </div>
                                <span class="badge bg-secondary-subtle text-body border font-monospace">
                                    <?= date('H:i', strtotime($agH['data_inicio'])) ?> - <?= date('H:i', strtotime($agH['data_fim'])) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function aprovarAgendamentoDashboard(id, status) {
    sendAjaxRequest(BASE_URL + '/api/agenda/aprovar', { id: id, status: status }, function(res) {
        $(`#linha-aprovacao-${id}`).fadeOut(300, function() { $(this).remove(); });
    });
}

function desbloquearRecursoDashboard(id) {
    bootbox.confirm({
        title: "Liberar Equipamento para Uso",
        message: "Deseja alterar o estado do equipamento para <strong>Funcionando</strong> e desbloqueá-lo para novos agendamentos?",
        buttons: { confirm: { label: 'Liberar Recurso', className: 'btn-success' } },
        callback: function(result) {
            if (result) {
                sendAjaxRequest(BASE_URL + '/api/recursos/estado', { id: id, estado: 'Funcionando' });
            }
        }
    });
}
</script>
