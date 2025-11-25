<!-- Agenda Create View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Nueva Actividad</h2>
            <p class="mt-1 text-sm text-gray-500">Registra una nueva actividad en tu agenda</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/agenda" class="text-blue-600 hover:text-blue-800">
            ← Volver a Agenda
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
        
        <!-- Activity Info -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Información de la Actividad</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700">Título *</label>
                    <input type="text" id="title" name="title" required
                           placeholder="Ej: Llamada de seguimiento, Visita al cliente..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="activity_type" class="block text-sm font-medium text-gray-700">Tipo de Actividad *</label>
                    <select id="activity_type" name="activity_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <?php foreach ($activityTypes as $value => $label): ?>
                        <option value="<?php echo $value; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Prioridad</label>
                    <select id="priority" name="priority"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <?php foreach ($priorities as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo $value === 'media' ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="contact_id" class="block text-sm font-medium text-gray-700">Contacto Relacionado</label>
                    <select id="contact_id" name="contact_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="">Seleccionar contacto...</option>
                        <?php foreach ($contacts as $contact): ?>
                        <option value="<?php echo $contact['id']; ?>" 
                            <?php echo (isset($prefilledContactId) && $prefilledContactId == $contact['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? 'Sin nombre'); ?>
                            <?php if (!empty($contact['rfc'])): ?> (<?php echo htmlspecialchars($contact['rfc']); ?>)<?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Fecha y Hora *</label>
                    <input type="datetime-local" id="scheduled_date" name="scheduled_date" required
                           value="<?php echo date('Y-m-d\TH:i'); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="description" name="description" rows="4"
                              placeholder="Describe el objetivo y detalles de la actividad..."
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                </div>
            </div>
        </div>
        
        <!-- Follow-up -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Seguimiento (Opcional)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="next_action" class="block text-sm font-medium text-gray-700">Próxima Acción</label>
                    <input type="text" id="next_action" name="next_action" 
                           placeholder="Ej: Enviar propuesta, Agendar cita..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="next_action_date" class="block text-sm font-medium text-gray-700">Fecha de Próxima Acción</label>
                    <input type="datetime-local" id="next_action_date" name="next_action_date"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>/agenda" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Crear Actividad
            </button>
        </div>
    </form>
</div>
