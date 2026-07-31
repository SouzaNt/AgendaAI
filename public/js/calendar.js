// Engine de Calendário Interativo - AgendaAI
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar-container');
    if (!calendarEl) return;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            day: 'Dia'
        },
        events: function (fetchInfo, successCallback, failureCallback) {
            const instId = $('#filtro-instituicao').val();
            $.ajax({
                url: BASE_URL + '/api/agenda/events' + (instId ? '?instituicao_id=' + instId : ''),
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    successCallback(res);
                },
                error: function () {
                    failureCallback();
                }
            });
        },
        eventClick: function (info) {
            const props = info.event.extendedProps;
            let statusBadge = '<span class="badge bg-success px-3 py-2 fs-7">Aprovado ✅</span>';
            if (props.status === 'Pendente') statusBadge = '<span class="badge bg-warning text-dark px-3 py-2 fs-7">Pendente ⏳</span>';
            if (props.status && props.status.includes('Cancelado')) statusBadge = '<span class="badge bg-danger px-3 py-2 fs-7">Cancelado ❌</span>';

            const isOwnerOrAdmin = props.is_owner || props.is_admin;

            // Formatação visual elegante dos itens agendados
            let itensHtml = '';
            if (props.itens_objetos && props.itens_objetos.length > 0) {
                props.itens_objetos.forEach(item => {
                    if (item.tipo === 'sala') {
                        itensHtml += `<div class="badge bg-primary-subtle text-primary border border-primary-subtle p-2 text-wrap text-start me-1 mb-1 fs-6">
                            <i class="fa-solid fa-door-open me-1"></i> <strong>${item.nome}</strong> (Sala)
                        </div>`;
                    } else {
                        itensHtml += `<div class="badge bg-success-subtle text-success border border-success-subtle p-2 text-wrap text-start me-1 mb-1 fs-6">
                            <i class="fa-solid fa-laptop me-1"></i> <strong>${item.nome}</strong> ${item.patrimonio ? `(${item.patrimonio})` : ''}
                        </div>`;
                    }
                });
            } else {
                itensHtml = `<span class="fw-bold text-primary">${props.itens_str || 'Recurso / Sala'}</span>`;
            }

            let bodyHtml = `
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div><strong>Status:</strong> ${statusBadge}</div>
                    ${props.prioridade_emergencia ? '<span class="badge bg-danger">⚡ Prioridade Alta</span>' : ''}
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-boxes-stacked me-1 text-primary"></i> Recurso(s) / Sala(s) Agendado(s):</label>
                    <div class="p-2 bg-body-tertiary rounded-3 border border-secondary-subtle">
                        ${itensHtml}
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 border rounded-3 text-center">
                            <small class="text-muted d-block">Agendador / Requisitante</small>
                            <strong class="text-primary fs-6"><i class="fa-solid fa-user me-1"></i> ${props.usuario_nome}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded-3 text-center">
                            <small class="text-muted d-block">Tipo de Uso</small>
                            <strong class="fs-6"><i class="fa-solid fa-tag me-1"></i> ${props.tipo_uso || 'Unidade'}</strong>
                        </div>
                    </div>
                </div>

                <div class="bg-body-tertiary p-3 rounded-3 mb-3 border">
                    <div class="row g-2 text-center">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block"><i class="fa-solid fa-play me-1 text-success"></i> Data & Início</small>
                            <strong class="text-success fs-6">${moment(props.data_inicio).format('DD/MM/YYYY HH:mm')}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block"><i class="fa-solid fa-stop me-1 text-danger"></i> Término</small>
                            <strong class="text-danger fs-6">${moment(props.data_fim).format('DD/MM/YYYY HH:mm')}</strong>
                        </div>
                    </div>
                </div>

                ${props.motivo ? `
                <div class="mb-2">
                    <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-comment-dots me-1 text-primary"></i> Motivo / Justificativa:</label>
                    <p class="mb-0 small text-body bg-body-tertiary p-2 rounded border border-secondary-subtle">"${props.motivo}"</p>
                </div>` : ''}

                ${props.observacoes ? `
                <div class="mb-2">
                    <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-note-sticky me-1 text-secondary"></i> Observações:</label>
                    <p class="mb-0 small text-body bg-body-tertiary p-2 rounded border border-secondary-subtle">${props.observacoes}</p>
                </div>` : ''}
            `;

            let dialogButtons = {
                fechar: {
                    label: "Fechar",
                    className: 'btn-secondary'
                }
            };

            if (isOwnerOrAdmin) {
                dialogButtons.cancelar = {
                    label: "Cancelar Reserva",
                    className: 'btn-danger',
                    callback: function () {
                        abrirModalCancelar(props.id);
                    }
                };
            }

            bootbox.dialog({
                title: (props.prioridade_emergencia ? '⚡ ' : '') + 'Detalhes do Agendamento #' + props.id,
                message: bodyHtml,
                buttons: dialogButtons
            });
        }
    });

    calendar.render();
    window.agendaCalendar = calendar;

    $('#filtro-instituicao').on('change', function () {
        calendar.refetchEvents();
    });
});

function abrirModalCancelar(agendamentoId) {
    bootbox.prompt({
        title: "Justificativa do Cancelamento",
        inputType: 'textarea',
        placeholder: "Informe o motivo do cancelamento...",
        callback: function (result) {
            if (result !== null) {
                sendAjaxRequest(BASE_URL + '/api/agenda/cancelar', {
                    id: agendamentoId,
                    motivo_cancelamento: result
                });
            }
        }
    });
}
