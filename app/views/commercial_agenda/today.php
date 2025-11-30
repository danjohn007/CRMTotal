<!-- Commercial Agenda - Today View -->
<div class="space-y-6">
    <!-- Header with Motivational Message -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center space-x-4">
            <div class="text-4xl"><?php echo $motivationalMessage['icon']; ?></div>
            <div>
                <h2 class="text-2xl font-bold"><?php echo $motivationalMessage['title']; ?></h2>
                <p class="mt-1 text-blue-100"><?php echo $motivationalMessage['message']; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="<?php echo BASE_URL; ?>/agenda-comercial" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Agenda Comercial
        </a>
        <h1 class="text-xl font-bold text-gray-900">Acciones de Hoy - <?php echo date('d/m/Y'); ?></h1>
    </div>
    
    <!-- Today's Activities -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividades Programadas</h3>
        
        <?php if (empty($todayActivities)): ?>
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-gray-500">¡No hay actividades pendientes para hoy!</p>
            <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva" class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800">
                + Crear nueva actividad
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($todayActivities as $activity): ?>
            <div class="p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl
                            <?php echo match($activity['activity_type']) {
                                'llamada' => 'bg-blue-100',
                                'whatsapp' => 'bg-green-100',
                                'email' => 'bg-purple-100',
                                'visita' => 'bg-orange-100',
                                'reunion' => 'bg-indigo-100',
                                default => 'bg-gray-100'
                            }; ?>">
                            <?php echo match($activity['activity_type']) {
                                'llamada' => '📞',
                                'whatsapp' => '💬',
                                'email' => '✉️',
                                'visita' => '🚗',
                                'reunion' => '👥',
                                default => '📋'
                            }; ?>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                            <p class="text-sm text-gray-500">
                                <?php echo date('H:i', strtotime($activity['scheduled_date'])); ?> - 
                                <?php echo $activityTypes[$activity['activity_type']] ?? ucfirst($activity['activity_type']); ?>
                            </p>
                            <?php if ($activity['business_name']): ?>
                            <p class="text-sm text-blue-600"><?php echo htmlspecialchars($activity['business_name']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-xs rounded-full 
                            <?php echo $activity['priority'] === 'urgente' ? 'bg-red-100 text-red-800' : 
                                      ($activity['priority'] === 'alta' ? 'bg-orange-100 text-orange-800' : 
                                      ($activity['priority'] === 'media' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')); ?>">
                            <?php echo $priorities[$activity['priority']] ?? ucfirst($activity['priority']); ?>
                        </span>
                        <a href="<?php echo BASE_URL; ?>/agenda-comercial/<?php echo $activity['id']; ?>/editar" 
                           class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Ver / Editar
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Overdue Activities -->
    <?php if (!empty($overdueActivities)): ?>
    <div class="bg-red-50 rounded-lg shadow-sm p-6 border border-red-200">
        <h3 class="text-lg font-semibold text-red-800 mb-4">⚠️ Actividades Vencidas - Requieren Atención</h3>
        <div class="space-y-3">
            <?php foreach ($overdueActivities as $activity): ?>
            <div class="p-4 rounded-lg bg-white border border-red-200 hover:bg-red-50 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                        <p class="text-sm text-red-600">
                            Programada: <?php echo date('d/m/Y H:i', strtotime($activity['scheduled_date'])); ?>
                        </p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/<?php echo $activity['id']; ?>/editar" 
                       class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Atender
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Today's Notifications -->
    <?php if (!empty($todayNotifications)): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Notificaciones de Hoy</h3>
        <div class="space-y-3">
            <?php foreach ($todayNotifications as $notification): ?>
            <div class="p-3 rounded-lg border border-gray-200 <?php echo !$notification['is_read'] ? 'bg-blue-50' : ''; ?>">
                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($notification['title']); ?></p>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($notification['message']); ?></p>
                <p class="text-xs text-gray-400 mt-1"><?php echo date('H:i', strtotime($notification['created_at'])); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
