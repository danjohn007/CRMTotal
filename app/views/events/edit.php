<!-- Event Edit View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Editar Evento</h2>
            <p class="mt-1 text-sm text-gray-500">Modifica la información del evento</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>" class="text-blue-600 hover:text-blue-800">
            ← Volver al Evento
        </a>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <!-- Edit Form -->
    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <!-- Basic Info -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Información del Evento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700">Título del Evento *</label>
                    <input type="text" id="title" name="title" required
                           value="<?php echo htmlspecialchars($event['title']); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="description" name="description" rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
                </div>
                
                <div>
                    <label for="event_type" class="block text-sm font-medium text-gray-700">Tipo de Evento *</label>
                    <select id="event_type" name="event_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <?php foreach ($eventTypes as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo $event['event_type'] === $value ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Categoría</label>
                    <input type="text" id="category" name="category" 
                           value="<?php echo htmlspecialchars($event['category'] ?? ''); ?>"
                           placeholder="Ej: Networking, Capacitación, Conferencia"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="draft" <?php echo $event['status'] === 'draft' ? 'selected' : ''; ?>>Borrador</option>
                        <option value="published" <?php echo $event['status'] === 'published' ? 'selected' : ''; ?>>Publicado</option>
                        <option value="cancelled" <?php echo $event['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                        <option value="completed" <?php echo $event['status'] === 'completed' ? 'selected' : ''; ?>>Completado</option>
                    </select>
                </div>
                
                <div>
                    <label for="max_capacity" class="block text-sm font-medium text-gray-700">Capacidad Máxima</label>
                    <input type="number" id="max_capacity" name="max_capacity" min="0"
                           value="<?php echo $event['max_capacity']; ?>"
                           placeholder="0 = Sin límite"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Image Upload -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Imagen del Evento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Adjuntar Imagen</label>
                    <input type="file" id="image" name="image" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB</p>
                </div>
                <?php if (!empty($event['image'])): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Imagen Actual</label>
                    <img src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($event['image']); ?>" 
                         alt="Imagen actual" class="h-32 w-auto rounded-lg shadow-sm">
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Friendly URL -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">URL Pública</h3>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="registration_url" class="block text-sm font-medium text-gray-700">URL Amigable</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            <?php echo BASE_URL; ?>/evento/
                        </span>
                        <input type="text" id="registration_url" name="registration_url"
                               value="<?php echo htmlspecialchars($event['registration_url'] ?? ''); ?>"
                               pattern="[a-z0-9\-]+"
                               class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Solo letras minúsculas, números y guiones. Ejemplo: mi-evento-2025</p>
                </div>
            </div>
        </div>
        
        <!-- Date & Time -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Fecha y Hora</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha y Hora de Inicio *</label>
                    <input type="datetime-local" id="start_date" name="start_date" required
                           value="<?php echo date('Y-m-d\TH:i', strtotime($event['start_date'])); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha y Hora de Fin *</label>
                    <input type="datetime-local" id="end_date" name="end_date" required
                           value="<?php echo date('Y-m-d\TH:i', strtotime($event['end_date'])); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Location -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Ubicación</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" id="is_online" name="is_online" value="1"
                               <?php echo $event['is_online'] ? 'checked' : ''; ?>
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Evento en línea</span>
                    </label>
                </div>
                
                <div id="physical-location" class="<?php echo $event['is_online'] ? 'hidden' : ''; ?>">
                    <label for="location" class="block text-sm font-medium text-gray-700">Lugar</label>
                    <input type="text" id="location" name="location" 
                           value="<?php echo htmlspecialchars($event['location'] ?? ''); ?>"
                           placeholder="Ej: Salón Principal CCQ"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div id="physical-address" class="<?php echo $event['is_online'] ? 'hidden' : ''; ?>">
                    <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                    <input type="text" id="address" name="address"
                           value="<?php echo htmlspecialchars($event['address'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div id="online-url" class="<?php echo $event['is_online'] ? '' : 'hidden'; ?> md:col-span-2">
                    <label for="online_url" class="block text-sm font-medium text-gray-700">URL del Evento Online</label>
                    <input type="url" id="online_url" name="online_url" 
                           value="<?php echo htmlspecialchars($event['online_url'] ?? ''); ?>"
                           placeholder="https://zoom.us/..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div class="<?php echo $event['is_online'] ? 'hidden' : ''; ?>">
                    <label for="google_maps_url" class="block text-sm font-medium text-gray-700">URL Google Maps</label>
                    <input type="url" id="google_maps_url" name="google_maps_url"
                           value="<?php echo htmlspecialchars($event['google_maps_url'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Pricing -->
        <div class="mb-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b flex items-center">
                <span class="text-green-600 mr-2">$</span> Configuración de Precios
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="flex items-center mb-4">
                        <input type="checkbox" id="is_paid" name="is_paid" value="1"
                               <?php echo $event['is_paid'] ? 'checked' : ''; ?>
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Evento de pago</span>
                    </label>
                </div>
            </div>
            
            <div id="pricing-fields" class="<?php echo $event['is_paid'] ? '' : 'hidden'; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Costo del Evento (MXN)</label>
                        <input type="number" id="price" name="price" min="0" step="0.01"
                               value="<?php echo $event['price'] ?? 0; ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <p class="mt-1 text-xs text-gray-500">Precio regular del boleto</p>
                    </div>
                    
                    <div>
                        <label for="promo_price" class="block text-sm font-medium text-gray-700">Precio de Preventa (MXN)</label>
                        <input type="number" id="promo_price" name="promo_price" min="0" step="0.01"
                               value="<?php echo $event['promo_price'] ?? 0; ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <p class="mt-1 text-xs text-gray-500">Precio especial para público general hasta la fecha límite</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="member_price" class="block text-sm font-medium text-gray-700">Precio Afiliados (MXN)</label>
                        <input type="number" id="member_price" name="member_price" min="0" step="0.01"
                               value="<?php echo $event['member_price'] ?? 0; ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <p class="mt-1 text-xs text-gray-500">Precio especial para afiliados vigentes</p>
                    </div>
                    
                    <div>
                        <label for="promo_member_price" class="block text-sm font-medium text-gray-700">Precio Preventa Afiliado (MXN)</label>
                        <input type="number" id="promo_member_price" name="promo_member_price" min="0" step="0.01"
                               value="<?php echo $event['promo_member_price'] ?? 0; ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <p class="mt-1 text-xs text-gray-500">Precio de preventa exclusivo para afiliados vigentes</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="promo_end_date" class="block text-sm font-medium text-gray-700">Fecha Límite de Preventa</label>
                        <input type="datetime-local" id="promo_end_date" name="promo_end_date"
                               value="<?php echo !empty($event['promo_end_date']) ? date('Y-m-d\TH:i', strtotime($event['promo_end_date'])) : ''; ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <div class="mt-1 p-2 bg-blue-100 rounded text-xs text-blue-700 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Después de esta fecha, se cobrarán los precios regulares
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Free access for affiliates -->
            <div class="mt-4 p-4 bg-white rounded-lg border border-blue-200">
                <label class="flex items-start">
                    <input type="checkbox" id="free_for_affiliates" name="free_for_affiliates" value="1"
                           <?php echo ($event['free_for_affiliates'] ?? 1) ? 'checked' : ''; ?>
                           class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-900">Acceso gratuito para afiliados vigentes</span>
                        <p class="text-xs text-gray-600 mt-1">
                            Los afiliados con membresía activa obtienen 1 acceso gratis automáticamente. 
                            Desactiva esta opción si el evento no incluye esta cortesía.
                        </p>
                    </div>
                </label>
            </div>
        </div>
        
        <!-- Target Audiences -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Público Objetivo</h3>
            <?php $selectedAudiences = json_decode($event['target_audiences'] ?? '[]', true) ?: []; ?>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($audiences as $value => $label): ?>
                <label class="flex items-center">
                    <input type="checkbox" name="target_audiences[]" value="<?php echo $value; ?>"
                           <?php echo in_array($value, $selectedAudiences) ? 'checked' : ''; ?>
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700"><?php echo htmlspecialchars($label); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                <label class="flex items-center">
                    <input type="checkbox" name="target_audiences[]" value="patrocinador_mesa"
                           <?php echo in_array('patrocinador_mesa', $selectedAudiences) ? 'checked' : ''; ?>
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Patrocinadores/Mesa Directiva</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="target_audiences[]" value="colaborador_empresa"
                           <?php echo in_array('colaborador_empresa', $selectedAudiences) ? 'checked' : ''; ?>
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Colaboradores de Empresas</span>
                </label>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('is_online').addEventListener('change', function() {
    const physicalFields = ['physical-location', 'physical-address'];
    const onlineField = document.getElementById('online-url');
    const mapsField = document.querySelector('[for="google_maps_url"]')?.parentElement;
    
    physicalFields.forEach(id => {
        document.getElementById(id)?.classList.toggle('hidden', this.checked);
    });
    onlineField.classList.toggle('hidden', !this.checked);
    if (mapsField) mapsField.classList.toggle('hidden', this.checked);
});

document.getElementById('is_paid').addEventListener('change', function() {
    document.getElementById('pricing-fields').classList.toggle('hidden', !this.checked);
});

// Validate promo_end_date is before start_date
function showPromoDateError(message) {
    const promoEndInput = document.getElementById('promo_end_date');
    let errorEl = document.getElementById('promo-date-error');
    if (!errorEl) {
        errorEl = document.createElement('div');
        errorEl.id = 'promo-date-error';
        errorEl.className = 'mt-1 p-2 bg-red-100 border border-red-400 text-red-700 text-xs rounded';
        promoEndInput.parentNode.appendChild(errorEl);
    }
    errorEl.textContent = message;
    errorEl.classList.remove('hidden');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        errorEl.classList.add('hidden');
    }, 5000);
}

document.getElementById('promo_end_date').addEventListener('change', function() {
    const startDate = document.getElementById('start_date').value;
    const promoEndDate = this.value;
    
    if (startDate && promoEndDate && promoEndDate >= startDate) {
        showPromoDateError('La fecha límite de preventa debe ser anterior a la fecha del evento.');
        this.value = '';
    }
});

document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    const promoEndDate = document.getElementById('promo_end_date').value;
    
    if (startDate && promoEndDate && promoEndDate >= startDate) {
        showPromoDateError('La fecha límite de preventa debe ser anterior a la fecha del evento.');
        document.getElementById('promo_end_date').value = '';
    }
});

// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const startDate = document.getElementById('start_date').value;
    const promoEndDate = document.getElementById('promo_end_date').value;
    const isPaid = document.getElementById('is_paid').checked;
    
    if (isPaid && promoEndDate && startDate && promoEndDate >= startDate) {
        e.preventDefault();
        showPromoDateError('La fecha límite de preventa debe ser anterior a la fecha del evento.');
        document.getElementById('promo_end_date').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
});
</script>
