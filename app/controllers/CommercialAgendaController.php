<?php
/**
 * Commercial Agenda Controller
 * Unified section combining: Agenda, Notifications, and Commercial Requirements
 * "Agenda y Acciones Comerciales"
 */
class CommercialAgendaController extends Controller {
    
    private Activity $activityModel;
    private Contact $contactModel;
    private Notification $notificationModel;
    private CommercialRequirement $requirementModel;
    private User $userModel;
    
    // Work hours: Monday-Friday 9am-6pm
    private const WORK_START_HOUR = 9;
    private const WORK_END_HOUR = 18;
    private const WORK_DAYS = [1, 2, 3, 4, 5]; // Monday = 1, Friday = 5
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->activityModel = new Activity();
        $this->contactModel = new Contact();
        $this->notificationModel = new Notification();
        $this->requirementModel = new CommercialRequirement();
        $this->userModel = new User();
    }
    
    /**
     * Main index - Unified dashboard for commercial actions
     */
    public function index(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? '';
        
        // Log user activity (including off-hours tracking)
        $this->logUserActivity($userId, 'access_commercial_agenda');
        
        // Get today's date info
        $today = new DateTime();
        $currentHour = (int)$today->format('H');
        $currentDayOfWeek = (int)$today->format('N');
        $isWorkingHours = $this->isWorkingHours($currentHour, $currentDayOfWeek);
        
        // Get motivational message
        $motivationalMessage = $this->getMotivationalMessage($isWorkingHours, $currentHour);
        
        // ========== ACTIVITIES DATA (From Agenda) ==========
        $todayActivities = $this->activityModel->getToday($userId);
        $pendingActivities = $this->activityModel->getPending($userId);
        $overdueActivities = $this->activityModel->getOverdue($userId);
        $weekActivities = $this->activityModel->getUpcoming($userId, 'week');
        $monthActivities = $this->activityModel->getUpcoming($userId, 'month');
        $activityStats = $this->activityModel->getStats($userId);
        $typeStats = $this->activityModel->getActivityTypeStats($userId);
        
        // ========== NOTIFICATIONS DATA ==========
        $notifications = $this->notificationModel->getAll($userId, 50);
        $unreadNotifications = $this->notificationModel->getUnread($userId);
        $unreadCount = $this->notificationModel->countUnread($userId);
        
        // Group notifications by type for classification
        $notificationsByType = $this->groupNotificationsByType($notifications);
        
        // ========== REQUIREMENTS DATA ==========
        $requirements = $this->requirementModel->getByUser($userId);
        $pendingRequirements = $this->requirementModel->getByUser($userId, 'pending');
        $requirementStats = $this->requirementModel->getStats();
        
        // ========== PRIORITIZED PROSPECTS ==========
        // Priority 1: New prospects with RFC + WhatsApp that attended events
        // Priority 2: Follow-ups to existing prospects
        // Priority 3: Cross & Up selling opportunities
        $prioritizedProspects = $this->getPrioritizedProspects($userId);
        $crossSellingOpportunities = $this->getCrossSellingOpportunities($userId);
        $upsellingOpportunities = $this->getUpsellingOpportunities($userId);
        
        // ========== PERFORMANCE METRICS ==========
        $performanceMetrics = $this->getPerformanceMetrics($userId);
        
        // ========== OFF-HOURS ACTIVITY (For managers) ==========
        $offHoursActivity = [];
        if (in_array($userRole, ['jefe_comercial', 'direccion', 'superadmin', 'mesa_directiva', 'consejero'])) {
            $offHoursActivity = $this->getTeamOffHoursActivity();
        }
        
        $this->view('commercial_agenda/index', [
            'pageTitle' => 'Agenda y Acciones Comerciales',
            'currentPage' => 'agenda_comercial',
            
            // Time context
            'isWorkingHours' => $isWorkingHours,
            'currentHour' => $currentHour,
            'motivationalMessage' => $motivationalMessage,
            
            // Activities
            'todayActivities' => $todayActivities,
            'pendingActivities' => $pendingActivities,
            'overdueActivities' => $overdueActivities,
            'weekActivities' => $weekActivities,
            'monthActivities' => $monthActivities,
            'activityStats' => $activityStats,
            'typeStats' => $typeStats,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities(),
            
            // Notifications
            'notifications' => $notifications,
            'unreadNotifications' => $unreadNotifications,
            'unreadCount' => $unreadCount,
            'notificationsByType' => $notificationsByType,
            'notificationTypes' => $this->getNotificationTypes(),
            
            // Requirements
            'requirements' => $requirements,
            'pendingRequirements' => $pendingRequirements,
            'requirementStats' => $requirementStats,
            
            // Prospects & Opportunities
            'prioritizedProspects' => $prioritizedProspects,
            'crossSellingOpportunities' => $crossSellingOpportunities,
            'upsellingOpportunities' => $upsellingOpportunities,
            
            // Performance
            'performanceMetrics' => $performanceMetrics,
            
            // Off-hours (for managers)
            'offHoursActivity' => $offHoursActivity,
            'userRole' => $userRole,
            
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Today's view - Focus on daily actions
     */
    public function today(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        
        $this->logUserActivity($userId, 'view_today_actions');
        
        $todayActivities = $this->activityModel->getToday($userId);
        $overdueActivities = $this->activityModel->getOverdue($userId);
        $todayNotifications = $this->getTodayNotifications($userId);
        
        $this->view('commercial_agenda/today', [
            'pageTitle' => 'Acciones de Hoy',
            'currentPage' => 'agenda_comercial',
            'todayActivities' => $todayActivities,
            'overdueActivities' => $overdueActivities,
            'todayNotifications' => $todayNotifications,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities(),
            'motivationalMessage' => $this->getMotivationalMessage(true, (int)date('H')),
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Week view - Weekly planning
     */
    public function week(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        
        $this->logUserActivity($userId, 'view_week_actions');
        
        $weekActivities = $this->activityModel->getUpcoming($userId, 'week');
        $pendingActivities = $this->activityModel->getPending($userId);
        
        $this->view('commercial_agenda/week', [
            'pageTitle' => 'Acciones de la Semana',
            'currentPage' => 'agenda_comercial',
            'weekActivities' => $weekActivities,
            'pendingActivities' => $pendingActivities,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities(),
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Month view - Monthly planning
     */
    public function month(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        
        $this->logUserActivity($userId, 'view_month_actions');
        
        $monthActivities = $this->activityModel->getUpcoming($userId, 'month');
        $activityStats = $this->activityModel->getStats($userId);
        
        $this->view('commercial_agenda/month', [
            'pageTitle' => 'Acciones del Mes',
            'currentPage' => 'agenda_comercial',
            'monthActivities' => $monthActivities,
            'activityStats' => $activityStats,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities(),
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Create new activity
     */
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
                $data = $this->getActivityFormData();
                $data['user_id'] = $userId;
                $data['status'] = 'pendiente';
                
                try {
                    $this->logUserActivity($userId, 'create_activity');
                    $id = $this->activityModel->create($data);
                    $_SESSION['flash_success'] = 'Actividad creada exitosamente.';
                    $this->redirect('agenda-comercial');
                } catch (Exception $e) {
                    $error = 'Error al crear la actividad: ' . $e->getMessage();
                }
            }
        }
        
        // Pre-fill contact if passed
        $prefilledContactId = $this->getInput('contact_id');
        $prefilledType = $this->getInput('type');
        
        $this->view('commercial_agenda/create', [
            'pageTitle' => 'Nueva Actividad',
            'currentPage' => 'agenda_comercial',
            'contacts' => $allContacts,
            'prefilledContactId' => $prefilledContactId,
            'prefilledType' => $prefilledType,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities(),
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Edit activity
     */
    public function edit(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $activity = $this->activityModel->find($id);
        
        if (!$activity) {
            $_SESSION['flash_error'] = 'Actividad no encontrada.';
            $this->redirect('agenda-comercial');
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
                $data = $this->getActivityFormData();
                
                // Handle completion
                if ($this->getInput('mark_complete')) {
                    $data['status'] = 'completada';
                    $data['completed_date'] = date('Y-m-d H:i:s');
                    $this->logUserActivity($userId, 'complete_activity', $id);
                }
                
                try {
                    $this->activityModel->update($id, $data);
                    $_SESSION['flash_success'] = 'Actividad actualizada exitosamente.';
                    $this->redirect('agenda-comercial');
                } catch (Exception $e) {
                    $error = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('commercial_agenda/edit', [
            'pageTitle' => 'Editar Actividad',
            'currentPage' => 'agenda_comercial',
            'activity' => $activity,
            'contacts' => $allContacts,
            'activityTypes' => $this->getActivityTypes(),
            'priorities' => $this->getPriorities(),
            'statuses' => $this->getStatuses(),
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Quick action - Send WhatsApp invitation
     */
    public function sendWhatsapp(): void {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('agenda-comercial');
        }
        
        $userId = $_SESSION['user_id'];
        $contactId = (int) $this->getInput('contact_id');
        $message = $this->sanitize($this->getInput('message', ''));
        $actionType = $this->sanitize($this->getInput('action_type', 'invitation'));
        
        $contact = $this->contactModel->find($contactId);
        
        if (!$contact || empty($contact['whatsapp'])) {
            $_SESSION['flash_error'] = 'Contacto no encontrado o sin WhatsApp.';
            $this->redirect('agenda-comercial');
        }
        
        // Log the WhatsApp send action
        $this->logUserActivity($userId, 'send_whatsapp', $contactId, [
            'phone' => $contact['whatsapp'],
            'action_type' => $actionType
        ]);
        
        // Create activity record
        $this->activityModel->create([
            'user_id' => $userId,
            'contact_id' => $contactId,
            'activity_type' => 'whatsapp',
            'title' => 'Envío de WhatsApp - ' . ucfirst($actionType),
            'description' => $message,
            'scheduled_date' => date('Y-m-d H:i:s'),
            'completed_date' => date('Y-m-d H:i:s'),
            'status' => 'completada',
            'priority' => 'media'
        ]);
        
        // Generate WhatsApp URL
        // Note: Phone numbers should include country code. If not present, default to Mexico (52)
        $phone = preg_replace('/[^0-9]/', '', $contact['whatsapp']);
        // If phone number doesn't have country code (typically starts with country code and has 12+ digits)
        // For Mexico, local numbers are 10 digits. If it's just 10 digits, prepend 52
        if (strlen($phone) === 10) {
            $phone = '52' . $phone;
        }
        $encodedMessage = urlencode($message);
        $whatsappUrl = "https://wa.me/{$phone}?text={$encodedMessage}";
        
        $_SESSION['flash_success'] = 'Acción registrada. Redirigiendo a WhatsApp...';
        header('Location: ' . $whatsappUrl);
        exit;
    }
    
    /**
     * Quick action - Send Email invitation
     */
    public function sendEmail(): void {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('agenda-comercial');
        }
        
        $userId = $_SESSION['user_id'];
        $contactId = (int) $this->getInput('contact_id');
        $subject = $this->sanitize($this->getInput('subject', ''));
        $message = $this->sanitize($this->getInput('message', ''));
        $actionType = $this->sanitize($this->getInput('action_type', 'invitation'));
        
        $contact = $this->contactModel->find($contactId);
        
        if (!$contact || empty($contact['corporate_email'])) {
            $_SESSION['flash_error'] = 'Contacto no encontrado o sin email.';
            $this->redirect('agenda-comercial');
        }
        
        // Log the email send action
        $this->logUserActivity($userId, 'send_email', $contactId, [
            'email' => $contact['corporate_email'],
            'action_type' => $actionType,
            'subject' => $subject
        ]);
        
        // Create activity record
        $this->activityModel->create([
            'user_id' => $userId,
            'contact_id' => $contactId,
            'activity_type' => 'email',
            'title' => 'Envío de Email - ' . ucfirst($actionType),
            'description' => "Asunto: {$subject}\n\n{$message}",
            'scheduled_date' => date('Y-m-d H:i:s'),
            'completed_date' => date('Y-m-d H:i:s'),
            'status' => 'completada',
            'priority' => 'media'
        ]);
        
        // Generate mailto URL
        $mailtoUrl = "mailto:{$contact['corporate_email']}?subject=" . urlencode($subject) . "&body=" . urlencode($message);
        
        $_SESSION['flash_success'] = 'Acción registrada. Abriendo cliente de correo...';
        header('Location: ' . $mailtoUrl);
        exit;
    }
    
    /**
     * Mark notification as read
     */
    public function markNotificationRead(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        
        if ($id) {
            $this->notificationModel->markAsRead($id);
        }
        
        // If AJAX request, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->json(['success' => true]);
        }
        
        $this->redirect('agenda-comercial');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $this->notificationModel->markAllAsRead($userId);
        
        $_SESSION['flash_success'] = 'Todas las notificaciones marcadas como leídas.';
        $this->redirect('agenda-comercial');
    }
    
    /**
     * Notifications management view
     */
    public function notifications(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $filter = $this->getInput('filter', 'all');
        
        $notifications = $this->notificationModel->getAll($userId, 100);
        $unreadCount = $this->notificationModel->countUnread($userId);
        
        // Filter notifications by type if specified
        if ($filter !== 'all') {
            $notifications = array_filter($notifications, function($n) use ($filter) {
                return $n['type'] === $filter;
            });
        }
        
        // Group by date
        $groupedNotifications = [];
        foreach ($notifications as $notification) {
            $date = date('Y-m-d', strtotime($notification['created_at']));
            if (!isset($groupedNotifications[$date])) {
                $groupedNotifications[$date] = [];
            }
            $groupedNotifications[$date][] = $notification;
        }
        
        $this->view('commercial_agenda/notifications', [
            'pageTitle' => 'Gestión de Notificaciones',
            'currentPage' => 'agenda_comercial',
            'groupedNotifications' => $groupedNotifications,
            'unreadCount' => $unreadCount,
            'filter' => $filter,
            'notificationTypes' => $this->getNotificationTypes(),
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * API endpoint for calendar events
     */
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
    
    /**
     * Performance metrics view
     */
    public function metrics(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? '';
        
        $performanceMetrics = $this->getPerformanceMetrics($userId);
        $monthlyTrend = $this->getMonthlyTrend($userId);
        
        // For managers, get team metrics
        $teamMetrics = [];
        if (in_array($userRole, ['jefe_comercial', 'direccion', 'superadmin'])) {
            $teamMetrics = $this->getTeamMetrics();
        }
        
        $this->view('commercial_agenda/metrics', [
            'pageTitle' => 'Métricas de Desempeño',
            'currentPage' => 'agenda_comercial',
            'performanceMetrics' => $performanceMetrics,
            'monthlyTrend' => $monthlyTrend,
            'teamMetrics' => $teamMetrics,
            'userRole' => $userRole,
            'motivationalMessage' => $this->getMotivationalMessage(true, (int)date('H'))
        ]);
    }
    
    // ========== PRIVATE HELPER METHODS ==========
    
    /**
     * Check if current time is within working hours
     */
    private function isWorkingHours(int $hour, int $dayOfWeek): bool {
        return in_array($dayOfWeek, self::WORK_DAYS) && 
               $hour >= self::WORK_START_HOUR && 
               $hour < self::WORK_END_HOUR;
    }
    
    /**
     * Get motivational message based on time and context
     */
    private function getMotivationalMessage(bool $isWorkingHours, int $hour): array {
        if (!$isWorkingHours) {
            $messages = [
                [
                    'icon' => '🌟',
                    'title' => '¡Compromiso Excepcional!',
                    'message' => 'Tu dedicación fuera del horario laboral demuestra un compromiso extraordinario con nuestros objetivos.'
                ],
                [
                    'icon' => '🏆',
                    'title' => '¡Esfuerzo Reconocido!',
                    'message' => 'Trabajar fuera de horario muestra tu pasión. Tu esfuerzo no pasa desapercibido.'
                ],
                [
                    'icon' => '💪',
                    'title' => '¡Dedicación Ejemplar!',
                    'message' => 'Los grandes logros requieren dedicación extra. ¡Sigue adelante!'
                ]
            ];
            return $messages[array_rand($messages)];
        }
        
        if ($hour < 12) {
            $messages = [
                [
                    'icon' => '☀️',
                    'title' => '¡Buenos días!',
                    'message' => 'Comienza el día con energía. Cada llamada es una oportunidad de éxito.'
                ],
                [
                    'icon' => '🚀',
                    'title' => '¡A conquistar el día!',
                    'message' => 'Las mañanas productivas generan resultados extraordinarios.'
                ],
                [
                    'icon' => '💡',
                    'title' => '¡Momento de crear!',
                    'message' => 'Tu primer contacto del día puede ser el cierre más importante del mes.'
                ]
            ];
        } elseif ($hour < 15) {
            $messages = [
                [
                    'icon' => '⚡',
                    'title' => '¡Mantén el ritmo!',
                    'message' => 'El mediodía es perfecto para dar seguimiento a tus prospectos más calientes.'
                ],
                [
                    'icon' => '🎯',
                    'title' => '¡Enfócate en los objetivos!',
                    'message' => 'Cada acción cuenta. Estás más cerca de tu meta de lo que crees.'
                ]
            ];
        } else {
            $messages = [
                [
                    'icon' => '🌅',
                    'title' => '¡Cierra el día con fuerza!',
                    'message' => 'Las últimas horas son oro. Un seguimiento ahora puede marcar la diferencia.'
                ],
                [
                    'icon' => '✨',
                    'title' => '¡Sprint final!',
                    'message' => 'Aprovecha las últimas horas. Los mejores cierres ocurren al final del día.'
                ]
            ];
        }
        
        return $messages[array_rand($messages)];
    }
    
    /**
     * Log user activity for tracking purposes
     * Note: This method gracefully handles the case where user_activity_log table
     * doesn't exist yet (before running update_v2.7.0.sql migration)
     */
    private function logUserActivity(int $userId, string $action, ?int $relatedId = null, ?array $metadata = null): void {
        try {
            // First check if the table exists to avoid repeated failed queries
            static $tableExists = null;
            
            if ($tableExists === null) {
                try {
                    $checkSql = "SHOW TABLES LIKE 'user_activity_log'";
                    $result = $this->db->fetchAll($checkSql);
                    $tableExists = !empty($result);
                } catch (Exception $e) {
                    $tableExists = false;
                }
            }
            
            if (!$tableExists) {
                return; // Table doesn't exist yet, skip logging
            }
            
            $currentHour = (int)date('H');
            $currentDay = (int)date('N');
            $isOutsideHours = !$this->isWorkingHours($currentHour, $currentDay);
            
            $sql = "INSERT INTO user_activity_log (user_id, action, related_id, metadata, is_outside_hours, created_at) 
                    VALUES (:user_id, :action, :related_id, :metadata, :is_outside_hours, NOW())";
            
            $this->db->query($sql, [
                'user_id' => $userId,
                'action' => $action,
                'related_id' => $relatedId,
                'metadata' => $metadata ? json_encode($metadata) : null,
                'is_outside_hours' => $isOutsideHours ? 1 : 0
            ]);
        } catch (Exception $e) {
            // Log the error but don't break the user experience
            // This could happen during table creation or other edge cases
            error_log("CommercialAgendaController: Unable to log activity - " . $e->getMessage());
        }
    }
    
    /**
     * Get prioritized prospects for affiliators
     * Priority: 1) New with RFC+WhatsApp, 2) Follow-ups, 3) Cross/Upselling
     */
    private function getPrioritizedProspects(int $userId): array {
        // Priority 1: New prospects with RFC AND WhatsApp (from events or chatbot)
        $sql = "SELECT c.*, 
                       DATEDIFF(CURDATE(), DATE(c.created_at)) as days_since_creation,
                       'new_prospect' as priority_type,
                       1 as priority_order
                FROM contacts c
                WHERE c.contact_type = 'prospecto'
                AND c.assigned_affiliate_id = :user_id
                AND c.rfc IS NOT NULL AND c.rfc != ''
                AND c.whatsapp IS NOT NULL AND c.whatsapp != ''
                AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                
                UNION ALL
                
                -- Priority 2: Prospects needing follow-up (no recent activity)
                SELECT c.*, 
                       DATEDIFF(CURDATE(), DATE(COALESCE(
                           (SELECT MAX(a.scheduled_date) FROM activities a WHERE a.contact_id = c.id), 
                           c.created_at
                       ))) as days_since_creation,
                       'follow_up' as priority_type,
                       2 as priority_order
                FROM contacts c
                WHERE c.contact_type = 'prospecto'
                AND c.assigned_affiliate_id = :user_id2
                AND NOT EXISTS (
                    SELECT 1 FROM activities a 
                    WHERE a.contact_id = c.id 
                    AND a.scheduled_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                )
                
                ORDER BY priority_order, days_since_creation DESC
                LIMIT 20";
        
        try {
            return $this->db->fetchAll($sql, ['user_id' => $userId, 'user_id2' => $userId]);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get cross-selling opportunities
     */
    private function getCrossSellingOpportunities(int $userId): array {
        // Affiliates who might be interested in additional services
        $sql = "SELECT c.*, 
                       a.membership_type_id,
                       m.name as membership_name,
                       DATEDIFF(a.expiration_date, CURDATE()) as days_until_expiration
                FROM contacts c
                JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                JOIN membership_types m ON a.membership_type_id = m.id
                WHERE (c.assigned_affiliate_id = :user_id OR c.assigned_affiliate_id IS NULL)
                AND a.membership_type_id < (SELECT MAX(id) FROM membership_types)
                ORDER BY a.expiration_date
                LIMIT 10";
        
        try {
            return $this->db->fetchAll($sql, ['user_id' => $userId]);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get upselling opportunities
     */
    private function getUpsellingOpportunities(int $userId): array {
        // Affiliates approaching renewal who could upgrade
        $sql = "SELECT c.*, 
                       a.membership_type_id,
                       m.name as current_membership,
                       m2.name as suggested_upgrade,
                       m2.price as upgrade_price,
                       DATEDIFF(a.expiration_date, CURDATE()) as days_until_expiration
                FROM contacts c
                JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                JOIN membership_types m ON a.membership_type_id = m.id
                JOIN membership_types m2 ON m2.id = m.id + 1
                WHERE (c.assigned_affiliate_id = :user_id OR c.assigned_affiliate_id IS NULL)
                AND DATEDIFF(a.expiration_date, CURDATE()) BETWEEN 0 AND 60
                ORDER BY a.expiration_date
                LIMIT 10";
        
        try {
            return $this->db->fetchAll($sql, ['user_id' => $userId]);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get performance metrics for user
     */
    private function getPerformanceMetrics(int $userId): array {
        $metrics = [];
        
        // Activities completed this month
        $sql = "SELECT COUNT(*) as count FROM activities 
                WHERE user_id = :user_id AND status = 'completada' 
                AND MONTH(completed_date) = MONTH(CURDATE()) AND YEAR(completed_date) = YEAR(CURDATE())";
        $result = $this->db->fetch($sql, ['user_id' => $userId]);
        $metrics['activities_completed'] = $result['count'] ?? 0;
        
        // Contacts created this month
        $sql = "SELECT COUNT(*) as count FROM contacts 
                WHERE assigned_affiliate_id = :user_id 
                AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $result = $this->db->fetch($sql, ['user_id' => $userId]);
        $metrics['contacts_created'] = $result['count'] ?? 0;
        
        // Affiliations closed this month
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount FROM affiliations 
                WHERE affiliate_user_id = :user_id 
                AND MONTH(affiliation_date) = MONTH(CURDATE()) AND YEAR(affiliation_date) = YEAR(CURDATE())";
        $result = $this->db->fetch($sql, ['user_id' => $userId]);
        $metrics['affiliations_closed'] = $result['count'] ?? 0;
        $metrics['revenue_generated'] = $result['total_amount'] ?? 0;
        
        // Response rate (completed vs total)
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'completada' THEN 1 ELSE 0 END) as completed
                FROM activities 
                WHERE user_id = :user_id 
                AND MONTH(scheduled_date) = MONTH(CURDATE())";
        $result = $this->db->fetch($sql, ['user_id' => $userId]);
        $total = $result['total'] ?? 1;
        $completed = $result['completed'] ?? 0;
        $metrics['completion_rate'] = $total > 0 ? round(($completed / $total) * 100) : 0;
        
        return $metrics;
    }
    
    /**
     * Get team off-hours activity (for managers)
     */
    private function getTeamOffHoursActivity(): array {
        $sql = "SELECT u.id, u.name, u.email,
                       COUNT(ual.id) as off_hours_actions,
                       MAX(ual.created_at) as last_off_hours_action
                FROM users u
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN user_activity_log ual ON u.id = ual.user_id AND ual.is_outside_hours = 1
                WHERE r.name = 'afiliador'
                AND ual.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY u.id, u.name, u.email
                HAVING off_hours_actions > 0
                ORDER BY off_hours_actions DESC";
        
        try {
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get monthly trend data
     */
    private function getMonthlyTrend(int $userId): array {
        $sql = "SELECT 
                    DATE_FORMAT(scheduled_date, '%Y-%m') as month,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'completada' THEN 1 ELSE 0 END) as completed
                FROM activities
                WHERE user_id = :user_id
                AND scheduled_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(scheduled_date, '%Y-%m')
                ORDER BY month";
        
        try {
            return $this->db->fetchAll($sql, ['user_id' => $userId]);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get team metrics (for managers)
     */
    private function getTeamMetrics(): array {
        $sql = "SELECT u.id, u.name,
                       COUNT(DISTINCT a.id) as activities_count,
                       SUM(CASE WHEN a.status = 'completada' THEN 1 ELSE 0 END) as completed_count,
                       COUNT(DISTINCT c.id) as prospects_count
                FROM users u
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN activities a ON u.id = a.user_id AND MONTH(a.scheduled_date) = MONTH(CURDATE())
                LEFT JOIN contacts c ON u.id = c.assigned_affiliate_id AND c.contact_type = 'prospecto'
                WHERE r.name = 'afiliador'
                GROUP BY u.id, u.name
                ORDER BY completed_count DESC";
        
        try {
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get today's notifications
     */
    private function getTodayNotifications(int $userId): array {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = :user_id 
                AND DATE(created_at) = CURDATE()
                ORDER BY created_at DESC";
        
        try {
            return $this->db->fetchAll($sql, ['user_id' => $userId]);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Group notifications by type
     */
    private function groupNotificationsByType(array $notifications): array {
        $grouped = [];
        foreach ($notifications as $notification) {
            $type = $notification['type'] ?? 'sistema';
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $notification;
        }
        return $grouped;
    }
    
    /**
     * Get activity form data
     */
    private function getActivityFormData(): array {
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
            'invitacion' => 'Envío de Invitación',
            'prospectacion' => 'Prospectación en Territorio',
            'captura' => 'Captura de Prospecto',
            'factura' => 'Solicitud de Factura',
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
    
    private function getNotificationTypes(): array {
        return [
            'vencimiento' => ['icon' => '⏰', 'color' => 'yellow', 'label' => 'Vencimiento'],
            'actividad' => ['icon' => '📋', 'color' => 'blue', 'label' => 'Actividad'],
            'no_match' => ['icon' => '🔍', 'color' => 'purple', 'label' => 'Búsqueda'],
            'oportunidad' => ['icon' => '💡', 'color' => 'green', 'label' => 'Oportunidad'],
            'beneficio' => ['icon' => '🎁', 'color' => 'pink', 'label' => 'Beneficio'],
            'sistema' => ['icon' => '⚙️', 'color' => 'gray', 'label' => 'Sistema'],
            'cross_selling' => ['icon' => '🔄', 'color' => 'indigo', 'label' => 'Cross-Selling'],
            'up_selling' => ['icon' => '📈', 'color' => 'emerald', 'label' => 'Up-Selling'],
            'evento' => ['icon' => '🎉', 'color' => 'orange', 'label' => 'Evento']
        ];
    }
}
