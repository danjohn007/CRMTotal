<!-- Commercial Reports -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Reportes Comerciales</h2>
            <p class="mt-1 text-sm text-gray-500">Métricas de ventas y desempeño comercial</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/reportes" class="text-blue-600 hover:text-blue-800 text-sm">← Volver a Reportes</a>
    </div>
    
    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-center">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Desde</label>
                <input type="date" name="start_date" value="<?php echo $startDate; ?>" 
                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Hasta</label>
                <input type="date" name="end_date" value="<?php echo $endDate; ?>" 
                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 mt-4 sm:mt-5">
                Filtrar
            </button>
        </form>
    </div>
    
    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Tasa de Renovación</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $renewalRate; ?>%</p>
            <p class="text-xs <?php echo $renewalRate >= 80 ? 'text-green-600' : 'text-yellow-600'; ?> mt-1">
                Meta: 80%
            </p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Total Afiliadores</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo count($teamPerformance); ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Canales Activos</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo count($channelStats); ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Ingresos del Año</p>
            <p class="text-3xl font-bold text-gray-900">
                $<?php 
                $totalRevenue = array_sum(array_column($teamPerformance, 'revenue'));
                echo number_format($totalRevenue, 0); 
                ?>
            </p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Channel Performance -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversión por Canal</h3>
            <canvas id="channelChart" height="200"></canvas>
            <div class="mt-4 space-y-2">
                <?php 
                $channelLabels = [
                    'chatbot' => 'Chatbot',
                    'alta_directa' => 'Alta Directa',
                    'evento_gratuito' => 'Evento Gratuito',
                    'evento_pagado' => 'Evento Pagado',
                    'buscador' => 'Buscador',
                    'jefatura_comercial' => 'Reasignaciones'
                ];
                foreach ($channelStats as $channel): ?>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600"><?php echo $channelLabels[$channel['source_channel']] ?? $channel['source_channel']; ?></span>
                    <span class="font-medium text-gray-900"><?php echo $channel['count']; ?> prospectos</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Monthly Trend -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendencia Mensual <?php echo date('Y'); ?></h3>
            <canvas id="monthlyChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Team Performance -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Desempeño por Afiliador</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afiliador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Afiliaciones</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nuevas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ingresos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meta (20)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($teamPerformance as $member): 
                        $goalProgress = min(100, ($member['total'] / 20) * 100);
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $member['total']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $member['new']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$<?php echo number_format($member['revenue'], 0); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-<?php echo $goalProgress >= 100 ? 'green' : ($goalProgress >= 50 ? 'yellow' : 'red'); ?>-500 h-2 rounded-full" 
                                         style="width: <?php echo $goalProgress; ?>%"></div>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Channel Chart
    const channelData = <?php echo json_encode($channelStats); ?>;
    const channelLabels = {
        'chatbot': 'Chatbot',
        'alta_directa': 'Alta Directa',
        'evento_gratuito': 'Evento Gratuito',
        'evento_pagado': 'Evento Pagado',
        'buscador': 'Buscador',
        'jefatura_comercial': 'Reasignaciones'
    };
    
    new Chart(document.getElementById('channelChart'), {
        type: 'pie',
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
    
    // Monthly Trend Chart
    const monthlyData = <?php echo json_encode($monthlyStats ?? []); ?>;
    const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
    const chartData = new Array(12).fill(0);
    monthlyData.forEach(item => {
        chartData[parseInt(item.month) - 1] = parseInt(item.count);
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
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
