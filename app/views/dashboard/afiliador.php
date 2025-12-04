<!-- Enhanced Afiliador Dashboard -->
<div class="space-y-6">
    <!-- Welcome & Summary -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        <p class="mt-1 text-blue-100">Panel de Afiliador - <?php echo date('l, d \d\e F \d\e Y'); ?></p>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Prospects -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Prospectos</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $prospectsCount; ?></p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>/prospectos" class="mt-4 inline-flex items-center text-sm text-purple-600 hover:text-purple-800">
                Ver todos →
            </a>
        </div>
        
        <!-- Today's Sales -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ventas Hoy</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $salesToday['count'] ?? 0; ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-sm text-gray-500">$<?php echo number_format($salesToday['amount'] ?? 0, 0); ?></span>
            </div>
        </div>
        
        <!-- Monthly Sales -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ventas del Mes</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $salesCurrentMonth['count'] ?? 0; ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-sm text-gray-500">$<?php echo number_format($salesCurrentMonth['amount'] ?? 0, 0); ?></span>
            </div>
        </div>
        
        <!-- Commission -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Comisión Estimada</p>
                    <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($monthlyCommission, 0); ?></p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-sm text-gray-500">Este mes</p>
        </div>
    </div>
    
    <!-- Overdue Alert -->
    <?php if ($overdueCount > 0): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    Tienes <strong><?php echo $overdueCount; ?></strong> actividades vencidas pendientes.
                    <a href="<?php echo BASE_URL; ?>/agenda" class="font-medium underline">Ver ahora</a>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Sales Charts Section -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Resumen de Ventas</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 uppercase">Ayer</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo $salesYesterday['count'] ?? 0; ?></p>
                <p class="text-sm text-gray-600">$<?php echo number_format($salesYesterday['amount'] ?? 0, 0); ?></p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-xs text-blue-600 uppercase">Semana Actual</p>
                <p class="text-2xl font-bold text-blue-900"><?php echo $salesCurrentWeek['count'] ?? 0; ?></p>
                <p class="text-sm text-blue-600">$<?php echo number_format($salesCurrentWeek['amount'] ?? 0, 0); ?></p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 uppercase">Semana Pasada</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo $salesLastWeek['count'] ?? 0; ?></p>
                <p class="text-sm text-gray-600">$<?php echo number_format($salesLastWeek['amount'] ?? 0, 0); ?></p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-xs text-green-600 uppercase">Mes Actual</p>
                <p class="text-2xl font-bold text-green-900"><?php echo $salesCurrentMonth['count'] ?? 0; ?></p>
                <p class="text-sm text-green-600">$<?php echo number_format($salesCurrentMonth['amount'] ?? 0, 0); ?></p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 uppercase">Mes Pasado</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo $salesLastMonth['count'] ?? 0; ?></p>
                <p class="text-sm text-gray-600">$<?php echo number_format($salesLastMonth['amount'] ?? 0, 0); ?></p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-xs text-purple-600 uppercase">Acumulado Anual</p>
                <p class="text-2xl font-bold text-purple-900"><?php echo $salesYear['count'] ?? 0; ?></p>
                <p class="text-sm text-purple-600">$<?php echo number_format($salesYear['amount'] ?? 0, 0); ?></p>
            </div>
        </div>
        
        <!-- Weekly Sales Chart -->
        <div class="mt-6">
            <canvas id="weeklySalesChart" height="100"></canvas>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- New Assigned Prospects -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">🎯 Nuevos Prospectos Asignados</h3>
                <p class="text-xs text-gray-500 mt-1">Últimos 7 días</p>
            </div>
            <div class="p-6">
                <?php if (empty($newProspects)): ?>
                <p class="text-gray-500 text-center py-4">No hay prospectos nuevos asignados</p>
                <?php else: ?>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    <?php foreach ($newProspects as $prospect): ?>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($prospect['business_name'] ?? $prospect['owner_name'] ?? 'Sin nombre'); ?>
                            </p>
                            <div class="flex items-center space-x-2 mt-1">
                                <?php 
                                $channelLabels = [
                                    'chatbot' => ['label' => 'Chatbot', 'class' => 'bg-purple-100 text-purple-800'],
                                    'evento_gratuito' => ['label' => 'Evento', 'class' => 'bg-blue-100 text-blue-800'],
                                    'evento_pagado' => ['label' => 'Evento', 'class' => 'bg-green-100 text-green-800'],
                                    'alta_directa' => ['label' => 'Manual', 'class' => 'bg-gray-100 text-gray-800'],
                                    'buscador' => ['label' => 'Buscador', 'class' => 'bg-yellow-100 text-yellow-800'],
                                    'jefatura_comercial' => ['label' => 'Reasignaciones', 'class' => 'bg-indigo-100 text-indigo-800'],
                                ];
                                $channel = $channelLabels[$prospect['source_channel']] ?? ['label' => $prospect['source_channel'], 'class' => 'bg-gray-100 text-gray-800'];
                                ?>
                                <span class="px-2 py-0.5 text-xs rounded-full <?php echo $channel['class']; ?>">
                                    <?php echo $channel['label']; ?>
                                </span>
                                <span class="text-xs text-gray-400">
                                    hace <?php echo $prospect['days_ago']; ?> día<?php echo $prospect['days_ago'] != 1 ? 's' : ''; ?>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-1">
                            <?php if (!empty($prospect['whatsapp'])): ?>
                            <a href="https://wa.me/52<?php echo preg_replace('/[^0-9]/', '', $prospect['whatsapp']); ?>" 
                               target="_blank" 
                               class="p-2 text-green-600 hover:bg-green-100 rounded-full" 
                               title="WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($prospect['phone'])): ?>
                            <a href="tel:<?php echo $prospect['phone']; ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-100 rounded-full" 
                               title="Llamar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>/prospectos/<?php echo $prospect['id']; ?>" 
                               class="p-2 text-gray-600 hover:bg-gray-200 rounded-full" 
                               title="Ver Expediente">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Activities -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">📋 Actividades Pendientes</h3>
            </div>
            <div class="p-6">
                <?php if (empty($todayActivities)): ?>
                <p class="text-gray-500 text-center py-8">No tienes actividades programadas para hoy</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($todayActivities, 0, 5) as $activity): ?>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-2 h-2 rounded-full 
                            <?php echo $activity['priority'] === 'urgente' ? 'bg-red-500' : 
                                       ($activity['priority'] === 'alta' ? 'bg-orange-500' : 'bg-blue-500'); ?>">
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                            <p class="text-xs text-gray-500">
                                <?php echo date('H:i', strtotime($activity['scheduled_date'])); ?>
                                <?php if ($activity['business_name']): ?>
                                - <?php echo htmlspecialchars($activity['business_name']); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?php echo $activity['activity_type'] === 'llamada' ? 'bg-blue-100 text-blue-800' : 
                                       ($activity['activity_type'] === 'whatsapp' ? 'bg-green-100 text-green-800' : 
                                       ($activity['activity_type'] === 'visita' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')); ?>">
                            <?php echo ucfirst($activity['activity_type']); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/agenda/nueva" class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-800">
                    + Nueva Actividad
                </a>
            </div>
        </div>
        
        <!-- Upcoming Activities (Week/Month) -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">📆 Próxima Agenda</h3>
                <p class="text-xs text-gray-500 mt-1">Próximos 7 días</p>
            </div>
            <div class="p-6">
                <?php if (empty($upcomingActivities)): ?>
                <p class="text-gray-500 text-center py-4">No tienes actividades programadas próximamente</p>
                <?php else: ?>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    <?php foreach ($upcomingActivities as $activity): ?>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-16 text-center">
                            <p class="text-xs text-gray-500"><?php echo date('D', strtotime($activity['scheduled_date'])); ?></p>
                            <p class="text-lg font-bold text-gray-900"><?php echo date('d', strtotime($activity['scheduled_date'])); ?></p>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                            <p class="text-xs text-gray-500">
                                <?php echo date('H:i', strtotime($activity['scheduled_date'])); ?>
                                <?php if ($activity['business_name']): ?>
                                - <?php echo htmlspecialchars($activity['business_name']); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                            <?php echo ucfirst($activity['activity_type']); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/agenda" class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-800">
                    Ver calendario completo →
                </a>
            </div>
        </div>
    </div>
    
    <!-- Expiring Affiliations -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">⏰ Membresías por Vencer (30 días)</h3>
        </div>
        <div class="p-6">
            <?php if (empty($expiringAffiliations)): ?>
            <p class="text-gray-500 text-center py-8">No hay membresías próximas a vencer</p>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach (array_slice($expiringAffiliations, 0, 6) as $affiliation): ?>
                <?php 
                    $daysLeft = floor((strtotime($affiliation['expiration_date']) - time()) / 86400);
                    $urgency = $daysLeft <= 7 ? 'red' : ($daysLeft <= 15 ? 'yellow' : 'green');
                ?>
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($affiliation['business_name']); ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?php echo htmlspecialchars($affiliation['membership_name']); ?>
                        </p>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-<?php echo $urgency; ?>-100 text-<?php echo $urgency; ?>-800">
                        <?php echo $daysLeft; ?> días
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Goals Progress -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Progreso de Metas Mensuales</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- New Affiliations Goal -->
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Nuevas Afiliaciones</span>
                    <span class="text-sm text-gray-500"><?php echo $monthlyStats['new_affiliations'] ?? 0; ?>/<?php echo $newAffiliationsGoal; ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full" style="width: <?php echo min(100, (($monthlyStats['new_affiliations'] ?? 0) / $newAffiliationsGoal) * 100); ?>%"></div>
                </div>
            </div>
            
            <!-- Revenue Goal -->
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Ventas del Mes</span>
                    <span class="text-sm text-gray-500">$<?php echo number_format($monthlyStats['total_amount'] ?? 0, 0); ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-green-600 h-3 rounded-full" style="width: <?php echo min(100, (($monthlyStats['total_amount'] ?? 0) / 100000) * 100); ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="<?php echo BASE_URL; ?>/prospectos/nuevo" class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-3 bg-purple-100 rounded-full mb-3">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Nuevo Prospecto</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/afiliados/nuevo" class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-3 bg-green-100 rounded-full mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Nueva Afiliación</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/agenda/nueva" class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-3 bg-blue-100 rounded-full mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Nueva Actividad</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/buscador" class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-3 bg-yellow-100 rounded-full mb-3">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Buscador</span>
        </a>
    </div>
</div>

<script>
// Weekly Sales Chart
document.addEventListener('DOMContentLoaded', function() {
    const weeklyData = <?php echo json_encode($weeklySalesChart ?? []); ?>;
    
    // Prepare data for all 7 days of the week
    const dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    const salesByDay = [0, 0, 0, 0, 0, 0, 0];
    const amountByDay = [0, 0, 0, 0, 0, 0, 0];
    
    weeklyData.forEach(item => {
        const dayIndex = item.day_num - 1; // MySQL DAYOFWEEK is 1-7 (Sunday=1)
        salesByDay[dayIndex] = item.count;
        amountByDay[dayIndex] = item.amount;
    });
    
    const ctx = document.getElementById('weeklySalesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dayNames,
                datasets: [{
                    label: 'Ventas',
                    data: salesByDay,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Ventas de la Semana Actual'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
