<!-- Agenda Index with Calendar -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Agenda</h2>
            <p class="mt-1 text-sm text-gray-500">Gestión de actividades y seguimiento</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/agenda/nueva" 
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nueva Actividad
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Hoy</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo count($todayActivities); ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Pendientes</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending'] ?? 0; ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Vencidas</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo count($overdueActivities); ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Completadas (Mes)</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['completed'] ?? 0; ?></p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
            <div id="calendar"></div>
        </div>
        
        <!-- Today's Activities -->
        <div class="space-y-6">
            <!-- Today -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividades de Hoy</h3>
                <?php if (empty($todayActivities)): ?>
                <p class="text-gray-500 text-center py-4">No hay actividades para hoy</p>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($todayActivities as $activity): ?>
                    <a href="<?php echo BASE_URL; ?>/agenda/<?php echo $activity['id']; ?>/editar" 
                       class="block p-3 rounded-lg border border-gray-200 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></span>
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $activity['priority'] === 'urgente' ? 'bg-red-100 text-red-800' : 
                                          ($activity['priority'] === 'alta' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo ucfirst($activity['priority']); ?>
                            </span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                            <span><?php echo date('H:i', strtotime($activity['scheduled_date'])); ?></span>
                            <span class="mx-2">•</span>
                            <span><?php echo $activityTypes[$activity['activity_type']] ?? $activity['activity_type']; ?></span>
                        </div>
                        <?php if ($activity['business_name']): ?>
                        <p class="mt-1 text-xs text-blue-600"><?php echo htmlspecialchars($activity['business_name']); ?></p>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Overdue -->
            <?php if (!empty($overdueActivities)): ?>
            <div class="bg-red-50 rounded-lg shadow-sm p-6 border border-red-200">
                <h3 class="text-lg font-semibold text-red-800 mb-4">⚠ Actividades Vencidas</h3>
                <div class="space-y-3">
                    <?php foreach (array_slice($overdueActivities, 0, 5) as $activity): ?>
                    <a href="<?php echo BASE_URL; ?>/agenda/<?php echo $activity['id']; ?>/editar" 
                       class="block p-3 rounded-lg bg-white border border-red-200 hover:bg-red-50">
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                        <p class="text-xs text-red-600 mt-1">
                            Vencida: <?php echo date('d/m/Y', strtotime($activity['scheduled_date'])); ?>
                        </p>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Activity Types Distribution -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Por Tipo (Este mes)</h3>
                <div class="space-y-3">
                    <?php foreach ($typeStats as $type): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600"><?php echo $activityTypes[$type['activity_type']] ?? $type['activity_type']; ?></span>
                        <span class="text-sm font-medium text-gray-900"><?php echo $type['count']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: '<?php echo BASE_URL; ?>/agenda/api/eventos',
        eventClick: function(info) {
            window.location.href = '<?php echo BASE_URL; ?>/agenda/' + info.event.id + '/editar';
        },
        eventDidMount: function(info) {
            if (info.event.extendedProps.status === 'completada') {
                info.el.style.opacity = '0.6';
                info.el.style.textDecoration = 'line-through';
            }
        }
    });
    calendar.render();
});
</script>
