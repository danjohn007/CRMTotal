<?php
/**
 * Agenda Controller
 * Manages activities and calendar
 */
class AgendaController extends Controller {
    
    private Activity $activityModel;
    private Contact $contactModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->activityModel = new Activity();
        $this->contactModel = new Contact();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        
        $todayActivities = $this->activityModel->getToday($userId);
        $pendingActivities = $this->activityModel->getPending($userId);
        $overdueActivities = $this->activityModel->getOverdue($userId);
        $stats = $this->activityModel->getStats($userId);
        $typeStats = $this->activityModel->getActivityTypeStats($userId);
        
        $this->view('agenda/index', [
            'pageTitle' => 'Agenda',
            'currentPage' => 'agenda',
            'todayActivities' => $todayActivities,
            'pendingActivities' => $pendingActivities,
            'overdueActivities' => $overdueActivities,
            'stats' => $stats,
            'typeStats' => $typeStats,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities()
        ]);
    }
    
    public function create(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $error = null;
        
        // Get contacts for dropdown
        $contacts = $this->contactModel->getAffiliates();
        $prospects = $this->contactModel->getProspects($userId);
        $allContacts = array_merge($contacts, $prospects);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = $this->getFormData();
                $data['user_id'] = $userId;
                $data['status'] = 'pendiente';
                
                try {
                    $id = $this->activityModel->create($data);
                    $_SESSION['flash_success'] = 'Actividad creada exitosamente.';
                    $this->redirect('agenda');
                } catch (Exception $e) {
                    $error = 'Error al crear la actividad: ' . $e->getMessage();
                }
            }
        }
        
        // Pre-fill contact if passed
        $prefilledContactId = $this->getInput('contact_id');
        
        $this->view('agenda/create', [
            'pageTitle' => 'Nueva Actividad',
            'currentPage' => 'agenda',
            'contacts' => $allContacts,
            'prefilledContactId' => $prefilledContactId,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities(),
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function show(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $activity = $this->activityModel->find($id);
        
        if (!$activity) {
            $_SESSION['flash_error'] = 'Actividad no encontrada.';
            $this->redirect('agenda');
        }
        
        // Get related contact
        $contact = null;
        if ($activity['contact_id']) {
            $contact = $this->contactModel->find($activity['contact_id']);
        }
        
        $this->view('agenda/show', [
            'pageTitle' => 'Detalle de Actividad',
            'currentPage' => 'agenda',
            'activity' => $activity,
            'contact' => $contact,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities()
        ]);
    }
    
    public function edit(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $activity = $this->activityModel->find($id);
        
        if (!$activity) {
            $_SESSION['flash_error'] = 'Actividad no encontrada.';
            $this->redirect('agenda');
        }
        
        $error = null;
        $userId = $_SESSION['user_id'];
        
        // Get contacts for dropdown
        $contacts = $this->contactModel->getAffiliates();
        $prospects = $this->contactModel->getProspects($userId);
        $allContacts = array_merge($contacts, $prospects);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = $this->getFormData();
                
                // Handle completion
                if ($this->getInput('mark_complete')) {
                    $data['status'] = 'completada';
                    $data['completed_date'] = date('Y-m-d H:i:s');
                }
                
                try {
                    $this->activityModel->update($id, $data);
                    $_SESSION['flash_success'] = 'Actividad actualizada exitosamente.';
                    $this->redirect('agenda');
                } catch (Exception $e) {
                    $error = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('agenda/edit', [
            'pageTitle' => 'Editar Actividad',
            'currentPage' => 'agenda',
            'activity' => $activity,
            'contacts' => $allContacts,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities(),
            'statuses' => $this->getStatuses(),
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function apiEvents(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $start = $this->getInput('start', date('Y-m-01'));
        $end = $this->getInput('end', date('Y-m-t'));
        
        $activities = $this->activityModel->getForCalendar($userId, $start, $end);
        
        // Format for FullCalendar
        $events = array_map(function($activity) {
            $colors = [
                'llamada' => '#3b82f6',
                'whatsapp' => '#10b981',
                'email' => '#6366f1',
                'visita' => '#f59e0b',
                'reunion' => '#8b5cf6',
                'seguimiento' => '#ec4899',
                'otro' => '#6b7280'
            ];
            
            $priorityBorder = [
                'urgente' => '#ef4444',
                'alta' => '#f97316',
                'media' => '#3b82f6',
                'baja' => '#6b7280'
            ];
            
            return [
                'id' => $activity['id'],
                'title' => $activity['title'],
                'start' => $activity['start'],
                'backgroundColor' => $colors[$activity['activity_type']] ?? '#6b7280',
                'borderColor' => $priorityBorder[$activity['priority']] ?? '#3b82f6',
                'extendedProps' => [
                    'type' => $activity['activity_type'],
                    'status' => $activity['status'],
                    'contact' => $activity['business_name'] ?? $activity['commercial_name'] ?? ''
                ]
            ];
        }, $activities);
        
        $this->json($events);
    }
    
    private function getFormData(): array {
        return [
            'contact_id' => $this->getInput('contact_id') ? (int) $this->getInput('contact_id') : null,
            'activity_type' => $this->sanitize($this->getInput('activity_type', 'llamada')),
            'title' => $this->sanitize($this->getInput('title', '')),
            'description' => $this->sanitize($this->getInput('description', '')),
            'scheduled_date' => $this->getInput('scheduled_date', ''),
            'priority' => $this->sanitize($this->getInput('priority', 'media')),
            'result' => $this->sanitize($this->getInput('result', '')),
            'next_action' => $this->sanitize($this->getInput('next_action', '')),
            'next_action_date' => $this->getInput('next_action_date') ?: null
        ];
    }
    
    private function getActivityTypes(): array {
        return [
            'llamada' => 'Llamada Telefónica',
            'whatsapp' => 'WhatsApp',
            'email' => 'Correo Electrónico',
            'visita' => 'Visita',
            'reunion' => 'Reunión',
            'seguimiento' => 'Seguimiento',
            'otro' => 'Otro'
        ];
    }
    
    private function getPriorities(): array {
        return [
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'urgente' => 'Urgente'
        ];
    }
    
    private function getStatuses(): array {
        return [
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En Progreso',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada'
        ];
    }
}
