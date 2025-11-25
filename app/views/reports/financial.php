<!-- Financial Reports -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Reportes Financieros</h2>
            <p class="mt-1 text-sm text-gray-500">Ingresos y métricas financieras</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/reportes" class="text-blue-600 hover:text-blue-800 text-sm">← Volver a Reportes</a>
    </div>
    
    <!-- Revenue Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-sm p-6 text-white">
            <p class="text-green-100 text-sm">Ingresos del Mes</p>
            <p class="text-3xl font-bold mt-1">$<?php echo number_format($affiliationRevenueMonth + $servicesRevenueMonth, 0); ?></p>
            <div class="mt-3 text-sm text-green-100">
                <p>Afiliaciones: $<?php echo number_format($affiliationRevenueMonth, 0); ?></p>
                <p>Servicios: $<?php echo number_format($servicesRevenueMonth, 0); ?></p>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
            <p class="text-blue-100 text-sm">Ingresos Anuales</p>
            <p class="text-3xl font-bold mt-1">$<?php echo number_format($affiliationRevenueYear + $servicesRevenueYear, 0); ?></p>
            <p class="mt-3 text-sm text-blue-100"><?php echo date('Y'); ?></p>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
            <p class="text-purple-100 text-sm">Ingresos por Afiliaciones</p>
            <p class="text-3xl font-bold mt-1">$<?php echo number_format($affiliationRevenueYear, 0); ?></p>
            <p class="mt-3 text-sm text-purple-100">
                <?php 
                $totalRevenue = $affiliationRevenueYear + $servicesRevenueYear;
                $affiliationPercent = $totalRevenue > 0 ? round(($affiliationRevenueYear / $totalRevenue) * 100) : 0;
                echo $affiliationPercent . '% del total';
                ?>
            </p>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-sm p-6 text-white">
            <p class="text-yellow-100 text-sm">Ingresos por Servicios</p>
            <p class="text-3xl font-bold mt-1">$<?php echo number_format($servicesRevenueYear, 0); ?></p>
            <p class="mt-3 text-sm text-yellow-100">
                <?php 
                $servicesPercent = $totalRevenue > 0 ? round(($servicesRevenueYear / $totalRevenue) * 100) : 0;
                echo $servicesPercent . '% del total';
                ?>
            </p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Revenue Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ingresos Mensuales <?php echo date('Y'); ?></h3>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
        
        <!-- Revenue Distribution -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Ingresos</h3>
            <canvas id="distributionChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Services by Category -->
    <?php if (!empty($servicesByCategory)): ?>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ingresos por Categoría de Servicio</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contratos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ingresos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Promedio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% del Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php 
                    $categoryLabels = [
                        'salon_rental' => 'Renta de Salones',
                        'event_organization' => 'Organización de Eventos',
                        'course' => 'Cursos',
                        'conference' => 'Conferencias',
                        'training' => 'Capacitaciones',
                        'marketing_email' => 'Email Marketing',
                        'marketing_videowall' => 'Videowall',
                        'marketing_social' => 'Redes Sociales',
                        'marketing_platform' => 'Plataforma de Proveeduría',
                        'gestoria' => 'Gestoría',
                        'tramites' => 'Trámites'
                    ];
                    $totalServices = array_sum(array_column($servicesByCategory, 'total'));
                    foreach ($servicesByCategory as $category): 
                        $label = $categoryLabels[$category['category']] ?? ucfirst(str_replace('_', ' ', $category['category']));
                        $avg = $category['count'] > 0 ? $category['total'] / $category['count'] : 0;
                        $percent = $totalServices > 0 ? round(($category['total'] / $totalServices) * 100) : 0;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900"><?php echo $label; ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $category['count']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            $<?php echo number_format($category['total'], 0); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $<?php echo number_format($avg, 0); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo $percent; ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">Total</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo array_sum(array_column($servicesByCategory, 'count')); ?></td>
                        <td class="px-6 py-4 text-sm font-semibold text-green-600">$<?php echo number_format($totalServices, 0); ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Desglose Mensual de Afiliaciones</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afiliaciones</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ingresos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Promedio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php 
                    $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    foreach ($monthlyStats as $month): 
                        $avg = $month['count'] > 0 ? $month['total'] / $month['count'] : 0;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo $monthNames[intval($month['month'])] ?? $month['month']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $month['count']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            $<?php echo number_format($month['total'], 0); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $<?php echo number_format($avg, 0); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Revenue Chart
    const monthlyData = <?php echo json_encode($monthlyStats ?? []); ?>;
    const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
    const revenueData = new Array(12).fill(0);
    monthlyData.forEach(item => {
        revenueData[parseInt(item.month) - 1] = parseFloat(item.total);
    });
    
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Ingresos',
                data: revenueData,
                backgroundColor: 'rgba(16, 185, 129, 0.5)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // Distribution Chart
    const affiliationRevenue = <?php echo $affiliationRevenueYear; ?>;
    const servicesRevenue = <?php echo $servicesRevenueYear; ?>;
    
    new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Afiliaciones', 'Servicios'],
            datasets: [{
                data: [affiliationRevenue, servicesRevenue],
                backgroundColor: ['rgb(139, 92, 246)', 'rgb(245, 158, 11)']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
