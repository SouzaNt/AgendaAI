<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-laptop text-primary me-2"></i> Cadastro & Gestão de Recursos</h3>
        <p class="text-muted mb-0">Gerencie o inventário de equipamentos, controle de manutenção e histórico de movimentação.</p>
    </div>

    <button class="btn btn-primary-custom rounded-pill px-4" onclick="abrirModalRecurso()">
        <i class="fa-solid fa-plus me-1"></i> Cadastrar Recurso
    </button>
</div>

<!-- Seletor Obrigatório de Unidade / Instituição Senac -->
<div class="card card-glass border-primary border-opacity-25 p-3 mb-4 shadow-sm" style="background: rgba(0, 75, 135, 0.03);">
    <div class="row align-items-center g-3">
        <div class="col-md-5">
            <label class="form-label fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-building-columns fs-5 text-warning"></i> Selecione a Unidade / Instituição Senac:
            </label>
            <small class="text-muted d-block">Escolha a unidade operacional para listar os equipamentos.</small>
        </div>
        <div class="col-md-7">
            <select class="form-select form-select-lg select2" id="filtro-inst-main" onchange="aplicarFiltroInstituicao()">
                <option value="">🔍 Selecione uma Unidade do Senac para carregar os recursos...</option>
                <option value="ALL">🌐 Visualizar Recursos de Todas as Unidades Senac</option>
                <?php foreach ($instituicoes as $inst): ?>
                    <option value="<?= $inst['id'] ?>">🏫 <?= htmlspecialchars($inst['nome']) ?> (<?= htmlspecialchars($inst['municipio'] ?? 'ES') ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Estado Inicial: Nenhuma Instituição Selecionada -->
<div class="card card-glass border-0 p-5 text-center my-3" id="container-empty-inst-prompt">
    <div class="py-4">
        <div class="stat-icon blue mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem; background: rgba(0, 75, 135, 0.1); color: #004b87;">
            <i class="fa-solid fa-building-circle-check"></i>
        </div>
        <h4 class="fw-bold mb-2">Selecione uma Unidade do Senac</h4>
        <p class="text-muted mx-auto" style="max-width: 520px;">
            Para manter o inventário organizado e limpo, escolha uma unidade operacional no seletor acima para carregar e gerenciar os equipamentos desta instituição.
        </p>
    </div>
</div>

<!-- Tabela de Recursos (Carregada após selecionar a Unidade) -->
<div class="card card-glass border-0 p-4 d-none" id="container-tabela-principal">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable tabela-recursos" id="tabela-recursos-main">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Recurso</th>
                    <th>Tipo</th>
                    <th>Instituição Responsável</th>
                    <th>Patrimônio</th>
                    <th>Nº Série</th>
                    <th>Estado</th>
                    <th>Disponível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recursos as $rec): ?>
                <tr class="linha-recurso" data-inst-id="<?= $rec['id_instituicao_responsavel'] ?>">
                    <td><strong>#<?= $rec['id'] ?></strong></td>
                    <td>
                        <strong class="text-primary"><?= htmlspecialchars($rec['nome']) ?></strong>
                    </td>
                    <td>
                        <?php 
                            $tipoObj = JsonDatabase::findById('tipos_recurso', $rec['id_tipo_recurso']);
                            echo '<span class="badge bg-secondary-subtle text-secondary border">' . htmlspecialchars($tipoObj['nome'] ?? 'Equipamento') . '</span>';
                        ?>
                    </td>
                    <td>
                        <?php 
                            $instObj = JsonDatabase::findById('instituicoes', $rec['id_instituicao_responsavel']);
                            echo '<strong class="text-secondary"><i class="fa-solid fa-building-columns me-1 text-primary"></i>' . htmlspecialchars($instObj['nome'] ?? 'N/A') . '</strong>';
                        ?>
                    </td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($rec['patrimonio']) ?></span></td>
                    <td><?= htmlspecialchars($rec['numero_serie'] ?? '-') ?></td>
                    <td>
                        <?php if (($rec['estado'] ?? '') === 'Funcionando'): ?>
                            <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Funcionando</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><i class="fa-solid fa-wrench me-1"></i> Não Funcionando</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= !empty($rec['disponivel_agendamento']) ? '<span class="text-success fw-bold">Sim</span>' : '<span class="text-danger fw-bold">Não</span>' ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" title="Editar Recurso" onclick='editarRecurso(<?= json_encode($rec) ?>)'>
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" title="Alterar Status de Manutenção" onclick="toggleManutencao(<?= $rec['id'] ?>, '<?= $rec['estado'] ?? 'Funcionando' ?>')">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Excluir (Lógico)" onclick="deletarRecurso(<?= $rec['id'] ?>)">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal CRUD Recurso -->
<div class="modal fade" id="modal-recurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-glass border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="modal-recurso-titulo">Cadastrar Recurso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-recurso">
                <input type="hidden" id="rec-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome do Recurso *</label>
                        <input type="text" class="form-control" id="rec-nome" required placeholder="Ex: Notebook Dell Inspiron i7">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Recurso *</label>
                        <select class="form-select" id="rec-tipo" required>
                            <?php foreach ($tipos as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instituição Responsável *</label>
                        <select class="form-select" id="rec-instituicao" required>
                            <?php foreach ($instituicoes as $inst): ?>
                                <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Patrimônio *</label>
                            <input type="text" class="form-control mask-patrimonio" id="rec-patrimonio" required placeholder="PAT-0000-000">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Nº de Série</label>
                            <input type="text" class="form-control" id="rec-serie" placeholder="SN123456789">
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="rec-disponivel" checked>
                        <label class="form-check-label fw-semibold" for="rec-disponivel">Disponível para Agendamento</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Salvar Recurso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// DataTables custom search filter para Recursos
if ($.fn.dataTable) {
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData, counter) {
        if (!settings.nTable || ($(settings.nTable).attr('id') !== 'tabela-recursos-main' && !$(settings.nTable).hasClass('tabela-recursos'))) {
            return true;
        }

        const val = $('#filtro-inst-main').val();
        if (!val || val === 'ALL') return true;

        const node = settings.aoData[dataIndex].nTr;
        const instId = $(node).data('inst-id') || $(node).attr('data-inst-id');

        if (instId) {
            return String(instId) === String(val);
        }
        return true;
    });
}

function aplicarFiltroInstituicao() {
    const val = $('#filtro-inst-main').val();
    if (!val) {
        $('#container-empty-inst-prompt').removeClass('d-none');
        $('#container-tabela-principal').addClass('d-none');
    } else {
        $('#container-empty-inst-prompt').addClass('d-none');
        $('#container-tabela-principal').removeClass('d-none');
        
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabela-recursos-main')) {
            $('#tabela-recursos-main').DataTable().draw();
        } else if ($.fn.DataTable && $.fn.DataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable().draw();
        }
        localStorage.setItem('agendaai_selected_inst', val);
    }
}

$(document).ready(function() {
    const savedInst = localStorage.getItem('agendaai_selected_inst');
    if (savedInst) {
        $('#filtro-inst-main').val(savedInst).trigger('change');
    } else {
        aplicarFiltroInstituicao();
    }
});

function abrirModalRecurso() {
    $('#rec-id').val('');
    $('#rec-nome').val('');
    $('#rec-patrimonio').val('');
    $('#rec-serie').val('');
    $('#rec-disponivel').prop('checked', true);
    
    const selInst = $('#filtro-inst-main').val();
    if (selInst && selInst !== 'ALL') {
        $('#rec-instituicao').val(selInst);
    }
    
    $('#modal-recurso-titulo').text('Cadastrar Recurso');
    $('#modal-recurso').modal('show');
}

function editarRecurso(rec) {
    $('#rec-id').val(rec.id);
    $('#rec-nome').val(rec.nome);
    $('#rec-tipo').val(rec.id_tipo_recurso);
    $('#rec-instituicao').val(rec.id_instituicao_responsavel);
    $('#rec-patrimonio').val(rec.patrimonio);
    $('#rec-serie').val(rec.numero_serie || '');
    $('#rec-disponivel').prop('checked', rec.disponivel_agendamento !== false);
    $('#modal-recurso-titulo').text('Editar Recurso #' + rec.id);
    $('#modal-recurso').modal('show');
}

$('#form-recurso').on('submit', function(e) {
    e.preventDefault();
    sendAjaxRequest(BASE_URL + '/api/recursos/store', {
        id: $('#rec-id').val() || null,
        nome: $('#rec-nome').val(),
        id_tipo_recurso: $('#rec-tipo').val(),
        id_instituicao_responsavel: $('#rec-instituicao').val(),
        patrimonio: $('#rec-patrimonio').val(),
        numero_serie: $('#rec-serie').val(),
        disponivel_agendamento: $('#rec-disponivel').is(':checked')
    });
});

function toggleManutencao(id, estadoAtual) {
    const novoEstado = estadoAtual === 'Funcionando' ? 'Não Funcionando' : 'Funcionando';
    bootbox.confirm({
        title: "Alterar Estado de Manutenção",
        message: `Deseja alterar o estado do recurso para <strong>${novoEstado}</strong>?`,
        buttons: { confirm: { label: 'Confirmar', className: 'btn-warning' } },
        callback: function(result) {
            if (result) sendAjaxRequest(BASE_URL + '/api/recursos/toggle-manutencao', { id: id, estado: novoEstado });
        }
    });
}

function deletarRecurso(id) {
    bootbox.confirm({
        title: "Excluir Recurso",
        message: "Tem certeza que deseja remover este recurso do inventário?",
        buttons: { confirm: { label: 'Excluir', className: 'btn-danger' } },
        callback: function(result) {
            if (result) sendAjaxRequest(BASE_URL + '/api/recursos/delete', { id: id });
        }
    });
}
</script>
