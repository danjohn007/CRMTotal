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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asistencia</th>
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
                                    <?php 
                                    $paymentStatus = strtolower($reg['payment_status'] ?? 'pending');
                                    if ($paymentStatus === 'paid'): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Pagado</span>
                                    <?php elseif ($paymentStatus === 'free'): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Gratuito</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pendiente Pago</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($reg['attended']): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Asistió</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Sin confirmar</span>
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
            
            <!-- Action Buttons for Bulk Communication -->
            <?php if ($registeredCount > 0): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Comunicación Masiva</h3>
                <div class="space-y-3">
                    <!-- Export Emails Button -->
                    <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>/export-emails" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        EXPORTAR (CSV)
                    </a>
                    
                    <!-- Send WhatsApp Button -->
                    <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>/send-whatsapp" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        ENVIAR WhatsApp
                    </a>
                    
                    <!-- Send Email Button -->
                    <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>/send-email" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        EMAIL con QR
                    </a>
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    Exporta la lista de asistentes o envía comunicaciones masivas por WhatsApp o Email.
                </p>
            </div>
            <?php endif; ?>
            
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
    const text = input.value;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            alert('URL copiada al portapapeles');
        }).catch(function(err) {
            fallbackCopy(input);
        });
    } else {
        fallbackCopy(input);
    }
}

function fallbackCopy(input) {
    input.select();
    input.setSelectionRange(0, 99999);
    try {
        document.execCommand('copy');
        alert('URL copiada al portapapeles');
    } catch (err) {
        alert('No se pudo copiar. Por favor, selecciona y copia manualmente.');
    }
}
</script>
