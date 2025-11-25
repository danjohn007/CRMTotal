<!-- Audit by User View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Auditoría de Usuario</h2>
            <p class="mt-1 text-sm text-gray-500"><?php echo htmlspecialchars($user['name']); ?> - <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>/auditoria/actividad" class="text-blue-600 hover:text-blue-800">
            ← Volver a Actividad
        </a>
    </div>
    
    <!-- Logs Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Últimas acciones (<?php echo count($logs); ?>)</h3>
        </div>
        <?php if (!empty($logs)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
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
        <?php else: ?>
        <div class="p-6 text-center text-gray-500">
            No hay registros de auditoría para este usuario.
        </div>
        <?php endif; ?>
    </div>
</div>
