<?php
/**
 * Requirements Controller
 * Manages commercial requirements
 */
class RequirementsController extends Controller {
    
    private CommercialRequirement $requirementModel;
    private Contact $contactModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->requirementModel = new CommercialRequirement();
        $this->contactModel = new Contact();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $status = $this->getInput('status', '');
        $requirements = $this->requirementModel->getAll($status ?: null);
        $stats = $this->requirementModel->getStats();
        $categoryStats = $this->requirementModel->getByCategory();
        
        $this->view('requirements/index', [
            'pageTitle' => 'Requerimientos Comerciales',
            'currentPage' => 'requerimientos',
            'requirements' => $requirements,
            'stats' => $stats,
            'categoryStats' => $categoryStats,
            'status' => $status
        ]);
    }
    
    public function create(): void {
        $this->requireAuth();
        
        $error = null;
        $contacts = $this->contactModel->getAffiliates();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = [
                    'title' => $this->sanitize($this->getInput('title', '')),
                    'description' => $this->sanitize($this->getInput('description', '')),
                    'contact_id' => (int) $this->getInput('contact_id') ?: null,
                    'user_id' => $_SESSION['user_id'],
                    'priority' => $this->sanitize($this->getInput('priority', 'medium')),
                    'status' => 'pending',
                    'due_date' => $this->sanitize($this->getInput('due_date', '')),
                    'budget' => (float) $this->getInput('budget', 0),
                    'category' => $this->sanitize($this->getInput('category', '')),
                    'notes' => $this->sanitize($this->getInput('notes', ''))
                ];
                
                try {
                    $id = $this->requirementModel->create($data);
                    $_SESSION['flash_success'] = 'Requerimiento creado exitosamente.';
                    $this->redirect('requerimientos/' . $id);
                } catch (Exception $e) {
                    $error = 'Error al crear el requerimiento: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('requirements/create', [
            'pageTitle' => 'Nuevo Requerimiento',
            'currentPage' => 'requerimientos',
            'contacts' => $contacts,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function show(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $requirement = $this->requirementModel->find($id);
        
        if (!$requirement) {
            $_SESSION['flash_error'] = 'Requerimiento no encontrado.';
            $this->redirect('requerimientos');
        }
        
        // Get related contact if exists
        $contact = null;
        if ($requirement['contact_id']) {
            $contact = $this->contactModel->find($requirement['contact_id']);
        }
        
        // Get user who created it
        $userModel = new User();
        $user = $userModel->find($requirement['user_id']);
        
        $this->view('requirements/show', [
            'pageTitle' => $requirement['title'],
            'currentPage' => 'requerimientos',
            'requirement' => $requirement,
            'contact' => $contact,
            'user' => $user,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function edit(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $requirement = $this->requirementModel->find($id);
        
        if (!$requirement) {
            $_SESSION['flash_error'] = 'Requerimiento no encontrado.';
            $this->redirect('requerimientos');
        }
        
        $error = null;
        $contacts = $this->contactModel->getAffiliates();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = [
                    'title' => $this->sanitize($this->getInput('title', '')),
                    'description' => $this->sanitize($this->getInput('description', '')),
                    'contact_id' => (int) $this->getInput('contact_id') ?: null,
                    'priority' => $this->sanitize($this->getInput('priority', 'medium')),
                    'status' => $this->sanitize($this->getInput('status', 'pending')),
                    'due_date' => $this->sanitize($this->getInput('due_date', '')),
                    'budget' => (float) $this->getInput('budget', 0),
                    'category' => $this->sanitize($this->getInput('category', '')),
                    'notes' => $this->sanitize($this->getInput('notes', ''))
                ];
                
                try {
                    $this->requirementModel->update($id, $data);
                    $_SESSION['flash_success'] = 'Requerimiento actualizado.';
                    $this->redirect('requerimientos/' . $id);
                } catch (Exception $e) {
                    $error = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('requirements/edit', [
            'pageTitle' => 'Editar Requerimiento',
            'currentPage' => 'requerimientos',
            'requirement' => $requirement,
            'contacts' => $contacts,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function updateStatus(): void {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->validateCsrf()) {
            $_SESSION['flash_error'] = 'Acción no válida.';
            $this->redirect('requerimientos');
        }
        
        $id = (int) $this->getInput('id');
        $status = $this->sanitize($this->getInput('status', ''));
        
        $validStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            $_SESSION['flash_error'] = 'Estado no válido.';
            $this->redirect('requerimientos/' . $id);
        }
        
        try {
            $this->requirementModel->update($id, ['status' => $status]);
            $_SESSION['flash_success'] = 'Estado actualizado.';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al actualizar el estado.';
        }
        
        $this->redirect('requerimientos/' . $id);
    }
    
    public function myRequirements(): void {
        $this->requireAuth();
        
        $status = $this->getInput('status', '');
        $requirements = $this->requirementModel->getByUser($_SESSION['user_id'], $status ?: null);
        
        $this->view('requirements/my_requirements', [
            'pageTitle' => 'Mis Requerimientos',
            'currentPage' => 'requerimientos',
            'requirements' => $requirements,
            'status' => $status
        ]);
    }
}
