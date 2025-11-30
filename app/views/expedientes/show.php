<!-- Expediente Digital Afiliado (EDA) - Show View with Modal -->
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/expedientes" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver a Expedientes
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                📁 Expediente Digital Afiliado (EDA)
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                <?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? 'Sin nombre'); ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <button @click="showModal = true" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                📊 Ver Resumen Afiliación
            </button>
            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>/editar" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                ✏️ Editar Datos
            </a>
        </div>
    </div>
    
    <!-- Days Remaining Alert Banner -->
    <?php if ($daysRemaining <= 30 && $daysRemaining > 0): ?>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>¡Atención!</strong> La afiliación vence en <strong><?php echo $daysRemaining; ?> días</strong>. 
                    <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>" class="underline font-medium">Ver detalles de renovación →</a>
                </p>
            </div>
        </div>
    </div>
    <?php elseif ($daysRemaining <= 0): ?>
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    <strong>¡Membresía Vencida!</strong> La afiliación ha expirado. 
                    <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>" class="underline font-medium">Gestionar renovación →</a>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Person Type & NIZA Classification -->
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
    <div class="flex flex-wrap gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center">
            <span class="text-2xl mr-3"><?php echo $personType === 'fisica' ? '👤' : '🏢'; ?></span>
            <div>
                <p class="text-sm font-medium text-gray-900"><?php echo $personTypeLabel; ?></p>
                <p class="text-xs text-gray-500"><?php echo $personTypeDesc; ?></p>
            </div>
        </div>
        <?php if (!empty($contact['niza_classification'])): ?>
        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center">
            <span class="text-2xl mr-3">🏷️</span>
            <div>
                <p class="text-sm font-medium text-gray-900">Clasificación NIZA: <?php echo htmlspecialchars($contact['niza_classification']); ?></p>
                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($contact['industry'] ?? 'Sin industria'); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Progress Overview -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Avance del Expediente Digital Afiliado</h3>
            <span class="text-2xl font-bold <?php echo ($contact['profile_completion'] ?? 0) === 100 ? 'text-green-600' : 'text-yellow-600'; ?>">
                <?php echo $contact['profile_completion'] ?? 0; ?>%
            </span>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-4 rounded-full transition-all duration-500" 
                 style="width: <?php echo $contact['profile_completion'] ?? 0; ?>%"></div>
        </div>
        
        <!-- Stage Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Stage A (25%) -->
            <div class="p-4 rounded-lg border-2 <?php echo $stageA['stage_complete'] ? 'border-green-500 bg-green-50' : 'border-yellow-500 bg-yellow-50'; ?>">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-700">Etapa 1</span>
                    <span class="text-sm font-bold <?php echo $stageA['stage_complete'] ? 'text-green-600' : 'text-yellow-600'; ?>">
                        <?php echo $stageA['percentage']; ?>% / 25%
                    </span>
                </div>
                <p class="text-xs text-gray-600 mb-3">RFC, Propietario, Razón Social, Nombre Comercial, Dirección, WhatsApp</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500"><?php echo $stageA['completed']; ?>/<?php echo $stageA['total']; ?> campos</span>
                    <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>/etapa-a" 
                       class="text-sm font-medium <?php echo $stageA['stage_complete'] ? 'text-green-600' : 'text-yellow-600'; ?> hover:underline">
                        <?php echo $stageA['stage_complete'] ? '✓ Completado' : 'Completar →'; ?>
                    </a>
                </div>
            </div>
            
            <!-- Stage B (35%) -->
            <div class="p-4 rounded-lg border-2 <?php echo $stageB['stage_complete'] ? 'border-green-500 bg-green-50' : 'border-yellow-500 bg-yellow-50'; ?>">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-700">Etapa 2</span>
                    <span class="text-sm font-bold <?php echo $stageB['stage_complete'] ? 'text-green-600' : 'text-yellow-600'; ?>">
                        <?php echo $stageB['percentage']; ?>% / 35%
                    </span>
                </div>
                <p class="text-xs text-gray-600 mb-3">Contacto Ventas, Contacto Compras, Sucursales, Web, Productos</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500"><?php echo $stageB['completed']; ?>/<?php echo $stageB['total']; ?> campos</span>
                    <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>/etapa-b" 
                       class="text-sm font-medium <?php echo $stageB['stage_complete'] ? 'text-green-600' : 'text-yellow-600'; ?> hover:underline">
                        <?php echo $stageB['stage_complete'] ? '✓ Completado' : 'Completar →'; ?>
                    </a>
                </div>
            </div>
            
            <!-- Stage C (40%) -->
            <div class="p-4 rounded-lg border-2 <?php echo $stageC['stage_complete'] ? 'border-green-500 bg-green-50' : 'border-yellow-500 bg-yellow-50'; ?>">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-700">Etapa 3</span>
                    <span class="text-sm font-bold <?php echo $stageC['stage_complete'] ? 'text-green-600' : 'text-yellow-600'; ?>">
                        <?php echo $stageC['percentage']; ?>% / 40%
                    </span>
                </div>
                <p class="text-xs text-gray-600 mb-3">Fecha Afiliación, CSF/Factura, Servicios de Interés</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500"><?php echo $stageC['completed']; ?>/<?php echo $stageC['total']; ?> campos</span>
                    <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>/etapa-c" 
                       class="text-sm font-medium <?php echo $stageC['stage_complete'] ? 'text-green-600' : 'text-yellow-600'; ?> hover:underline">
                        <?php echo $stageC['stage_complete'] ? '✓ Completado' : 'Completar →'; ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Call to Action -->
        <?php 
        $nextStage = '';
        if (!$stageA['stage_complete']) $nextStage = 'etapa-a';
        elseif (!$stageB['stage_complete']) $nextStage = 'etapa-b';
        elseif (!$stageC['stage_complete']) $nextStage = 'etapa-c';
        ?>
        <?php if ($nextStage): ?>
        <div class="mt-6 flex justify-end">
            <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>/<?php echo $nextStage; ?>" 
               class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                Continuar con el EDA
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Company Information (Stage A) -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">🏢 Información de la Empresa (Etapa 1 - 25%)</h3>
                    <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>/etapa-a" class="text-blue-600 hover:text-blue-800 text-sm">Editar</a>
                </div>
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
                        </p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">WhatsApp</label>
                        <p class="text-gray-900 font-medium">
                            <?php 
                            $whatsappNumber = preg_replace('/[^0-9]/', '', $contact['whatsapp'] ?? '');
                            if (!empty($whatsappNumber) && strlen($whatsappNumber) === 10): 
                            ?>
                            <a href="https://wa.me/52<?php echo htmlspecialchars($whatsappNumber); ?>" target="_blank" class="text-green-600 hover:underline">
                                <?php echo htmlspecialchars($contact['whatsapp']); ?>
                            </a>
                            <?php elseif (!empty($contact['whatsapp'])): ?>
                            <span class="text-gray-900"><?php echo htmlspecialchars($contact['whatsapp']); ?></span>
                            <?php else: ?>-<?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Contact & Products (Stage B) -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">📞 Contactos y Productos (Etapa 2 - 35%)</h3>
                    <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>/etapa-b" class="text-blue-600 hover:text-blue-800 text-sm">Editar</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">WhatsApp Ventas</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($contact['whatsapp_sales'] ?? '-'); ?></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">WhatsApp Compras</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($contact['whatsapp_purchases'] ?? '-'); ?></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">Sitio Web / E-commerce</label>
                        <p class="text-gray-900 font-medium">
                            <?php if (!empty($contact['website'])): ?>
                            <a href="<?php echo htmlspecialchars($contact['website']); ?>" target="_blank" class="text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($contact['website']); ?>
                            </a>
                            <?php else: ?>-<?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Products -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
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
            
            <!-- Affiliation Info (Stage C) -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">📋 Afiliación y Documentos (Etapa 3 - 40%)</h3>
                    <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>/etapa-c" class="text-blue-600 hover:text-blue-800 text-sm">Editar</a>
                </div>
                <?php if ($currentAffiliation): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">Fecha de Afiliación</label>
                        <p class="text-gray-900 font-medium"><?php echo date('d/m/Y', strtotime($currentAffiliation['affiliation_date'])); ?></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">Fecha de Vencimiento</label>
                        <p class="text-gray-900 font-medium"><?php echo date('d/m/Y', strtotime($currentAffiliation['expiration_date'])); ?></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">CSF / No. Factura</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($currentAffiliation['invoice_number'] ?? 'No registrado'); ?></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-500 uppercase tracking-wider">Estado de Pago</label>
                        <span class="px-2 py-1 text-xs rounded-full <?php echo $currentAffiliation['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo $currentAffiliation['payment_status'] === 'paid' ? 'Pagado' : 'Pendiente'; ?>
                        </span>
                    </div>
                </div>
                <?php else: ?>
                <p class="text-gray-500">No hay afiliación activa</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Days Remaining Card -->
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
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Acciones Rápidas</h3>
                <div class="space-y-2">
                    <?php 
                    $quickWhatsapp = preg_replace('/[^0-9]/', '', $contact['whatsapp'] ?? '');
                    if (!empty($quickWhatsapp) && strlen($quickWhatsapp) === 10): 
                    ?>
                    <a href="https://wa.me/52<?php echo htmlspecialchars($quickWhatsapp); ?>" target="_blank"
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
                    <a href="<?php echo BASE_URL; ?>/agenda/nueva?contact_id=<?php echo $contact['id']; ?>" 
                       class="block w-full px-4 py-2 text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        📝 Agendar Actividad
                    </a>
                </div>
            </div>
            
            <!-- Event Attendance -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Eventos</h3>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-gray-500 text-sm">Eventos Asistidos</span>
                    <span class="text-2xl font-bold text-blue-600"><?php echo $attendanceCount; ?></span>
                </div>
                <?php if (!empty($eventRegistrations)): ?>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <?php foreach (array_slice($eventRegistrations, 0, 5) as $reg): ?>
                    <div class="p-2 bg-gray-50 rounded text-sm">
                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($reg['event_title']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo date('d/m/Y', strtotime($reg['start_date'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-sm text-center">Sin registros de eventos</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal: Affiliation Summary -->
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 transition-opacity" @click="showModal = false">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>
            
            <!-- Modal Content -->
            <div class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-blue-600">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">
                            📊 Resumen de Afiliación
                        </h3>
                        <button @click="showModal = false" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-indigo-100"><?php echo htmlspecialchars($contact['business_name'] ?? ''); ?></p>
                </div>
                
                <!-- Modal Body -->
                <div class="px-6 py-4 space-y-6 max-h-96 overflow-y-auto">
                    <!-- Days Remaining -->
                    <div class="flex items-center justify-between p-4 rounded-lg <?php echo $daysRemaining <= 30 ? 'bg-red-50' : ($daysRemaining <= 60 ? 'bg-yellow-50' : 'bg-green-50'); ?>">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Días Restantes de Afiliación</p>
                            <p class="text-xs text-gray-500">Fecha afiliación (365) - hoy</p>
                        </div>
                        <span class="text-3xl font-bold <?php echo $daysRemaining <= 30 ? 'text-red-600' : ($daysRemaining <= 60 ? 'text-yellow-600' : 'text-green-600'); ?>">
                            <?php echo $daysRemaining; ?> días
                        </span>
                    </div>
                    
                    <!-- Membership Status -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-2">Membresía Pagada</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-semibold text-gray-900">
                                <?php echo htmlspecialchars($currentAffiliation['membership_name'] ?? 'Sin membresía'); ?>
                            </span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo ($currentAffiliation['payment_status'] ?? '') === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo ($currentAffiliation['payment_status'] ?? '') === 'paid' ? '✓ Pagado' : '⏳ Pendiente'; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Benefits -->
                    <?php if ($membershipBenefits): ?>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-2">Beneficios Disponibles</p>
                        <?php 
                        $benefits = [];
                        if (!empty($membershipBenefits['benefits'])) {
                            $benefits = json_decode($membershipBenefits['benefits'], true) ?: [];
                        }
                        ?>
                        <?php if (!empty($benefits)): ?>
                        <ul class="space-y-1">
                            <?php foreach ($benefits as $key => $value): ?>
                            <li class="flex items-center text-sm text-gray-700">
                                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?>: 
                                <?php echo is_bool($value) ? ($value ? 'Sí' : 'No') : $value; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Benefit Usage -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-2">Beneficios Enviados/Recibidos</p>
                        <span class="text-2xl font-bold text-indigo-600"><?php echo count($benefitUsage); ?></span>
                        <span class="text-sm text-gray-500 ml-1">registros</span>
                    </div>
                    
                    <!-- Event Attendance -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-2">Registro de Eventos / Asistencia</p>
                        <div class="flex items-center space-x-4">
                            <div>
                                <span class="text-2xl font-bold text-blue-600"><?php echo count($eventRegistrations); ?></span>
                                <span class="text-sm text-gray-500 ml-1">registros</span>
                            </div>
                            <div>
                                <span class="text-2xl font-bold text-green-600"><?php echo $attendanceCount; ?></span>
                                <span class="text-sm text-gray-500 ml-1">asistencias</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cross & Upselling -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-2">Compras Adicionales (Cross & Upselling)</p>
                        <span class="text-2xl font-bold text-purple-600"><?php echo count($contracts); ?></span>
                        <span class="text-sm text-gray-500 ml-1">servicios contratados</span>
                        <?php if (!empty($contracts)): ?>
                        <div class="mt-2 space-y-1">
                            <?php foreach (array_slice($contracts, 0, 3) as $contract): ?>
                            <div class="text-sm text-gray-600">
                                • <?php echo htmlspecialchars($contract['service_name'] ?? 'Servicio'); ?> 
                                ($<?php echo number_format($contract['amount'] ?? 0, 0); ?>)
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 flex justify-end">
                    <button @click="showModal = false" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
