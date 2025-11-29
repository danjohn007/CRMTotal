<!-- Expediente Digital Único - Stage B Edit (35%) -->
<div class="space-y-6" x-data="{ sameAsOwner: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al Expediente
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                📁 Expediente Digital Único - Etapa 2
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                <?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? 'Sin nombre'); ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium">
                Avance Etapa 2: <?php echo $stageB['percentage']; ?>% / 35%
            </span>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <!-- Progress Indicator -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Progreso del Expediente</h3>
            <span class="text-sm text-gray-500"><?php echo $stageB['completed']; ?>/<?php echo $stageB['total']; ?> campos completados</span>
        </div>
        
        <!-- Progress Steps -->
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold">✓</div>
                    <div class="flex-1 h-1 mx-2 bg-green-600"></div>
                </div>
                <p class="text-xs text-green-600 font-medium mt-1">Etapa 1 (25%)</p>
            </div>
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">2</div>
                    <div class="flex-1 h-1 mx-2 bg-blue-600"></div>
                </div>
                <p class="text-xs text-blue-600 font-medium mt-1">Etapa 2 (35%)</p>
            </div>
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">3</div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Etapa 3 (40%)</p>
            </div>
        </div>
    </div>
    
    <!-- Stage B Form -->
    <form method="POST" class="bg-white rounded-xl shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">📞 Datos de Contacto y Productos</h3>
            <p class="text-sm text-gray-500">Estos datos conforman el 35% del expediente digital único.</p>
        </div>
        
        <!-- Sales Contact Section -->
        <div class="mb-8 p-4 border border-gray-200 rounded-lg">
            <h4 class="text-md font-semibold text-gray-800 mb-4">👤 Datos de Contacto - Personal de Ventas</h4>
            
            <!-- Same as Owner Checkbox -->
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="sales_same_as_owner" value="1" x-model="sameAsOwner"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">¿Son los mismos datos que el representante legal / propietario?</span>
                </label>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-show="!sameAsOwner">
                <div>
                    <label for="sales_contact_name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="sales_contact_name" name="sales_contact_name" 
                           value="<?php echo htmlspecialchars($contact['owner_name'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="Nombre del contacto de ventas">
                </div>
                <div>
                    <label for="sales_contact_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="sales_contact_email" name="sales_contact_email" 
                           value="<?php echo htmlspecialchars($contact['corporate_email'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="email@empresa.com">
                </div>
                <div>
                    <label for="whatsapp_sales" class="block text-sm font-medium text-gray-700">
                        WhatsApp Ventas
                        <?php if (!$stageB['fields']['whatsapp_sales']): ?>
                        <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                        <?php else: ?>
                        <span class="text-green-600 text-xs ml-1">✓</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="whatsapp_sales" name="whatsapp_sales" 
                           value="<?php echo htmlspecialchars($contact['whatsapp_sales'] ?? ''); ?>"
                           maxlength="10" pattern="[0-9]{10}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="10 dígitos">
                </div>
            </div>
            
            <div x-show="sameAsOwner" class="p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">Se usarán los datos del propietario: <strong><?php echo htmlspecialchars($contact['owner_name'] ?? $contact['legal_representative'] ?? '-'); ?></strong></p>
            </div>
        </div>
        
        <!-- Purchasing Contact Section -->
        <div class="mb-8 p-4 border border-gray-200 rounded-lg">
            <h4 class="text-md font-semibold text-gray-800 mb-4">🛒 Datos de Contacto - Personal de Compras</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="purchases_contact_name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="purchases_contact_name" name="purchases_contact_name" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="Nombre del contacto de compras">
                </div>
                <div>
                    <label for="purchases_contact_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="purchases_contact_email" name="purchases_contact_email" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="email@empresa.com">
                </div>
                <div>
                    <label for="whatsapp_purchases" class="block text-sm font-medium text-gray-700">
                        WhatsApp Compras
                        <?php if (!$stageB['fields']['whatsapp_purchases']): ?>
                        <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                        <?php else: ?>
                        <span class="text-green-600 text-xs ml-1">✓</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="whatsapp_purchases" name="whatsapp_purchases" 
                           value="<?php echo htmlspecialchars($contact['whatsapp_purchases'] ?? ''); ?>"
                           maxlength="10" pattern="[0-9]{10}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="10 dígitos">
                </div>
            </div>
        </div>
        
        <!-- Branches Section -->
        <div class="mb-8 p-4 border border-gray-200 rounded-lg">
            <h4 class="text-md font-semibold text-gray-800 mb-4">🏪 Sucursales</h4>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="has_branches" value="1"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">¿Cuenta con sucursales?</span>
                </label>
            </div>
            
            <?php if (!empty($branches)): ?>
            <div class="space-y-2">
                <?php foreach ($branches as $branch): ?>
                <div class="p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($branch['name']); ?></p>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($branch['address']); ?></p>
                    </div>
                    <?php if ($branch['is_main']): ?>
                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Principal</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-500">No hay sucursales registradas</p>
            <?php endif; ?>
        </div>
        
        <!-- Website Section -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700">
                        Sitio Web / E-commerce
                        <?php if (!$stageB['fields']['website']): ?>
                        <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                        <?php else: ?>
                        <span class="text-green-600 text-xs ml-1">✓</span>
                        <?php endif; ?>
                    </label>
                    <input type="url" id="website" name="website" 
                           value="<?php echo htmlspecialchars($contact['website'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="https://www.ejemplo.com">
                </div>
            </div>
        </div>
        
        <!-- Products Sells Section -->
        <div class="mb-8 p-4 border border-gray-200 rounded-lg">
            <h4 class="text-md font-semibold text-gray-800 mb-4">
                🏷️ 4 Productos o Servicios que MÁS VENDE
                <?php if (!$stageB['fields']['products_sells']): ?>
                <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                <?php else: ?>
                <span class="text-green-600 text-xs ml-1">✓</span>
                <?php endif; ?>
            </h4>
            
            <?php 
            $productsSells = [];
            if (!empty($contact['products_sells'])) {
                $productsSells = json_decode($contact['products_sells'], true) ?: [];
            }
            ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Producto/Servicio 1</label>
                    <input type="text" name="products_sells[]" 
                           value="<?php echo htmlspecialchars($productsSells[0] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="Ej: Consultoría empresarial">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Producto/Servicio 2</label>
                    <input type="text" name="products_sells[]" 
                           value="<?php echo htmlspecialchars($productsSells[1] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="Ej: Software personalizado">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Producto/Servicio 3</label>
                    <input type="text" name="products_sells[]" 
                           value="<?php echo htmlspecialchars($productsSells[2] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="Ej: Capacitación">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Producto/Servicio 4</label>
                    <input type="text" name="products_sells[]" 
                           value="<?php echo htmlspecialchars($productsSells[3] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="Ej: Soporte técnico">
                </div>
            </div>
        </div>
        
        <!-- Products Buys Section -->
        <div class="mb-8 p-4 border border-gray-200 rounded-lg">
            <h4 class="text-md font-semibold text-gray-800 mb-4">
                🛒 2 Productos o Servicios que MÁS COMPRA
                <?php if (!$stageB['fields']['products_buys']): ?>
                <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                <?php else: ?>
                <span class="text-green-600 text-xs ml-1">✓</span>
                <?php endif; ?>
            </h4>
            
            <?php 
            $productsBuys = [];
            if (!empty($contact['products_buys'])) {
                $productsBuys = json_decode($contact['products_buys'], true) ?: [];
            }
            ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Producto/Servicio 1</label>
                    <input type="text" name="products_buys[]" 
                           value="<?php echo htmlspecialchars($productsBuys[0] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="Ej: Insumos de oficina">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Producto/Servicio 2</label>
                    <input type="text" name="products_buys[]" 
                           value="<?php echo htmlspecialchars($productsBuys[1] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="Ej: Servicios de transporte">
                </div>
            </div>
        </div>
        
        <!-- Progress Legend -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-800">Avance de esta etapa: <?php echo $stageB['percentage']; ?>% de 35%</p>
                    <p class="text-xs text-blue-600 mt-1">Complete todos los campos para alcanzar el 60% del expediente (25% + 35%)</p>
                </div>
                <div class="w-24 bg-blue-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full" style="width: <?php echo ($stageB['percentage'] / 35) * 100; ?>%"></div>
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>" 
               class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                Cancelar
            </a>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>/etapa-a" 
                   class="w-full sm:w-auto px-6 py-3 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    Etapa Anterior
                </a>
                <button type="submit" name="save_only" value="1"
                        class="w-full sm:w-auto px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Guardar
                </button>
                <button type="submit" name="next_stage" value="1"
                        class="w-full sm:w-auto px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                    Guardar y Continuar
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>
        </div>
    </form>
</div>
