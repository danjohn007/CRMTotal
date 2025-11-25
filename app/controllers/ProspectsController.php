<?php
/**
 * Prospects Controller
 * Manages prospect operations (6 channels)
 */
class ProspectsController extends Controller {
    
    private Contact $contactModel;
    private User $userModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->contactModel = new Contact();
        $this->userModel = new User();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        // Get prospects based on role
        if (in_array($role, ['superadmin', 'direccion', 'jefe_comercial'])) {
            $prospects = $this->contactModel->getProspects();
        } else {
            $prospects = $this->contactModel->getProspects($userId);
        }
        
        // Channel statistics
        $channelStats = $this->contactModel->getStatsByChannel();
        $affiliators = $this->userModel->getAffiliators();
        
        $this->view('prospects/index', [
            'pageTitle' => 'Prospectos',
            'currentPage' => 'prospectos',
            'prospects' => $prospects,
            'channelStats' => $channelStats,
            'affiliators' => $affiliators,
            'channels' => $this->getChannels(),
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function create(): void {
        $this->requireAuth();
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = $this->getFormData();
                $data['contact_type'] = 'prospecto';
                $data['assigned_affiliate_id'] = $_SESSION['user_id'];
                
                // Calculate initial completion
                $data['profile_completion'] = 25;
                $data['completion_stage'] = 'A';
                
                try {
                    $id = $this->contactModel->create($data);
                    $this->contactModel->updateCompletion($id);
                    $_SESSION['flash_success'] = 'Prospecto creado exitosamente.';
                    $this->redirect('prospectos/' . $id);
                } catch (Exception $e) {
                    $error = 'Error al crear el prospecto: ' . $e->getMessage();
                }
            }
        }
        
        $affiliators = $this->userModel->getAffiliators();
        
        $this->view('prospects/create', [
            'pageTitle' => 'Nuevo Prospecto',
            'currentPage' => 'prospectos',
            'affiliators' => $affiliators,
            'channels' => $this->getChannels(),
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function show(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $prospect = $this->contactModel->find($id);
        
        if (!$prospect || $prospect['contact_type'] !== 'prospecto') {
            $_SESSION['flash_error'] = 'Prospecto no encontrado.';
            $this->redirect('prospectos');
        }
        
        // Get activities for this prospect
        $activityModel = new Activity();
        $activities = $activityModel->getByContact($id);
        
        $this->view('prospects/show', [
            'pageTitle' => 'Detalle de Prospecto',
            'currentPage' => 'prospectos',
            'prospect' => $prospect,
            'activities' => $activities,
            'channels' => $this->getChannels()
        ]);
    }
    
    public function edit(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $prospect = $this->contactModel->find($id);
        
        if (!$prospect) {
            $_SESSION['flash_error'] = 'Prospecto no encontrado.';
            $this->redirect('prospectos');
        }
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = $this->getFormData();
                
                try {
                    $this->contactModel->update($id, $data);
                    $this->contactModel->updateCompletion($id);
                    $_SESSION['flash_success'] = 'Prospecto actualizado exitosamente.';
                    $this->redirect('prospectos/' . $id);
                } catch (Exception $e) {
                    $error = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }
        
        $affiliators = $this->userModel->getAffiliators();
        
        $this->view('prospects/edit', [
            'pageTitle' => 'Editar Prospecto',
            'currentPage' => 'prospectos',
            'prospect' => $prospect,
            'affiliators' => $affiliators,
            'channels' => $this->getChannels(),
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function delete(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->contactModel->delete($id);
            $_SESSION['flash_success'] = 'Prospecto eliminado.';
        }
        
        $this->redirect('prospectos');
    }
    
    public function byChannel(): void {
        $this->requireAuth();
        
        $channel = $this->params['channel'] ?? '';
        $prospects = $this->contactModel->getByChannel($channel);
        
        // Filter to only prospects
        $prospects = array_filter($prospects, fn($p) => $p['contact_type'] === 'prospecto');
        
        $this->view('prospects/by_channel', [
            'pageTitle' => 'Prospectos por Canal',
            'currentPage' => 'prospectos',
            'prospects' => $prospects,
            'channel' => $channel,
            'channels' => $this->getChannels()
        ]);
    }
    
    private function getFormData(): array {
        return [
            'rfc' => $this->sanitize($this->getInput('rfc', '')),
            'whatsapp' => $this->sanitize($this->getInput('whatsapp', '')),
            'business_name' => $this->sanitize($this->getInput('business_name', '')),
            'commercial_name' => $this->sanitize($this->getInput('commercial_name', '')),
            'owner_name' => $this->sanitize($this->getInput('owner_name', '')),
            'legal_representative' => $this->sanitize($this->getInput('legal_representative', '')),
            'corporate_email' => $this->sanitize($this->getInput('corporate_email', '')),
            'phone' => $this->sanitize($this->getInput('phone', '')),
            'industry' => $this->sanitize($this->getInput('industry', '')),
            'commercial_address' => $this->sanitize($this->getInput('commercial_address', '')),
            'fiscal_address' => $this->sanitize($this->getInput('fiscal_address', '')),
            'city' => $this->sanitize($this->getInput('city', 'Santiago de Querétaro')),
            'state' => $this->sanitize($this->getInput('state', 'Querétaro')),
            'postal_code' => $this->sanitize($this->getInput('postal_code', '')),
            'website' => $this->sanitize($this->getInput('website', '')),
            'source_channel' => $this->sanitize($this->getInput('source_channel', 'alta_directa')),
            'assigned_affiliate_id' => (int) $this->getInput('assigned_affiliate_id', $_SESSION['user_id']),
            'notes' => $this->sanitize($this->getInput('notes', ''))
        ];
    }
    
    private function getChannels(): array {
        return [
            'chatbot' => 'Chatbot',
            'alta_directa' => 'Alta Directa',
            'evento_gratuito' => 'Evento Gratuito',
            'evento_pagado' => 'Evento Pagado',
            'buscador' => 'Buscador Inteligente',
            'jefatura_comercial' => 'Jefatura Comercial'
        ];
    }
}
