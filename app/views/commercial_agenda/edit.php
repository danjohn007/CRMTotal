<!-- Commercial Agenda - Edit Activity -->
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
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Editar Actividad</h2>
            <span class="px-3 py-1 rounded-full text-sm 
                <?php echo $activity['status'] === 'completada' ? 'bg-green-100 text-green-800' : 
                          ($activity['status'] === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                          ($activity['status'] === 'en_progreso' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')); ?>">
                <?php echo $statuses[$activity['status']] ?? ucfirst($activity['status']); ?>
            </span>
        </div>
        
        <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <!-- Activity Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Actividad</label>
                <div class="grid grid-cols-3 md:grid-cols-4 gap-3">
                    <?php foreach ($activityTypes as $key => $label): ?>
                    <label class="relative">
                        <input type="radio" name="activity_type" value="<?php echo $key; ?>" class="peer sr-only" 
                               <?php echo $activity['activity_type'] === $key ? 'checked' : ''; ?>>
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
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                <input type="text" id="title" name="title" required
                       value="<?php echo htmlspecialchars($activity['title'] ?? ''); ?>"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            </div>
            
            <!-- Contact -->
            <div>
                <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-1">Contacto</label>
                <select id="contact_id" name="contact_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
                    <option value="">Seleccionar contacto...</option>
                    <?php foreach ($contacts as $contact): ?>
                    <option value="<?php echo $contact['id']; ?>" 
                            <?php echo $activity['contact_id'] == $contact['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? $contact['owner_name'] ?? 'Sin nombre'); ?>
                        <?php if ($contact['contact_type']): ?>
                        (<?php echo ucfirst($contact['contact_type']); ?>)
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Status & Priority -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="status" name="status"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
                        <?php foreach ($statuses as $key => $label): ?>
                        <option value="<?php echo $key; ?>" <?php echo $activity['status'] === $key ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                    <select id="priority" name="priority"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
                        <?php foreach ($priorities as $key => $label): ?>
                        <option value="<?php echo $key; ?>" <?php echo $activity['priority'] === $key ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Scheduled Date -->
            <div>
                <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora Programada *</label>
                <input type="datetime-local" id="scheduled_date" name="scheduled_date" required
                       value="<?php echo date('Y-m-d\TH:i', strtotime($activity['scheduled_date'])); ?>"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            </div>
            
            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"><?php echo htmlspecialchars($activity['description'] ?? ''); ?></textarea>
            </div>
            
            <!-- Result (for completed activities) -->
            <div>
                <label for="result" class="block text-sm font-medium text-gray-700 mb-1">Resultado</label>
                <textarea id="result" name="result" rows="3"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                          placeholder="Describe el resultado de la actividad..."><?php echo htmlspecialchars($activity['result'] ?? ''); ?></textarea>
            </div>
            
            <!-- Next Action -->
            <div class="border-t pt-6">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Siguiente Acción</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="next_action" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <input type="text" id="next_action" name="next_action"
                               value="<?php echo htmlspecialchars($activity['next_action'] ?? ''); ?>"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
                    </div>
                    <div>
                        <label for="next_action_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="datetime-local" id="next_action_date" name="next_action_date"
                               value="<?php echo $activity['next_action_date'] ? date('Y-m-d\TH:i', strtotime($activity['next_action_date'])) : ''; ?>"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="flex items-center justify-between pt-4 border-t">
                <div>
                    <?php if ($activity['status'] !== 'completada'): ?>
                    <button type="submit" name="mark_complete" value="1"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Marcar como Completada
                    </button>
                    <?php endif; ?>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Activity Info -->
    <div class="mt-6 bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
        <p>Creada: <?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?></p>
        <?php if ($activity['completed_date']): ?>
        <p>Completada: <?php echo date('d/m/Y H:i', strtotime($activity['completed_date'])); ?></p>
        <?php endif; ?>
    </div>
</div>
