<!-- Audit Search View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Búsqueda en Auditoría</h2>
            <p class="mt-1 text-sm text-gray-500">Buscar en el historial de acciones</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/auditoria" class="text-blue-600 hover:text-blue-800">
            ← Volver a Auditoría
        </a>
    </div>
    
    <!-- Search Form -->
    <form method="GET" class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                <input type="date" id="start_date" name="start_date" 
                       value="<?php echo htmlspecialchars($startDate); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                <input type="date" id="end_date" name="end_date" 
                       value="<?php echo htmlspecialchars($endDate); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            <div>
                <label for="action" class="block text-sm font-medium text-gray-700">Tipo de Acción</label>
                <select id="action" name="action"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="">Todas</option>
                    <?php foreach ($actionsSummary as $act): ?>
                    <option value="<?php echo htmlspecialchars($act['action']); ?>" 
                            <?php echo $action === $act['action'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($act['action']); ?> (<?php echo $act['count']; ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Buscar
                </button>
            </div>
        </div>
    </form>
    
    <!-- Results -->
    <?php if (!empty($logs)): ?>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Resultados (<?php echo count($logs); ?>)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tabla</th>
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
                            <?php echo htmlspecialchars($log['table_name'] ?? '-'); ?>
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
    <?php else: ?>
    <div class="bg-white rounded-lg shadow-sm p-6 text-center text-gray-500">
        No se encontraron registros con los criterios seleccionados.
    </div>
    <?php endif; ?>
</div>
