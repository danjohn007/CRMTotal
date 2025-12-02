<?php
// Include header
require_once APP_PATH . '/views/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Reportes de Eventos</h1>
        <p class="mt-2 text-gray-600">Métricas de asistencia, boletos y rendimiento de eventos</p>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>
        <form method="GET" action="<?php echo BASE_URL; ?>/reportes/eventos" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="event_id" class="block text-sm font-medium text-gray-700 mb-2">Evento Específico</label>
                <select name="event_id" id="event_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos los eventos</option>
                    <?php foreach ($allEvents as $event): ?>
                    <option value="<?php echo $event['id']; ?>" <?php echo $selectedEventId == $event['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($event['title']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Evento</label>
                <select name="event_type" id="event_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($eventTypes as $code => $label): ?>
                    <option value="<?php echo $code; ?>" <?php echo $selectedEventType == $code ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                <select name="category" id="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $selectedCategory == $cat ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
    
    <!-- Overall Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Eventos</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($metrics['total_events'] ?? 0); ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                Pagados: <?php echo $metrics['paid_events'] ?? 0; ?> | Gratuitos: <?php echo $metrics['free_events'] ?? 0; ?>
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Boletos</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($metrics['total_tickets'] ?? 0); ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                Registros generados
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Asistencias</p>
                    <p class="text-3xl font-bold text-green-600 mt-2"><?php echo number_format($metrics['total_attendance'] ?? 0); ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                Tasa: <?php echo number_format($metrics['attendance_rate'] ?? 0, 2); ?>%
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Inasistencias</p>
                    <p class="text-3xl font-bold text-red-600 mt-2"><?php echo number_format($metrics['total_no_show'] ?? 0); ?></p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                % Inasistencias: <?php echo number_format($metrics['no_show_rate'] ?? 0, 2); ?>%
            </p>
        </div>
    </div>
    
    <!-- Paid Events Metrics -->
    <?php if (($metrics['paid_events'] ?? 0) > 0): ?>
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Eventos de Pago - Desglose</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Courtesy vs Paid -->
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-3">Boletos de Cortesía vs Pagados</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm font-medium">Cortesías</span>
                        <span class="text-lg font-bold text-purple-600"><?php echo number_format($metrics['courtesy_tickets'] ?? 0); ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium">Pagados</span>
                        <span class="text-lg font-bold text-blue-600"><?php echo number_format($metrics['paid_tickets'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Attendance Breakdown -->
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-3">Asistencia e Inasistencia</h3>
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span>Cortesías Asistentes</span>
                        <span class="font-semibold text-green-600"><?php echo number_format($metrics['courtesy_attended'] ?? 0); ?></span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span>Cortesías Inasistentes</span>
                        <span class="font-semibold text-red-600"><?php echo number_format($metrics['courtesy_no_show'] ?? 0); ?></span>
                    </div>
                    <div class="flex justify-between items-center text-sm pt-2 border-t">
                        <span>Pagados Asistentes</span>
                        <span class="font-semibold text-green-600"><?php echo number_format($metrics['paid_attended'] ?? 0); ?></span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span>Pagados Inasistentes</span>
                        <span class="font-semibold text-red-600"><?php echo number_format($metrics['paid_no_show'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Top 50 Attending Businesses -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Top 50 Razones Sociales Asistentes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Razón Social</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RFC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Asistencias</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eventos Asistidos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagados</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gratuitos</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($topBusinesses)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No hay datos disponibles
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($topBusinesses as $index => $business): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo $index + 1; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($business['business_name'] ?? 'Sin nombre'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                <?php echo htmlspecialchars($business['rfc'] ?? 'N/A'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                <?php echo number_format($business['attendance_count'] ?? 0); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo number_format($business['events_attended'] ?? 0); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                <?php echo number_format($business['paid_events_attended'] ?? 0); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-600">
                                <?php echo number_format($business['free_events_attended'] ?? 0); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Metrics by Type and Category -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- By Type -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Métricas por Tipo de Evento</h2>
            <div class="space-y-3">
                <?php foreach ($metricsByType as $typeMetric): ?>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">
                            <?php echo htmlspecialchars($eventTypes[$typeMetric['event_type']] ?? $typeMetric['event_type']); ?>
                        </span>
                        <span class="text-xs text-gray-500"><?php echo $typeMetric['total_events']; ?> eventos</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-gray-600">
                        <span>Boletos: <?php echo number_format($typeMetric['total_tickets'] ?? 0); ?></span>
                        <span class="text-green-600">Asistencias: <?php echo number_format($typeMetric['total_attendance'] ?? 0); ?></span>
                        <span class="text-red-600">Inasistencias: <?php echo number_format($typeMetric['total_no_show'] ?? 0); ?></span>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $typeMetric['attendance_rate'] ?? 0; ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Tasa de asistencia: <?php echo number_format($typeMetric['attendance_rate'] ?? 0, 2); ?>%</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- By Category -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Métricas por Categoría</h2>
            <div class="space-y-3">
                <?php foreach ($metricsByCategory as $catMetric): ?>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">
                            <?php echo htmlspecialchars($catMetric['category'] ?? 'Sin categoría'); ?>
                        </span>
                        <span class="text-xs text-gray-500"><?php echo $catMetric['total_events']; ?> eventos</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-gray-600">
                        <span>Boletos: <?php echo number_format($catMetric['total_tickets'] ?? 0); ?></span>
                        <span class="text-green-600">Asistencias: <?php echo number_format($catMetric['total_attendance'] ?? 0); ?></span>
                        <span class="text-red-600">Inasistencias: <?php echo number_format($catMetric['total_no_show'] ?? 0); ?></span>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $catMetric['attendance_rate'] ?? 0; ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Tasa de asistencia: <?php echo number_format($catMetric['attendance_rate'] ?? 0, 2); ?>%</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
