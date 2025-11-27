<!-- Events Index -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Eventos</h2>
            <p class="mt-1 text-sm text-gray-500">Gestión de eventos internos, públicos y de terceros</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/eventos/categorias" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Categorías
            </a>
            <a href="<?php echo BASE_URL; ?>/eventos/nuevo" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Evento
            </a>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-500">Eventos del Año</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_events'] ?? 0; ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-500">Eventos Pagados</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['paid_events'] ?? 0; ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-500">Total Registros</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_registrations'] ?? 0; ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-500">Asistencia Total</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_attendance'] ?? 0; ?></p>
        </div>
    </div>
    
    <!-- Upcoming Events -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Próximos Eventos</h3>
        </div>
        <?php if (empty($upcomingEvents)): ?>
        <p class="text-gray-500 text-center py-12">No hay eventos próximos</p>
        <?php else: ?>
        <div class="divide-y divide-gray-200">
            <?php foreach ($upcomingEvents as $event): ?>
            <div class="p-6 hover:bg-gray-50">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4">
                        <!-- Date Badge -->
                        <div class="flex-shrink-0 w-16 h-16 bg-blue-100 rounded-lg flex flex-col items-center justify-center">
                            <span class="text-xs font-medium text-blue-600 uppercase">
                                <?php echo date('M', strtotime($event['start_date'])); ?>
                            </span>
                            <span class="text-2xl font-bold text-blue-800">
                                <?php echo date('d', strtotime($event['start_date'])); ?>
                            </span>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">
                                <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>" class="hover:text-blue-600">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </a>
                            </h4>
                            <div class="mt-1 flex items-center text-sm text-gray-500 space-x-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <?php echo date('H:i', strtotime($event['start_date'])); ?>
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($event['location'] ?? ($event['is_online'] ? 'Online' : '-')); ?>
                                </span>
                            </div>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full bg-<?php echo $event['event_type'] === 'interno' ? 'blue' : ($event['event_type'] === 'externo' ? 'green' : 'purple'); ?>-100 text-<?php echo $event['event_type'] === 'interno' ? 'blue' : ($event['event_type'] === 'externo' ? 'green' : 'purple'); ?>-800">
                                    <?php echo $eventTypes[$event['event_type']] ?? $event['event_type']; ?>
                                </span>
                                <?php if ($event['is_paid']): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    $<?php echo number_format($event['price'], 0); ?>
                                </span>
                                <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    Gratuito
                                </span>
                                <?php endif; ?>
                                <span class="text-xs text-gray-500">
                                    <?php echo $event['registered_count']; ?> registrados
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>" 
                           class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                            Ver
                        </a>
                        <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>/asistencia" 
                           class="px-3 py-1 text-sm border border-blue-300 text-blue-600 rounded-lg hover:bg-blue-50">
                            Asistencia
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Past Events -->
    <?php if (!empty($pastEvents)): ?>
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Eventos Pasados</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registros</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($pastEvents as $event): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                <?php echo htmlspecialchars($event['title']); ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo date('d/m/Y', strtotime($event['start_date'])); ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                <?php echo $eventTypes[$event['event_type']] ?? $event['event_type']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?php echo $event['registered_count']; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?php echo $event['attended_count']; ?>
                            <span class="text-gray-500">
                                (<?php echo $event['registered_count'] > 0 ? round(($event['attended_count'] / $event['registered_count']) * 100) : 0; ?>%)
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
