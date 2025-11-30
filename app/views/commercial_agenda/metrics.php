<!-- Commercial Agenda - Performance Metrics -->
<div class="space-y-6">
    <!-- Header with Motivational Message -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center space-x-4">
            <div class="text-4xl"><?php echo $motivationalMessage['icon']; ?></div>
            <div>
                <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($motivationalMessage['title']); ?></h2>
                <p class="mt-1 text-indigo-100"><?php echo htmlspecialchars($motivationalMessage['message']); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="<?php echo BASE_URL; ?>/agenda-comercial" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Agenda Comercial
        </a>
        <h1 class="text-xl font-bold text-gray-900">Métricas de Desempeño</h1>
    </div>
    
    <!-- Performance Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-2xl">📋</span>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $performanceMetrics['activities_completed']; ?></p>
            <p class="text-sm text-gray-500">Actividades Completadas</p>
            <p class="text-xs text-gray-400 mt-1">Este mes</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-2xl">👥</span>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $performanceMetrics['contacts_created']; ?></p>
            <p class="text-sm text-gray-500">Contactos Creados</p>
            <p class="text-xs text-gray-400 mt-1">Este mes</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-2xl">🤝</span>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo $performanceMetrics['affiliations_closed']; ?></p>
            <p class="text-sm text-gray-500">Afiliaciones Cerradas</p>
            <p class="text-xs text-gray-400 mt-1">Este mes</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-2xl">💰</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($performanceMetrics['revenue_generated'], 0); ?></p>
            <p class="text-sm text-gray-500">Ingresos Generados</p>
            <p class="text-xs text-gray-400 mt-1">Este mes</p>
        </div>
    </div>
    
    <!-- Completion Rate -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tasa de Cumplimiento</h3>
        <div class="flex items-center justify-center">
            <div class="relative w-48 h-48">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="96" cy="96" r="80" fill="none" stroke="#e5e7eb" stroke-width="16"/>
                    <circle cx="96" cy="96" r="80" fill="none" stroke="url(#gradient)" stroke-width="16" 
                            stroke-dasharray="<?php echo $performanceMetrics['completion_rate'] * 5.027; ?> 502.7"
                            stroke-linecap="round"/>
                    <defs>
                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#3b82f6"/>
                            <stop offset="100%" stop-color="#10b981"/>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-gray-900"><?php echo $performanceMetrics['completion_rate']; ?>%</p>
                        <p class="text-sm text-gray-500">Completado</p>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center text-sm text-gray-500 mt-4">
            Porcentaje de actividades completadas vs programadas este mes
        </p>
    </div>
    
    <!-- Monthly Trend -->
    <?php if (!empty($monthlyTrend)): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendencia Mensual</h3>
        <div class="space-y-3">
            <?php foreach ($monthlyTrend as $month): 
                $total = max($month['total'], 1);
                $completedPct = round(($month['completed'] / $total) * 100);
            ?>
            <div class="flex items-center">
                <span class="w-20 text-sm text-gray-600"><?php echo $month['month']; ?></span>
                <div class="flex-1 mx-4">
                    <div class="h-6 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-green-500" 
                             style="width: <?php echo $completedPct; ?>%"></div>
                    </div>
                </div>
                <span class="w-16 text-right text-sm font-medium text-gray-900">
                    <?php echo $month['completed']; ?>/<?php echo $month['total']; ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Team Metrics (For Managers) -->
    <?php if (in_array($userRole, ['jefe_comercial', 'direccion', 'superadmin']) && !empty($teamMetrics)): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rendimiento del Equipo</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afiliador</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actividades</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Completadas</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Prospectos</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tasa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($teamMetrics as $member): 
                        $memberTotal = max($member['activities_count'], 1);
                        $memberRate = round(($member['completed_count'] / $memberTotal) * 100);
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-indigo-600 font-semibold text-sm">
                                        <?php echo mb_substr($member['name'], 0, 1, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <span class="font-medium text-gray-900"><?php echo htmlspecialchars($member['name']); ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-500">
                            <?php echo $member['activities_count']; ?>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-green-600 font-medium">
                            <?php echo $member['completed_count']; ?>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-blue-600">
                            <?php echo $member['prospects_count']; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $memberRate >= 80 ? 'bg-green-100 text-green-800' : 
                                          ($memberRate >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                <?php echo $memberRate; ?>%
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
