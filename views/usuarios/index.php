<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-users text-primary me-2"></i> Funcionários & Usuários</h3>
        <p class="text-muted mb-0">Gestão de credenciais, permissões de grupo e reset administrativo de senha.</p>
    </div>

    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/usuarios/grupos" class="btn btn-outline-primary rounded-pill px-3">
            <i class="fa-solid fa-user-shield me-1"></i> Grupos de Acesso
        </a>
        <button class="btn btn-primary-custom rounded-pill px-4" onclick="abrirModalUsuario()">
            <i class="fa-solid fa-user-plus me-1"></i> Cadastrar Funcionário
        </button>
    </div>
</div>

<div class="card card-glass border-0 p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Função / Cargo</th>
                    <th>Grupo de Acesso</th>
                    <th>Recebe E-mails</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><strong>#<?= $u['id'] ?></strong></td>
                    <td>
                        <strong class="text-primary"><?= htmlspecialchars($u['nome']) ?></strong>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php 
                            $func = JsonDatabase::findById('funcoes', $u['id_funcao']);
                            echo htmlspecialchars($func['nome'] ?? 'N/A');
                        ?>
                    </td>
                    <td>
                        <?php 
                            $grp = JsonDatabase::findById('grupos', $u['id_grupo']);
                            echo '<span class="badge bg-secondary">' . htmlspecialchars($grp['nome'] ?? 'Usuário') . '</span>';
                        ?>
                    </td>
                    <td>
                        <?= !empty($u['recebe_email']) ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-light text-dark">Não</span>' ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-warning rounded-start" title="Resetar Senha Padrão (123456)" onclick="resetSenhaAdmin(<?= $u['id'] ?>)">
                                <i class="fa-solid fa-key me-1"></i> Reset 123456
                            </button>
                            <button class="btn btn-sm btn-outline-danger rounded-end" title="Desativar Funcionário" onclick="deletarUsuario(<?= $u['id'] ?>)">
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

<!-- Modal CRUD Funcionario -->
<div class="modal fade" id="modal-usuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-glass border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Cadastrar Funcionário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-usuario">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome Completo *</label>
                        <input type="text" class="form-control" id="u-nome" required placeholder="Ex: Maria Oliveira">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">E-mail Institucional *</label>
                        <input type="email" class="form-control" id="u-email" required placeholder="maria@empresa.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Função / Cargo *</label>
                        <select class="form-select" id="u-funcao" required>
                            <?php foreach ($funcoes as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Grupo de Acesso (Permissões) *</label>
                        <select class="form-select" id="u-grupo" required>
                            <?php foreach ($grupos as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instituições Vinculadas</label>
                        <select class="form-select select2" id="u-instituicoes" multiple>
                            <?php foreach ($instituicoes as $inst): ?>
                                <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="u-recebe-email" checked>
                        <label class="form-check-label fw-semibold" for="u-recebe-email">Receber Notificações e Lembretes por E-mail (Padrão: Sim)</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Salvar Funcionário</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalUsuario() {
    $('#u-nome').val('');
    $('#u-email').val('');
    $('#modal-usuario').modal('show');
}

$('#form-usuario').on('submit', function(e) {
    e.preventDefault();
    sendAjaxRequest(BASE_URL + '/api/usuarios/store', {
        nome: $('#u-nome').val(),
        email: $('#u-email').val(),
        id_funcao: $('#u-funcao').val(),
        id_grupo: $('#u-grupo').val(),
        instituicoes_vinculadas: $('#u-instituicoes').val(),
        recebe_email: $('#u-recebe-email').is(':checked')
    });
});

function resetSenhaAdmin(id) {
    bootbox.confirm({
        title: "Reset de Senha Administrativo",
        message: "Deseja redefinir a senha deste funcionário para o valor padrão <strong>123456</strong>?",
        buttons: { confirm: { label: 'Resetar para 123456', className: 'btn-warning' } },
        callback: function(result) {
            if (result) sendAjaxRequest(BASE_URL + '/api/usuarios/reset-admin', { id: id });
        }
    });
}

function deletarUsuario(id) {
    bootbox.confirm({
        title: "Desativar Funcionário",
        message: "Tem certeza que deseja desativar este funcionário (Exclusão Lógica)?",
        buttons: { confirm: { label: 'Desativar', className: 'btn-danger' } },
        callback: function(result) {
            if (result) sendAjaxRequest(BASE_URL + '/api/usuarios/delete', { id: id });
        }
    });
}
</script>
