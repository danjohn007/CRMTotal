<!-- Financial Transactions View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Movimientos Financieros</h2>
            <p class="mt-1 text-sm text-gray-500">Registro y consulta de ingresos y egresos</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/financiero/reporte-movimientos?date_from=<?php echo $filters['date_from']; ?>&date_to=<?php echo $filters['date_to']; ?>" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reporte Detallado
            </a>
            <a href="<?php echo BASE_URL; ?>/financiero" class="text-blue-600 hover:text-blue-800">
                ← Volver al Módulo Financiero
            </a>
        </div>
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
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Ingresos</p>
                    <p class="text-2xl font-bold text-green-600">$<?php echo number_format($summary['total_income'], 2); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Egresos</p>
                    <p class="text-2xl font-bold text-red-600">$<?php echo number_format($summary['total_expense'], 2); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Balance</p>
                    <?php $balance = $summary['total_income'] - $summary['total_expense']; ?>
                    <p class="text-2xl font-bold <?php echo $balance >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                        $<?php echo number_format($balance, 2); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- New Transaction Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Nuevo Movimiento</h3>
            <?php 
            $activeCategories = array_filter($categories, fn($c) => $c['is_active']);
            if (empty($activeCategories)): 
            ?>
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800 text-sm">
                <p>No hay categorías activas.</p>
                <a href="<?php echo BASE_URL; ?>/financiero/categorias" class="text-yellow-600 underline">Crear categorías</a>
            </div>
            <?php else: ?>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Categoría *</label>
                    <select id="category_id" name="category_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="">Seleccionar...</option>
                        <optgroup label="Ingresos">
                            <?php foreach ($activeCategories as $cat): ?>
                                <?php if ($cat['type'] === 'ingreso'): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="Egresos">
                            <?php foreach ($activeCategories as $cat): ?>
                                <?php if ($cat['type'] === 'egreso'): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción *</label>
                    <input type="text" id="description" name="description" required
                           placeholder="Descripción del movimiento"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Monto *</label>
                    <input type="number" id="amount" name="amount" required
                           min="0.01" step="0.01"
                           placeholder="0.00"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700">Fecha *</label>
                    <input type="date" id="transaction_date" name="transaction_date" required
                           value="<?php echo date('Y-m-d'); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="reference" class="block text-sm font-medium text-gray-700">Referencia</label>
                    <input type="text" id="reference" name="reference"
                           placeholder="Número de factura, recibo, etc."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notas</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                </div>
                
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Registrar Movimiento
                </button>
            </form>
            <?php endif; ?>
        </div>
        
        <!-- Transactions List -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="filter_type" class="block text-xs text-gray-500">Tipo</label>
                        <select name="type" id="filter_type" onchange="this.form.submit()"
                                class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border text-sm">
                            <option value="">Todos</option>
                            <option value="ingreso" <?php echo $filters['type'] === 'ingreso' ? 'selected' : ''; ?>>Ingresos</option>
                            <option value="egreso" <?php echo $filters['type'] === 'egreso' ? 'selected' : ''; ?>>Egresos</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_from" class="block text-xs text-gray-500">Desde</label>
                        <input type="date" name="date_from" id="date_from" value="<?php echo $filters['date_from']; ?>"
                               onchange="this.form.submit()"
                               class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border text-sm">
                    </div>
                    <div>
                        <label for="date_to" class="block text-xs text-gray-500">Hasta</label>
                        <input type="date" name="date_to" id="date_to" value="<?php echo $filters['date_to']; ?>"
                               onchange="this.form.submit()"
                               class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border text-sm">
                    </div>
                </form>
            </div>
            
            <?php if (!empty($transactions)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($transactions as $transaction): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($transaction['transaction_date'])); ?>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($transaction['description']); ?></p>
                                <?php if (!empty($transaction['reference'])): ?>
                                <p class="text-xs text-gray-500">Ref: <?php echo htmlspecialchars($transaction['reference']); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $transaction['category_type'] === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo htmlspecialchars($transaction['category_name']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right <?php echo $transaction['category_type'] === 'ingreso' ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo $transaction['category_type'] === 'ingreso' ? '+' : '-'; ?>$<?php echo number_format($transaction['amount'], 2); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <form method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este movimiento?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $transaction['id']; ?>">
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
                No hay movimientos en el período seleccionado.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
