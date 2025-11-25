<!-- Operational Reports -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Reportes Operativos</h2>
            <p class="mt-1 text-sm text-gray-500">Métricas operativas y de eficiencia</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/reportes" class="text-blue-600 hover:text-blue-800 text-sm">← Volver a Reportes</a>
    </div>
    
    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- By Type Stats -->
        <?php 
        $affiliatesCount = 0;
        $prospectsCount = 0;
        $exaffiliatesCount = 0;
        $othersCount = 0;
        foreach ($typeStats as $stat) {
            if ($stat['contact_type'] === 'afiliado') $affiliatesCount = $stat['count'];
            elseif ($stat['contact_type'] === 'prospecto') $prospectsCount = $stat['count'];
            elseif ($stat['contact_type'] === 'exafiliado') $exaffiliatesCount = $stat['count'];
            else $othersCount += $stat['count'];
        }
        ?>
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Afiliados Activos</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $affiliatesCount; ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Prospectos</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $prospectsCount; ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-gray-500">
            <p class="text-sm text-gray-500">Exafiliados</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $exaffiliatesCount; ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Otros Contactos</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $othersCount; ?></p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Contact Distribution -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Contactos</h3>
            <canvas id="typeChart" height="200"></canvas>
        </div>
        
        <!-- Events Stats -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas de Eventos</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-blue-50 rounded-lg text-center">
                    <p class="text-3xl font-bold text-blue-600"><?php echo $eventStats['total_events'] ?? 0; ?></p>
                    <p class="text-sm text-gray-600">Total Eventos</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg text-center">
                    <p class="text-3xl font-bold text-green-600"><?php echo $eventStats['paid_events'] ?? 0; ?></p>
                    <p class="text-sm text-gray-600">Eventos Pagados</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-lg text-center">
                    <p class="text-3xl font-bold text-purple-600"><?php echo $eventStats['total_registrations'] ?? 0; ?></p>
                    <p class="text-sm text-gray-600">Total Registros</p>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg text-center">
                    <p class="text-3xl font-bold text-yellow-600"><?php echo $eventStats['total_attendance'] ?? 0; ?></p>
                    <p class="text-sm text-gray-600">Total Asistencias</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tasa de Asistencia</span>
                    <span class="font-medium text-gray-900">
                        <?php 
                        $attendanceRate = ($eventStats['total_registrations'] ?? 0) > 0 
                            ? round(($eventStats['total_attendance'] / $eventStats['total_registrations']) * 100) 
                            : 0;
                        echo $attendanceRate . '%';
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Buscador Inteligente - Impacto</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-3xl font-bold text-blue-600"><?php echo $searchStats['total_searches'] ?? 0; ?></p>
                <p class="text-sm text-gray-600">Búsquedas Totales</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-3xl font-bold text-green-600">
                    <?php echo ($searchStats['total_searches'] ?? 0) - ($searchStats['no_match_count'] ?? 0); ?>
                </p>
                <p class="text-sm text-gray-600">Con Resultados</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-3xl font-bold text-yellow-600"><?php echo $searchStats['no_match_count'] ?? 0; ?></p>
                <p class="text-sm text-gray-600">Sin Resultados (NO MATCH)</p>
            </div>
        </div>
        
        <?php if (!empty($noMatches)): ?>
        <div class="mt-6">
            <h4 class="text-md font-medium text-gray-900 mb-3">Top Búsquedas Sin Resultados (Oportunidades)</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Término</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Búsquedas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Última Búsqueda</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($noMatches as $noMatch): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($noMatch['search_term']); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    <?php echo $noMatch['count']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($noMatch['last_search'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="<?php echo BASE_URL; ?>/prospectos/nuevo?industry=<?php echo urlencode($noMatch['search_term']); ?>" 
                                   class="text-blue-600 hover:text-blue-800">
                                    Crear prospecto →
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Profile Completion Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Completitud de Expedientes Digitales</h3>
        <div class="space-y-4">
            <?php
            $typeLabels = [
                'afiliado' => ['label' => 'Afiliados', 'color' => 'green'],
                'prospecto' => ['label' => 'Prospectos', 'color' => 'blue'],
                'exafiliado' => ['label' => 'Exafiliados', 'color' => 'gray'],
                'nuevo_usuario' => ['label' => 'Nuevos Usuarios', 'color' => 'yellow'],
                'funcionario' => ['label' => 'Funcionarios', 'color' => 'purple'],
                'publico_general' => ['label' => 'Público General', 'color' => 'pink']
            ];
            $total = array_sum(array_column($typeStats, 'count'));
            foreach ($typeStats as $stat):
                $info = $typeLabels[$stat['contact_type']] ?? ['label' => ucfirst(str_replace('_', ' ', $stat['contact_type'])), 'color' => 'gray'];
                $percentage = $total > 0 ? round(($stat['count'] / $total) * 100) : 0;
            ?>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700"><?php echo $info['label']; ?></span>
                    <span class="text-sm text-gray-500"><?php echo $stat['count']; ?> (<?php echo $percentage; ?>%)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-<?php echo $info['color']; ?>-500 h-3 rounded-full transition-all" 
                         style="width: <?php echo $percentage; ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Type Distribution Chart
    const typeData = <?php echo json_encode($typeStats); ?>;
    const typeLabels = {
        'afiliado': 'Afiliados',
        'prospecto': 'Prospectos',
        'exafiliado': 'Exafiliados',
        'nuevo_usuario': 'Nuevos',
        'funcionario': 'Funcionarios',
        'publico_general': 'Público',
        'consejero_propietario': 'Consejeros',
        'consejero_invitado': 'Consejeros Inv.',
        'mesa_directiva': 'Mesa Directiva'
    };
    
    const colors = {
        'afiliado': 'rgb(16, 185, 129)',
        'prospecto': 'rgb(59, 130, 246)',
        'exafiliado': 'rgb(107, 114, 128)',
        'nuevo_usuario': 'rgb(245, 158, 11)',
        'funcionario': 'rgb(139, 92, 246)',
        'publico_general': 'rgb(236, 72, 153)',
        'consejero_propietario': 'rgb(14, 165, 233)',
        'consejero_invitado': 'rgb(99, 102, 241)',
        'mesa_directiva': 'rgb(217, 70, 239)'
    };
    
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: typeData.map(t => typeLabels[t.contact_type] || t.contact_type),
            datasets: [{
                data: typeData.map(t => t.count),
                backgroundColor: typeData.map(t => colors[t.contact_type] || 'rgb(156, 163, 175)')
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
