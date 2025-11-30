<!-- Commercial Agenda - Week View -->
<div class="space-y-6">
    <!-- Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="<?php echo BASE_URL; ?>/agenda-comercial" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Agenda Comercial
        </a>
        <h1 class="text-xl font-bold text-gray-900">Actividades de la Semana</h1>
    </div>
    
    <!-- Week Activities -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <?php if (empty($weekActivities)): ?>
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-500">No hay actividades programadas para esta semana.</p>
            <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva" class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800">
                + Crear nueva actividad
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-6">
            <?php 
            $groupedByDate = [];
            foreach ($weekActivities as $act) {
                $date = date('Y-m-d', strtotime($act['scheduled_date']));
                if (!isset($groupedByDate[$date])) {
                    $groupedByDate[$date] = [];
                }
                $groupedByDate[$date][] = $act;
            }
            $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            foreach ($groupedByDate as $date => $activities): 
                $dateObj = new DateTime($date);
            ?>
            <div class="border-l-4 border-blue-500 pl-4">
                <h4 class="font-semibold text-gray-900 mb-3 text-lg">
                    <?php echo $days[(int)$dateObj->format('w')]; ?> <?php echo $dateObj->format('d/m'); ?>
                </h4>
                <div class="space-y-3">
                    <?php foreach ($activities as $activity): ?>
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/<?php echo $activity['id']; ?>/editar" 
                       class="block p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex items-center justify-between">
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
                                        <?php echo date('H:i', strtotime($activity['scheduled_date'])); ?> - 
                                        <?php echo $activityTypes[$activity['activity_type']] ?? ucfirst($activity['activity_type']); ?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($activity['business_name']): ?>
                            <span class="text-sm text-blue-600"><?php echo htmlspecialchars($activity['business_name']); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Pending Activities -->
    <?php if (!empty($pendingActivities)): ?>
    <div class="bg-yellow-50 rounded-lg shadow-sm p-6 border border-yellow-200">
        <h3 class="text-lg font-semibold text-yellow-800 mb-4">📋 Actividades Pendientes Sin Fecha Asignada</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach (array_slice($pendingActivities, 0, 6) as $activity): ?>
            <a href="<?php echo BASE_URL; ?>/agenda-comercial/<?php echo $activity['id']; ?>/editar" 
               class="p-4 rounded-lg bg-white border border-yellow-200 hover:bg-yellow-50 transition">
                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                <p class="text-sm text-gray-500 mt-1"><?php echo $activityTypes[$activity['activity_type']] ?? ucfirst($activity['activity_type']); ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
