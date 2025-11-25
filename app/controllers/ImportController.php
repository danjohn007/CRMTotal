<?php
/**
 * Import Controller
 * Handles Excel imports for companies
 */
class ImportController extends Controller {
    
    private Contact $contactModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->contactModel = new Contact();
    }
    
    public function index(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial']);
        
        $this->view('import/index', [
            'pageTitle' => 'Importar Empresas',
            'currentPage' => 'importar',
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function process(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('importar');
        }
        
        if (!$this->validateCsrf()) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido.';
            $this->redirect('importar');
        }
        
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'Error al subir el archivo.';
            $this->redirect('importar');
        }
        
        $file = $_FILES['excel_file'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, ['csv', 'xls', 'xlsx'])) {
            $_SESSION['flash_error'] = 'Formato de archivo no válido. Use CSV, XLS o XLSX.';
            $this->redirect('importar');
        }
        
        try {
            // Process file
            if ($extension === 'csv') {
                $data = $this->parseCSV($file['tmp_name']);
            } else {
                // For XLS/XLSX, we'll convert to CSV first (simple approach)
                // In production, you'd use a library like PhpSpreadsheet
                $_SESSION['flash_error'] = 'Para archivos XLS/XLSX, por favor convierta a CSV primero.';
                $this->redirect('importar');
            }
            
            if (empty($data)) {
                $_SESSION['flash_error'] = 'El archivo está vacío o no tiene el formato correcto.';
                $this->redirect('importar');
            }
            
            // Get contact type
            $contactType = $this->sanitize($this->getInput('contact_type', 'prospecto'));
            
            // Preview mode or import
            if ($this->getInput('preview') === '1') {
                $this->view('import/preview', [
                    'pageTitle' => 'Vista Previa de Importación',
                    'currentPage' => 'importar',
                    'data' => array_slice($data, 0, 10),
                    'total' => count($data),
                    'contactType' => $contactType,
                    'csrf_token' => $this->csrfToken()
                ]);
            } else {
                $result = $this->importData($data, $contactType);
                $_SESSION['flash_success'] = "Importación completada: {$result['imported']} registros importados, {$result['errors']} errores.";
                $this->redirect('importar');
            }
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al procesar el archivo: ' . $e->getMessage();
            $this->redirect('importar');
        }
    }
    
    public function template(): void {
        $this->requireAuth();
        
        // Generate CSV template
        $headers = [
            'rfc', 'business_name', 'commercial_name', 'owner_name', 
            'corporate_email', 'phone', 'whatsapp', 'industry',
            'commercial_address', 'city', 'state', 'postal_code', 'website'
        ];
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=plantilla_importacion.csv');
        
        $output = fopen('php://output', 'w');
        
        // BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, $headers);
        
        // Sample row
        fputcsv($output, [
            'ABC123456789', 'Empresa Ejemplo SA de CV', 'EjemploMX', 'Juan Pérez',
            'contacto@ejemplo.com', '442 123 4567', '4421234567', 'Comercio',
            'Calle Principal #123', 'Santiago de Querétaro', 'Querétaro', '76000', 'www.ejemplo.com'
        ]);
        
        fclose($output);
        exit;
    }
    
    private function parseCSV(string $filepath): array {
        $data = [];
        $handle = fopen($filepath, 'r');
        
        if (!$handle) {
            throw new Exception('No se pudo abrir el archivo.');
        }
        
        // Read headers
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new Exception('El archivo no tiene encabezados.');
        }
        
        // Normalize headers
        $headers = array_map(function($h) {
            return strtolower(trim(str_replace([' ', '-'], '_', $h)));
        }, $headers);
        
        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }
        
        fclose($handle);
        return $data;
    }
    
    private function importData(array $data, string $contactType): array {
        $imported = 0;
        $errors = 0;
        
        $fieldMapping = [
            'rfc' => 'rfc',
            'business_name' => 'business_name',
            'razon_social' => 'business_name',
            'nombre_comercial' => 'commercial_name',
            'commercial_name' => 'commercial_name',
            'propietario' => 'owner_name',
            'owner_name' => 'owner_name',
            'email' => 'corporate_email',
            'corporate_email' => 'corporate_email',
            'correo' => 'corporate_email',
            'telefono' => 'phone',
            'phone' => 'phone',
            'whatsapp' => 'whatsapp',
            'giro' => 'industry',
            'industry' => 'industry',
            'direccion' => 'commercial_address',
            'commercial_address' => 'commercial_address',
            'ciudad' => 'city',
            'city' => 'city',
            'estado' => 'state',
            'state' => 'state',
            'codigo_postal' => 'postal_code',
            'postal_code' => 'postal_code',
            'cp' => 'postal_code',
            'website' => 'website',
            'sitio_web' => 'website'
        ];
        
        foreach ($data as $row) {
            try {
                $contactData = ['contact_type' => $contactType];
                
                foreach ($row as $key => $value) {
                    $normalizedKey = strtolower(trim($key));
                    if (isset($fieldMapping[$normalizedKey]) && !empty($value)) {
                        $contactData[$fieldMapping[$normalizedKey]] = $this->sanitize(trim($value));
                    }
                }
                
                // Require at least a business name
                if (empty($contactData['business_name'])) {
                    $errors++;
                    continue;
                }
                
                // Check for duplicates by RFC or email
                if (!empty($contactData['rfc'])) {
                    $existing = $this->contactModel->findByRfc($contactData['rfc']);
                    if ($existing) {
                        $errors++;
                        continue;
                    }
                }
                
                // Set defaults
                $contactData['city'] = $contactData['city'] ?? 'Santiago de Querétaro';
                $contactData['state'] = $contactData['state'] ?? 'Querétaro';
                $contactData['assigned_affiliate_id'] = $_SESSION['user_id'];
                $contactData['source_channel'] = 'alta_directa';
                
                $this->contactModel->create($contactData);
                $imported++;
                
            } catch (Exception $e) {
                $errors++;
            }
        }
        
        // Log the import action
        $this->logAudit('import_companies', 'contacts', 0, [
            'imported' => $imported,
            'errors' => $errors,
            'type' => $contactType
        ]);
        
        return ['imported' => $imported, 'errors' => $errors];
    }
    
    private function logAudit(string $action, string $table, int $recordId, array $data = []): void {
        $sql = "INSERT INTO audit_log (user_id, action, table_name, record_id, new_values, ip_address, created_at)
                VALUES (:user_id, :action, :table_name, :record_id, :new_values, :ip_address, NOW())";
        $this->db->query($sql, [
            'user_id' => $_SESSION['user_id'],
            'action' => $action,
            'table_name' => $table,
            'record_id' => $recordId,
            'new_values' => json_encode($data),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }
}
