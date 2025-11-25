<!-- Requirements Categories Management View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Categorías de Requerimientos</h2>
            <p class="mt-1 text-sm text-gray-500">Gestiona los tipos de categorías para los requerimientos comerciales</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/requerimientos" class="text-blue-600 hover:text-blue-800">
            ← Volver a Requerimientos
        </a>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
    <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Add New Category Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Nueva Categoría</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre *</label>
                    <input type="text" id="name" name="name" required
                           placeholder="Ej: Consultoría, Capacitación, etc."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Código *</label>
                    <input type="text" id="code" name="code" required
                           placeholder="Ej: consultoria, capacitacion, etc."
                           maxlength="50" pattern="[a-z0-9_]+" title="Solo letras minúsculas, números y guion bajo"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <p class="text-xs text-gray-500 mt-1">Solo letras minúsculas, números y guion bajo</p>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="description" name="description" rows="2"
                              placeholder="Descripción opcional de la categoría"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" checked
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">Activa</label>
                </div>
                
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Agregar Categoría
                </button>
            </form>
        </div>
        
        <!-- Categories List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Categorías Existentes</h3>
            </div>
            <?php if (!empty($categories)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($categories as $category): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($category['name']); ?></p>
                                <?php if (!empty($category['description'])): ?>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($category['description']); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($category['code']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $category['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $category['is_active'] ? 'Activa' : 'Inactiva'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <?php echo $category['is_active'] ? 'Desactivar' : 'Activar'; ?>
                                    </button>
                                </form>
                                <form method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta categoría?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="p-6 text-center text-gray-500">
                <p>No hay categorías definidas.</p>
                <p class="text-sm mt-2">Las categorías predeterminadas se mostrarán en el formulario de nuevo requerimiento.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Default Categories Info -->
    <div class="bg-blue-50 rounded-lg p-4">
        <h4 class="text-sm font-medium text-blue-800 mb-2">Categorías Predeterminadas</h4>
        <p class="text-sm text-blue-700">Si no define categorías personalizadas, se usarán las siguientes:</p>
        <div class="mt-2 flex flex-wrap gap-2">
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Nueva Membresía</span>
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Renovación</span>
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Servicio Adicional</span>
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Evento</span>
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Capacitación</span>
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Marketing</span>
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Otro</span>
        </div>
    </div>
</div>
