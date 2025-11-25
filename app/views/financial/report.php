<!-- Financial Report View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Reporte Financiero</h2>
            <p class="mt-1 text-sm text-gray-500">Estadísticas e informes de ingresos</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/financiero" class="text-blue-600 hover:text-blue-800">
            ← Volver al Módulo Financiero
        </a>
    </div>
    
    <!-- Year Selector -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex items-center space-x-4">
            <label for="year" class="text-sm font-medium text-gray-700">Año:</label>
            <select id="year" name="year" onchange="this.form.submit()"
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?php echo $y; ?>" <?php echo $year === $y ? 'selected' : ''; ?>>
                    <?php echo $y; ?>
                </option>
                <?php endfor; ?>
            </select>
        </form>
    </div>
    
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Total de Afiliaciones</h3>
            <p class="text-3xl font-bold text-gray-900">
                <?php echo array_sum(array_column($monthlyStats, 'count')); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Ingresos Totales</h3>
            <p class="text-3xl font-bold text-green-600">
                $<?php echo number_format(array_sum(array_column($monthlyStats, 'total')), 2); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Promedio por Afiliación</h3>
            <?php 
            $totalAfiliaciones = array_sum(array_column($monthlyStats, 'count'));
            $totalIngresos = array_sum(array_column($monthlyStats, 'total'));
            $promedio = $totalAfiliaciones > 0 ? $totalIngresos / $totalAfiliaciones : 0;
            ?>
            <p class="text-3xl font-bold text-blue-600">
                $<?php echo number_format($promedio, 2); ?>
            </p>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ingresos por Mes</h3>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
        
        <!-- Affiliations Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Afiliaciones por Mes</h3>
            <canvas id="affiliationsChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Monthly Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Detalle Mensual</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Afiliaciones</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ingresos</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Promedio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php 
                    $monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    for ($m = 1; $m <= 12; $m++):
                        $stat = array_filter($monthlyStats, fn($s) => (int)$s['month'] === $m);
                        $stat = !empty($stat) ? array_values($stat)[0] : ['count' => 0, 'total' => 0];
                        $avg = $stat['count'] > 0 ? $stat['total'] / $stat['count'] : 0;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo $monthNames[$m - 1]; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                            <?php echo $stat['count']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                            $<?php echo number_format($stat['total'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                            $<?php echo number_format($avg, 2); ?>
                        </td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">Total</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                            <?php echo array_sum(array_column($monthlyStats, 'count')); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 text-right">
                            $<?php echo number_format(array_sum(array_column($monthlyStats, 'total')), 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                            $<?php echo number_format($promedio, 2); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
const monthlyData = <?php echo json_encode($monthlyStats); ?>;
const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

const revenueData = months.map((_, i) => {
    const stat = monthlyData.find(s => parseInt(s.month) === i + 1);
    return stat ? parseFloat(stat.total) : 0;
});

const affiliationsData = months.map((_, i) => {
    const stat = monthlyData.find(s => parseInt(s.month) === i + 1);
    return stat ? parseInt(stat.count) : 0;
});

// Revenue Chart
new Chart(document.getElementById('revenueChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Ingresos',
            data: revenueData,
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            fill: true,
            tension: 0.3
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

// Affiliations Chart
new Chart(document.getElementById('affiliationsChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Afiliaciones',
            data: affiliationsData,
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
