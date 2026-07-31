<div class="row g-4 mb-4">
    <!-- Stat 1: Total Agendamentos -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-glass stat-widget">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Total de Agendamentos</span>
                <h2 class="fw-bold mb-0"><?= $totalAgendamentos ?></h2>
            </div>
            <div class="stat-icon blue">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Stat 2: Solicitacoes Pendentes -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-glass stat-widget">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Pendentes de Aprovação</span>
                <h2 class="fw-bold mb-0 text-warning"><?= $totalPendentes ?></h2>
            </div>
            <div class="stat-icon amber">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
    </div>

    <!-- Stat 3: Total Aprovados -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-glass stat-widget">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Agendamentos Confirmados</span>
                <h2 class="fw-bold mb-0 text-success"><?= $totalAprovados ?></h2>
            </div>
            <div class="stat-icon emerald">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Stat 4: Recursos em Manutencao -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-glass stat-widget">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Recursos em Manutenção</span>
                <h2 class="fw-bold mb-0 text-danger"><?= $totalManutencao ?></h2>
            </div>
            <div class="stat-icon rose">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Lista de Tarefas / Aprovações Pendentes -->
    <div class="col-12 col-lg-7">
        <div class="card card-glass border-0 h-100">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-list-check me-2 text-warning"></i> Fila de Aprovações Pendentes</h5>
                <span class="badge badge-soft-warning px-3 py-2 fs-7 fw-bold"><?= count($agendamentosPendentes) ?> Pendente(s)</span>
            </div>
            <div class="card-body p-4">
                <?php if (empty($agendamentosPendentes)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-circle-check fs-1 mb-2 text-success opacity-50"></i>
                        <p class="mb-0 fw-semibold">Nenhum agendamento aguardando aprovação no momento.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Requisitante</th>
                                    <th>Data / Horário</th>
                                    <th>Uso</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agendamentosPendentes as $ag): ?>
                                <tr>
                                    <td><strong>#<?= $ag['id'] ?></strong></td>
                                    <td><?= htmlspecialchars($ag['usuario_nome']) ?></td>
                                    <td>
                                        <small class="d-block fw-semibold"><?= date('d/m/Y H:i', strtotime($ag['data_inicio'])) ?></small>
                                        <small class="text-muted"><?= date('H:i', strtotime($ag['data_fim'])) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?= $ag['tipo_uso'] === 'Externo' ? 'bg-danger' : 'bg-primary' ?>">
                                            <?= $ag['tipo_uso'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success rounded-pill px-3 me-1" onclick="aprovarAgendamento(<?= $ag['id'] ?>, 'Aprovado')">
                                            <i class="fa-solid fa-check me-1"></i> Aprovar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="aprovarAgendamento(<?= $ag['id'] ?>, 'Recusado')">
                                            <i class="fa-solid fa-xmark me-1"></i> Recusar
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Alertas do Dia & Equipamentos em Manutenção -->
    <div class="col-12 col-lg-5">
        <div class="card card-glass border-0 mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-danger"><i class="fa-solid fa-screwdriver-wrench me-2"></i> Recursos em Manutenção</h5>
            </div>
            <div class="card-body p-4">
                <?php if (empty($recursosManutencaoList)): ?>
                    <p class="text-muted mb-0 small">Todos os recursos estão funcionando normalmente.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recursosManutencaoList as $rec): ?>
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-middle px-0">
                            <div>
                                <strong><?= htmlspecialchars($rec['nome']) ?></strong>
                                <small class="d-block text-muted">Patrimônio: <?= htmlspecialchars($rec['patrimonio']) ?></small>
                            </div>
                            <span class="badge bg-danger rounded-pill align-self-center">BLOQUEADO</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="card card-glass border-0">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-bell me-2"></i> Reservas de Hoje</h5>
            </div>
            <div class="card-body p-4">
                <?php if (empty($agendamentosHoje)): ?>
                    <p class="text-muted mb-0 small">Nenhum empréstimo agendado para o dia de hoje.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($agendamentosHoje as $agH): ?>
                        <div class="list-group-item bg-transparent px-0">
                            <div class="d-flex justify-content-between">
                                <strong class="text-primary"><?= htmlspecialchars($agH['usuario_nome']) ?></strong>
                                <small class="fw-bold"><?= date('H:i', strtotime($agH['data_inicio'])) ?> - <?= date('H:i', strtotime($agH['data_fim'])) ?></small>
                            </div>
                            <small class="text-muted d-block"><?= htmlspecialchars($agH['motivo']) ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function aprovarAgendamento(id, status) {
    sendAjaxRequest(BASE_URL + '/api/agenda/aprovar', { id: id, status: status });
}
</script>
