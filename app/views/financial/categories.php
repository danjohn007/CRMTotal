<!-- Financial Categories Management View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Categorías Financieras</h2>
            <p class="mt-1 text-sm text-gray-500">Gestiona las categorías de ingresos y egresos</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/financiero" class="text-blue-600 hover:text-blue-800">
            ← Volver al Módulo Financiero
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
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add New Category Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Nueva Categoría</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre *</label>
                    <input type="text" id="name" name="name" required
                           placeholder="Ej: Membresías, Servicios, etc."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Tipo *</label>
                    <select id="type" name="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="ingreso">Ingreso</option>
                        <option value="egreso">Egreso</option>
                    </select>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="description" name="description" rows="2"
                              placeholder="Descripción opcional de la categoría"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                </div>
                
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Agregar Categoría
                </button>
            </form>
        </div>
        
        <!-- Income Categories -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-green-50">
                <h3 class="text-lg font-medium text-green-800">Categorías de Ingreso</h3>
            </div>
            <?php 
            $incomeCategories = array_filter($categories, fn($c) => $c['type'] === 'ingreso');
            if (!empty($incomeCategories)): 
            ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($incomeCategories as $category): ?>
                <li class="px-6 py-4 flex justify-between items-center hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($category['name']); ?></p>
                        <?php if (!empty($category['description'])): ?>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($category['description']); ?></p>
                        <?php endif; ?>
                        <span class="inline-flex mt-1 px-2 py-0.5 text-xs rounded-full <?php echo $category['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo $category['is_active'] ? 'Activa' : 'Inactiva'; ?>
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <form method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                            <button type="submit" class="text-blue-600 hover:text-blue-900 text-sm">
                                <?php echo $category['is_active'] ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </form>
                        <form method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta categoría?');">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Eliminar</button>
                        </form>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <div class="p-6 text-center text-gray-500">
                No hay categorías de ingreso definidas.
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Expense Categories -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-red-50">
                <h3 class="text-lg font-medium text-red-800">Categorías de Egreso</h3>
            </div>
            <?php 
            $expenseCategories = array_filter($categories, fn($c) => $c['type'] === 'egreso');
            if (!empty($expenseCategories)): 
            ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($expenseCategories as $category): ?>
                <li class="px-6 py-4 flex justify-between items-center hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($category['name']); ?></p>
                        <?php if (!empty($category['description'])): ?>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($category['description']); ?></p>
                        <?php endif; ?>
                        <span class="inline-flex mt-1 px-2 py-0.5 text-xs rounded-full <?php echo $category['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo $category['is_active'] ? 'Activa' : 'Inactiva'; ?>
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <form method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                            <button type="submit" class="text-blue-600 hover:text-blue-900 text-sm">
                                <?php echo $category['is_active'] ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </form>
                        <form method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta categoría?');">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Eliminar</button>
                        </form>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <div class="p-6 text-center text-gray-500">
                No hay categorías de egreso definidas.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
