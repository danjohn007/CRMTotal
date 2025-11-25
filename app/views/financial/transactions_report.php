<!-- Financial Transactions Report View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Reporte de Movimientos</h2>
            <p class="mt-1 text-sm text-gray-500">
                Período: <?php echo date('d/m/Y', strtotime($dateFrom)); ?> - <?php echo date('d/m/Y', strtotime($dateTo)); ?>
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir
            </button>
            <a href="<?php echo BASE_URL; ?>/financiero/movimientos" class="text-blue-600 hover:text-blue-800">
                ← Volver a Movimientos
            </a>
        </div>
    </div>
    
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4 print:hidden">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Desde</label>
                <input type="date" name="date_from" id="date_from" value="<?php echo $dateFrom; ?>"
                       class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Hasta</label>
                <input type="date" name="date_to" id="date_to" value="<?php echo $dateTo; ?>"
                       class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                <select name="type" id="type"
                        class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="">Todos</option>
                    <option value="ingreso" <?php echo $type === 'ingreso' ? 'selected' : ''; ?>>Solo Ingresos</option>
                    <option value="egreso" <?php echo $type === 'egreso' ? 'selected' : ''; ?>>Solo Egresos</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Generar Reporte
            </button>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Total Ingresos</h3>
            <p class="text-3xl font-bold text-green-600">$<?php echo number_format($summary['total_income'], 2); ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Total Egresos</h3>
            <p class="text-3xl font-bold text-red-600">$<?php echo number_format($summary['total_expense'], 2); ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Balance Neto</h3>
            <?php $balance = $summary['total_income'] - $summary['total_expense']; ?>
            <p class="text-3xl font-bold <?php echo $balance >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                $<?php echo number_format($balance, 2); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Total Movimientos</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $summary['total_count']; ?></p>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 print:hidden">
        <!-- By Category Pie Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Distribución por Categoría</h3>
            <canvas id="categoryChart" height="200"></canvas>
        </div>
        
        <!-- Income vs Expense Bar -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ingresos vs Egresos</h3>
            <canvas id="comparisonChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- By Category Table -->
    <?php if (!empty($byCategory)): ?>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Resumen por Categoría</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($byCategory as $cat): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $cat['type'] === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $cat['type'] === 'ingreso' ? 'Ingreso' : 'Egreso'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right <?php echo $cat['type'] === 'ingreso' ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo $cat['type'] === 'ingreso' ? '+' : '-'; ?>$<?php echo number_format($cat['total'], 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Transactions Detail Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Detalle de Movimientos</h3>
        </div>
        <?php if (!empty($transactions)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referencia</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php 
                    $runningBalance = 0;
                    foreach ($transactions as $transaction): 
                        if ($transaction['category_type'] === 'ingreso') {
                            $runningBalance += $transaction['amount'];
                        } else {
                            $runningBalance -= $transaction['amount'];
                        }
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('d/m/Y', strtotime($transaction['transaction_date'])); ?>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($transaction['description']); ?></p>
                            <?php if (!empty($transaction['notes'])): ?>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($transaction['notes']); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $transaction['category_type'] === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo htmlspecialchars($transaction['category_name']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($transaction['reference'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right <?php echo $transaction['category_type'] === 'ingreso' ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo $transaction['category_type'] === 'ingreso' ? '+' : '-'; ?>$<?php echo number_format($transaction['amount'], 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-sm font-bold text-gray-900">Balance del Período</td>
                        <td class="px-6 py-4 text-sm font-bold text-right <?php echo $balance >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                            $<?php echo number_format($balance, 2); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php else: ?>
        <div class="p-6 text-center text-gray-500">
            No hay movimientos en el período seleccionado.
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
<?php if (!empty($byCategory)): ?>
// Category Chart
const categoryData = <?php echo json_encode($byCategory); ?>;
const incomeCategories = categoryData.filter(c => c.type === 'ingreso');
const expenseCategories = categoryData.filter(c => c.type === 'egreso');

new Chart(document.getElementById('categoryChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: categoryData.map(c => c.name),
        datasets: [{
            data: categoryData.map(c => parseFloat(c.total)),
            backgroundColor: categoryData.map(c => c.type === 'ingreso' ? 
                ['#10B981', '#34D399', '#6EE7B7', '#A7F3D0'][incomeCategories.indexOf(c) % 4] : 
                ['#EF4444', '#F87171', '#FCA5A5', '#FECACA'][expenseCategories.indexOf(c) % 4]
            )
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
<?php endif; ?>

// Comparison Chart
new Chart(document.getElementById('comparisonChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Ingresos', 'Egresos', 'Balance'],
        datasets: [{
            label: 'Monto',
            data: [
                <?php echo $summary['total_income']; ?>,
                <?php echo $summary['total_expense']; ?>,
                <?php echo $balance; ?>
            ],
            backgroundColor: ['#10B981', '#EF4444', '<?php echo $balance >= 0 ? '#3B82F6' : '#F59E0B'; ?>']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: value => '$' + value.toLocaleString() }
            }
        }
    }
});
</script>

<style>
@media print {
    .print\:hidden { display: none !important; }
    body { font-size: 12px; }
    .bg-white { box-shadow: none !important; border: 1px solid #e5e7eb; }
}
</style>
