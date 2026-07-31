<div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-sliders text-primary me-2"></i> Configurações Gerais do Sistema</h3>
    <p class="text-muted mb-0">Parâmetros globais de tolerância, agendamento direto, e-mails e regras de negócio.</p>
</div>

<form id="form-configuracoes">
    <div class="row g-4">
        <!-- Card 1: Regras de Agendamento -->
        <div class="col-12 col-lg-6">
            <div class="card card-glass border-0 h-100 p-4">
                <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-clock me-2"></i> Regras de Agendamento & Tolerância</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Intervalo de Tolerância entre Agendamentos (Minutos)</label>
                    <input type="number" class="form-control" id="cfg-tolerancia" value="<?= $config['tolerancia_minutos'] ?? 15 ?>" required>
                    <small class="text-muted">Intervalo mínimo de margem entre um empréstimo e o próximo.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Antecedência Mínima para Reserva (Horas)</label>
                    <input type="number" class="form-control" id="cfg-antecedencia" value="<?= $config['antecedencia_horas'] ?? 1 ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Duração Máxima do Agendamento (Horas por Reserva)</label>
                    <input type="number" class="form-control" id="cfg-duracao-maxima" value="<?= $config['duracao_maxima_horas'] ?? 8 ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Quota Máxima de Itens Simultâneos por Usuário</label>
                    <input type="number" class="form-control" id="cfg-limite-itens" value="<?= $config['limite_itens_usuario'] ?? 5 ?>" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Horário Abertura</label>
                        <input type="text" class="form-control mask-hora" id="cfg-abertura" value="<?= $config['horario_abertura'] ?? '07:00' ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Horário Fechamento</label>
                        <input type="text" class="form-control mask-hora" id="cfg-fechamento" value="<?= $config['horario_fechamento'] ?? '22:00' ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Workflow, Cancelamento e E-mails -->
        <div class="col-12 col-lg-6">
            <div class="card card-glass border-0 h-100 p-4">
                <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-gears me-2"></i> Workflow & Notificações</h5>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="cfg-agendamento-direto" <?= !empty($config['agendamento_direto']) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="cfg-agendamento-direto">
                        Agendamento Direto (Aprovação Automática Sem Análise Prévia)
                    </label>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Prazo Limite para Cancelamento (Horas de Antecedência)</label>
                    <input type="number" class="form-control" id="cfg-prazo-cancelamento" value="<?= $config['prazo_cancelamento_horas'] ?? 2 ?>" required>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="cfg-justificativa-obrigatoria" <?= !empty($config['justificativa_cancelamento_obrigatoria']) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="cfg-justificativa-obrigatoria">
                        Justificativa de Cancelamento Obrigatória
                    </label>
                </div>

                <div class="border-top pt-3 mt-3">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-layer-group me-2"></i> Agendamento em Massa</h6>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="cfg-massa-habilitado" <?= !empty($config['agendamento_massa_habilitado']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="cfg-massa-habilitado">Habilitar Funcionalidade de Agendamento em Massa</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Limite Máximo de Dias para Agendamento em Massa</label>
                        <input type="number" class="form-control" id="cfg-limite-dias-massa" value="<?= $config['limite_dias_agendamento_massa'] ?? 30 ?>" required>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-envelope me-2"></i> Servidor de E-mail (SMTP) & Lembretes</h6>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="cfg-flags-email" <?= !empty($config['flags_email']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="cfg-flags-email">Disparar E-mails e Lembretes Automáticos</label>
                    </div>
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm mb-2" id="cfg-smtp-servidor" placeholder="Servidor SMTP" value="<?= htmlspecialchars($config['smtp_servidor'] ?? '') ?>">
                        <input type="text" class="form-control form-control-sm" id="cfg-smtp-usuario" placeholder="E-mail Remetente" value="<?= htmlspecialchars($config['smtp_usuario'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary-custom btn-lg rounded-pill px-5">
                <i class="fa-solid fa-floppy-disk me-2"></i> Salvar Parâmetros Globais
            </button>
        </div>
    </div>
</form>

<script>
$('#form-configuracoes').on('submit', function(e) {
    e.preventDefault();
    const payload = {
        tolerancia_minutos: $('#cfg-tolerancia').val(),
        antecedencia_horas: $('#cfg-antecedencia').val(),
        duracao_maxima_horas: $('#cfg-duracao-maxima').val(),
        limite_itens_usuario: $('#cfg-limite-itens').val(),
        horario_abertura: $('#cfg-abertura').val(),
        horario_fechamento: $('#cfg-fechamento').val(),
        agendamento_direto: $('#cfg-agendamento-direto').is(':checked'),
        prazo_cancelamento_horas: $('#cfg-prazo-cancelamento').val(),
        justificativa_cancelamento_obrigatoria: $('#cfg-justificativa-obrigatoria').is(':checked'),
        agendamento_massa_habilitado: $('#cfg-massa-habilitado').is(':checked'),
        limite_dias_agendamento_massa: $('#cfg-limite-dias-massa').val(),
        flags_email: $('#cfg-flags-email').is(':checked'),
        smtp_servidor: $('#cfg-smtp-servidor').val(),
        smtp_usuario: $('#cfg-smtp-usuario').val()
    };

    sendAjaxRequest(BASE_URL + '/api/configuracoes/store', payload);
});
</script>
