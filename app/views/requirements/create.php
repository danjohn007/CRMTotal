<!-- Create Requirement View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Nuevo Requerimiento</h2>
            <p class="mt-1 text-sm text-gray-500">Registrar una nueva solicitud comercial</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/requerimientos" class="text-blue-600 hover:text-blue-800">
            ← Volver a Requerimientos
        </a>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <!-- Form -->
    <form method="POST" class="bg-white rounded-lg shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                <input type="text" id="title" name="title" required
                       placeholder="Descripción breve del requerimiento"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Detalles del requerimiento..."
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
            </div>
            
            <!-- Contact -->
            <div>
                <label for="contact_id" class="block text-sm font-medium text-gray-700">Contacto (opcional)</label>
                <select id="contact_id" name="contact_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="">Sin contacto asociado</option>
                    <?php foreach ($contacts as $contact): ?>
                    <option value="<?php echo $contact['id']; ?>">
                        <?php echo htmlspecialchars($contact['business_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Categoría</label>
                <select id="category" name="category" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="">Seleccionar...</option>
                    <option value="membresia">Nueva Membresía</option>
                    <option value="renovacion">Renovación</option>
                    <option value="servicio">Servicio Adicional</option>
                    <option value="evento">Evento</option>
                    <option value="capacitacion">Capacitación</option>
                    <option value="marketing">Marketing</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            
            <!-- Priority -->
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700">Prioridad</label>
                <select id="priority" name="priority" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="low">Baja</option>
                    <option value="medium" selected>Media</option>
                    <option value="high">Alta</option>
                </select>
            </div>
            
            <!-- Due Date -->
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700">Fecha Límite</label>
                <input type="date" id="due_date" name="due_date"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Budget -->
            <div>
                <label for="budget" class="block text-sm font-medium text-gray-700">Presupuesto Estimado (MXN)</label>
                <input type="number" id="budget" name="budget" min="0" step="0.01"
                       placeholder="0.00"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Notes -->
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notas Adicionales</label>
                <textarea id="notes" name="notes" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>/requerimientos" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Crear Requerimiento
            </button>
        </div>
    </form>
</div>
