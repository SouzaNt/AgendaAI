<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-building-columns text-primary me-2"></i> Instituições & Unidades</h3>
        <p class="text-muted mb-0">Estrutura organizacional, sedes e filiais vinculadas aos agendamentos.</p>
    </div>

    <button class="btn btn-primary-custom rounded-pill px-4" onclick="abrirModalInstituicao()">
        <i class="fa-solid fa-plus me-1"></i> Cadastrar Instituição
    </button>
</div>

<div class="card card-glass border-0 p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome da Instituição</th>
                    <th>Unidade Pai</th>
                    <th>Município / Bairro</th>
                    <th>Logradouro Completo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instituicoes as $inst): ?>
                <tr>
                    <td><strong>#<?= $inst['id'] ?></strong></td>
                    <td><strong class="text-primary"><?= htmlspecialchars($inst['nome']) ?></strong></td>
                    <td>
                        <?php 
                            if (!empty($inst['unidade_pai'])) {
                                $pai = JsonDatabase::findById('instituicoes', $inst['unidade_pai']);
                                echo htmlspecialchars($pai['nome'] ?? 'Sede');
                            } else {
                                echo '<span class="badge bg-primary">Sede Principal</span>';
                            }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($inst['municipio'] ?? '') ?> / <?= htmlspecialchars($inst['bairro'] ?? '') ?></td>
                    <td><?= htmlspecialchars($inst['logradouro_completo'] ?? '') ?>, <?= htmlspecialchars($inst['numero'] ?? '') ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary rounded-pill me-1" onclick='editarInstituicao(<?= json_encode($inst) ?>)'>
                            <i class="fa-solid fa-pen-to-square"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="deletarInstituicao(<?= $inst['id'] ?>)">
                            <i class="fa-solid fa-trash-can"></i> Excluir
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal CRUD Instituicao -->
<div class="modal fade" id="modal-instituicao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-glass border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="modal-inst-titulo">Cadastrar Instituição / Unidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-instituicao">
                <input type="hidden" id="inst-id" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome da Instituição *</label>
                        <input type="text" class="form-control" id="inst-nome" required placeholder="Ex: Campus Central - Sede">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unidade Pai (Opcional se for Sede)</label>
                        <select class="form-select" id="inst-pai">
                            <option value="">Nenhuma (Sede Principal)</option>
                            <?php foreach ($instituicoes as $i): ?>
                                <option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Município</label>
                            <input type="text" class="form-control" id="inst-municipio" placeholder="São Paulo">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Bairro</label>
                            <input type="text" class="form-control" id="inst-bairro" placeholder="Centro">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label fw-semibold">Logradouro</label>
                            <input type="text" class="form-control" id="inst-logradouro" placeholder="Av. Paulista">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Número</label>
                            <input type="text" class="form-control" id="inst-numero" placeholder="1000">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Salvar Unidade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalInstituicao() {
    $('#inst-id').val('');
    $('#inst-nome').val('');
    $('#inst-municipio').val('');
    $('#inst-bairro').val('');
    $('#inst-logradouro').val('');
    $('#inst-numero').val('');
    $('#modal-inst-titulo').text('Cadastrar Instituição / Unidade');
    $('#modal-instituicao').modal('show');
}

function editarInstituicao(inst) {
    $('#inst-id').val(inst.id);
    $('#inst-nome').val(inst.nome);
    $('#inst-pai').val(inst.unidade_pai || '');
    $('#inst-municipio').val(inst.municipio || '');
    $('#inst-bairro').val(inst.bairro || '');
    $('#inst-logradouro').val(inst.logradouro_completo || '');
    $('#inst-numero').val(inst.numero || '');
    $('#modal-inst-titulo').text('Editar Instituição #' + inst.id);
    $('#modal-instituicao').modal('show');
}

$('#form-instituicao').on('submit', function(e) {
    e.preventDefault();
    sendAjaxRequest(BASE_URL + '/api/instituicoes/store', {
        id: $('#inst-id').val() || null,
        nome: $('#inst-nome').val(),
        unidade_pai: $('#inst-pai').val(),
        municipio: $('#inst-municipio').val(),
        bairro: $('#inst-bairro').val(),
        logradouro_completo: $('#inst-logradouro').val(),
        numero: $('#inst-numero').val()
    });
});

function deletarInstituicao(id) {
    bootbox.confirm({
        title: "Excluir Instituição",
        message: "Tem certeza que deseja remover esta instituição?",
        buttons: { confirm: { label: 'Excluir', className: 'btn-danger' } },
        callback: function(result) {
            if (result) sendAjaxRequest(BASE_URL + '/api/instituicoes/delete', { id: id });
        }
    });
}
</script>
