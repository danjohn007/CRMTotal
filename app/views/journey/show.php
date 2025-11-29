<!-- Customer Journey - Individual Contact View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/journey" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al Customer Journey
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                Customer Journey de <?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? 'Contacto'); ?>
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Etapa actual: <?php echo $stage['current_name']; ?> (<?php echo $stage['progress']; ?>% completado)
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                Ver Afiliado
            </a>
            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>/expediente" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                Expediente Digital
            </a>
        </div>
    </div>
    
    <!-- Journey Progress Bar -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Progreso del Customer Journey</h3>
        
        <!-- Progress Bar -->
        <div class="relative mb-8">
            <div class="h-2 bg-gray-200 rounded-full">
                <div class="h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-amber-500 rounded-full transition-all duration-500" 
                     style="width: <?php echo $stage['progress']; ?>%"></div>
            </div>
            <div class="absolute right-0 top-4 text-sm font-medium text-gray-700">
                <?php echo $stage['progress']; ?>%
            </div>
        </div>
        
        <!-- 6 Stages Horizontal -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <?php foreach ($journeyStages as $num => $stageInfo): 
                $stageData = $stage['stages'][$num] ?? [];
                $status = $stageData['status'] ?? 'pending';
                $isActive = $num == $stage['current'];
                $isCompleted = $status === 'completed';
                $isEligible = $status === 'eligible';
            ?>
            <div class="relative">
                <div class="text-center p-4 rounded-lg border-2 transition-all
                    <?php 
                    if ($isCompleted) {
                        echo 'bg-green-50 border-green-500';
                    } elseif ($isEligible) {
                        echo 'bg-yellow-50 border-yellow-500';
                    } elseif ($isActive) {
                        echo 'bg-blue-50 border-blue-500 ring-2 ring-blue-200';
                    } else {
                        echo 'bg-gray-50 border-gray-300';
                    }
                    ?>">
                    <div class="text-3xl mb-2"><?php echo $stageInfo['icon']; ?></div>
                    <p class="text-sm font-semibold 
                        <?php echo $isCompleted ? 'text-green-700' : ($isActive ? 'text-blue-700' : 'text-gray-600'); ?>">
                        <?php echo $num; ?>. <?php echo $stageInfo['name']; ?>
                    </p>
                    <div class="mt-2">
                        <?php if ($isCompleted): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                            ✓ Completado
                        </span>
                        <?php elseif ($isEligible): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                            ⭐ Elegible
                        </span>
                        <?php elseif ($isActive): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            → En progreso
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                            ○ Pendiente
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($stageData['date'])): ?>
                    <p class="text-xs text-gray-500 mt-1"><?php echo date('d/m/Y', strtotime($stageData['date'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Recommendations -->
            <?php if (!empty($recommendations)): ?>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Recomendaciones de Acción</h3>
                <div class="space-y-3">
                    <?php foreach ($recommendations as $rec): ?>
                    <div class="flex items-start p-4 rounded-lg border
                        <?php 
                        echo $rec['priority'] === 'high' ? 'bg-red-50 border-red-200' : 
                            ($rec['priority'] === 'medium' ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200'); 
                        ?>">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                <?php 
                                echo $rec['priority'] === 'high' ? 'bg-red-100 text-red-600' : 
                                    ($rec['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600'); 
                                ?>">
                                <?php echo $journeyStages[$rec['stage']]['icon'] ?? '📌'; ?>
                            </span>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($rec['title']); ?></h4>
                                <span class="text-xs text-gray-500">Etapa <?php echo $rec['stage']; ?></span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600"><?php echo htmlspecialchars($rec['description']); ?></p>
                            <a href="<?php echo BASE_URL; ?>/<?php echo $rec['action']; ?>" 
                               class="mt-2 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                Tomar acción →
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Up-selling Invitations History -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">📈 Historial de Invitaciones de Upgrade</h3>
                    <span class="text-sm text-gray-500">
                        <?php 
                        $thisYearInvitations = count(array_filter($upsellingInvitations, function($inv) {
                            return date('Y', strtotime($inv['invitation_date'])) === date('Y');
                        }));
                        echo $thisYearInvitations; ?>/2 este año
                    </span>
                </div>
                
                <?php if (empty($upsellingInvitations)): ?>
                <p class="text-gray-500 text-center py-4">No hay invitaciones de upgrade registradas</p>
                <div class="mt-4 text-center">
                    <a href="<?php echo BASE_URL; ?>/journey/upselling" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-sm">
                        📤 Enviar Primera Invitación
                    </a>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">De → A</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($upsellingInvitations as $inv): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <?php echo date('d/m/Y H:i', strtotime($inv['invitation_date'])); ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <?php echo htmlspecialchars($inv['current_membership']); ?> → <?php echo htmlspecialchars($inv['target_membership']); ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <?php 
                                    $typeLabels = [
                                        'email' => '📧 Email',
                                        'whatsapp' => '💬 WhatsApp',
                                        'phone' => '📞 Teléfono',
                                        'in_person' => '🤝 Presencial',
                                        'payment_link' => '🔗 Liga de pago'
                                    ];
                                    echo $typeLabels[$inv['invitation_type']] ?? $inv['invitation_type'];
                                    ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php 
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-green-100 text-green-800',
                                        'declined' => 'bg-red-100 text-red-800',
                                        'no_response' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'accepted' => 'Aceptada',
                                        'declined' => 'Rechazada',
                                        'no_response' => 'Sin respuesta'
                                    ];
                                    ?>
                                    <span class="px-2 py-1 text-xs rounded-full <?php echo $statusClasses[$inv['response_status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $statusLabels[$inv['response_status']] ?? $inv['response_status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Free Event Attendance -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🎟️ Asistencia a Eventos Gratuitos</h3>
                
                <?php if (empty($freeEventAttendance)): ?>
                <p class="text-gray-500 text-center py-4">No hay asistencia a eventos gratuitos registrada</p>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($freeEventAttendance, 0, 10) as $event): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-xl mr-3">
                                <?php 
                                $catIcons = [
                                    'desayuno' => '☕',
                                    'open_day' => '🚪',
                                    'conferencia' => '🎤',
                                    'feria' => '🎪',
                                    'exposicion' => '🖼️',
                                    'networking' => '🤝'
                                ];
                                echo $catIcons[$event['category']] ?? '📅';
                                ?>
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['title']); ?></p>
                                <p class="text-xs text-gray-500">
                                    <?php echo date('d/m/Y', strtotime($event['start_date'])); ?>
                                    <?php if (!empty($event['location'])): ?>
                                    - <?php echo htmlspecialchars($event['location']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✓ Asistió</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Service Contracts (Cross-selling) -->
            <?php if (!empty($contracts)): ?>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💼 Servicios Contratados (Cross-selling)</h3>
                <div class="space-y-3">
                    <?php foreach ($contracts as $contract): ?>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($contract['service_name'] ?? 'Servicio'); ?></p>
                            <p class="text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($contract['contract_date'] ?? $contract['created_at'])); ?>
                                - <?php echo ucfirst($contract['category'] ?? 'otros'); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-purple-600">$<?php echo number_format($contract['amount'] ?? 0, 0); ?></p>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo ($contract['status'] ?? '') === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo ucfirst($contract['status'] ?? 'N/A'); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            
            <!-- Contact Info Card -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Información del Contacto</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase">RFC</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($contact['rfc'] ?? 'No registrado'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Propietario/Rep. Legal</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['owner_name'] ?? $contact['legal_representative'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase">WhatsApp</label>
                        <p class="text-gray-900">
                            <?php if (!empty($contact['whatsapp'])): ?>
                            <a href="https://wa.me/52<?php echo preg_replace('/[^0-9]/', '', $contact['whatsapp']); ?>" target="_blank" class="text-green-600 hover:underline">
                                <?php echo htmlspecialchars($contact['whatsapp']); ?>
                            </a>
                            <?php else: ?>-<?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Tipo de Contacto</label>
                        <?php
                        $contactTypeLabels = [
                            'prospecto' => 'Prospecto',
                            'afiliado' => 'Afiliado',
                            'exafiliado' => 'Ex-Afiliado',
                            'nuevo_usuario' => 'Nuevo Usuario',
                            'funcionario' => 'Funcionario',
                            'publico_general' => 'Público General',
                            'consejero_propietario' => 'Consejero Propietario',
                            'consejero_invitado' => 'Consejero Invitado',
                            'mesa_directiva' => 'Mesa Directiva',
                            'colaborador_empresa' => 'Colaborador de Empresa'
                        ];
                        $contactType = $contact['contact_type'] ?? 'prospecto';
                        ?>
                        <p class="text-gray-900"><?php echo $contactTypeLabels[$contactType] ?? ucfirst($contactType); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Council Eligibility -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🏛️ Elegibilidad para Consejo</h3>
                
                <?php if ($councilEligibility['eligible']): ?>
                <div class="p-4 bg-amber-50 rounded-lg border border-amber-200">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">⭐</span>
                        <div>
                            <p class="font-semibold text-amber-800">Elegible para el Consejo</p>
                            <p class="text-sm text-amber-600">
                                <?php echo $councilEligibility['years_affiliated']; ?> años de afiliación
                            </p>
                        </div>
                    </div>
                    <?php if ($councilEligibility['council_status']): ?>
                    <div class="mt-3 pt-3 border-t border-amber-200">
                        <p class="text-sm text-amber-700">
                            Estado en consejo: <strong><?php echo ucfirst($councilEligibility['council_status']['status'] ?? 'N/A'); ?></strong>
                        </p>
                    </div>
                    <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/journey/council" 
                       class="mt-3 block text-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition text-sm">
                        Solicitar Ingreso al Consejo
                    </a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-600 text-sm">
                        Requiere <?php echo $councilEligibility['years_required']; ?> años de afiliación continua.
                    </p>
                    <p class="text-gray-500 text-sm mt-1">
                        Actualmente: <?php echo $councilEligibility['years_affiliated']; ?> años
                    </p>
                    <div class="mt-3">
                        <div class="h-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-amber-500 rounded-full" 
                                 style="width: <?php echo min(100, ($councilEligibility['years_affiliated'] / $councilEligibility['years_required']) * 100); ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Current Affiliation -->
            <?php if (!empty($affiliations)): ?>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💳 Afiliación Actual</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Membresía</span>
                        <span class="font-medium text-gray-900"><?php echo htmlspecialchars($affiliations[0]['membership_name'] ?? '-'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Estado</span>
                        <span class="px-2 py-1 text-xs rounded-full <?php echo $affiliations[0]['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo $affiliations[0]['status'] === 'active' ? 'Activa' : ucfirst($affiliations[0]['status']); ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Vencimiento</span>
                        <span class="font-medium text-gray-900"><?php echo date('d/m/Y', strtotime($affiliations[0]['expiration_date'])); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Monto</span>
                        <span class="font-bold text-green-600">$<?php echo number_format($affiliations[0]['amount'] ?? 0, 0); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Acciones Rápidas</h3>
                <div class="space-y-2">
                    <a href="<?php echo BASE_URL; ?>/agenda/nueva?contact_id=<?php echo $contact['id']; ?>" 
                       class="block w-full px-4 py-2 text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        📝 Nueva Actividad
                    </a>
                    <a href="<?php echo BASE_URL; ?>/journey/upselling" 
                       class="block w-full px-4 py-2 text-center bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-sm">
                        📈 Enviar Invitación Upgrade
                    </a>
                    <?php if (!empty($contact['whatsapp'])): ?>
                    <a href="https://wa.me/52<?php echo preg_replace('/[^0-9]/', '', $contact['whatsapp']); ?>" target="_blank"
                       class="block w-full px-4 py-2 text-center bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                        💬 WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity History -->
    <?php if (!empty($activities)): ?>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Historial de Actividades</h3>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            <?php foreach (array_slice($activities, 0, 10) as $activity): ?>
            <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                    <?php 
                    $activityIcons = [
                        'whatsapp' => '💬',
                        'email' => '📧',
                        'llamada' => '📞',
                        'visita' => '🏃',
                        'reunion' => '🤝',
                        'nota' => '📝',
                    ];
                    echo $activityIcons[$activity['activity_type']] ?? '📌';
                    ?>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?php echo $activity['status'] === 'completada' ? 'bg-green-100 text-green-800' : 
                                ($activity['status'] === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo ucfirst($activity['status']); ?>
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <?php echo date('d/m/Y H:i', strtotime($activity['scheduled_date'])); ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
