<!-- Jefe Comercial Dashboard -->
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Dashboard Comercial</h2>
        <p class="mt-1 text-teal-100">Gestión del equipo de ventas</p>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500">Prospectos Totales</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $prospectsCount; ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">Afiliaciones Activas</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $activeAffiliationsCount; ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500">Por Vencer (30 días)</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $expiringCount; ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <p class="text-sm font-medium text-gray-500">Vendedores Activos</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo count($affiliators); ?></p>
        </div>
    </div>
    
    <!-- Team Performance Table -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Desempeño por Vendedor</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afiliaciones</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ingresos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actividades</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendientes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($teamPerformance as $member): 
                        $affiliations = $member['affiliations']['total'] ?? 0;
                        $revenue = $member['affiliations']['total_amount'] ?? 0;
                        $activities = $member['activities']['total'] ?? 0;
                        $pending = $member['activities']['pending'] ?? 0;
                        $goalProgress = min(100, ($affiliations / 20) * 100);
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold"><?php echo substr($member['user']['name'], 0, 1); ?></span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($member['user']['name']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($member['user']['email']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $affiliations; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$<?php echo number_format($revenue, 0); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $activities; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $pending > 10 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                <?php echo $pending; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-<?php echo $goalProgress >= 80 ? 'green' : ($goalProgress >= 50 ? 'yellow' : 'red'); ?>-500 h-2 rounded-full" style="width: <?php echo $goalProgress; ?>%"></div>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo round($goalProgress); ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendencia Mensual</h3>
            <canvas id="monthlyTrendChart" height="200"></canvas>
        </div>
        
        <!-- Channel Distribution -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prospectos por Canal</h3>
            <canvas id="channelChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="<?php echo BASE_URL; ?>/prospectos" class="flex items-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Ver Prospectos</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/afiliados/vencimientos" class="flex items-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Vencimientos</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/reportes/comerciales" class="flex items-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-2 bg-purple-100 rounded-lg mr-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Reportes</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/journey/upselling" class="flex items-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-2 bg-green-100 rounded-lg mr-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Oportunidades</span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trend Chart
    const monthlyData = <?php echo json_encode($monthlyStats ?? []); ?>;
    const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
    const chartData = new Array(12).fill(0);
    monthlyData.forEach(item => {
        chartData[parseInt(item.month) - 1] = parseInt(item.count);
    });
    
    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Afiliaciones',
                data: chartData,
                borderColor: 'rgb(20, 184, 166)',
                tension: 0.3,
                fill: true,
                backgroundColor: 'rgba(20, 184, 166, 0.1)'
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
    
    // Channel Distribution Chart
    const channelData = <?php echo json_encode($channelStats ?? []); ?>;
    const channelLabels = {
        'chatbot': 'Chatbot',
        'alta_directa': 'Alta Directa',
        'evento_gratuito': 'Evento Gratuito',
        'evento_pagado': 'Evento Pagado',
        'buscador': 'Buscador',
        'jefatura_comercial': 'Jefatura'
    };
    
    new Chart(document.getElementById('channelChart'), {
        type: 'doughnut',
        data: {
            labels: channelData.map(c => channelLabels[c.source_channel] || c.source_channel),
            datasets: [{
                data: channelData.map(c => c.count),
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(139, 92, 246)',
                    'rgb(236, 72, 153)',
                    'rgb(107, 114, 128)'
                ]
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
