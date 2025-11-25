<!-- Requirements Index View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Requerimientos Comerciales</h2>
            <p class="mt-1 text-sm text-gray-500">Gestión de oportunidades y solicitudes comerciales</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/requerimientos/categorias" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Categorías
            </a>
            <a href="<?php echo BASE_URL; ?>/requerimientos/nuevo" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Requerimiento
            </a>
            <a href="<?php echo BASE_URL; ?>/requerimientos/mis-requerimientos" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Mis Requerimientos
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total'] ?? 0; ?></p>
            <p class="text-xs text-gray-500">Total</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600"><?php echo $stats['pending'] ?? 0; ?></p>
            <p class="text-xs text-gray-500">Pendientes</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600"><?php echo $stats['in_progress'] ?? 0; ?></p>
            <p class="text-xs text-gray-500">En Progreso</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-green-600"><?php echo $stats['completed'] ?? 0; ?></p>
            <p class="text-xs text-gray-500">Completados</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-400"><?php echo $stats['cancelled'] ?? 0; ?></p>
            <p class="text-xs text-gray-500">Cancelados</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-red-600"><?php echo $stats['overdue'] ?? 0; ?></p>
            <p class="text-xs text-gray-500">Vencidos</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                <select id="status" name="status" onchange="this.form.submit()"
                        class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="">Todos</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pendientes</option>
                    <option value="in_progress" <?php echo $status === 'in_progress' ? 'selected' : ''; ?>>En Progreso</option>
                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completados</option>
                    <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelados</option>
                </select>
            </div>
        </form>
    </div>
    
    <!-- Requirements List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requerimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Límite</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($requirements as $req): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="<?php echo BASE_URL; ?>/requerimientos/<?php echo $req['id']; ?>" 
                               class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                <?php echo htmlspecialchars($req['title']); ?>
                            </a>
                            <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($req['user_name'] ?? ''); ?></p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($req['business_name'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($req['category'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $req['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                          ($req['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                <?php 
                                $priorityLabels = ['high' => 'Alta', 'medium' => 'Media', 'low' => 'Baja'];
                                echo $priorityLabels[$req['priority']] ?? ucfirst($req['priority']);
                                ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $req['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                          ($req['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                          ($req['status'] === 'cancelled' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800')); ?>">
                                <?php 
                                $statusLabels = ['pending' => 'Pendiente', 'in_progress' => 'En Progreso', 'completed' => 'Completado', 'cancelled' => 'Cancelado'];
                                echo $statusLabels[$req['status']] ?? ucfirst($req['status']);
                                ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo ($req['due_date'] && strtotime($req['due_date']) < time() && $req['status'] === 'pending') ? 'text-red-600 font-medium' : 'text-gray-500'; ?>">
                            <?php echo $req['due_date'] ? date('d/m/Y', strtotime($req['due_date'])) : '-'; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?php echo BASE_URL; ?>/requerimientos/<?php echo $req['id']; ?>" 
                               class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                            <a href="<?php echo BASE_URL; ?>/requerimientos/<?php echo $req['id']; ?>/editar" 
                               class="text-indigo-600 hover:text-indigo-900">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
