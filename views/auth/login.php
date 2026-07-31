<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AgendaAI</title>
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
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <h3 class="fw-bold">Agenda<span class="text-primary">AI</span></h3>
            <p class="text-muted fs-6">Sistema Integrado de Agendamento de Recursos</p>
        </div>

        <form id="form-login">
            <div class="form-floating mb-3 text-start">
                <input type="email" class="form-control" id="login-email" placeholder="nome@empresa.com" required value="admin@agendaai.com">
                <label for="login-email"><i class="fa-solid fa-envelope me-2"></i>E-mail Institucional</label>
            </div>

            <div class="form-floating mb-3 text-start">
                <input type="password" class="form-control" id="login-senha" placeholder="Sua senha" required value="123456">
                <label for="login-senha"><i class="fa-solid fa-lock me-2"></i>Senha de Acesso</label>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <small><a href="javascript:void(0)" class="text-decoration-none fw-semibold" id="btn-esqueci-senha">Esqueceu a senha?</a></small>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-2 fs-6 mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Acessar Sistema
            </button>
        </form>

        <div class="border-top pt-3 text-muted small">
            AgendaAI &copy; <?= date('Y') ?> - Todos os direitos reservados.
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js"></script>
<script src="<?= BASE_URL ?>/public/js/app.js"></script>

<script>
$('#form-login').on('submit', function(e) {
    e.preventDefault();
    const email = $('#login-email').val();
    const senha = $('#login-senha').val();

    $.ajax({
        url: BASE_URL + '/login',
        type: 'POST',
        data: JSON.stringify({ email: email, senha: senha }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                window.location.href = BASE_URL + '/dashboard';
            } else {
                bootbox.alert("<strong class='text-danger'>Erro no Acesso!</strong> " + res.message);
            }
        }
    });
});

$('#btn-esqueci-senha').on('click', function() {
    bootbox.prompt({
        title: "Recuperação de Senha por E-mail",
        message: "<p class='text-muted small'>Informe seu e-mail cadastrado. Enviaremos um link temporário para redefinição de sua credencial.</p>",
        inputType: 'email',
        placeholder: "seu.email@empresa.com",
        buttons: {
            confirm: { label: 'Enviar Link', className: 'btn-primary' },
            cancel: { label: 'Cancelar', className: 'btn-secondary' }
        },
        callback: function (email) {
            if (email) {
                $.ajax({
                    url: BASE_URL + '/forgot-password',
                    type: 'POST',
                    data: JSON.stringify({ email: email }),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            let msg = res.message;
                            if (res.debug_link) {
                                msg += "<br><br><a href='" + res.debug_link + "' class='btn btn-sm btn-outline-primary mt-2' target='_blank'>📎 Acessar Link Temporário (Simulador)</a>";
                            }
                            bootbox.alert({ title: "Verifique seu E-mail", message: msg });
                        } else {
                            bootbox.alert("<strong class='text-danger'>Erro!</strong> " + res.message);
                        }
                    }
                });
            }
        }
    });
});
</script>
</body>
</html>
