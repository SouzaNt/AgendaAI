<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-door-open text-primary me-2"></i> Cadastro & Gestão de Salas de Aula</h3>
        <p class="text-muted mb-0">Controle de auditórios, laboratórios e salas de aula por instituição.</p>
    </div>

    <button class="btn btn-primary-custom rounded-pill px-4" onclick="abrirModalSala()">
        <i class="fa-solid fa-plus me-1"></i> Cadastrar Nova Sala
    </button>
</div>

<!-- Seletor Obrigatório de Unidade / Instituição Senac -->
<div class="card card-glass border-primary border-opacity-25 p-3 mb-4 shadow-sm" style="background: rgba(0, 75, 135, 0.03);">
    <div class="row align-items-center g-3">
        <div class="col-md-5">
            <label class="form-label fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-building-columns fs-5 text-warning"></i> Selecione a Unidade / Instituição Senac:
            </label>
            <small class="text-muted d-block">Escolha a unidade para listar os auditórios e salas vinculadas.</small>
        </div>
        <div class="col-md-7">
            <select class="form-select form-select-lg select2" id="filtro-inst-main" onchange="aplicarFiltroInstituicao()">
                <option value="">🔍 Selecione uma Unidade do Senac para carregar as salas...</option>
                <option value="ALL">🌐 Visualizar Salas de Todas as Unidades Senac</option>
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
            <i class="fa-solid fa-door-closed"></i>
        </div>
        <h4 class="fw-bold mb-2">Selecione uma Unidade do Senac</h4>
        <p class="text-muted mx-auto" style="max-width: 520px;">
            Escolha uma unidade operacional no seletor acima para listar e gerenciar as salas de aula, laboratórios e auditórios desta instituição.
        </p>
    </div>
</div>

<!-- Tabela de Salas (Carregada após selecionar a Unidade) -->
<div class="card card-glass border-0 p-4 d-none" id="container-tabela-principal">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable tabela-salas" id="tabela-salas-main">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome da Sala / Espaço</th>
                    <th>Instituição Vinculada</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salas as $sala): ?>
                <tr class="linha-sala" data-inst-id="<?= $sala['id_instituicao_vinculada'] ?>">
                    <td><strong>#<?= $sala['id'] ?></strong></td>
                    <td><strong class="text-primary"><?= htmlspecialchars($sala['nome']) ?></strong></td>
                    <td>
                        <?php 
                            $inst = JsonDatabase::findById('instituicoes', $sala['id_instituicao_vinculada']);
                            echo htmlspecialchars($inst['nome'] ?? 'N/A');
                        ?>
                    </td>
                    <td><span class="badge bg-success">Ativa</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary rounded-pill me-1" onclick='editarSala(<?= json_encode($sala) ?>)'>
                            <i class="fa-solid fa-pen-to-square"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="deletarSala(<?= $sala['id'] ?>)">
                            <i class="fa-solid fa-trash-can"></i> Excluir
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal CRUD Sala -->
<div class="modal fade" id="modal-sala" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-glass border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="modal-sala-titulo">Cadastrar Sala de Aula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-sala">
                <input type="hidden" id="sala-id" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome da Sala / Auditório *</label>
                        <input type="text" class="form-control" id="sala-nome" required placeholder="Ex: Laboratório de Informática 01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instituição Vinculada *</label>
                        <select class="form-select" id="sala-instituicao" required>
                            <?php foreach ($instituicoes as $inst): ?>
                                <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Salvar Sala</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// DataTables custom search filter para Salas
if ($.fn.dataTable) {
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData, counter) {
        if (!settings.nTable || ($(settings.nTable).attr('id') !== 'tabela-salas-main' && !$(settings.nTable).hasClass('tabela-salas'))) {
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
        
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabela-salas-main')) {
            $('#tabela-salas-main').DataTable().draw();
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

function abrirModalSala() {
    $('#sala-id').val('');
    $('#sala-nome').val('');
    
    const selInst = $('#filtro-inst-main').val();
    if (selInst && selInst !== 'ALL') {
        $('#sala-instituicao').val(selInst);
    }
    
    $('#modal-sala-titulo').text('Cadastrar Sala de Aula');
    $('#modal-sala').modal('show');
}

function editarSala(sala) {
    $('#sala-id').val(sala.id);
    $('#sala-nome').val(sala.nome);
    $('#sala-instituicao').val(sala.id_instituicao_vinculada);
    $('#modal-sala-titulo').text('Editar Sala #' + sala.id);
    $('#modal-sala').modal('show');
}

$('#form-sala').on('submit', function(e) {
    e.preventDefault();
    sendAjaxRequest(BASE_URL + '/api/salas/store', {
        id: $('#sala-id').val() || null,
        nome: $('#sala-nome').val(),
        id_instituicao_vinculada: $('#sala-instituicao').val()
    });
});

function deletarSala(id) {
    bootbox.confirm({
        title: "Excluir Sala de Aula",
        message: "Tem certeza que deseja remover esta sala?",
        buttons: { confirm: { label: 'Excluir', className: 'btn-danger' } },
        callback: function(result) {
            if (result) sendAjaxRequest(BASE_URL + '/api/salas/delete', { id: id });
        }
    });
}
</script>
