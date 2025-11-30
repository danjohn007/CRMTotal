<!-- Commercial Agenda - Create Activity -->
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?php echo BASE_URL; ?>/agenda-comercial" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Agenda
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Nueva Actividad</h2>
        
        <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <!-- Activity Type Quick Select -->
            <?php if ($prefilledType): ?>
            <input type="hidden" name="activity_type" value="<?php echo htmlspecialchars($prefilledType); ?>">
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    Tipo de actividad: <strong><?php echo $activityTypes[$prefilledType] ?? ucfirst($prefilledType); ?></strong>
                </p>
            </div>
            <?php else: ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Actividad</label>
                <div class="grid grid-cols-3 md:grid-cols-4 gap-3">
                    <?php foreach ($activityTypes as $key => $label): ?>
                    <label class="relative">
                        <input type="radio" name="activity_type" value="<?php echo $key; ?>" class="peer sr-only" 
                               <?php echo $key === 'llamada' ? 'checked' : ''; ?>>
                        <div class="p-3 text-center rounded-lg border-2 border-gray-200 cursor-pointer 
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition">
                            <span class="text-xl block mb-1">
                                <?php echo match($key) {
                                    'llamada' => '📞',
                                    'whatsapp' => '💬',
                                    'email' => '✉️',
                                    'visita' => '🚗',
                                    'reunion' => '👥',
                                    'seguimiento' => '🔄',
                                    'invitacion' => '📨',
                                    'prospectacion' => '🎯',
                                    'captura' => '👤',
                                    'factura' => '📄',
                                    default => '📋'
                                }; ?>
                            </span>
                            <span class="text-xs text-gray-600"><?php echo $label; ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                <input type="text" id="title" name="title" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                       placeholder="Ej: Llamada de seguimiento, Visita comercial...">
            </div>
            
            <!-- Contact -->
            <div>
                <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-1">Contacto (opcional)</label>
                <select id="contact_id" name="contact_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
                    <option value="">Seleccionar contacto...</option>
                    <?php foreach ($contacts as $contact): ?>
                    <option value="<?php echo $contact['id']; ?>" 
                            <?php echo $prefilledContactId == $contact['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? $contact['owner_name'] ?? 'Sin nombre'); ?>
                        <?php if ($contact['contact_type']): ?>
                        (<?php echo ucfirst($contact['contact_type']); ?>)
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Scheduled Date & Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora *</label>
                    <input type="datetime-local" id="scheduled_date" name="scheduled_date" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                           value="<?php echo date('Y-m-d\TH:i'); ?>">
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                    <select id="priority" name="priority"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
                        <?php foreach ($priorities as $key => $label): ?>
                        <option value="<?php echo $key; ?>" <?php echo $key === 'media' ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                          placeholder="Detalles adicionales de la actividad..."></textarea>
            </div>
            
            <!-- Next Action (Optional) -->
            <div class="border-t pt-6">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Siguiente Acción (Opcional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="next_action" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <input type="text" id="next_action" name="next_action"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                               placeholder="Ej: Enviar propuesta, Llamar para confirmar...">
                    </div>
                    <div>
                        <label for="next_action_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="datetime-local" id="next_action_date" name="next_action_date"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="<?php echo BASE_URL; ?>/agenda-comercial" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Crear Actividad
                </button>
            </div>
        </form>
    </div>
</div>
