<!-- Audit Index View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Auditoría del Sistema</h2>
            <p class="mt-1 text-sm text-gray-500">Registro de todas las acciones realizadas en el sistema</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/auditoria/buscar" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Búsqueda Avanzada
            </a>
            <a href="<?php echo BASE_URL; ?>/auditoria/actividad" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Actividad de Usuarios
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Acciones (30 días)</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_actions'] ?? 0); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Usuarios Activos</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['unique_users'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Tipos de Acción</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['unique_actions'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Tablas Afectadas</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['tables_affected'] ?? 0; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions Summary -->
    <?php if (!empty($actionsSummary)): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones más frecuentes (30 días)</h3>
        <div class="flex flex-wrap gap-2">
            <?php foreach ($actionsSummary as $action): ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">
                <?php echo htmlspecialchars($action['action']); ?>
                <span class="ml-2 bg-blue-600 text-white px-2 py-0.5 rounded-full text-xs">
                    <?php echo $action['count']; ?>
                </span>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Recent Logs -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Registros Recientes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tabla</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Registro</th>
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
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($log['user_name'] ?? 'Sistema'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($log['user_email'] ?? ''); ?></p>
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
</div>
