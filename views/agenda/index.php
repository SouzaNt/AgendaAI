<!-- Header & Abas Principais -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-calendar-check text-primary me-2"></i> Agendamento Passo a Passo</h3>
        <p class="text-muted mb-0">Selecione a data e horários primeiro para filtrar apenas itens disponíveis no período.</p>
    </div>

    <div class="nav nav-pills gap-2" id="agenda-mode-tabs">
        <button class="nav-link active rounded-pill px-4 fw-semibold" id="tab-btn-wizard" onclick="switchAgendaView('wizard')">
            <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Novo Agendamento
        </button>
        <button class="nav-link rounded-pill px-4 fw-semibold border" id="tab-btn-calendar" onclick="switchAgendaView('calendar')">
            <i class="fa-solid fa-calendar-days me-2"></i> Calendário Geral
        </button>
    </div>
</div>

<!-- CONTAINER 1: WIZARD DE AGENDAMENTO SLIDE -->
<div id="container-wizard">
    <div class="card card-glass border-0 p-4 p-md-5">
        <!-- Indicador de Progresso (Nodes 1 a 5) -->
        <div class="wizard-progress">
            <div class="wizard-progress-bar" id="wizard-progress-bar" style="width: 0%;"></div>
            <div class="wizard-step-node active" id="node-1" onclick="goToSlide(1)">1</div>
            <div class="wizard-step-node" id="node-2" onclick="goToSlide(2)">2</div>
            <div class="wizard-step-node" id="node-3" onclick="goToSlide(3)">3</div>
            <div class="wizard-step-node" id="node-4" onclick="goToSlide(4)">4</div>
            <div class="wizard-step-node" id="node-5" onclick="goToSlide(5)">5</div>
        </div>

        <form id="form-wizard-agendamento">
            <!-- SLIDE 1: Instituição & Categoria de Reserva -->
            <div class="wizard-slide active" id="slide-1">
                <h4 class="fw-bold text-center mb-1">Passo 1: Instituição & Categoria</h4>
                <p class="text-muted text-center mb-4">Primeiro selecione a instituição e o tipo de item que deseja reservar.</p>

                <!-- 1. Seleção da Instituição / Unidade -->
                <?php if ($somenteUmaInstituicao && $instituicaoUnica): ?>
                    <input type="hidden" id="wz-instituicao" value="<?= $instituicaoUnica['id'] ?>">
                    <div class="alert alert-primary border-0 rounded-4 text-center mb-4 p-3 shadow-sm">
                        <i class="fa-solid fa-building-columns me-2"></i> Instituição Vinculada: <strong><?= htmlspecialchars($instituicaoUnica['nome']) ?></strong>
                    </div>
                <?php else: ?>
                    <div class="row justify-content-center mb-4">
                        <div class="col-12 col-md-8">
                            <div class="card p-3 border-primary-subtle bg-primary-subtle rounded-4">
                                <label class="form-label fw-bold text-primary fs-6 mb-2">
                                    <i class="fa-solid fa-building-columns me-2"></i> 1. Escolha a Instituição / Unidade Responsável *
                                </label>
                                <select class="form-select form-select-lg border-primary" id="wz-instituicao" required>
                                    <?php foreach ($instituicoes as $inst): ?>
                                        <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 2. Escolha do Tipo de Reserva -->
                <div class="row g-4 justify-content-center mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold text-center mb-3">2. Selecione a Categoria do Agendamento *</h6>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="selectable-card text-center py-4" id="card-tipo-sala" onclick="selectTipoReserva('sala')">
                            <div class="stat-icon blue mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.8rem;">
                                <i class="fa-solid fa-door-open"></i>
                            </div>
                            <h5 class="fw-bold mb-1">Sala de Aula / Auditório</h5>
                            <p class="text-muted small mb-0">Reserva de auditórios, laboratórios e espaços físicos.</p>
                        </div>
                    </div>

                    <div class="col-12 col-md-5">
                        <div class="selectable-card text-center py-4" id="card-tipo-recurso" onclick="selectTipoReserva('recurso')">
                            <div class="stat-icon emerald mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.8rem;">
                                <i class="fa-solid fa-laptop"></i>
                            </div>
                            <h5 class="fw-bold mb-1">Equipamentos / Recursos</h5>
                            <p class="text-muted small mb-0">Reserva de um ou múltiplos notebooks, projetores, som, etc.</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-primary-custom btn-lg rounded-pill px-5" onclick="nextSlide(1)">
                        Avançar <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- SLIDE 2: Escolha da Data -->
            <div class="wizard-slide" id="slide-2">
                <h4 class="fw-bold text-center mb-1">Passo 2: Escolha a Data da Reserva</h4>
                <p class="text-muted text-center mb-4">Informe o dia referente ao seu agendamento.</p>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-6 text-center">
                        <label class="form-label fw-semibold fs-5 mb-3"><i class="fa-solid fa-calendar-day text-primary me-2"></i> Data Selecionada *</label>
                        <input type="text" class="form-control form-control-lg text-center fw-bold fs-4 border-primary" id="wz-data-selecionada" placeholder="Clique para escolher a data" required>
                        <div class="mt-3 text-primary fw-bold fs-6" id="data-extenso-badge"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="prevSlide(2)">
                        <i class="fa-solid fa-arrow-left me-2"></i> Voltar
                    </button>
                    <button type="button" class="btn btn-primary-custom btn-lg rounded-pill px-5" onclick="nextSlide(2)">
                        Avançar <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- SLIDE 3: Horários de Início e Término do Dia -->
            <div class="wizard-slide" id="slide-3">
                <h4 class="fw-bold text-center mb-1">Passo 3: Horários de Início e Término</h4>
                <p class="text-muted text-center mb-4">Defina o período para o dia selecionado.</p>

                <!-- Botões de Turnos Rápidos -->
                <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="setHorariosRapidos('08:00', '12:00')">
                        🌅 Manhã (08:00 às 12:00)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="setHorariosRapidos('13:30', '17:30')">
                        ☀️ Tarde (13:30 às 17:30)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="setHorariosRapidos('19:00', '22:00')">
                        🌙 Noite (19:00 às 22:00)
                    </button>
                </div>

                <div class="row g-4 justify-content-center">
                    <div class="col-12 col-md-5">
                        <div class="card p-3 border text-center">
                            <label class="form-label fw-semibold text-primary"><i class="fa-solid fa-play me-2"></i> Horário de Início *</label>
                            <input type="text" class="form-control form-control-lg text-center mask-hora fw-bold fs-4" id="wz-hora-inicio" placeholder="08:00" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="card p-3 border text-center">
                            <label class="form-label fw-semibold text-danger"><i class="fa-solid fa-stop me-2"></i> Horário de Término *</label>
                            <input type="text" class="form-control form-control-lg text-center mask-hora fw-bold fs-4" id="wz-hora-fim" placeholder="11:30" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="prevSlide(3)">
                        <i class="fa-solid fa-arrow-left me-2"></i> Voltar
                    </button>
                    <button type="button" class="btn btn-primary-custom btn-lg rounded-pill px-5" onclick="nextSlide(3)">
                        Ver Itens Disponíveis <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- SLIDE 4: Seleção dos Itens Disponíveis no Horário Filtrado -->
            <div class="wizard-slide" id="slide-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-0" id="txt-slide4-titulo">Passo 4: Selecione os Itens Disponíveis</h4>
                        <p class="text-muted small mb-0" id="txt-slide4-sub">Mostrando disponibilidade real para a data e horários escolhidos.</p>
                    </div>
                    <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill" id="badge-itens-selecionados">0 selecionado(s)</span>
                </div>

                <!-- Pesquisa por Nome e Filtros por Tipo de Equipamento -->
                <div class="row g-2 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" id="busca-item-nome" placeholder="Pesquisar por nome ou patrimônio..." onkeyup="renderGridItens()">
                        </div>
                    </div>
                    <div class="col-12 col-md-6" id="box-filtro-tipos">
                        <select class="form-select" id="busca-item-tipo" onchange="renderGridItens()">
                            <option value="">Todos os Tipos de Recurso</option>
                            <?php foreach ($tipos as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Loading Spinner para checagem em tempo real -->
                <div class="text-center py-5 d-none" id="loading-disponibilidade">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Consultando disponibilidade...</span>
                    </div>
                    <p class="mt-2 text-muted fw-semibold">Verificando disponibilidade em tempo real para o horário selecionado...</p>
                </div>

                <!-- Grid de Cards de Itens -->
                <div class="row g-3" id="grid-itens-selecao" style="max-height: 420px; overflow-y: auto;">
                    <!-- Preenchido via JS -->
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="prevSlide(4)">
                        <i class="fa-solid fa-arrow-left me-2"></i> Voltar
                    </button>
                    <button type="button" class="btn btn-primary-custom btn-lg rounded-pill px-5" onclick="nextSlide(4)">
                        Avançar <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- SLIDE 5: Finalidade & Confirmação Resumo -->
            <div class="wizard-slide" id="slide-5">
                <h4 class="fw-bold text-center mb-1">Passo 5: Finalidade & Confirmação</h4>
                <p class="text-muted text-center mb-4">Confira os dados da reserva antes de finalizar.</p>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo de Uso *</label>
                            <select class="form-select form-select-lg" id="wz-tipo-uso" required onchange="toggleMotivoWz()">
                                <option value="Unidade">Uso na Unidade (Institucional)</option>
                                <option value="Externo">Uso Externo (Obrigatório Justificativa)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Motivo / Finalidade <span id="lbl-motivo-wz-req" class="text-danger d-none">*</span></label>
                            <input type="text" class="form-control" id="wz-motivo" placeholder="Descreva a finalidade da reserva...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Observações Adicionais (Opcional)</label>
                            <textarea class="form-control" id="wz-observacoes" rows="2" placeholder="Notas adicionais..."></textarea>
                        </div>

                        <div class="form-check form-switch bg-danger-subtle p-3 rounded border border-danger-subtle">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="wz-emergencia">
                            <label class="form-check-label fw-bold text-danger" for="wz-emergencia">⚡ Reserva de Emergência (Prioridade Alta)</label>
                        </div>
                    </div>

                    <!-- Resumo da Reserva Card -->
                    <div class="col-12 col-md-6">
                        <div class="card bg-primary-subtle border-primary p-4 rounded-4 h-100 shadow-sm">
                            <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-receipt me-2"></i> Resumo do Agendamento</h5>

                            <div class="mb-2"><strong>Categoria:</strong> <span id="rsm-tipo" class="text-uppercase fw-bold text-primary"></span></div>
                            <div class="mb-2"><strong>Unidade:</strong> <span id="rsm-unidade" class="fw-bold"></span></div>
                            <div class="mb-2"><strong>Itens Selecionados:</strong> <ul id="rsm-itens-list" class="mb-0 ps-3 fw-semibold"></ul></div>
                            <div class="mb-2"><strong>Data:</strong> <span id="rsm-data" class="fw-bold"></span></div>
                            <div class="mb-2"><strong>Horário:</strong> <span id="rsm-horario" class="fw-bold"></span></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="prevSlide(5)">
                        <i class="fa-solid fa-arrow-left me-2"></i> Voltar
                    </button>
                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 shadow">
                        <i class="fa-solid fa-circle-check me-2"></i> Finalizar Agendamento
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- CONTAINER 2: VISUALIZAÇÃO DO CALENDÁRIO GERAL -->
<div id="container-calendar" style="display: none;">
    <div class="card card-glass border-0 p-3 mb-4">
        <div class="row align-items-center">
            <div class="col-12 col-md-3">
                <label for="filtro-instituicao" class="form-label fw-semibold mb-md-0"><i class="fa-solid fa-filter text-primary me-2"></i> Unidade:</label>
            </div>
            <div class="col-12 col-md-9">
                <select class="form-select select2" id="filtro-instituicao">
                    <option value="">Todas as Instituições</option>
                    <?php foreach ($instituicoes as $inst): ?>
                        <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="card card-glass border-0 p-4">
        <div id="calendar-container"></div>
    </div>
</div>

<script>
const TODAS_SALAS = <?= json_encode($salas) ?>;
const TODOS_RECURSOS = <?= json_encode($recursos) ?>;
const TIPOS_RECURSO = <?= json_encode($tipos) ?>;

let wizardState = {
    tipoReserva: 'sala', // 'sala' ou 'recurso'
    instituicaoId: $('#wz-instituicao').val() || 1,
    selectedItemIds: [],
    selectedItemObjects: [],
    data: '',
    horaInicio: '',
    horaFim: '',
    disponibilidadeRecursos: {},
    disponibilidadeSalas: {},
    motivosIndisponibilidade: {}
};

document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#wz-data-selecionada", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        minDate: "today",
        locale: "pt",
        onChange: function(selectedDates, dateStr) {
            wizardState.data = dateStr;
            if (selectedDates[0]) {
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                $('#data-extenso-badge').text(selectedDates[0].toLocaleDateString('pt-BR', options));
            }
        }
    });

    selectTipoReserva('sala');
});

function switchAgendaView(mode) {
    if (mode === 'wizard') {
        $('#container-wizard').show();
        $('#container-calendar').hide();
        $('#tab-btn-wizard').addClass('active');
        $('#tab-btn-calendar').removeClass('active');
    } else {
        $('#container-wizard').hide();
        $('#container-calendar').show();
        $('#tab-btn-wizard').removeClass('active');
        $('#tab-btn-calendar').addClass('active');
        if (window.agendaCalendar) window.agendaCalendar.render();
    }
}

function selectTipoReserva(tipo) {
    wizardState.tipoReserva = tipo;
    wizardState.selectedItemIds = [];
    wizardState.selectedItemObjects = [];

    if (tipo === 'sala') {
        $('#card-tipo-sala').addClass('selected');
        $('#card-tipo-recurso').removeClass('selected');
        $('#box-filtro-tipos').hide();
    } else {
        $('#card-tipo-recurso').addClass('selected');
        $('#card-tipo-sala').removeClass('selected');
        $('#box-filtro-tipos').show();
    }
}

function consultarDisponibilidadeEmTempoReal(callback) {
    $('#loading-disponibilidade').removeClass('d-none');
    $('#grid-itens-selecao').addClass('d-none');

    const dtData = $('#wz-data-selecionada').val();
    const hrInicio = $('#wz-hora-inicio').val();
    const hrFim = $('#wz-hora-fim').val();

    const dtInicioStr = `${dtData} ${hrInicio}:00`;
    const dtFimStr = `${dtData} ${hrFim}:00`;

    $.ajax({
        url: BASE_URL + '/api/agenda/disponibilidade',
        type: 'POST',
        data: JSON.stringify({ data_inicio: dtInicioStr, data_fim: dtFimStr }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(res) {
            $('#loading-disponibilidade').addClass('d-none');
            $('#grid-itens-selecao').removeClass('d-none');

            if (res.success) {
                wizardState.disponibilidadeRecursos = res.disponibilidade_recursos || {};
                wizardState.disponibilidadeSalas = res.disponibilidade_salas || {};
                wizardState.motivosIndisponibilidade = res.motivos_indisponibilidade || {};
                renderGridItens();
                if (callback) callback();
            } else {
                bootbox.alert("<strong class='text-danger'>Erro!</strong> " + res.message);
            }
        },
        error: function() {
            $('#loading-disponibilidade').addClass('d-none');
            $('#grid-itens-selecao').removeClass('d-none');
            renderGridItens();
            if (callback) callback();
        }
    });
}

function renderGridItens() {
    const grid = $('#grid-itens-selecao');
    grid.empty();

    const termNome = ($('#busca-item-nome').val() || '').toLowerCase().trim();
    const filtroTipoId = $('#busca-item-tipo').val();

    if (wizardState.tipoReserva === 'sala') {
        $('#txt-slide4-titulo').text('Passo 4: Selecione a Sala de Aula');
        $('#txt-slide4-sub').text('Exibindo disponibilidade para a data e horários escolhidos:');

        const salasFiltradas = TODAS_SALAS.filter(sala => {
            if (termNome && !sala.nome.toLowerCase().includes(termNome)) return false;
            return true;
        });

        if (salasFiltradas.length === 0) {
            grid.append('<div class="col-12 text-center py-4 text-muted">Nenhuma sala encontrada para esta busca.</div>');
            return;
        }

        salasFiltradas.forEach(sala => {
            const isLivre = wizardState.disponibilidadeSalas[sala.id] !== false;
            const motivoIndisp = wizardState.motivosIndisponibilidade['sala_' + sala.id] || 'Indisponível neste horário';
            const isSelected = wizardState.selectedItemIds.includes(sala.id);

            grid.append(`
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="selectable-card ${!isLivre ? 'disabled' : ''} ${isSelected ? 'selected' : ''}" 
                         ${isLivre ? `onclick="toggleSelectItem(${sala.id}, '${sala.nome.replace(/'/g, "\\'")}')"` : ''}>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon ${isLivre ? 'blue' : 'rose'}" style="width: 44px; height: 44px;">
                                    <i class="fa-solid fa-door-open"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">${sala.nome}</h6>
                                    ${isLivre ? '<span class="badge bg-success mt-1">Disponível neste horário</span>' : `<span class="badge bg-danger mt-1">${motivoIndisp}</span>`}
                                </div>
                            </div>
                            ${isSelected ? '<span class="badge bg-primary rounded-circle p-2"><i class="fa-solid fa-check"></i></span>' : ''}
                        </div>
                    </div>
                </div>
            `);
        });
    } else {
        $('#txt-slide4-titulo').text('Passo 4: Selecione os Equipamentos');
        $('#txt-slide4-sub').text('Exibindo equipamentos disponíveis para a data e horários escolhidos:');

        const recursosFiltrados = TODOS_RECURSOS.filter(rec => {
            if (filtroTipoId && String(rec.id_tipo_recurso) !== String(filtroTipoId)) return false;
            if (termNome) {
                const matchNome = rec.nome.toLowerCase().includes(termNome);
                const matchPatrimonio = (rec.patrimonio || '').toLowerCase().includes(termNome);
                if (!matchNome && !matchPatrimonio) return false;
            }
            return true;
        });

        if (recursosFiltrados.length === 0) {
            grid.append('<div class="col-12 text-center py-4 text-muted">Nenhum equipamento encontrado com estes filtros.</div>');
            return;
        }

        recursosFiltrados.forEach(rec => {
            const isLivre = wizardState.disponibilidadeRecursos[rec.id] !== false;
            const motivoIndisp = wizardState.motivosIndisponibilidade['recurso_' + rec.id] || 'Indisponível neste horário';
            const isSelected = wizardState.selectedItemIds.includes(rec.id);
            const tipoObj = TIPOS_RECURSO.find(t => String(t.id) === String(rec.id_tipo_recurso));
            const tipoNome = tipoObj ? tipoObj.nome : 'Recurso';

            grid.append(`
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="selectable-card ${!isLivre ? 'disabled' : ''} ${isSelected ? 'selected' : ''}" 
                         ${isLivre ? `onclick="toggleSelectItem(${rec.id}, '${rec.nome.replace(/'/g, "\\'")}')"` : ''}>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon ${isLivre ? 'emerald' : 'rose'}" style="width: 44px; height: 44px;">
                                    <i class="fa-solid ${isLivre ? 'fa-laptop' : 'fa-ban'}"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">${rec.nome}</h6>
                                    <small class="badge bg-secondary-subtle text-secondary mb-1">${tipoNome}</small>
                                    <small class="d-block text-muted">Patrimônio: ${rec.patrimonio}</small>
                                    ${isLivre ? '<span class="badge bg-success mt-1">Disponível</span>' : `<span class="badge bg-danger mt-1">${motivoIndisp}</span>`}
                                </div>
                            </div>
                            ${isSelected ? '<span class="badge bg-success rounded-circle p-2"><i class="fa-solid fa-check"></i></span>' : ''}
                        </div>
                    </div>
                </div>
            `);
        });
    }

    updateCounterBadge();
}

function toggleSelectItem(id, nome) {
    const idx = wizardState.selectedItemIds.indexOf(id);
    if (idx > -1) {
        wizardState.selectedItemIds.splice(idx, 1);
        wizardState.selectedItemObjects = wizardState.selectedItemObjects.filter(o => o.id !== id);
    } else {
        if (wizardState.tipoReserva === 'sala') {
            wizardState.selectedItemIds = [id];
            wizardState.selectedItemObjects = [{ id: id, nome: nome }];
        } else {
            wizardState.selectedItemIds.push(id);
            wizardState.selectedItemObjects.push({ id: id, nome: nome });
        }
    }
    renderGridItens();
}

function updateCounterBadge() {
    const count = wizardState.selectedItemIds.length;
    $('#badge-itens-selecionados').text(`${count} item(ns) selecionado(s)`);
}

function setHorariosRapidos(inicio, fim) {
    $('#wz-hora-inicio').val(inicio);
    $('#wz-hora-fim').val(fim);
    wizardState.horaInicio = inicio;
    wizardState.horaFim = fim;
}

let maxStepReached = 1;

function goToSlide(slideNum) {
    if (slideNum > maxStepReached) {
        bootbox.alert("<strong class='text-warning'>Atenção!</strong> Siga o passo a passo em sequência. Preencha os campos anteriores para avançar.");
        return;
    }

    $('.wizard-slide').removeClass('active');
    $(`#slide-${slideNum}`).addClass('active');

    const percent = ((slideNum - 1) / 4) * 100;
    $('#wizard-progress-bar').css('width', `${percent}%`);

    for (let i = 1; i <= 5; i++) {
        const node = $(`#node-${i}`);
        if (i < slideNum) {
            node.addClass('completed').removeClass('active');
        } else if (i === slideNum) {
            node.addClass('active').removeClass('completed');
        } else {
            node.removeClass('active completed');
        }
    }
}

function nextSlide(currentSlide) {
    if (currentSlide === 1) {
        wizardState.instituicaoId = $('#wz-instituicao').val();
        if (!wizardState.instituicaoId) {
            bootbox.alert("<strong class='text-danger'>Atenção!</strong> Selecione a instituição.");
            return;
        }
    }
    if (currentSlide === 2 && !$('#wz-data-selecionada').val()) {
        bootbox.alert("<strong class='text-danger'>Atenção!</strong> Escolha a data da reserva.");
        return;
    }
    if (currentSlide === 3) {
        wizardState.horaInicio = $('#wz-hora-inicio').val();
        wizardState.horaFim = $('#wz-hora-fim').val();
        if (!wizardState.horaInicio || !wizardState.horaFim) {
            bootbox.alert("<strong class='text-danger'>Atenção!</strong> Informe o horário de início e término.");
            return;
        }

        const nextStep = 4;
        if (nextStep > maxStepReached) maxStepReached = nextStep;

        consultarDisponibilidadeEmTempoReal(function() {
            goToSlide(4);
        });
        return;
    }
    if (currentSlide === 4 && wizardState.selectedItemIds.length === 0) {
        bootbox.alert("<strong class='text-danger'>Atenção!</strong> Selecione ao menos um item disponível para prosseguir.");
        return;
    }
    if (currentSlide === 4) {
        atualizarResumo();
    }

    const targetStep = currentSlide + 1;
    if (targetStep > maxStepReached) {
        maxStepReached = targetStep;
    }
    goToSlide(targetStep);
}

function prevSlide(currentSlide) {
    goToSlide(currentSlide - 1);
}

function toggleMotivoWz() {
    if ($('#wz-tipo-uso').val() === 'Externo') {
        $('#lbl-motivo-wz-req').removeClass('d-none');
        $('#wz-motivo').attr('required', true);
    } else {
        $('#lbl-motivo-wz-req').addClass('d-none');
        $('#wz-motivo').removeAttr('required');
    }
}

function atualizarResumo() {
    $('#rsm-tipo').text(wizardState.tipoReserva === 'sala' ? 'Sala de Aula' : 'Equipamento(s)');
    $('#rsm-unidade').text($('#wz-instituicao option:selected').text());

    const list = $('#rsm-itens-list');
    list.empty();
    wizardState.selectedItemObjects.forEach(obj => {
        list.append(`<li>${obj.nome}</li>`);
    });

    $('#rsm-data').text(moment($('#wz-data-selecionada').val()).format('DD/MM/YYYY'));
    $('#rsm-horario').text(`${wizardState.horaInicio} às ${wizardState.horaFim}`);
}

$('#form-wizard-agendamento').on('submit', function(e) {
    e.preventDefault();

    const dtData = $('#wz-data-selecionada').val();
    const hrInicio = $('#wz-hora-inicio').val();
    const hrFim = $('#wz-hora-fim').val();

    const payload = {
        data_inicio: `${dtData} ${hrInicio}:00`,
        data_fim: `${dtData} ${hrFim}:00`,
        tipo_uso: $('#wz-tipo-uso').val(),
        motivo: $('#wz-motivo').val(),
        recursos_ids: wizardState.tipoReserva === 'recurso' ? wizardState.selectedItemIds : [],
        salas_ids: wizardState.tipoReserva === 'sala' ? wizardState.selectedItemIds : [],
        instituicao_id: wizardState.instituicaoId,
        observacoes: $('#wz-observacoes').val(),
        prioridade_emergencia: $('#wz-emergencia').is(':checked')
    };

    sendAjaxRequest(BASE_URL + '/api/agenda/store', payload, function() {
        switchAgendaView('calendar');
        if (window.agendaCalendar) window.agendaCalendar.refetchEvents();
    });
});
</script>
