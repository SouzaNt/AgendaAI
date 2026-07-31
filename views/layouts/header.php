<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? null;
$currentRoute = $_GET['route'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgendaAI - Sistema de Agendamento de Recursos</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 CSS & Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Flatpickr (DatePicker) CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/public/css/custom.css" rel="stylesheet">

    <!-- JS Core Libraries (Carregadas no Head para disponibilidade global em views) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/pt-br.global.min.js"></script>

    <script>
        const BASE_URL = '<?= BASE_URL ?>';
    </script>
</head>
<body>

<div class="app-wrapper">
    <!-- Sidebar Menu -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="stat-icon blue" style="width: 36px; height: 36px; font-size: 1rem;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <a href="<?= BASE_URL ?>/dashboard" class="sidebar-brand">Agenda<strong>AI</strong></a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Navegação Principal</div>
            <?php if (Auth::canViewScreen('dashboard')): ?>
            <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= strpos($currentRoute, 'dashboard') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i> Painel BI / Dashboard
            </a>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('agenda')): ?>
            <a href="<?= BASE_URL ?>/agenda" class="nav-link <?= $currentRoute === 'agenda' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-days"></i> Módulo Agenda
            </a>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('aprovacoes')): ?>
            <a href="<?= BASE_URL ?>/aprovacoes" class="nav-link <?= strpos($currentRoute, 'aprovacoes') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-clipboard-check text-warning"></i> Fila de Aprovações
            </a>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('recursos') || Auth::canViewScreen('salas') || Auth::canViewScreen('instituicoes')): ?>
            <div class="nav-label">Gestão de Recursos</div>
            <?php if (Auth::canViewScreen('recursos')): ?>
            <a href="<?= BASE_URL ?>/recursos" class="nav-link <?= strpos($currentRoute, 'recursos') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-laptop"></i> Equipamentos / Recursos
            </a>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('salas')): ?>
            <a href="<?= BASE_URL ?>/salas" class="nav-link <?= strpos($currentRoute, 'salas') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-door-open"></i> Salas de Aula
            </a>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('instituicoes')): ?>
            <a href="<?= BASE_URL ?>/instituicoes" class="nav-link <?= strpos($currentRoute, 'instituicoes') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-building-columns"></i> Instituições & Unidades
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('usuarios') || Auth::canViewScreen('localizacao') || Auth::canViewScreen('relatorios') || Auth::canViewScreen('auditoria') || Auth::canViewScreen('configuracoes')): ?>
            <div class="nav-label">Administração</div>
            <?php if (Auth::canViewScreen('usuarios')): ?>
            <a href="<?= BASE_URL ?>/usuarios" class="nav-link <?= strpos($currentRoute, 'usuarios') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Funcionários & Perfis
            </a>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('localizacao')): ?>
            <a href="<?= BASE_URL ?>/localizacao" class="nav-link <?= strpos($currentRoute, 'localizacao') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-map-location-dot"></i> Base Geográfica
            </a>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('relatorios')): ?>
            <a href="<?= BASE_URL ?>/relatorios" class="nav-link <?= strpos($currentRoute, 'relatorios') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-file-invoice"></i> Relatórios & BI
            </a>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('auditoria')): ?>
            <a href="<?= BASE_URL ?>/auditoria" class="nav-link <?= strpos($currentRoute, 'auditoria') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-shield-halved"></i> Logs & Auditoria
            </a>
            <?php endif; ?>

            <?php if (Auth::canViewScreen('configuracoes')): ?>
            <a href="<?= BASE_URL ?>/configuracoes" class="nav-link <?= strpos($currentRoute, 'configuracoes') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-sliders"></i> Configurações Gerais
            </a>
            <?php endif; ?>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <main class="main-content">
        <!-- Top Bar Header -->
        <header class="top-bar">
            <div class="d-flex align-items-center gap-3">
                <span class="fs-5 font-monospace fw-semibold text-secondary">
                    <i class="fa-solid fa-clock opacity-50 me-1"></i> <?= date('d/m/Y') ?>
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Theme Switcher Toggle Button -->
                <button type="button" class="btn btn-outline-secondary border-0 rounded-circle" id="theme-toggle-btn" title="Alternar Tema Claro/Escuro">
                    <i class="fa-solid fa-moon fs-5" id="theme-toggle-icon"></i>
                </button>

                <!-- User Info Profile Dropdown -->
                <?php if ($usuario): ?>
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center gap-2 p-0 text-body" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                            <?= strtoupper(substr($usuario['nome'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="text-start d-none d-sm-block">
                            <div class="fw-semibold text-truncate" style="max-width: 140px;"><?= htmlspecialchars($usuario['nome']) ?></div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;"><?= htmlspecialchars($usuario['grupo_nome'] ?? 'Usuário') ?></small>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Sair do Sistema</a></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-container">
