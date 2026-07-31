<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-door-open text-primary me-2"></i> Cadastro & Gestão de Salas de Aula</h3>
        <p class="text-muted mb-0">Controle de auditórios, laboratórios e salas de aula por instituição.</p>
    </div>

    <button class="btn btn-primary-custom rounded-pill px-4" onclick="abrirModalSala()">
        <i class="fa-solid fa-plus me-1"></i> Cadastrar Nova Sala
    </button>
</div>

<div class="card card-glass border-0 p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
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
                <tr>
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
function abrirModalSala() {
    $('#sala-id').val('');
    $('#sala-nome').val('');
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
        title: "Remover Sala",
        message: "Tem certeza que deseja desativar esta sala de aula?",
        buttons: { confirm: { label: 'Excluir', className: 'btn-danger' } },
        callback: function(result) {
            if (result) sendAjaxRequest(BASE_URL + '/api/salas/delete', { id: id });
        }
    });
}
</script>
