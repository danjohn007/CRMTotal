<?php
/**
 * Financial Controller
 * Manages financial records, payments, invoices, categories and transactions
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
        
        // Get transactions summary
        $transactionsSummary = $this->getTransactionsSummary();
        
        $this->view('financial/index', [
            'pageTitle' => 'Módulo Financiero',
            'currentPage' => 'financiero',
            'monthlyRevenue' => $monthlyRevenue,
            'yearlyRevenue' => $yearlyRevenue,
            'pendingPayments' => $pendingPayments,
            'monthlyStats' => $monthlyStats,
            'transactionsSummary' => $transactionsSummary
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
    
    /**
     * Manage financial categories
     */
    public function categories(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'contabilidad']);
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $action = $this->getInput('action', '');
                
                switch ($action) {
                    case 'create':
                        $name = $this->sanitize($this->getInput('name', ''));
                        $type = $this->sanitize($this->getInput('type', 'ingreso'));
                        $description = $this->sanitize($this->getInput('description', ''));
                        
                        if (empty($name)) {
                            $error = 'El nombre es obligatorio.';
                        } elseif (!in_array($type, ['ingreso', 'egreso'])) {
                            $error = 'Tipo de categoría inválido.';
                        } else {
                            try {
                                $sql = "INSERT INTO financial_categories (name, type, description, is_active) VALUES (:name, :type, :description, 1)";
                                $this->db->query($sql, [
                                    'name' => $name,
                                    'type' => $type,
                                    'description' => $description
                                ]);
                                $success = 'Categoría creada exitosamente.';
                            } catch (Exception $e) {
                                $error = 'Error al crear la categoría: ' . $e->getMessage();
                            }
                        }
                        break;
                        
                    case 'toggle':
                        $id = (int) $this->getInput('id');
                        try {
                            $sql = "UPDATE financial_categories SET is_active = NOT is_active WHERE id = :id";
                            $this->db->query($sql, ['id' => $id]);
                            $success = 'Estado de la categoría actualizado.';
                        } catch (Exception $e) {
                            $error = 'Error al actualizar la categoría.';
                        }
                        break;
                        
                    case 'delete':
                        $id = (int) $this->getInput('id');
                        try {
                            // Check if category has transactions
                            $sql = "SELECT COUNT(*) as count FROM financial_transactions WHERE category_id = :id";
                            $result = $this->db->fetchOne($sql, ['id' => $id]);
                            if ($result && $result['count'] > 0) {
                                $error = 'No se puede eliminar una categoría con transacciones.';
                            } else {
                                $sql = "DELETE FROM financial_categories WHERE id = :id";
                                $this->db->query($sql, ['id' => $id]);
                                $success = 'Categoría eliminada.';
                            }
                        } catch (Exception $e) {
                            $error = 'Error al eliminar la categoría.';
                        }
                        break;
                }
            }
        }
        
        // Get all categories
        $categories = $this->getFinancialCategories();
        
        $this->view('financial/categories', [
            'pageTitle' => 'Categorías Financieras',
            'currentPage' => 'financiero',
            'categories' => $categories,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Manage financial transactions
     */
    public function transactions(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'contabilidad']);
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $action = $this->getInput('action', 'create');
                
                if ($action === 'create') {
                    $categoryId = (int) $this->getInput('category_id');
                    $description = $this->sanitize($this->getInput('description', ''));
                    $amount = (float) $this->getInput('amount', 0);
                    $transactionDate = $this->sanitize($this->getInput('transaction_date', date('Y-m-d')));
                    $reference = $this->sanitize($this->getInput('reference', ''));
                    $notes = $this->sanitize($this->getInput('notes', ''));
                    
                    if ($categoryId <= 0) {
                        $error = 'Seleccione una categoría válida.';
                    } elseif ($amount <= 0) {
                        $error = 'El monto debe ser mayor a cero.';
                    } elseif (empty($description)) {
                        $error = 'La descripción es obligatoria.';
                    } else {
                        try {
                            $sql = "INSERT INTO financial_transactions (category_id, description, amount, transaction_date, reference, notes, created_by, created_at) 
                                    VALUES (:category_id, :description, :amount, :transaction_date, :reference, :notes, :created_by, NOW())";
                            $this->db->query($sql, [
                                'category_id' => $categoryId,
                                'description' => $description,
                                'amount' => $amount,
                                'transaction_date' => $transactionDate,
                                'reference' => $reference,
                                'notes' => $notes,
                                'created_by' => $_SESSION['user_id']
                            ]);
                            
                            $this->logAudit('transaction_created', 'financial_transactions', $this->db->lastInsertId());
                            $success = 'Movimiento registrado exitosamente.';
                        } catch (Exception $e) {
                            $error = 'Error al registrar el movimiento: ' . $e->getMessage();
                        }
                    }
                } elseif ($action === 'delete') {
                    $id = (int) $this->getInput('id');
                    try {
                        $sql = "DELETE FROM financial_transactions WHERE id = :id";
                        $this->db->query($sql, ['id' => $id]);
                        $success = 'Movimiento eliminado.';
                    } catch (Exception $e) {
                        $error = 'Error al eliminar el movimiento.';
                    }
                }
            }
        }
        
        // Get filter parameters
        $type = $this->getInput('type', '');
        $categoryId = (int) $this->getInput('category_id', 0);
        $dateFrom = $this->getInput('date_from', date('Y-m-01'));
        $dateTo = $this->getInput('date_to', date('Y-m-d'));
        
        $transactions = $this->getTransactions($type, $categoryId, $dateFrom, $dateTo);
        $categories = $this->getFinancialCategories();
        $summary = $this->getTransactionsSummaryByRange($dateFrom, $dateTo);
        
        $this->view('financial/transactions', [
            'pageTitle' => 'Movimientos Financieros',
            'currentPage' => 'financiero',
            'transactions' => $transactions,
            'categories' => $categories,
            'summary' => $summary,
            'filters' => [
                'type' => $type,
                'category_id' => $categoryId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ],
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Transactions report by date range
     */
    public function transactionsReport(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'contabilidad']);
        
        $dateFrom = $this->getInput('date_from', date('Y-m-01'));
        $dateTo = $this->getInput('date_to', date('Y-m-d'));
        $type = $this->getInput('type', '');
        
        $transactions = $this->getTransactions($type, 0, $dateFrom, $dateTo);
        $summary = $this->getTransactionsSummaryByRange($dateFrom, $dateTo);
        $byCategory = $this->getTransactionsByCategory($dateFrom, $dateTo);
        
        $this->view('financial/transactions_report', [
            'pageTitle' => 'Reporte de Movimientos',
            'currentPage' => 'financiero',
            'transactions' => $transactions,
            'summary' => $summary,
            'byCategory' => $byCategory,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'type' => $type
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
    
    private function getFinancialCategories(): array {
        try {
            $sql = "SELECT * FROM financial_categories ORDER BY type, name";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            // Table may not exist yet - this is expected before running the update SQL
            // Log for debugging if needed
            error_log("FinancialController: Unable to fetch financial categories - " . $e->getMessage());
            return [];
        }
    }
    
    private function getTransactions(string $type = '', int $categoryId = 0, string $dateFrom = '', string $dateTo = ''): array {
        try {
            $sql = "SELECT t.*, c.name as category_name, c.type as category_type, u.name as created_by_name
                    FROM financial_transactions t
                    JOIN financial_categories c ON t.category_id = c.id
                    LEFT JOIN users u ON t.created_by = u.id
                    WHERE 1=1";
            $params = [];
            
            if (!empty($type)) {
                $sql .= " AND c.type = :type";
                $params['type'] = $type;
            }
            
            if ($categoryId > 0) {
                $sql .= " AND t.category_id = :category_id";
                $params['category_id'] = $categoryId;
            }
            
            if (!empty($dateFrom)) {
                $sql .= " AND t.transaction_date >= :date_from";
                $params['date_from'] = $dateFrom;
            }
            
            if (!empty($dateTo)) {
                $sql .= " AND t.transaction_date <= :date_to";
                $params['date_to'] = $dateTo;
            }
            
            $sql .= " ORDER BY t.transaction_date DESC, t.created_at DESC";
            return $this->db->fetchAll($sql, $params);
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function getTransactionsSummary(): array {
        try {
            $sql = "SELECT 
                        COALESCE(SUM(CASE WHEN c.type = 'ingreso' THEN t.amount ELSE 0 END), 0) as total_income,
                        COALESCE(SUM(CASE WHEN c.type = 'egreso' THEN t.amount ELSE 0 END), 0) as total_expense,
                        COUNT(*) as total_count
                    FROM financial_transactions t
                    JOIN financial_categories c ON t.category_id = c.id
                    WHERE MONTH(t.transaction_date) = MONTH(CURDATE()) 
                    AND YEAR(t.transaction_date) = YEAR(CURDATE())";
            $result = $this->db->fetchOne($sql);
            return $result ?: ['total_income' => 0, 'total_expense' => 0, 'total_count' => 0];
        } catch (Exception $e) {
            return ['total_income' => 0, 'total_expense' => 0, 'total_count' => 0];
        }
    }
    
    private function getTransactionsSummaryByRange(string $dateFrom, string $dateTo): array {
        try {
            $sql = "SELECT 
                        COALESCE(SUM(CASE WHEN c.type = 'ingreso' THEN t.amount ELSE 0 END), 0) as total_income,
                        COALESCE(SUM(CASE WHEN c.type = 'egreso' THEN t.amount ELSE 0 END), 0) as total_expense,
                        COUNT(*) as total_count
                    FROM financial_transactions t
                    JOIN financial_categories c ON t.category_id = c.id
                    WHERE t.transaction_date BETWEEN :date_from AND :date_to";
            $result = $this->db->fetchOne($sql, ['date_from' => $dateFrom, 'date_to' => $dateTo]);
            return $result ?: ['total_income' => 0, 'total_expense' => 0, 'total_count' => 0];
        } catch (Exception $e) {
            return ['total_income' => 0, 'total_expense' => 0, 'total_count' => 0];
        }
    }
    
    private function getTransactionsByCategory(string $dateFrom, string $dateTo): array {
        try {
            $sql = "SELECT c.name, c.type, SUM(t.amount) as total
                    FROM financial_transactions t
                    JOIN financial_categories c ON t.category_id = c.id
                    WHERE t.transaction_date BETWEEN :date_from AND :date_to
                    GROUP BY c.id, c.name, c.type
                    ORDER BY c.type, total DESC";
            return $this->db->fetchAll($sql, ['date_from' => $dateFrom, 'date_to' => $dateTo]);
        } catch (Exception $e) {
            return [];
        }
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
