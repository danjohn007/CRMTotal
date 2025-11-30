<!-- Expediente Digital Afiliado (EDA) - Company Dashboard View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al Detalle
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                📁 Expediente Digital Afiliado (EDA)
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                <?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? 'Sin nombre'); ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>/editar" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Editar
            </a>
        </div>
    </div>
    
    <!-- Person Type Indicator -->
    <?php 
    $rfcLen = strlen($contact['rfc'] ?? '');
    $personType = '';
    $personTypeLabel = '';
    $personTypeDesc = '';
    if ($rfcLen === 13) {
        $personType = 'fisica';
        $personTypeLabel = 'Persona Física';
        $personTypeDesc = 'Dueño de empresa';
    } elseif ($rfcLen === 12) {
        $personType = 'moral';
        $personTypeLabel = 'Persona Moral';
        $personTypeDesc = 'Representante Legal';
    }
    ?>
    <?php if ($personType): ?>
    <div class="bg-white rounded-lg shadow-sm p-4 flex items-center justify-between">
        <div class="flex items-center">
            <span class="text-3xl mr-3"><?php echo $personType === 'fisica' ? '👤' : '🏢'; ?></span>
            <div>
                <p class="font-semibold <?php echo $personType === 'fisica' ? 'text-blue-800' : 'text-purple-800'; ?>"><?php echo $personTypeLabel; ?></p>
                <p class="text-sm text-gray-500"><?php echo $personTypeDesc; ?></p>
            </div>
        </div>
        <?php if (!empty($contact['niza_classification'])): ?>
        <div class="text-right">
            <p class="text-sm text-gray-500">Clasificación NIZA</p>
            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($contact['niza_classification']); ?> - <?php echo htmlspecialchars($contact['industry'] ?? ''); ?></p>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Company Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Days Remaining -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 <?php echo $daysRemaining <= 30 ? 'border-red-500' : ($daysRemaining <= 60 ? 'border-yellow-500' : 'border-green-500'); ?>">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Días Restantes</p>
                    <p class="text-3xl font-bold <?php echo $daysRemaining <= 30 ? 'text-red-600' : ($daysRemaining <= 60 ? 'text-yellow-600' : 'text-green-600'); ?>">
                        <?php echo $daysRemaining; ?>
                    </p>
                </div>
                <div class="p-3 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">de 365 días de afiliación</p>
        </div>
        
        <!-- Membership Status -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 <?php echo ($currentAffiliation['status'] ?? '') === 'active' ? 'border-green-500' : 'border-gray-500'; ?>">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Estado Membresía</p>
                    <p class="text-lg font-bold text-gray-900">
                        <?php echo ($currentAffiliation['status'] ?? '') === 'active' ? 'Activa' : 'Inactiva'; ?>
                    </p>
                </div>
                <div class="p-3 <?php echo ($currentAffiliation['status'] ?? '') === 'active' ? 'bg-green-100' : 'bg-gray-100'; ?> rounded-full">
                    <svg class="w-6 h-6 <?php echo ($currentAffiliation['status'] ?? '') === 'active' ? 'text-green-600' : 'text-gray-600'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500"><?php echo htmlspecialchars($currentAffiliation['membership_name'] ?? 'Sin membresía'); ?></p>
        </div>
        
        <!-- Events Attended -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Eventos Asistidos</p>
                    <p class="text-3xl font-bold text-blue-600"><?php echo $attendanceCount; ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Total de asistencias</p>
        </div>
        
        <!-- Yearly Payments -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pagado (Año)</p>
                    <p class="text-2xl font-bold text-purple-600">$<?php echo number_format($yearlyPayments, 0); ?></p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Productos y servicios</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Company Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Company Profile -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🏢 Información de la Empresa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">RFC</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($contact['rfc'] ?? 'No registrado'); ?></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">Propietario / Rep. Legal</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($contact['owner_name'] ?? $contact['legal_representative'] ?? '-'); ?></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">Razón Social</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($contact['business_name'] ?? '-'); ?></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">Nombre Comercial</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($contact['commercial_name'] ?? '-'); ?></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg md:col-span-2">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">Dirección Comercial/Fiscal</label>
                        <p class="text-gray-900 font-medium">
                            <?php echo htmlspecialchars($contact['commercial_address'] ?? $contact['fiscal_address'] ?? '-'); ?>
                            <?php if (!empty($contact['city']) || !empty($contact['state'])): ?>
                            <br><span class="text-sm text-gray-600"><?php echo htmlspecialchars(($contact['city'] ?? '') . ', ' . ($contact['state'] ?? '')); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">WhatsApp</label>
                        <p class="text-gray-900 font-medium">
                            <?php if (!empty($contact['whatsapp'])): ?>
                            <a href="https://wa.me/52<?php echo preg_replace('/[^0-9]/', '', $contact['whatsapp']); ?>" target="_blank" class="text-green-600 hover:underline">
                                <?php echo htmlspecialchars($contact['whatsapp']); ?>
                            </a>
                            <?php else: ?>-<?php endif; ?>
                        </p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">Sitio Web / E-commerce</label>
                        <p class="text-gray-900 font-medium">
                            <?php if (!empty($contact['website'])): ?>
                            <a href="<?php echo htmlspecialchars($contact['website']); ?>" target="_blank" class="text-blue-600 hover:underline">
                                <?php echo htmlspecialchars(str_replace(['https://', 'http://', 'www.'], '', $contact['website'])); ?>
                            </a>
                            <?php else: ?>-<?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Products & Services -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📦 Productos y Servicios</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">🏷️ 4 Productos/Servicios que MÁS VENDE</h4>
                        <?php 
                        $productsSells = [];
                        if (!empty($contact['products_sells'])) {
                            $productsSells = json_decode($contact['products_sells'], true) ?: [];
                        }
                        ?>
                        <?php if (!empty($productsSells)): ?>
                        <div class="space-y-2">
                            <?php foreach (array_slice($productsSells, 0, 4) as $product): ?>
                            <div class="px-3 py-2 bg-green-50 text-green-800 rounded-lg text-sm">
                                <?php echo htmlspecialchars($product); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-gray-500 text-sm">No registrado</p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">🛒 2 Productos/Servicios que MÁS COMPRA</h4>
                        <?php 
                        $productsBuys = [];
                        if (!empty($contact['products_buys'])) {
                            $productsBuys = json_decode($contact['products_buys'], true) ?: [];
                        }
                        ?>
                        <?php if (!empty($productsBuys)): ?>
                        <div class="space-y-2">
                            <?php foreach (array_slice($productsBuys, 0, 2) as $product): ?>
                            <div class="px-3 py-2 bg-blue-50 text-blue-800 rounded-lg text-sm">
                                <?php echo htmlspecialchars($product); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-gray-500 text-sm">No registrado</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Membership Benefits -->
            <?php if ($membershipBenefits): ?>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🎁 Beneficios de la Membresía</h3>
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2"><?php echo htmlspecialchars($membershipBenefits['name']); ?></h4>
                    <?php if (!empty($membershipBenefits['benefits'])): ?>
                    <div class="text-sm text-gray-700">
                        <?php echo nl2br(htmlspecialchars($membershipBenefits['benefits'])); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($membershipBenefits['description'])): ?>
                    <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($membershipBenefits['description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Event Registrations & Attendance -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Registro de Eventos</h3>
                <?php if (empty($eventRegistrations)): ?>
                <p class="text-gray-500 text-center py-4">No hay registros de eventos</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asistió</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pago</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach (array_slice($eventRegistrations, 0, 10) as $reg): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($reg['event_title']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($reg['start_date'])); ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($reg['attended']): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✓ Sí</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($reg['payment_status'] === 'paid' || $reg['payment_status'] === 'free'): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        <?php echo $reg['payment_status'] === 'free' ? 'Cortesía' : 'Pagado'; ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Cross & Upselling Purchases -->
            <?php if (!empty($contracts)): ?>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💼 Compras Adicionales (Cross & Upselling)</h3>
                <div class="space-y-3">
                    <?php foreach ($contracts as $contract): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($contract['service_name'] ?? 'Servicio'); ?></p>
                            <p class="text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($contract['start_date'] ?? $contract['created_at'])); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">$<?php echo number_format($contract['amount'] ?? 0, 0); ?></p>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo ($contract['status'] ?? '') === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo ucfirst($contract['status'] ?? 'N/A'); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Activities / Actions History -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Historial de Acciones</h3>
                <?php if (empty($activities)): ?>
                <p class="text-gray-500 text-center py-4">No hay actividades registradas</p>
                <?php else: ?>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php foreach ($activities as $activity): ?>
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
                                - <?php echo ucfirst($activity['activity_type']); ?>
                            </p>
                            <?php if (!empty($activity['result'])): ?>
                            <p class="text-sm text-gray-700 mt-2 bg-white p-2 rounded">
                                <strong>Resultado:</strong> <?php echo htmlspecialchars($activity['result']); ?>
                            </p>
                            <?php endif; ?>
                            <?php if (!empty($activity['next_action'])): ?>
                            <p class="text-sm text-blue-700 mt-1">
                                <strong>Próxima acción:</strong> <?php echo htmlspecialchars($activity['next_action']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/agenda/nueva?contact_id=<?php echo $contact['id']; ?>" 
                   class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-800">
                    + Nueva Acción
                </a>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Affiliation Info -->
            <?php if ($currentAffiliation): ?>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Afiliación Actual</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Membresía</span>
                        <span class="font-medium text-gray-900"><?php echo htmlspecialchars($currentAffiliation['membership_name'] ?? '-'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Fecha Afiliación</span>
                        <span class="font-medium text-gray-900"><?php echo date('d/m/Y', strtotime($currentAffiliation['affiliation_date'])); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Vencimiento</span>
                        <span class="font-medium <?php echo $daysRemaining <= 30 ? 'text-red-600' : 'text-gray-900'; ?>">
                            <?php echo date('d/m/Y', strtotime($currentAffiliation['expiration_date'])); ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Pago</span>
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?php echo $currentAffiliation['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo $currentAffiliation['payment_status'] === 'paid' ? 'Pagado' : 'Pendiente'; ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Monto</span>
                        <span class="font-bold text-gray-900">$<?php echo number_format($currentAffiliation['amount'] ?? 0, 0); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Invoice History -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🧾 Historial de Facturas</h3>
                <?php if (empty($invoices)): ?>
                <p class="text-gray-500 text-center text-sm py-4">Sin facturas registradas</p>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($invoices as $invoice): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo date('d/m/Y', strtotime($invoice['affiliation_date'])); ?></p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?php echo ($invoice['invoice_status'] ?? '') === 'sent' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo ($invoice['invoice_status'] ?? '') === 'sent' ? 'Enviada' : 'Generada'; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Collaborators -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">👥 Colaboradores Asistentes</h3>
                <?php if (empty($collaborators)): ?>
                <p class="text-gray-500 text-center text-sm py-4">Sin colaboradores registrados</p>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($collaborators as $collab): ?>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-700 font-semibold text-sm">
                                <?php echo mb_substr($collab['owner_name'] ?? 'U', 0, 1, 'UTF-8'); ?>
                            </span>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($collab['owner_name'] ?? '-'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($collab['position'] ?? $collab['corporate_email'] ?? '-'); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Acciones Rápidas</h3>
                <div class="space-y-2">
                    <a href="<?php echo BASE_URL; ?>/agenda/nueva?contact_id=<?php echo $contact['id']; ?>" 
                       class="block w-full px-4 py-2 text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        📝 Documentar Acción
                    </a>
                    <?php if (!empty($contact['whatsapp'])): ?>
                    <a href="https://wa.me/52<?php echo preg_replace('/[^0-9]/', '', $contact['whatsapp']); ?>" target="_blank"
                       class="block w-full px-4 py-2 text-center bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                        💬 WhatsApp
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['corporate_email'])): ?>
                    <a href="mailto:<?php echo htmlspecialchars($contact['corporate_email']); ?>"
                       class="block w-full px-4 py-2 text-center bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                        📧 Enviar Email
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['phone'])): ?>
                    <a href="tel:<?php echo $contact['phone']; ?>"
                       class="block w-full px-4 py-2 text-center bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                        📞 Llamar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Web & Social -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🌐 Redes Sociales</h3>
                <div class="space-y-2">
                    <?php if (!empty($contact['website'])): ?>
                    <a href="<?php echo htmlspecialchars($contact['website']); ?>" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600 text-sm">
                        🌍 Sitio Web
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($contact['facebook']); ?>" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600 text-sm">
                        📘 Facebook
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($contact['instagram']); ?>" target="_blank" class="flex items-center text-gray-700 hover:text-pink-600 text-sm">
                        📸 Instagram
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['linkedin'])): ?>
                    <a href="<?php echo htmlspecialchars($contact['linkedin']); ?>" target="_blank" class="flex items-center text-gray-700 hover:text-blue-700 text-sm">
                        💼 LinkedIn
                    </a>
                    <?php endif; ?>
                    <?php if (empty($contact['website']) && empty($contact['facebook']) && empty($contact['instagram']) && empty($contact['linkedin'])): ?>
                    <p class="text-gray-500 text-sm">No hay redes registradas</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Affiliation History -->
    <?php if (count($affiliations) > 1): ?>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📜 Historial de Afiliaciones</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membresía</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Fin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($affiliations as $aff): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($aff['membership_name'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($aff['affiliation_date'])); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($aff['expiration_date'])); ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $aff['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $aff['status'] === 'active' ? 'Activa' : ucfirst($aff['status']); ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">$<?php echo number_format($aff['amount'] ?? 0, 0); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
