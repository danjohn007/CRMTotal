<!-- Requirement Detail View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($requirement['title']); ?></h2>
            <p class="mt-1 text-sm text-gray-500">Creado el <?php echo date('d/m/Y H:i', strtotime($requirement['created_at'])); ?></p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/requerimientos" class="text-blue-600 hover:text-blue-800">
                ← Volver
            </a>
            <a href="<?php echo BASE_URL; ?>/requerimientos/<?php echo $requirement['id']; ?>/editar" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Editar
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Details Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detalles</h3>
                
                <div class="prose max-w-none">
                    <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($requirement['description'] ?? 'Sin descripción')); ?></p>
                </div>
                
                <?php if (!empty($requirement['notes'])): ?>
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Notas</h4>
                    <p class="text-sm text-gray-600"><?php echo nl2br(htmlspecialchars($requirement['notes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Status Update -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Estado</h3>
                <form method="POST" action="<?php echo BASE_URL; ?>/requerimientos/actualizar-estado" class="flex flex-wrap gap-2">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="id" value="<?php echo $requirement['id']; ?>">
                    
                    <button type="submit" name="status" value="pending" 
                            class="px-4 py-2 rounded-lg transition <?php echo $requirement['status'] === 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'; ?>">
                        Pendiente
                    </button>
                    <button type="submit" name="status" value="in_progress" 
                            class="px-4 py-2 rounded-lg transition <?php echo $requirement['status'] === 'in_progress' ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-800 hover:bg-blue-200'; ?>">
                        En Progreso
                    </button>
                    <button type="submit" name="status" value="completed" 
                            class="px-4 py-2 rounded-lg transition <?php echo $requirement['status'] === 'completed' ? 'bg-green-500 text-white' : 'bg-green-100 text-green-800 hover:bg-green-200'; ?>">
                        Completado
                    </button>
                    <button type="submit" name="status" value="cancelled" 
                            class="px-4 py-2 rounded-lg transition <?php echo $requirement['status'] === 'cancelled' ? 'bg-gray-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'; ?>">
                        Cancelado
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                        <dd class="mt-1">
                            <span class="px-3 py-1 text-sm rounded-full 
                                <?php echo $requirement['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                          ($requirement['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                          ($requirement['status'] === 'cancelled' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800')); ?>">
                                <?php 
                                $statusLabels = ['pending' => 'Pendiente', 'in_progress' => 'En Progreso', 'completed' => 'Completado', 'cancelled' => 'Cancelado'];
                                echo $statusLabels[$requirement['status']] ?? ucfirst($requirement['status']);
                                ?>
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Prioridad</dt>
                        <dd class="mt-1">
                            <span class="px-3 py-1 text-sm rounded-full 
                                <?php echo $requirement['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                          ($requirement['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                <?php 
                                $priorityLabels = ['high' => 'Alta', 'medium' => 'Media', 'low' => 'Baja'];
                                echo $priorityLabels[$requirement['priority']] ?? ucfirst($requirement['priority']);
                                ?>
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($requirement['category'] ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha Límite</dt>
                        <dd class="mt-1 text-sm <?php echo ($requirement['due_date'] && strtotime($requirement['due_date']) < time() && $requirement['status'] === 'pending') ? 'text-red-600 font-medium' : 'text-gray-900'; ?>">
                            <?php echo $requirement['due_date'] ? date('d/m/Y', strtotime($requirement['due_date'])) : '-'; ?>
                        </dd>
                    </div>
                    <?php if ($requirement['budget'] > 0): ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Presupuesto</dt>
                        <dd class="mt-1 text-sm text-gray-900">$<?php echo number_format($requirement['budget'], 2); ?></dd>
                    </div>
                    <?php endif; ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Asignado a</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user['name'] ?? '-'); ?></dd>
                    </div>
                </dl>
            </div>
            
            <!-- Contact Card -->
            <?php if ($contact): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h4 class="text-sm font-medium text-gray-500 mb-3">Contacto Asociado</h4>
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($contact['business_name']); ?></p>
                    <?php if ($contact['phone']): ?>
                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($contact['phone']); ?></p>
                    <?php endif; ?>
                    <?php if ($contact['corporate_email']): ?>
                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($contact['corporate_email']); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>" 
                       class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-800">
                        Ver perfil →
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
