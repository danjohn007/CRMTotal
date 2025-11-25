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
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por el servidor.',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario.',
                UPLOAD_ERR_PARTIAL => 'El archivo solo se subió parcialmente.',
                UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo.',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor.',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco.',
                UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo.'
            ];
            $errorCode = $_FILES['excel_file']['error'] ?? UPLOAD_ERR_NO_FILE;
            $errorMessage = $errorMessages[$errorCode] ?? 'Error al subir el archivo.';
            $_SESSION['flash_error'] = $errorMessage;
            $this->redirect('importar');
        }
        
        $file = $_FILES['excel_file'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, ['csv'])) {
            $_SESSION['flash_error'] = 'Solo archivos CSV están soportados. Para XLS/XLSX, exporte primero a CSV desde Excel.';
            $this->redirect('importar');
        }
        
        try {
            // Process CSV file
            $data = $this->parseCSV($file['tmp_name']);
            
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
        
        // Generate CSV template matching the CCQ format
        $headers = [
            'EMPRESA / RAZON SOCIAL',
            'RFC',
            'EMAIL',
            'TELÉFONO',
            'REPRESENTANTE',
            'DIRECCIÓN COMERCIAL',
            'DIRECCIÓN FISCAL',
            'SECTOR',
            'CATEGORÍA',
            'MEMBRESÍA',
            'TIPO DE AFILIACIÓN',
            'VENDEDOR',
            'FECHA DE RENOVACIÓN',
            'No. DE RECIBO',
            'No. DE FACTURA',
            'ENGOMADO'
        ];
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=plantilla_importacion_Empresas_CCQ.csv');
        
        $output = fopen('php://output', 'w');
        
        // BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, $headers);
        
        // Sample row
        fputcsv($output, [
            'Empresa Ejemplo SA de CV',
            'ABC123456789',
            'contacto@ejemplo.com',
            '442 123 4567',
            'Juan Pérez García',
            'Calle Principal #123, Centro, Querétaro',
            'Calle Principal #123, Centro, Querétaro',
            'COMERCIO',
            'Tienda de Ejemplo',
            'PYME',
            'MEMBRESIA',
            'VENDEDOR1',
            '2026-01-01',
            '',
            'AF-0001',
            '12345'
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
        
        // Field mapping for CCQ CSV template format
        $fieldMapping = [
            // CCQ Template Fields (Spanish headers)
            'empresa_/_razon_social' => 'business_name',
            'empresa/razon_social' => 'business_name',
            'empresa___razon_social' => 'business_name',
            'rfc' => 'rfc',
            'email' => 'corporate_email',
            'teléfono' => 'phone',
            'telefono' => 'phone',
            'representante' => 'owner_name',
            'dirección_comercial' => 'commercial_address',
            'direccion_comercial' => 'commercial_address',
            'dirección_fiscal' => 'fiscal_address',
            'direccion_fiscal' => 'fiscal_address',
            'sector' => 'industry',
            'categoría' => 'commercial_name',
            'categoria' => 'commercial_name',
            'membresía' => 'membership_code',
            'membresia' => 'membership_code',
            'tipo_de_afiliación' => 'affiliation_type',
            'tipo_de_afiliacion' => 'affiliation_type',
            'vendedor' => 'seller_code',
            'fecha_de_renovación' => 'renewal_date',
            'fecha_de_renovacion' => 'renewal_date',
            'no._de_recibo' => 'receipt_number',
            'no_de_recibo' => 'receipt_number',
            'no._de_factura' => 'invoice_number',
            'no_de_factura' => 'invoice_number',
            'engomado' => 'sticker_number',
            
            // Legacy/Alternative Field Names
            'business_name' => 'business_name',
            'razon_social' => 'business_name',
            'nombre_comercial' => 'commercial_name',
            'commercial_name' => 'commercial_name',
            'propietario' => 'owner_name',
            'owner_name' => 'owner_name',
            'corporate_email' => 'corporate_email',
            'correo' => 'corporate_email',
            'phone' => 'phone',
            'whatsapp' => 'whatsapp',
            'giro' => 'industry',
            'industry' => 'industry',
            'direccion' => 'commercial_address',
            'commercial_address' => 'commercial_address',
            'fiscal_address' => 'fiscal_address',
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
        
        // Get users for seller code mapping
        $userModel = new User();
        $users = $userModel->getAffiliators();
        $usersByCode = [];
        foreach ($users as $user) {
            // Map by first initial + last name initial or full name parts
            $nameParts = explode(' ', strtoupper($user['name']));
            if (count($nameParts) >= 2) {
                $code = substr($nameParts[0], 0, 1) . $nameParts[count($nameParts) - 1];
                $usersByCode[$code] = $user['id'];
            }
            // Also map by full name
            $usersByCode[strtoupper(str_replace(' ', '', $user['name']))] = $user['id'];
        }
        
        // Get membership types
        $membershipModel = new MembershipType();
        $memberships = $membershipModel->getActive();
        $membershipsByCode = [];
        foreach ($memberships as $membership) {
            $membershipsByCode[strtoupper($membership['code'])] = $membership;
            $membershipsByCode[strtoupper($membership['name'])] = $membership;
        }
        
        foreach ($data as $row) {
            try {
                $contactData = ['contact_type' => $contactType];
                $affiliationData = [];
                
                foreach ($row as $key => $value) {
                    $normalizedKey = strtolower(trim(str_replace([' ', '-', '.'], '_', $key)));
                    // Remove multiple underscores
                    $normalizedKey = preg_replace('/_+/', '_', $normalizedKey);
                    
                    if (isset($fieldMapping[$normalizedKey]) && !empty(trim($value))) {
                        $mappedField = $fieldMapping[$normalizedKey];
                        $sanitizedValue = $this->sanitize(trim($value));
                        
                        // Handle special fields that go to affiliation
                        if (in_array($mappedField, ['membership_code', 'affiliation_type', 'seller_code', 'renewal_date', 'receipt_number', 'invoice_number', 'sticker_number'])) {
                            $affiliationData[$mappedField] = $sanitizedValue;
                        } else {
                            $contactData[$mappedField] = $sanitizedValue;
                        }
                    }
                }
                
                // Require at least a business name
                if (empty($contactData['business_name'])) {
                    $errors++;
                    continue;
                }
                
                // Check for duplicates by RFC
                if (!empty($contactData['rfc'])) {
                    $existing = $this->contactModel->findByRfc($contactData['rfc']);
                    if ($existing) {
                        // Update existing contact instead of skipping
                        $this->contactModel->update($existing['id'], $contactData);
                        $imported++;
                        continue;
                    }
                }
                
                // Set defaults
                $contactData['city'] = $contactData['city'] ?? 'Santiago de Querétaro';
                $contactData['state'] = $contactData['state'] ?? 'Querétaro';
                
                // Map seller code to user ID
                if (!empty($affiliationData['seller_code'])) {
                    $sellerCode = strtoupper(str_replace(' ', '', $affiliationData['seller_code']));
                    if (isset($usersByCode[$sellerCode])) {
                        $contactData['assigned_affiliate_id'] = $usersByCode[$sellerCode];
                    }
                }
                
                if (empty($contactData['assigned_affiliate_id'])) {
                    $contactData['assigned_affiliate_id'] = $_SESSION['user_id'];
                }
                
                $contactData['source_channel'] = 'alta_directa';
                
                // Create contact
                $contactId = $this->contactModel->create($contactData);
                
                // If contact type is afiliado and we have affiliation data, create affiliation
                if ($contactType === 'afiliado' && !empty($affiliationData['membership_code'])) {
                    $membershipCode = strtoupper($affiliationData['membership_code']);
                    if (isset($membershipsByCode[$membershipCode])) {
                        $membership = $membershipsByCode[$membershipCode];
                        
                        $affiliationModel = new Affiliation();
                        $affiliationModel->create([
                            'contact_id' => $contactId,
                            'membership_type_id' => $membership['id'],
                            'affiliate_user_id' => $contactData['assigned_affiliate_id'],
                            'affiliation_date' => date('Y-m-d'),
                            'expiration_date' => !empty($affiliationData['renewal_date']) ? $affiliationData['renewal_date'] : date('Y-m-d', strtotime('+' . ($membership['duration_days'] ?? 360) . ' days')),
                            'status' => 'active',
                            'payment_status' => 'paid',
                            'amount' => $membership['price'] ?? 0,
                            'invoice_number' => $affiliationData['invoice_number'] ?? null,
                            'notes' => !empty($affiliationData['sticker_number']) ? 'Engomado: ' . $affiliationData['sticker_number'] : null
                        ]);
                    }
                }
                
                $this->contactModel->updateCompletion($contactId);
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
