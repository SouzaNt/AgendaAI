<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-chart-column text-primary me-2"></i> Relatórios & Indicadores BI</h3>
        <p class="text-muted mb-0">Análise de utilização, ranking de demandas, taxa de manutenção e suporte à decisão.</p>
    </div>

    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/relatorios/pdf?tipo=utilizacao" target="_blank" class="btn btn-outline-primary rounded-pill px-3">
            <i class="fa-solid fa-file-pdf me-1"></i> Imprimir / PDF (Utilização)
        </a>
        <a href="<?= BASE_URL ?>/relatorios/pdf?tipo=manutencao" target="_blank" class="btn btn-outline-danger rounded-pill px-3">
            <i class="fa-solid fa-file-pdf me-1"></i> Imprimir / PDF (Manutenções)
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stat 1: Total Reservas -->
    <div class="col-12 col-md-4">
        <div class="card-glass stat-widget">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Total Geral de Reservas</span>
                <h3 class="fw-bold mb-0"><?= $totalReservas ?></h3>
            </div>
            <div class="stat-icon blue"><i class="fa-solid fa-chart-line"></i></div>
        </div>
    </div>
    <!-- Stat 2: Reservas Aprovadas -->
    <div class="col-12 col-md-4">
        <div class="card-glass stat-widget">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Taxa de Conclusão / Aprovadas</span>
                <h3 class="fw-bold mb-0 text-success"><?= $totalAprovadas ?></h3>
            </div>
            <div class="stat-icon emerald"><i class="fa-solid fa-check-double"></i></div>
        </div>
    </div>
    <!-- Stat 3: Cancelamentos -->
    <div class="col-12 col-md-4">
        <div class="card-glass stat-widget">
            <div>
                <span class="text-muted fw-semibold small d-block mb-1">Cancelamentos & Desistências</span>
                <h3 class="fw-bold mb-0 text-danger"><?= $totalCanceladas ?></h3>
            </div>
            <div class="stat-icon rose"><i class="fa-solid fa-ban"></i></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Ranking de Recursos Mais Requisitados -->
    <div class="col-12 col-lg-6">
        <div class="card card-glass border-0 h-100 p-4">
            <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-award me-2"></i> Ranking: Recursos Mais Requisitados</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Recurso</th>
                            <th>Patrimônio</th>
                            <th>Qtd. Solicitações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rankingRecursos)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Nenhum dado de uso registrado ainda.</td></tr>
                        <?php else: ?>
                            <?php foreach (array_slice($rankingRecursos, 0, 5) as $idx => $r): ?>
                            <tr>
                                <td><strong>#<?= $idx + 1 ?></strong></td>
                                <td><strong class="text-primary"><?= htmlspecialchars($r['nome']) ?></strong></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($r['patrimonio']) ?></span></td>
                                <td><span class="badge bg-primary rounded-pill fs-7 px-3"><?= $r['quantidade'] ?> reservas</span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Feedbacks Pós-Agendamento -->
    <div class="col-12 col-lg-6">
        <div class="card card-glass border-0 h-100 p-4">
            <h5 class="fw-bold mb-3 text-success"><i class="fa-solid fa-comments me-2"></i> Feedbacks & Registros de Utilizadores</h5>
            <?php if (empty($feedbacks)): ?>
                <p class="text-muted mb-0 small">Nenhum feedback registrado pós-agendamento.</p>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($feedbacks as $fb): ?>
                    <div class="list-group-item bg-transparent px-0">
                        <div class="d-flex justify-content-between">
                            <strong>Aluno/Utilizador: <?= htmlspecialchars($fb['aluno_utilizador'] ?? 'Não Informado') ?></strong>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($fb['data_registro'])) ?></small>
                        </div>
                        <p class="mb-1 text-secondary small">"<?= htmlspecialchars($fb['comentario'] ?? '') ?>"</p>
                        <?php if (!empty($fb['imagem_path'])): ?>
                            <a href="<?= BASE_URL ?>/<?= $fb['imagem_path'] ?>" target="_blank" class="btn btn-sm btn-link p-0 text-decoration-none">📷 Ver Anexo Enviado</a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
