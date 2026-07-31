<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - AgendaAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/custom.css" rel="stylesheet">
    <script>const BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-primary bg-gradient">

<div class="card card-glass shadow-lg border-0" style="width: 100%; max-width: 420px;">
    <div class="card-body p-4 p-sm-5 text-center">
        <div class="mb-4">
            <div class="stat-icon blue mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                <i class="fa-solid fa-key"></i>
            </div>
            <h3 class="fw-bold">Redefinir Senha</h3>
            <p class="text-muted fs-6">Defina sua nova credencial de acesso para <strong><?= htmlspecialchars($user['email']) ?></strong></p>
        </div>

        <form id="form-reset">
            <input type="hidden" id="reset-token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-floating mb-3 text-start">
                <input type="password" class="form-control" id="nova-senha" placeholder="Nova Senha" required minlength="6">
                <label for="nova-senha"><i class="fa-solid fa-lock me-2"></i>Nova Senha (Mínimo 6 caracteres)</label>
            </div>

            <div class="form-floating mb-4 text-start">
                <input type="password" class="form-control" id="confirma-senha" placeholder="Confirme a Senha" required minlength="6">
                <label for="confirma-senha"><i class="fa-solid fa-lock me-2"></i>Confirme a Nova Senha</label>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-2 fs-6 mb-3">
                <i class="fa-solid fa-check-double me-2"></i> Salvar Nova Senha
            </button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js"></script>
<script src="<?= BASE_URL ?>/public/js/app.js"></script>

<script>
$('#form-reset').on('submit', function(e) {
    e.preventDefault();
    const token = $('#reset-token').val();
    const novaSenha = $('#nova-senha').val();
    const confirmaSenha = $('#confirma-senha').val();

    if (novaSenha !== confirmaSenha) {
        bootbox.alert("<strong class='text-danger'>Erro!</strong> As senhas digitadas não coincidem.");
        return;
    }

    $.ajax({
        url: BASE_URL + '/reset-password',
        type: 'POST',
        data: JSON.stringify({ token: token, nova_senha: novaSenha }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                bootbox.alert({
                    title: "Sucesso!",
                    message: res.message,
                    callback: function() {
                        window.location.href = BASE_URL + '/login';
                    }
                });
            } else {
                bootbox.alert("<strong class='text-danger'>Erro!</strong> " + res.message);
            }
        }
    });
});
</script>
</body>
</html>
