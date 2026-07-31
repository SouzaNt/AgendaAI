<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-users text-primary me-2"></i> Funcionários & Usuários</h3>
        <p class="text-muted mb-0">Gestão de credenciais, alteração de cadastros, permissões e reset administrativo de senha.</p>
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

<!-- Seletor Obrigatório de Unidade / Instituição Senac -->
<div class="card card-glass border-primary border-opacity-25 p-3 mb-4 shadow-sm" style="background: rgba(0, 75, 135, 0.03);">
    <div class="row align-items-center g-3">
        <div class="col-md-5">
            <label class="form-label fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-building-columns fs-5 text-warning"></i> Selecione a Unidade / Instituição Senac:
            </label>
            <small class="text-muted d-block">Escolha a unidade para listar os funcionários vinculados.</small>
        </div>
        <div class="col-md-7">
            <select class="form-select form-select-lg select2" id="filtro-inst-main" onchange="aplicarFiltroInstituicao()">
                <option value="">🔍 Selecione uma Unidade do Senac para carregar os funcionários...</option>
                <option value="ALL">🌐 Visualizar Funcionários de Todas as Unidades Senac</option>
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
            <i class="fa-solid fa-users-gear"></i>
        </div>
        <h4 class="fw-bold mb-2">Selecione uma Unidade do Senac</h4>
        <p class="text-muted mx-auto" style="max-width: 520px;">
            Escolha uma unidade operacional no seletor acima para listar e gerenciar as credenciais e permissões dos funcionários desta instituição.
        </p>
    </div>
</div>

<!-- Tabela de Funcionários (Carregada após selecionar a Unidade) -->
<div class="card card-glass border-0 p-4 d-none" id="container-tabela-principal">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable tabela-usuarios" id="tabela-usuarios-main">
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
                <tr class="linha-usuario" data-inst-ids='<?= json_encode($u['instituicoes_vinculadas'] ?? []) ?>'>
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
                            <button class="btn btn-sm btn-outline-primary" title="Editar Informações do Usuário" onclick='editarUsuario(<?= json_encode($u) ?>)'>
                                <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-outline-warning" title="Resetar Senha Padrão (123456)" onclick="resetSenhaAdmin(<?= $u['id'] ?>)">
                                <i class="fa-solid fa-key me-1"></i> Reset 123456
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Desativar Funcionário" onclick="deletarUsuario(<?= $u['id'] ?>)">
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
                <h5 class="modal-title fw-bold" id="modal-usuario-titulo">Cadastrar Funcionário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-usuario">
                <input type="hidden" id="u-id" value="">
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
                        <label class="form-label fw-semibold">Nova Senha (Opcional ao editar)</label>
                        <input type="password" class="form-control" id="u-senha" placeholder="Deixe em branco para manter a senha atual">
                        <small class="text-muted d-block mt-1">Ao cadastrar um novo usuário, se este campo estiver vazio, a senha inicial será <strong>123456</strong>.</small>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Função / Cargo *</label>
                            <select class="form-select" id="u-funcao" required>
                                <?php foreach ($funcoes as $f): ?>
                                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Grupo de Acesso *</label>
                            <select class="form-select" id="u-grupo" required>
                                <?php foreach ($grupos as $g): ?>
                                    <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
                        <label class="form-check-label fw-semibold" for="u-recebe-email">Receber Notificações e Lembretes por E-mail</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// DataTables custom search filter para Funcionários
if ($.fn.dataTable) {
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData, counter) {
        if (!settings.nTable || ($(settings.nTable).attr('id') !== 'tabela-usuarios-main' && !$(settings.nTable).hasClass('tabela-usuarios'))) {
            return true;
        }

        const val = $('#filtro-inst-main').val();
        if (!val || val === 'ALL') return true;

        const node = settings.aoData[dataIndex].nTr;
        const instIds = $(node).data('inst-ids') || $(node).attr('data-inst-ids') || [];
        let parsedIds = [];
        try {
            parsedIds = typeof instIds === 'string' ? JSON.parse(instIds) : instIds;
        } catch(e) {
            parsedIds = [instIds];
        }

        const stringIds = Array.isArray(parsedIds) ? parsedIds.map(String) : [String(parsedIds)];
        return stringIds.includes(String(val));
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
        
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabela-usuarios-main')) {
            $('#tabela-usuarios-main').DataTable().draw();
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

function abrirModalUsuario() {
    $('#u-id').val('');
    $('#u-nome').val('');
    $('#u-email').val('');
    $('#u-senha').val('');
    if ($.fn.select2) $('#u-instituicoes').val(null).trigger('change');
    
    const selInst = $('#filtro-inst-main').val();
    if (selInst && selInst !== 'ALL' && $.fn.select2) {
        $('#u-instituicoes').val([selInst]).trigger('change');
    }
    
    $('#u-recebe-email').prop('checked', true);
    $('#modal-usuario-titulo').text('Cadastrar Funcionário');
    $('#modal-usuario').modal('show');
}

function editarUsuario(u) {
    $('#u-id').val(u.id);
    $('#u-nome').val(u.nome);
    $('#u-email').val(u.email);
    $('#u-senha').val('');
    $('#u-funcao').val(u.id_funcao);
    $('#u-grupo').val(u.id_grupo);
    if ($.fn.select2) {
        $('#u-instituicoes').val(u.instituicoes_vinculadas || []).trigger('change');
    }
    $('#u-recebe-email').prop('checked', u.recebe_email !== false);
    $('#modal-usuario-titulo').text('Editar Funcionário #' + u.id);
    $('#modal-usuario').modal('show');
}

$('#form-usuario').on('submit', function(e) {
    e.preventDefault();
    sendAjaxRequest(BASE_URL + '/api/usuarios/store', {
        id: $('#u-id').val() || null,
        nome: $('#u-nome').val(),
        email: $('#u-email').val(),
        senha: $('#u-senha').val(),
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
