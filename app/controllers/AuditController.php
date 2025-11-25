<?php
/**
 * Audit Controller
 * Manages audit log viewing and reporting
 */
class AuditController extends Controller {
    
    private AuditLog $auditModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->auditModel = new AuditLog();
    }
    
    public function index(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion']);
        
        $logs = $this->auditModel->getRecent(100);
        $stats = $this->auditModel->getStats();
        $actionsSummary = $this->auditModel->getActionsSummary();
        
        $this->view('audit/index', [
            'pageTitle' => 'Auditoría',
            'currentPage' => 'auditoria',
            'logs' => $logs,
            'stats' => $stats,
            'actionsSummary' => $actionsSummary
        ]);
    }
    
    public function byUser(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion']);
        
        $userId = (int) ($this->params['id'] ?? 0);
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            $_SESSION['flash_error'] = 'Usuario no encontrado.';
            $this->redirect('auditoria');
        }
        
        $logs = $this->auditModel->getByUser($userId, 100);
        
        $this->view('audit/by_user', [
            'pageTitle' => 'Auditoría - ' . $user['name'],
            'currentPage' => 'auditoria',
            'user' => $user,
            'logs' => $logs
        ]);
    }
    
    public function byTable(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion']);
        
        $table = $this->sanitize($this->getInput('table', ''));
        $recordId = (int) $this->getInput('record_id', 0);
        
        $logs = [];
        if (!empty($table)) {
            $logs = $this->auditModel->getByTable($table, $recordId ?: null);
        }
        
        $this->view('audit/by_table', [
            'pageTitle' => 'Auditoría por Tabla',
            'currentPage' => 'auditoria',
            'table' => $table,
            'recordId' => $recordId,
            'logs' => $logs
        ]);
    }
    
    public function search(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion']);
        
        $startDate = $this->sanitize($this->getInput('start_date', date('Y-m-d', strtotime('-7 days'))));
        $endDate = $this->sanitize($this->getInput('end_date', date('Y-m-d')));
        $action = $this->sanitize($this->getInput('action', ''));
        
        $logs = [];
        if (!empty($action)) {
            $logs = $this->auditModel->getByAction($action);
        } else {
            $logs = $this->auditModel->getByDateRange($startDate, $endDate);
        }
        
        $actionsSummary = $this->auditModel->getActionsSummary();
        
        $this->view('audit/search', [
            'pageTitle' => 'Búsqueda en Auditoría',
            'currentPage' => 'auditoria',
            'logs' => $logs,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'action' => $action,
            'actionsSummary' => $actionsSummary
        ]);
    }
    
    public function activity(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion']);
        
        $userActivity = $this->auditModel->getUserActivity();
        
        $this->view('audit/activity', [
            'pageTitle' => 'Actividad de Usuarios',
            'currentPage' => 'auditoria',
            'userActivity' => $userActivity
        ]);
    }
}
