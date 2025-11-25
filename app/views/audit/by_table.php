<!-- Audit by Table View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Auditoría por Tabla</h2>
            <p class="mt-1 text-sm text-gray-500">Historial de cambios en tablas específicas</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/auditoria" class="text-blue-600 hover:text-blue-800">
            ← Volver a Auditoría
        </a>
    </div>
    
    <!-- Search Form -->
    <form method="GET" class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="table" class="block text-sm font-medium text-gray-700">Tabla</label>
                <select id="table" name="table" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="">Seleccionar...</option>
                    <option value="contacts" <?php echo $table === 'contacts' ? 'selected' : ''; ?>>Contactos</option>
                    <option value="affiliations" <?php echo $table === 'affiliations' ? 'selected' : ''; ?>>Afiliaciones</option>
                    <option value="users" <?php echo $table === 'users' ? 'selected' : ''; ?>>Usuarios</option>
                    <option value="events" <?php echo $table === 'events' ? 'selected' : ''; ?>>Eventos</option>
                    <option value="membership_types" <?php echo $table === 'membership_types' ? 'selected' : ''; ?>>Membresías</option>
                    <option value="activities" <?php echo $table === 'activities' ? 'selected' : ''; ?>>Actividades</option>
                </select>
            </div>
            <div>
                <label for="record_id" class="block text-sm font-medium text-gray-700">ID de Registro (opcional)</label>
                <input type="number" id="record_id" name="record_id" 
                       value="<?php echo $recordId ?: ''; ?>"
                       placeholder="Ej: 123"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Buscar
                </button>
            </div>
        </div>
    </form>
    
    <!-- Results -->
    <?php if (!empty($table) && !empty($logs)): ?>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">
                Historial de <?php echo htmlspecialchars($table); ?>
                <?php if ($recordId): ?> - Registro #<?php echo $recordId; ?><?php endif; ?>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($log['user_name'] ?? 'Sistema'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                <?php echo htmlspecialchars($log['action']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $log['record_id'] ?? '-'; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                            <?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif (!empty($table)): ?>
    <div class="bg-white rounded-lg shadow-sm p-6 text-center text-gray-500">
        No se encontraron registros para la tabla seleccionada.
    </div>
    <?php endif; ?>
</div>
