<!-- Expediente Digital Afiliado (EDA) - Stage A Edit (25%) -->
<?php 
// Determine person type based on current RFC
$currentRfc = $contact['rfc'] ?? '';
$rfcLen = strlen($currentRfc);
$personType = '';
if ($rfcLen === 13) {
    $personType = 'fisica';
} elseif ($rfcLen === 12) {
    $personType = 'moral';
}
?>
<div class="space-y-6" x-data="{ 
    rfc: '<?php echo htmlspecialchars($currentRfc); ?>',
    personType: '<?php echo $personType; ?>',
    updatePersonType() {
        const len = this.rfc.replace(/[^A-Za-z0-9]/g, '').length;
        if (len === 13) {
            this.personType = 'fisica';
        } else if (len === 12) {
            this.personType = 'moral';
        } else {
            this.personType = '';
        }
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $contact['id']; ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al Expediente
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                📁 Expediente Digital Afiliado - Etapa 1
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                <?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? 'Sin nombre'); ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium">
                Avance Etapa 1: <?php echo $stageA['percentage']; ?>% / 25%
            </span>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <!-- Person Type Indicator -->
    <div class="bg-white rounded-xl shadow-sm p-4" x-show="personType !== ''" x-cloak>
        <div class="flex items-center">
            <template x-if="personType === 'fisica'">
                <div class="flex items-center">
                    <span class="text-3xl mr-3">👤</span>
                    <div>
                        <p class="font-semibold text-blue-800">Persona Física</p>
                        <p class="text-sm text-gray-500">RFC de 13 caracteres - Dueño de empresa</p>
                    </div>
                </div>
            </template>
            <template x-if="personType === 'moral'">
                <div class="flex items-center">
                    <span class="text-3xl mr-3">🏢</span>
                    <div>
                        <p class="font-semibold text-purple-800">Persona Moral</p>
                        <p class="text-sm text-gray-500">RFC de 12 caracteres - Representante Legal</p>
                    </div>
                </div>
            </template>
        </div>
    </div>
    
    <!-- Progress Indicator -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Progreso del Expediente</h3>
            <span class="text-sm text-gray-500"><?php echo $stageA['completed']; ?>/<?php echo $stageA['total']; ?> campos completados</span>
        </div>
        
        <!-- Progress Steps -->
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">1</div>
                    <div class="flex-1 h-1 mx-2 bg-blue-600"></div>
                </div>
                <p class="text-xs text-blue-600 font-medium mt-1">Etapa 1 (25%)</p>
            </div>
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">2</div>
                    <div class="flex-1 h-1 mx-2 bg-gray-300"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Etapa 2 (35%)</p>
            </div>
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">3</div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Etapa 3 (40%)</p>
            </div>
        </div>
    </div>
    
    <!-- Stage A Form -->
    <form method="POST" class="bg-white rounded-xl shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">🏢 Información Básica de la Empresa</h3>
            <p class="text-sm text-gray-500">Estos datos conforman el 25% del expediente digital afiliado.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- RFC -->
            <div>
                <label for="rfc" class="block text-sm font-medium text-gray-700">
                    RFC
                    <?php if (!$stageA['fields']['rfc']): ?>
                    <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                    <?php else: ?>
                    <span class="text-green-600 text-xs ml-1">✓</span>
                    <?php endif; ?>
                </label>
                <input type="text" id="rfc" name="rfc" 
                       x-model="rfc"
                       @input="updatePersonType()"
                       value="<?php echo htmlspecialchars($contact['rfc'] ?? ''); ?>"
                       maxlength="13"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border uppercase"
                       placeholder="12 o 13 caracteres">
                <p class="text-xs text-gray-500 mt-1">
                    <span x-show="personType === 'fisica'" class="text-blue-600">Persona Física (13 caracteres)</span>
                    <span x-show="personType === 'moral'" class="text-purple-600">Persona Moral (12 caracteres)</span>
                    <span x-show="personType === ''">Ingrese RFC para detectar tipo de persona</span>
                </p>
            </div>
            
            <!-- Owner Name - Only for Persona Física -->
            <div x-show="personType !== 'moral'" x-cloak>
                <label for="owner_name" class="block text-sm font-medium text-gray-700">
                    Propietario / Dueño
                    <?php if (!$stageA['fields']['owner_name'] && $personType !== 'moral'): ?>
                    <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                    <?php elseif ($personType !== 'moral'): ?>
                    <span class="text-green-600 text-xs ml-1">✓</span>
                    <?php endif; ?>
                </label>
                <input type="text" id="owner_name" name="owner_name" 
                       value="<?php echo htmlspecialchars($contact['owner_name'] ?? ''); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                       placeholder="Nombre completo del dueño">
                <p class="text-xs text-gray-500 mt-1">Requerido para Persona Física</p>
            </div>
            
            <!-- Legal Representative - Only for Persona Moral -->
            <div x-show="personType === 'moral'" x-cloak>
                <label for="legal_representative" class="block text-sm font-medium text-gray-700">
                    Representante Legal
                    <?php if (!$stageA['fields']['owner_name'] && $personType === 'moral'): ?>
                    <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                    <?php elseif ($personType === 'moral'): ?>
                    <span class="text-green-600 text-xs ml-1">✓</span>
                    <?php endif; ?>
                </label>
                <input type="text" id="legal_representative" name="legal_representative" 
                       value="<?php echo htmlspecialchars($contact['legal_representative'] ?? ''); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                       placeholder="Nombre del representante legal">
                <p class="text-xs text-gray-500 mt-1">Requerido para Persona Moral (sin dueño)</p>
            </div>
            
            <!-- Business Name (Razón Social) -->
            <div>
                <label for="business_name" class="block text-sm font-medium text-gray-700">
                    Razón Social
                    <?php if (!$stageA['fields']['business_name']): ?>
                    <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                    <?php else: ?>
                    <span class="text-green-600 text-xs ml-1">✓</span>
                    <?php endif; ?>
                </label>
                <input type="text" id="business_name" name="business_name" required
                       value="<?php echo htmlspecialchars($contact['business_name'] ?? ''); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                       placeholder="Razón social de la empresa">
            </div>
            
            <!-- Commercial Name -->
            <div>
                <label for="commercial_name" class="block text-sm font-medium text-gray-700">
                    Nombre Comercial
                    <?php if (!$stageA['fields']['commercial_name']): ?>
                    <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                    <?php else: ?>
                    <span class="text-green-600 text-xs ml-1">✓</span>
                    <?php endif; ?>
                </label>
                <input type="text" id="commercial_name" name="commercial_name" 
                       value="<?php echo htmlspecialchars($contact['commercial_name'] ?? ''); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                       placeholder="Nombre comercial">
            </div>
            
            <!-- WhatsApp -->
            <div>
                <label for="whatsapp" class="block text-sm font-medium text-gray-700">
                    Número de WhatsApp
                    <?php if (!$stageA['fields']['whatsapp']): ?>
                    <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                    <?php else: ?>
                    <span class="text-green-600 text-xs ml-1">✓</span>
                    <?php endif; ?>
                </label>
                <input type="text" id="whatsapp" name="whatsapp" 
                       value="<?php echo htmlspecialchars($contact['whatsapp'] ?? ''); ?>"
                       maxlength="10" pattern="[0-9]{10}" title="Ingrese exactamente 10 dígitos"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                       placeholder="10 dígitos">
            </div>
            
            <!-- Commercial Address -->
            <div class="md:col-span-2">
                <label for="commercial_address" class="block text-sm font-medium text-gray-700">
                    Dirección Comercial
                    <?php if (!$stageA['fields']['commercial_address']): ?>
                    <span class="text-yellow-600 text-xs ml-1">* Pendiente</span>
                    <?php else: ?>
                    <span class="text-green-600 text-xs ml-1">✓</span>
                    <?php endif; ?>
                </label>
                <textarea id="commercial_address" name="commercial_address" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                          placeholder="Calle, número, colonia, ciudad, estado, CP"><?php echo htmlspecialchars($contact['commercial_address'] ?? ''); ?></textarea>
            </div>
            
            <!-- Fiscal Address -->
            <div class="md:col-span-2">
                <label for="fiscal_address" class="block text-sm font-medium text-gray-700">
                    Dirección Fiscal
                </label>
                <textarea id="fiscal_address" name="fiscal_address" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                          placeholder="Dirección fiscal (si es diferente a la comercial)"><?php echo htmlspecialchars($contact['fiscal_address'] ?? ''); ?></textarea>
            </div>
        </div>
        
        <!-- Progress Legend -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-800">Avance de esta etapa: <?php echo $stageA['percentage']; ?>% de 25%</p>
                    <p class="text-xs text-blue-600 mt-1">Complete todos los campos para avanzar al 25% del expediente</p>
                </div>
                <div class="w-24 bg-blue-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full" style="width: <?php echo ($stageA['percentage'] / 25) * 100; ?>%"></div>
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
