<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-laptop text-primary me-2"></i> Cadastro & Gestão de Recursos</h3>
        <p class="text-muted mb-0">Gerencie o inventário de equipamentos, controle de manutenção e histórico de movimentação.</p>
    </div>

    <button class="btn btn-primary-custom rounded-pill px-4" onclick="abrirModalRecurso()">
        <i class="fa-solid fa-plus me-1"></i> Cadastrar Recurso
    </button>
</div>

<div class="card card-glass border-0 p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Recurso</th>
                    <th>Patrimônio</th>
                    <th>Nº Série</th>
                    <th>Estado</th>
                    <th>Disponível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recursos as $rec): ?>
                <tr>
                    <td><strong>#<?= $rec['id'] ?></strong></td>
                    <td>
                        <strong class="text-primary"><?= htmlspecialchars($rec['nome']) ?></strong>
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
                        <label class="form-label fw-semibold">Número do Patrimônio *</label>
                        <input type="text" class="form-control mask-patrimonio" id="rec-patrimonio" required placeholder="PAT-2026-000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instituição Responsável *</label>
                        <select class="form-select" id="rec-instituicao" required>
                            <?php foreach ($instituicoes as $inst): ?>
                                <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Número de Série (Opcional)</label>
                        <input type="text" class="form-control" id="rec-serie" placeholder="Ex: SN-998811">
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
function abrirModalRecurso() {
    $('#rec-id').val('');
    $('#rec-nome').val('');
    $('#rec-patrimonio').val('');
    $('#rec-serie').val('');
    $('#modal-recurso-titulo').text('Cadastrar Recurso');
    $('#modal-recurso').modal('show');
}

function editarRecurso(rec) {
    $('#rec-id').val(rec.id);
    $('#rec-nome').val(rec.nome);
    $('#rec-tipo').val(rec.id_tipo_recurso);
    $('#rec-patrimonio').val(rec.patrimonio);
    $('#rec-instituicao').val(rec.id_instituicao_responsavel);
    $('#rec-serie').val(rec.numero_serie || '');
    $('#modal-recurso-titulo').text('Editar Recurso #' + rec.id);
    $('#modal-recurso').modal('show');
}

$('#form-recurso').on('submit', function(e) {
    e.preventDefault();
    const payload = {
        id: $('#rec-id').val(),
        nome: $('#rec-nome').val(),
        id_tipo_recurso: $('#rec-tipo').val(),
        patrimonio: $('#rec-patrimonio').val(),
        id_instituicao_responsavel: $('#rec-instituicao').val(),
        numero_serie: $('#rec-serie').val()
    };
    sendAjaxRequest(BASE_URL + '/api/recursos/store', payload);
});

function toggleManutencao(id, estadoAtual) {
    const novoEstado = (estadoAtual === 'Funcionando') ? 'Não Funcionando' : 'Funcionando';
    bootbox.confirm({
        title: "Alterar Estado de Manutenção",
        message: `Deseja alterar o estado do recurso para <strong>${novoEstado}</strong>? ${novoEstado === 'Não Funcionando' ? '<br><span class="text-danger fw-bold">Isso bloqueará automaticamente novos agendamentos!</span>' : ''}`,
        buttons: { confirm: { label: 'Confirmar', className: 'btn-warning' } },
        callback: function(result) {
            if (result) {
                sendAjaxRequest(BASE_URL + '/api/recursos/estado', { id: id, estado: novoEstado });
            }
        }
    });
}

function deletarRecurso(id) {
    bootbox.confirm({
        title: "Excluir Recurso",
        message: "Tem certeza que deseja remover este recurso? (Exclusão Lógica: o registro será desativado).",
        buttons: { confirm: { label: 'Excluir', className: 'btn-danger' } },
        callback: function(result) {
            if (result) {
                sendAjaxRequest(BASE_URL + '/api/recursos/delete', { id: id });
            }
        }
    });
}
</script>
