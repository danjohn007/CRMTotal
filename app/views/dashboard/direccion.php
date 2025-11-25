<!-- Dirección Dashboard -->
<div class="space-y-6">
    <!-- Executive Summary -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Dashboard Ejecutivo</h2>
        <p class="mt-1 text-indigo-100">Resumen general del departamento comercial</p>
    </div>
    
    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue Month -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ingresos del Mes</p>
                    <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($affiliationRevenueMonth + $servicesRevenueMonth, 0); ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 flex items-center text-sm">
                <span class="text-gray-500">Afiliaciones: $<?php echo number_format($affiliationRevenueMonth, 0); ?></span>
                <span class="mx-2 text-gray-300">|</span>
                <span class="text-gray-500">Servicios: $<?php echo number_format($servicesRevenueMonth, 0); ?></span>
            </div>
        </div>
        
        <!-- Annual Revenue -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ingresos Anuales</p>
                    <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($affiliationRevenueYear + $servicesRevenueYear, 0); ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-sm text-gray-500"><?php echo date('Y'); ?></p>
        </div>
        
        <!-- Renewal Rate -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tasa de Renovación</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $renewalRate; ?>%</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-sm <?php echo $renewalRate >= 80 ? 'text-green-600' : 'text-yellow-600'; ?>">
                <?php echo $renewalRate >= 80 ? '✓ Meta cumplida' : '⚠ Por debajo de meta (80%)'; ?>
            </p>
        </div>
        
        <!-- Events Stats -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Eventos del Año</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $eventStats['total_events'] ?? 0; ?></p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                <?php echo $eventStats['total_registrations'] ?? 0; ?> registros |
                <?php echo $eventStats['total_attendance'] ?? 0; ?> asistencias
            </div>
        </div>
    </div>
    
    <!-- Contact Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- By Type -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución por Tipo</h3>
            <div class="space-y-4">
                <?php 
                $typeLabels = [
                    'afiliado' => ['label' => 'Afiliados', 'color' => 'green'],
                    'prospecto' => ['label' => 'Prospectos', 'color' => 'blue'],
                    'exafiliado' => ['label' => 'Exafiliados', 'color' => 'gray'],
                    'funcionario' => ['label' => 'Funcionarios', 'color' => 'purple'],
                    'publico_general' => ['label' => 'Público General', 'color' => 'yellow']
                ];
                $totalContacts = array_sum(array_column($typeStats, 'count'));
                foreach ($typeStats as $stat): 
                    $info = $typeLabels[$stat['contact_type']] ?? ['label' => ucfirst($stat['contact_type']), 'color' => 'gray'];
                    $percentage = $totalContacts > 0 ? round(($stat['count'] / $totalContacts) * 100) : 0;
                ?>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700"><?php echo $info['label']; ?></span>
                        <span class="text-sm text-gray-500"><?php echo $stat['count']; ?> (<?php echo $percentage; ?>%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-<?php echo $info['color']; ?>-500 h-2 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Monthly Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Afiliaciones Mensuales <?php echo date('Y'); ?></h3>
            <canvas id="monthlyChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Team Performance -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Desempeño del Equipo Comercial</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Afiliaciones</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progreso</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($teamSummary as $member): 
                        $progress = min(100, ($member['affiliations'] / 20) * 100);
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium text-sm"><?php echo substr($member['name'], 0, 1); ?></span>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($member['name']); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $member['affiliations']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$<?php echo number_format($member['revenue'], 0); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Services Revenue by Category -->
    <?php if (!empty($servicesByCategory)): ?>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ingresos por Servicios</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php 
            $categoryLabels = [
                'salon_rental' => 'Salones',
                'course' => 'Cursos',
                'marketing_email' => 'Marketing',
                'gestoria' => 'Gestoría'
            ];
            foreach ($servicesByCategory as $category): 
                $label = $categoryLabels[$category['category']] ?? ucfirst($category['category']);
            ?>
            <div class="p-4 bg-gray-50 rounded-lg text-center">
                <p class="text-2xl font-bold text-gray-900">$<?php echo number_format($category['total'], 0); ?></p>
                <p class="text-sm text-gray-500"><?php echo $label; ?></p>
                <p class="text-xs text-gray-400"><?php echo $category['count']; ?> contratos</p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly affiliations chart
    const monthlyData = <?php echo json_encode($monthlyStats ?? []); ?>;
    const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
    const chartData = new Array(12).fill(0);
    const revenueData = new Array(12).fill(0);
    
    monthlyData.forEach(item => {
        const monthIndex = parseInt(item.month) - 1;
        chartData[monthIndex] = parseInt(item.count);
        revenueData[monthIndex] = parseFloat(item.total);
    });
    
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Afiliaciones',
                data: chartData,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
