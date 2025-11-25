<!-- Event Show View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="<?php echo BASE_URL; ?>/eventos" class="text-blue-600 hover:text-blue-800">
                    ← Volver a Eventos
                </a>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mt-2"><?php echo htmlspecialchars($event['title']); ?></h2>
            <div class="mt-1 flex items-center space-x-3">
                <span class="px-2 py-1 text-xs rounded-full bg-<?php echo $event['event_type'] === 'interno' ? 'blue' : ($event['event_type'] === 'externo' ? 'green' : 'purple'); ?>-100 text-<?php echo $event['event_type'] === 'interno' ? 'blue' : ($event['event_type'] === 'externo' ? 'green' : 'purple'); ?>-800">
                    <?php echo $eventTypes[$event['event_type']] ?? $event['event_type']; ?>
                </span>
                <span class="px-2 py-1 text-xs rounded-full <?php echo $event['status'] === 'published' ? 'bg-green-100 text-green-800' : ($event['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                    <?php echo ucfirst($event['status']); ?>
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
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>/asistencia" 
               class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Control de Asistencia
            </a>
            <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>/editar" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar Evento
            </a>
        </div>
    </div>
    
    <!-- Event Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Image -->
            <?php if (!empty($event['image'])): ?>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <img src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($event['image']); ?>" 
                     alt="<?php echo htmlspecialchars($event['title']); ?>"
                     class="w-full h-64 object-cover">
            </div>
            <?php endif; ?>
            
            <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Descripción</h3>
                <p class="text-gray-600 whitespace-pre-line"><?php echo !empty($event['description']) ? htmlspecialchars($event['description']) : 'Sin descripción'; ?></p>
            </div>
            
            <!-- Registration URL -->
            <?php if (!empty($event['registration_url'])): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">URL Pública de Registro</h3>
                <div class="flex items-center space-x-2">
                    <input type="text" readonly value="<?php echo BASE_URL; ?>/evento/<?php echo htmlspecialchars($event['registration_url']); ?>" 
                           class="flex-1 p-2 bg-gray-50 border rounded-md text-sm" id="registration-url">
                    <button onclick="copyToClipboard()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        Copiar
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">Comparte esta URL para que los invitados se registren al evento.</p>
            </div>
            <?php endif; ?>
            
            <!-- Registrations Table -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Registros</h3>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        <?php echo $registeredCount; ?> registrados
                    </span>
                </div>
                <?php if (empty($registrations)): ?>
                <p class="text-gray-500 text-center py-12">No hay registros para este evento</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Registro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($registrations as $reg): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($reg['guest_name'] ?? $reg['business_name'] ?? '-'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($reg['guest_email'] ?? '-'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($reg['guest_phone'] ?? '-'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($reg['guest_rfc'] ?? '-'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d/m/Y H:i', strtotime($reg['registration_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($reg['attended']): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Asistió</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Registrados</span>
                        <span class="font-semibold text-gray-900"><?php echo $registeredCount; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Asistieron</span>
                        <span class="font-semibold text-gray-900"><?php echo $attendedCount; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Capacidad</span>
                        <span class="font-semibold text-gray-900"><?php echo $event['max_capacity'] ?: 'Ilimitada'; ?></span>
                    </div>
                    <?php if ($event['max_capacity'] > 0): ?>
                    <div class="mt-2">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Ocupación</span>
                            <span><?php echo round(($registeredCount / $event['max_capacity']) * 100); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo min(100, ($registeredCount / $event['max_capacity']) * 100); ?>%"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Date & Time -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Fecha y Hora</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Inicio</p>
                            <p class="text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($event['start_date'])); ?></p>
                            <p class="text-sm text-gray-600"><?php echo date('H:i', strtotime($event['start_date'])); ?> hrs</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Fin</p>
                            <p class="text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($event['end_date'])); ?></p>
                            <p class="text-sm text-gray-600"><?php echo date('H:i', strtotime($event['end_date'])); ?> hrs</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Location -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ubicación</h3>
                <div class="space-y-3">
                    <?php if ($event['is_online']): ?>
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Evento en línea</p>
                            <?php if (!empty($event['online_url'])): ?>
                            <a href="<?php echo htmlspecialchars($event['online_url']); ?>" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                                Acceder al evento
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['location'] ?? 'Sin ubicación'); ?></p>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($event['address'] ?? ''); ?></p>
                            <?php if (!empty($event['google_maps_url'])): ?>
                            <a href="<?php echo htmlspecialchars($event['google_maps_url']); ?>" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                                Ver en Google Maps
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Pricing -->
            <?php if ($event['is_paid']): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Precios</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Precio General</span>
                        <span class="font-semibold text-gray-900">$<?php echo number_format($event['price'], 2); ?></span>
                    </div>
                    <?php if ($event['member_price'] > 0): ?>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Precio Afiliados</span>
                        <span class="font-semibold text-green-600">$<?php echo number_format($event['member_price'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Target Audiences -->
            <?php 
            $audiences = json_decode($event['target_audiences'] ?? '[]', true);
            $audienceLabels = [
                'afiliado' => 'Afiliados',
                'prospecto' => 'Prospectos',
                'exafiliado' => 'Exafiliados',
                'publico' => 'Público en General',
                'funcionario' => 'Funcionarios',
                'consejero' => 'Consejeros'
            ];
            ?>
            <?php if (!empty($audiences)): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Público Objetivo</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($audiences as $audience): ?>
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                        <?php echo $audienceLabels[$audience] ?? $audience; ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Category -->
            <?php if (!empty($event['category'])): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Categoría</h3>
                <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">
                    <?php echo htmlspecialchars($event['category']); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const input = document.getElementById('registration-url');
    input.select();
    document.execCommand('copy');
    alert('URL copiada al portapapeles');
}
</script>
