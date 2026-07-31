<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/AuditModel.php';
require_once ROOT_PATH . '/app/Models/EmailLogModel.php';

class AuditController extends Controller {
    public function index() {
        if (!Auth::check()) $this->redirect('login');

        $auditoriaLogs = AuditModel::getAll();
        $emailLogs = EmailLogModel::getAll();

        // Ordenar do mais recente para o mais antigo
        usort($auditoriaLogs, function ($a, $b) {
            return strtotime($b['data_hora'] ?? '0') - strtotime($a['data_hora'] ?? '0');
        });

        usort($emailLogs, function ($a, $b) {
            return strtotime($b['data_envio'] ?? '0') - strtotime($a['data_envio'] ?? '0');
        });

        $this->render('auditoria/index', [
            'auditoriaLogs' => $auditoriaLogs,
            'emailLogs' => $emailLogs
        ]);
    }
}
