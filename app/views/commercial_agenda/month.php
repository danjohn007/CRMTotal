<!-- Commercial Agenda - Month View -->
<div class="space-y-6">
    <!-- Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="<?php echo BASE_URL; ?>/agenda-comercial" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Agenda Comercial
        </a>
        <h1 class="text-xl font-bold text-gray-900">Resumen del Mes - <?php echo date('F Y'); ?></h1>
    </div>
    
    <!-- Monthly Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $activityStats['total'] ?? 0; ?></p>
            <p class="text-sm text-gray-500">Total Actividades</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-green-600"><?php echo $activityStats['completed'] ?? 0; ?></p>
            <p class="text-sm text-gray-500">Completadas</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-yellow-600"><?php echo $activityStats['pending'] ?? 0; ?></p>
            <p class="text-sm text-gray-500">Pendientes</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-red-600"><?php echo $activityStats['overdue'] ?? 0; ?></p>
            <p class="text-sm text-gray-500">Vencidas</p>
        </div>
    </div>
    
    <!-- Completion Rate -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tasa de Cumplimiento</h3>
        <div class="flex items-center">
            <div class="flex-1">
                <?php 
                $total = ($activityStats['total'] ?? 1) > 0 ? $activityStats['total'] : 1;
                $completed = $activityStats['completed'] ?? 0;
                $rate = round(($completed / $total) * 100);
                ?>
                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-green-500 transition-all duration-500" 
                         style="width: <?php echo $rate; ?>%"></div>
                </div>
            </div>
            <span class="ml-4 text-2xl font-bold text-gray-900"><?php echo $rate; ?>%</span>
        </div>
        <p class="text-sm text-gray-500 mt-2">
            <?php echo $completed; ?> de <?php echo $total; ?> actividades completadas este mes
        </p>
    </div>
    
    <!-- Upcoming Activities This Month -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Próximas Actividades del Mes</h3>
        
        <?php if (empty($monthActivities)): ?>
        <div class="text-center py-8">
            <p class="text-gray-500">No hay actividades adicionales programadas para este mes.</p>
            <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva" class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800">
                + Crear nueva actividad
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($monthActivities as $activity): ?>
            <a href="<?php echo BASE_URL; ?>/agenda-comercial/<?php echo $activity['id']; ?>/editar" 
               class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                <div class="flex items-center space-x-3">
                    <span class="text-xl">
                        <?php echo match($activity['activity_type']) {
                            'llamada' => '📞',
                            'whatsapp' => '💬',
                            'email' => '✉️',
                            'visita' => '🚗',
                            'reunion' => '👥',
                            default => '📋'
                        }; ?>
                    </span>
                    <div>
                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                        <p class="text-sm text-gray-500">
                            <?php echo date('d/m H:i', strtotime($activity['scheduled_date'])); ?>
                            <?php if ($activity['business_name']): ?>
                            - <?php echo htmlspecialchars($activity['business_name']); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs rounded-full 
                    <?php echo $activity['priority'] === 'urgente' ? 'bg-red-100 text-red-800' : 
                              ($activity['priority'] === 'alta' ? 'bg-orange-100 text-orange-800' : 
                              'bg-gray-100 text-gray-800'); ?>">
                    <?php echo $priorities[$activity['priority']] ?? ucfirst($activity['priority']); ?>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
