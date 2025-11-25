<!-- My Requirements View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Mis Requerimientos</h2>
            <p class="mt-1 text-sm text-gray-500">Requerimientos asignados a mí</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/requerimientos/nuevo" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo
            </a>
            <a href="<?php echo BASE_URL; ?>/requerimientos" class="text-blue-600 hover:text-blue-800">
                Ver todos →
            </a>
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
                </select>
            </div>
        </form>
    </div>
    
    <!-- Requirements List -->
    <?php if (!empty($requirements)): ?>
    <div class="space-y-4">
        <?php foreach ($requirements as $req): ?>
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <a href="<?php echo BASE_URL; ?>/requerimientos/<?php echo $req['id']; ?>" 
                           class="text-lg font-medium text-blue-600 hover:text-blue-800">
                            <?php echo htmlspecialchars($req['title']); ?>
                        </a>
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?php echo $req['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                      ($req['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                            <?php 
                            $priorityLabels = ['high' => 'Alta', 'medium' => 'Media', 'low' => 'Baja'];
                            echo $priorityLabels[$req['priority']] ?? ucfirst($req['priority']);
                            ?>
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        <?php echo htmlspecialchars(mb_substr($req['description'] ?? '', 0, 100)); ?>
                        <?php echo strlen($req['description'] ?? '') > 100 ? '...' : ''; ?>
                    </p>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                        <?php if ($req['business_name']): ?>
                        <span><?php echo htmlspecialchars($req['business_name']); ?></span>
                        <?php endif; ?>
                        <?php if ($req['due_date']): ?>
                        <span class="<?php echo strtotime($req['due_date']) < time() && $req['status'] === 'pending' ? 'text-red-600' : ''; ?>">
                            Límite: <?php echo date('d/m/Y', strtotime($req['due_date'])); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm rounded-full 
                    <?php echo $req['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                              ($req['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                    <?php 
                    $statusLabels = ['pending' => 'Pendiente', 'in_progress' => 'En Progreso', 'completed' => 'Completado'];
                    echo $statusLabels[$req['status']] ?? ucfirst($req['status']);
                    ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow-sm p-6 text-center text-gray-500">
        No tienes requerimientos asignados.
    </div>
    <?php endif; ?>
</div>
