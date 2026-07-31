<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Senac AgendaAI</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/public/css/custom.css" rel="stylesheet">

    <style>
        body.login-page {
            min-height: 100vh;
            margin: 0;
            background-color: var(--bg-primary);
            overflow-x: hidden;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Left Hero Banner (Desktop) */
        .login-hero {
            flex: 1.15;
            background: linear-gradient(135deg, #0a1d37 0%, #003366 40%, #004b87 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3.5rem;
            color: #ffffff;
            overflow: hidden;
        }

        .login-hero::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(243, 112, 33, 0.35) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            filter: blur(60px);
            animation: pulseGlow 8s ease-in-out infinite alternate;
        }

        .login-hero::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -20%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(0, 75, 135, 0.4) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            filter: blur(60px);
            animation: pulseGlow 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes pulseGlow {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.15) translate(30px, 30px); }
        }

        .hero-brand {
            z-index: 2;
        }

        .hero-title {
            font-size: 2.65rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.2;
            background: linear-gradient(135deg, #ffffff 0%, #ffedd5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content {
            z-index: 2;
            max-width: 540px;
        }

        .hero-features-list {
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
            margin-top: 2rem;
        }

        .hero-feature-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 1.15rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .hero-feature-card:hover {
            transform: translateX(8px);
            background: rgba(255, 255, 255, 0.14);
        }

        .hero-footer {
            z-index: 2;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        /* Right Form Section */
        .login-form-container {
            flex: 0.85;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            position: relative;
            background-color: var(--bg-surface);
        }

        .login-card {
            width: 100%;
            max-width: 440px;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom .form-control {
            border-radius: 12px;
            padding: 0.85rem 1rem 0.85rem 2.85rem;
            font-size: 0.95rem;
            border: 1px solid var(--border-color);
            background-color: var(--bg-primary);
            transition: all 0.25s ease;
        }

        .input-group-custom .form-control:focus {
            border-color: #004b87;
            box-shadow: 0 0 0 4px rgba(0, 75, 135, 0.15);
            background-color: var(--bg-surface);
        }

        .input-icon-left {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            z-index: 4;
            font-size: 1rem;
            pointer-events: none;
        }

        .btn-toggle-password {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: var(--text-muted);
            z-index: 4;
            padding: 0.35rem 0.5rem;
            cursor: pointer;
        }

        .quick-login-chip {
            cursor: pointer;
            padding: 0.4rem 0.85rem;
            border-radius: 50rem;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid var(--border-color);
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .quick-login-chip:hover {
            border-color: #f37021;
            background: rgba(243, 112, 33, 0.1);
            color: #f37021;
            transform: translateY(-2px);
        }

        .top-right-tools {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 10;
        }

        .senac-brand-text {
            color: #004b87;
            font-weight: 900;
        }

        .senac-orange-text {
            color: #f37021;
            font-weight: 900;
        }

        @media (max-width: 991.98px) {
            .login-wrapper {
                flex-direction: column;
            }
            .login-hero {
                display: none;
            }
            .login-form-container {
                min-height: 100vh;
                padding: 1.5rem;
            }
        }
    </style>
    <script>const BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body class="login-page">

<div class="login-wrapper">
    <!-- HERO SIDEBANNER (DESKTOP SENAC REALITY) -->
    <div class="login-hero">
        <div class="hero-brand">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= BASE_URL ?>/public/img/senac-logo-white.svg" alt="Logo Senac" style="height: 52px;" class="img-fluid">
                <div class="border-start border-white border-opacity-25 ps-3 ms-1">
                    <h4 class="fw-bold mb-0 text-white">Agenda<span style="color: #60a5fa;">AI</span></h4>
                    <span class="badge border" style="font-size: 0.68rem; background: rgba(243, 112, 33, 0.25); color: #ffedd5; border-color: rgba(243, 112, 33, 0.4) !important;">EDUCAÇÃO PROFISSIONAL</span>
                </div>
            </div>
        </div>

        <div class="hero-content">
            <h1 class="hero-title mb-3">Gestão Integrada de Ambientes & Recursos Pedagógicos</h1>
            <p class="fs-6 opacity-85 mb-4">
                Plataforma oficial para agendamento de auditórios, laboratórios de informática, cozinhas pedagógicas e equipamentos nas unidades do Senac.
            </p>

            <div class="hero-features-list">
                <div class="hero-feature-card">
                    <div class="stat-icon p-3" style="width: 44px; height: 44px; font-size: 1.2rem; background: rgba(243, 112, 33, 0.25); color: #f37021;">
                        <i class="fa-solid fa-door-open text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-white">Salas de Aula & Laboratórios Especializados</h6>
                        <small class="opacity-75">Reserva simplificada de auditórios, estúdios e espaços de aprendizado.</small>
                    </div>
                </div>

                <div class="hero-feature-card">
                    <div class="stat-icon p-3" style="width: 44px; height: 44px; font-size: 1.2rem; background: rgba(0, 114, 206, 0.3); color: #60a5fa;">
                        <i class="fa-solid fa-laptop text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-white">Kits Multimídia & Equipamentos de TI</h6>
                        <small class="opacity-75">Controle de patrimônio para notebooks, projetores 4K e periféricos.</small>
                    </div>
                </div>

                <div class="hero-feature-card">
                    <div class="stat-icon p-3" style="width: 44px; height: 44px; font-size: 1.2rem; background: rgba(16, 185, 129, 0.25); color: #34d399;">
                        <i class="fa-solid fa-building-columns text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-white">Gestão Descentralizada por Unidade</h6>
                        <small class="opacity-75">Fila de aprovações e relatórios analíticos por unidade operacional.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-footer">
            Serviço Nacional de Aprendizagem Comercial &bull; Senac AgendaAI &copy; <?= date('Y') ?>
        </div>
    </div>

    <!-- FORM SECTION -->
    <div class="login-form-container">
        <!-- Top Theme Toggle Button -->
        <div class="top-right-tools">
            <button type="button" class="btn btn-outline-secondary border-0 rounded-circle" id="theme-toggle-btn" title="Alternar Tema Claro/Escuro">
                <i class="fa-solid fa-moon fs-5" id="theme-toggle-icon"></i>
            </button>
        </div>

        <div class="login-card">
            <!-- Mobile & Desktop Brand Header -->
            <div class="text-center mb-4">
                <img src="<?= BASE_URL ?>/public/img/senac-logo.svg" alt="Logo Senac" style="height: 56px; max-width: 100%;" class="mb-2 img-fluid">
                <h4 class="fw-bold mb-0 fs-3">
                    <span class="senac-orange-text">Agenda</span><span class="senac-brand-text">AI</span>
                </h4>
                <small class="text-muted fw-semibold d-block mt-1">Serviço Nacional de Aprendizagem Comercial</small>
            </div>

            <div class="mb-4 text-center">
                <h4 class="fw-bold mb-1">Acesso ao Sistema</h4>
                <p class="text-muted small mb-0">Digite seu e-mail institucional e senha para entrar.</p>
            </div>

            <form id="form-login">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">E-mail Institucional</label>
                    <div class="input-group-custom">
                        <i class="fa-solid fa-envelope input-icon-left"></i>
                        <input type="email" class="form-control" id="login-email" placeholder="seu.email@senac.br" required value="admin@agendaai.com">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-semibold small text-muted mb-0">Senha de Acesso</label>
                        <a href="javascript:void(0)" class="small text-decoration-none fw-semibold text-primary" id="btn-esqueci-senha">Esqueceu a senha?</a>
                    </div>
                    <div class="input-group-custom">
                        <i class="fa-solid fa-lock input-icon-left"></i>
                        <input type="password" class="form-control" id="login-senha" placeholder="Sua senha de acesso" required value="123456">
                        <button type="button" class="btn-toggle-password" id="btn-toggle-pass" title="Mostrar/Ocultar Senha">
                            <i class="fa-solid fa-eye" id="pass-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Chips para Login Rápido de Demonstração -->
                <div class="mb-4">
                    <small class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-bolt text-warning me-1"></i> Contas de Teste (Clique para preencher):
                    </small>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="quick-login-chip" onclick="fillQuickLogin('admin@agendaai.com', '123456')">
                            👑 Admin Senac
                        </span>
                        <span class="quick-login-chip" onclick="fillQuickLogin('carlos.eduardo@agendaai.com', '123456')">
                            👨‍🏫 Docente / Instrutor
                        </span>
                        <span class="quick-login-chip" onclick="fillQuickLogin('natansmak@gmail.com', '123456')">
                            👤 Funcionário / Apoio
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-3 fs-6 shadow-sm fw-bold mb-3 d-flex align-items-center justify-content-center" id="btn-submit-login">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Entrar no Portal Senac
                </button>
            </form>

            <div class="text-center mt-4 text-muted small border-top pt-3">
                Senac &bull; Sistema de Agendamento de Recursos &bull; SSL Seguro
            </div>
        </div>
    </div>
</div>

<!-- JS Core Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js"></script>
<script src="<?= BASE_URL ?>/public/js/app.js"></script>

<script>
function fillQuickLogin(email, pass) {
    $('#login-email').val(email);
    $('#login-senha').val(pass);
}

$('#btn-toggle-pass').on('click', function() {
    const input = $('#login-senha');
    const icon = $('#pass-icon');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

$('#form-login').on('submit', function(e) {
    e.preventDefault();
    const email = $('#login-email').val();
    const senha = $('#login-senha').val();
    const btn = $('#btn-submit-login');

    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Autenticando no Senac...');

    $.ajax({
        url: BASE_URL + '/login',
        type: 'POST',
        data: JSON.stringify({ email: email, senha: senha }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                btn.html('<i class="fa-solid fa-check me-2"></i> Sucesso! Acessando Portal...');
                window.location.href = BASE_URL + '/dashboard';
            } else {
                btn.prop('disabled', false).html('<i class="fa-solid fa-right-to-bracket me-2"></i> Entrar no Portal Senac');
                bootbox.alert("<strong class='text-danger'>Erro no Acesso!</strong> " + res.message);
            }
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="fa-solid fa-right-to-bracket me-2"></i> Entrar no Portal Senac');
            bootbox.alert("<strong class='text-danger'>Erro!</strong> Falha na comunicação com o servidor.");
        }
    });
});

$('#btn-esqueci-senha').on('click', function() {
    bootbox.prompt({
        title: "Recuperação de Senha por E-mail - Senac",
        message: "<p class='text-muted small'>Informe seu e-mail institucional Senac. Enviaremos um link temporário para redefinição de sua credencial de acesso.</p>",
        inputType: 'email',
        placeholder: "seu.email@senac.br",
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
                            bootbox.alert({ title: "Verifique seu E-mail Institucional", message: msg });
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
