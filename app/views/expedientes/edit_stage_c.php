<!-- Expediente Digital Único - Stage C Edit (40%) -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al Expediente
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                📁 Expediente Digital Único - Etapa 3
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                <?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? 'Sin nombre'); ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium">
                Avance Etapa 3: <?php echo $stageC['percentage']; ?>% / 40%
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
            <span class="text-sm text-gray-500"><?php echo $stageC['completed']; ?>/<?php echo $stageC['total']; ?> campos completados</span>
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
                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold">✓</div>
                    <div class="flex-1 h-1 mx-2 bg-green-600"></div>
                </div>
                <p class="text-xs text-green-600 font-medium mt-1">Etapa 2 (35%)</p>
            </div>
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">3</div>
                </div>
                <p class="text-xs text-blue-600 font-medium mt-1">Etapa 3 (40%)</p>
            </div>
        </div>
    </div>
    
    <!-- Stage C Form -->
    <form method="POST" class="bg-white rounded-xl shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">📋 Afiliación y Documentos</h3>
            <p class="text-sm text-gray-500">Estos datos conforman el 40% del expediente digital único.</p>
        </div>
        
        <!-- Affiliation Information -->
        <div class="mb-8 p-4 border border-gray-200 rounded-lg">
            <h4 class="text-md font-semibold text-gray-800 mb-4">📅 Información de Afiliación</h4>
            
            <?php if ($currentAffiliation): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Fecha de Afiliación
                        <?php if ($stageC['fields']['affiliation_date']): ?>
                        <span class="text-green-600 text-xs ml-1">✓</span>
                        <?php endif; ?>
                    </label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-300 text-gray-900">
                        <?php echo date('d/m/Y', strtotime($currentAffiliation['affiliation_date'])); ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-300 text-gray-900">
                        <?php echo date('d/m/Y', strtotime($currentAffiliation['expiration_date'])); ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Membresía</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-300 text-gray-900">
                        <?php echo htmlspecialchars($currentAffiliation['membership_name'] ?? '-'); ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-yellow-700">No hay afiliación activa registrada. Primero debe crear una afiliación.</p>
                <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>/editar" 
                   class="mt-2 inline-block text-blue-600 hover:underline">Ir a editar afiliado →</a>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- CSF / Invoice Section -->
        <div class="mb-8 p-4 border border-gray-200 rounded-lg">
            <h4 class="text-md font-semibold text-gray-800 mb-4">
                🧾 CSF / Factura
                <?php if (!$stageC['fields']['invoice_or_csf']): ?>
                <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                <?php else: ?>
                <span class="text-green-600 text-xs ml-1">✓</span>
                <?php endif; ?>
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700">
                        Número de Factura / CSF
                    </label>
                    <input type="text" id="invoice_number" name="invoice_number" 
                           value="<?php echo htmlspecialchars($currentAffiliation['invoice_number'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           placeholder="Ej: FAC-2024-001">
                    <p class="mt-1 text-xs text-gray-500">Ingrese el número de factura o la Constancia de Situación Fiscal (CSF)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado de Facturación</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-300">
                        <span class="px-3 py-1 text-sm rounded-full <?php echo ($currentAffiliation['invoice_status'] ?? '') === 'invoiced' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php 
                            $invoiceStatusText = 'Pendiente';
                            if (($currentAffiliation['invoice_status'] ?? '') === 'invoiced') $invoiceStatusText = 'Facturado';
                            if (($currentAffiliation['invoice_status'] ?? '') === 'not_required') $invoiceStatusText = 'No requerido';
                            echo $invoiceStatusText;
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Services of Interest Section (Optional) -->
        <div class="mb-8 p-4 border border-gray-200 rounded-lg">
            <h4 class="text-md font-semibold text-gray-800 mb-2">🎯 Servicios de la Cámara que más le interesan</h4>
            <p class="text-xs text-gray-500 mb-4">(Opcional) Seleccione los servicios de interés para el afiliado</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php 
                $serviceCategories = [
                    'capacitacion' => '📚 Capacitación y Cursos',
                    'networking' => '🤝 Eventos de Networking',
                    'asesoria' => '💼 Asesoría Empresarial',
                    'marketing' => '📢 Marketing y Publicidad',
                    'gestoria' => '📋 Gestoría y Trámites',
                    'salones' => '🏢 Renta de Salones',
                    'financiamiento' => '💰 Financiamiento',
                    'exportacion' => '🌍 Comercio Exterior',
                    'legal' => '⚖️ Asesoría Legal'
                ];
                foreach ($serviceCategories as $key => $label): 
                ?>
                <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                    <input type="checkbox" name="services_interest[]" value="<?php echo $key; ?>"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700"><?php echo $label; ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Notes Section -->
        <div class="mb-8">
            <label for="affiliation_notes" class="block text-sm font-medium text-gray-700 mb-2">Notas Adicionales</label>
            <textarea id="affiliation_notes" name="affiliation_notes" rows="3"
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                      placeholder="Notas sobre la afiliación, comentarios adicionales..."><?php echo htmlspecialchars($currentAffiliation['notes'] ?? ''); ?></textarea>
        </div>
        
        <!-- Progress Legend -->
        <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-800">¡Última etapa! Avance: <?php echo $stageC['percentage']; ?>% de 40%</p>
                    <p class="text-xs text-green-600 mt-1">Complete esta etapa para alcanzar el 100% del expediente digital único</p>
                </div>
                <div class="w-24 bg-green-200 rounded-full h-3">
                    <div class="bg-green-600 h-3 rounded-full" style="width: <?php echo ($stageC['percentage'] / 40) * 100; ?>%"></div>
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
                <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>/etapa-b" 
                   class="w-full sm:w-auto px-6 py-3 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    Etapa Anterior
                </a>
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Completar Expediente
                </button>
            </div>
        </div>
    </form>
    
    <!-- Edit Data Button -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-lg font-semibold text-gray-900">✏️ Editar Datos del Afiliado</h4>
                <p class="text-sm text-gray-500">Modifique cualquier información del expediente completo</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>/editar" 
               class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Editar Datos Completos
            </a>
        </div>
    </div>
</div>
