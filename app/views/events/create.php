<!-- Event Create View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Nuevo Evento</h2>
            <p class="mt-1 text-sm text-gray-500">Crea un nuevo evento interno, externo o de terceros</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/eventos" class="text-blue-600 hover:text-blue-800">
            ← Volver a Eventos
        </a>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <!-- Create Form -->
    <form method="POST" class="bg-white rounded-lg shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <!-- Basic Info -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Información del Evento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700">Título del Evento *</label>
                    <input type="text" id="title" name="title" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="description" name="description" rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                </div>
                
                <div>
                    <label for="event_type" class="block text-sm font-medium text-gray-700">Tipo de Evento *</label>
                    <select id="event_type" name="event_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <?php foreach ($eventTypes as $value => $label): ?>
                        <option value="<?php echo $value; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Categoría</label>
                    <input type="text" id="category" name="category" 
                           placeholder="Ej: Networking, Capacitación, Conferencia"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="draft">Borrador</option>
                        <option value="published">Publicado</option>
                    </select>
                </div>
                
                <div>
                    <label for="max_capacity" class="block text-sm font-medium text-gray-700">Capacidad Máxima</label>
                    <input type="number" id="max_capacity" name="max_capacity" min="0"
                           placeholder="0 = Sin límite"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
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
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha y Hora de Fin *</label>
                    <input type="datetime-local" id="end_date" name="end_date" required
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
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Evento en línea</span>
                    </label>
                </div>
                
                <div id="physical-location">
                    <label for="location" class="block text-sm font-medium text-gray-700">Lugar</label>
                    <input type="text" id="location" name="location" 
                           placeholder="Ej: Salón Principal CCQ"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div id="physical-address">
                    <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                    <input type="text" id="address" name="address"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div id="online-url" class="hidden md:col-span-2">
                    <label for="online_url" class="block text-sm font-medium text-gray-700">URL del Evento Online</label>
                    <input type="url" id="online_url" name="online_url" 
                           placeholder="https://zoom.us/..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="google_maps_url" class="block text-sm font-medium text-gray-700">URL Google Maps</label>
                    <input type="url" id="google_maps_url" name="google_maps_url"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Pricing -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Precios</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="is_paid" name="is_paid" value="1"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Evento de pago</span>
                    </label>
                </div>
                
                <div id="price-field" class="hidden">
                    <label for="price" class="block text-sm font-medium text-gray-700">Precio General</label>
                    <input type="number" id="price" name="price" min="0" step="0.01"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div id="member-price-field" class="hidden">
                    <label for="member_price" class="block text-sm font-medium text-gray-700">Precio Afiliados</label>
                    <input type="number" id="member_price" name="member_price" min="0" step="0.01"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Target Audiences -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Público Objetivo</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($audiences as $value => $label): ?>
                <label class="flex items-center">
                    <input type="checkbox" name="target_audiences[]" value="<?php echo $value; ?>"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700"><?php echo htmlspecialchars($label); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>/eventos" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Crear Evento
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('is_online').addEventListener('change', function() {
    const physicalFields = ['physical-location', 'physical-address'];
    const onlineField = document.getElementById('online-url');
    
    physicalFields.forEach(id => {
        document.getElementById(id).classList.toggle('hidden', this.checked);
    });
    onlineField.classList.toggle('hidden', !this.checked);
});

document.getElementById('is_paid').addEventListener('change', function() {
    document.getElementById('price-field').classList.toggle('hidden', !this.checked);
    document.getElementById('member-price-field').classList.toggle('hidden', !this.checked);
});
</script>
