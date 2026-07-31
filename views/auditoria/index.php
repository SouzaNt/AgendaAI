<div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-shield-halved text-primary me-2"></i> Logs de Auditoria & Registro de E-mails</h3>
    <p class="text-muted mb-0">Rastreabilidade completa de ações de usuários, alterações e diagnósticos de envio de mensagens.</p>
</div>

<ul class="nav nav-pills mb-4 gap-2">
    <li class="nav-item">
        <button class="nav-link active rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-auditoria">Logs de Auditoria do Sistema</button>
    </li>
    <li class="nav-item">
        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-emails">Log de E-mails Disparados</button>
    </li>
</ul>

<div class="tab-content">
    <!-- Log de Auditoria -->
    <div class="tab-pane fade show active" id="tab-auditoria">
        <div class="card card-glass border-0 p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>Data / Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Tabela Afetada</th>
                            <th>Valores Anteriores</th>
                            <th>Valores Novos</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auditoriaLogs as $log): ?>
                        <tr>
                            <td><small class="fw-bold"><?= date('d/m/Y H:i:s', strtotime($log['data_hora'])) ?></small></td>
                            <td><?= htmlspecialchars($log['usuario_nome'] ?? 'Sistema') ?></td>
                            <td>
                                <span class="badge <?= strpos($log['acao'], 'Inclusão') !== false ? 'bg-success' : (strpos($log['acao'], 'Exclusão') !== false ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                    <?= htmlspecialchars($log['acao']) ?>
                                </span>
                            </td>
                            <td><code><?= htmlspecialchars($log['tabela']) ?></code></td>
                            <td>
                                <?php if (!empty($log['valores_anteriores'])): ?>
                                    <small class="d-block font-monospace text-truncate" style="max-width: 180px;"><?= htmlspecialchars(json_encode($log['valores_anteriores'], JSON_UNESCAPED_UNICODE)) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($log['valores_novos'])): ?>
                                    <small class="d-block font-monospace text-truncate" style="max-width: 180px;"><?= htmlspecialchars(json_encode($log['valores_novos'], JSON_UNESCAPED_UNICODE)) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($log['ip'] ?? '127.0.0.1') ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Log de E-mails -->
    <div class="tab-pane fade" id="tab-emails">
        <div class="card card-glass border-0 p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>Data Envio</th>
                            <th>Destinatário</th>
                            <th>Assunto</th>
                            <th>Tipo de Evento</th>
                            <th>Status de Entrega</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($emailLogs as $eLog): ?>
                        <tr>
                            <td><small class="fw-bold"><?= date('d/m/Y H:i:s', strtotime($eLog['data_envio'])) ?></small></td>
                            <td>
                                <strong><?= htmlspecialchars($eLog['destinatario_nome'] ?? '') ?></strong>
                                <small class="d-block text-muted"><?= htmlspecialchars($eLog['destinatario_email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($eLog['assunto']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($eLog['tipo_evento'] ?? 'Notificação') ?></span></td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($eLog['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
