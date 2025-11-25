<?php
/**
 * Financial Controller
 * Manages financial records, payments and invoices
 */
class FinancialController extends Controller {
    
    private Affiliation $affiliationModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->affiliationModel = new Affiliation();
    }
    
    public function index(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'contabilidad']);
        
        // Get financial summary
        $monthlyRevenue = $this->affiliationModel->getTotalRevenue('month');
        $yearlyRevenue = $this->affiliationModel->getTotalRevenue('year');
        $pendingPayments = $this->getPendingPayments();
        $monthlyStats = $this->affiliationModel->getMonthlyStats();
        
        $this->view('financial/index', [
            'pageTitle' => 'Módulo Financiero',
            'currentPage' => 'financiero',
            'monthlyRevenue' => $monthlyRevenue,
            'yearlyRevenue' => $yearlyRevenue,
            'pendingPayments' => $pendingPayments,
            'monthlyStats' => $monthlyStats
        ]);
    }
    
    public function payments(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'contabilidad']);
        
        $status = $this->getInput('status', 'all');
        $payments = $this->getPaymentsList($status);
        
        $this->view('financial/payments', [
            'pageTitle' => 'Pagos',
            'currentPage' => 'financiero',
            'payments' => $payments,
            'status' => $status
        ]);
    }
    
    public function invoices(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'contabilidad']);
        
        $status = $this->getInput('status', 'all');
        $invoices = $this->getInvoicesList($status);
        
        $this->view('financial/invoices', [
            'pageTitle' => 'Facturación',
            'currentPage' => 'financiero',
            'invoices' => $invoices,
            'status' => $status
        ]);
    }
    
    public function recordPayment(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'contabilidad']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $_SESSION['flash_error'] = 'Token de seguridad inválido.';
                $this->redirect('financiero/pagos');
            }
            
            $affiliationId = (int) $this->getInput('affiliation_id');
            $paymentMethod = $this->sanitize($this->getInput('payment_method', ''));
            $paymentReference = $this->sanitize($this->getInput('payment_reference', ''));
            
            try {
                $this->affiliationModel->update($affiliationId, [
                    'payment_status' => 'paid',
                    'payment_method' => $paymentMethod,
                    'payment_reference' => $paymentReference
                ]);
                
                // Log the action
                $this->logAudit('payment_recorded', 'affiliations', $affiliationId);
                
                $_SESSION['flash_success'] = 'Pago registrado correctamente.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Error al registrar el pago: ' . $e->getMessage();
            }
            
            $this->redirect('financiero/pagos');
        }
    }
    
    public function generateInvoice(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'contabilidad']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $_SESSION['flash_error'] = 'Token de seguridad inválido.';
                $this->redirect('financiero/facturas');
            }
            
            $affiliationId = (int) $this->getInput('affiliation_id');
            $invoiceNumber = $this->sanitize($this->getInput('invoice_number', ''));
            
            try {
                $this->affiliationModel->update($affiliationId, [
                    'invoice_number' => $invoiceNumber,
                    'invoice_status' => 'invoiced'
                ]);
                
                $this->logAudit('invoice_generated', 'affiliations', $affiliationId);
                
                $_SESSION['flash_success'] = 'Factura registrada correctamente.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Error al registrar la factura: ' . $e->getMessage();
            }
            
            $this->redirect('financiero/facturas');
        }
    }
    
    public function report(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'contabilidad']);
        
        $year = (int) $this->getInput('year', date('Y'));
        $monthlyStats = $this->affiliationModel->getMonthlyStats($year);
        
        $this->view('financial/report', [
            'pageTitle' => 'Reporte Financiero',
            'currentPage' => 'financiero',
            'year' => $year,
            'monthlyStats' => $monthlyStats
        ]);
    }
    
    private function getPendingPayments(): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name, c.rfc, c.corporate_email,
                       m.name as membership_name, u.name as affiliate_name
                FROM affiliations a
                JOIN contacts c ON a.contact_id = c.id
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON a.affiliate_user_id = u.id
                WHERE a.payment_status IN ('pending', 'partial')
                ORDER BY a.affiliation_date DESC";
        return $this->db->fetchAll($sql);
    }
    
    private function getPaymentsList(string $status): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name, c.rfc,
                       m.name as membership_name, u.name as affiliate_name
                FROM affiliations a
                JOIN contacts c ON a.contact_id = c.id
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON a.affiliate_user_id = u.id";
        
        if ($status !== 'all') {
            $sql .= " WHERE a.payment_status = :status";
            $sql .= " ORDER BY a.affiliation_date DESC";
            return $this->db->fetchAll($sql, ['status' => $status]);
        }
        
        $sql .= " ORDER BY a.affiliation_date DESC";
        return $this->db->fetchAll($sql);
    }
    
    private function getInvoicesList(string $status): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name, c.rfc, c.fiscal_address,
                       m.name as membership_name
                FROM affiliations a
                JOIN contacts c ON a.contact_id = c.id
                JOIN membership_types m ON a.membership_type_id = m.id
                WHERE a.payment_status = 'paid'";
        
        if ($status !== 'all') {
            $sql .= " AND a.invoice_status = :status";
            $sql .= " ORDER BY a.affiliation_date DESC";
            return $this->db->fetchAll($sql, ['status' => $status]);
        }
        
        $sql .= " ORDER BY a.affiliation_date DESC";
        return $this->db->fetchAll($sql);
    }
    
    private function logAudit(string $action, string $table, int $recordId): void {
        $sql = "INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address, created_at)
                VALUES (:user_id, :action, :table_name, :record_id, :ip_address, NOW())";
        $this->db->query($sql, [
            'user_id' => $_SESSION['user_id'],
            'action' => $action,
            'table_name' => $table,
            'record_id' => $recordId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }
}
