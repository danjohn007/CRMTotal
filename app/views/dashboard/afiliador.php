<!-- Afiliador Dashboard -->
<div class="space-y-6">
    <!-- Welcome & Summary -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        <p class="mt-1 text-blue-100">Panel de Afiliador - Resumen del día</p>
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
        
        <!-- Affiliations -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Afiliaciones del Mes</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $monthlyStats['total'] ?? 0; ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="flex items-center text-sm">
                    <span class="text-gray-500">Meta: <?php echo $newAffiliationsGoal; ?></span>
                    <span class="ml-2 text-green-600 font-medium">
                        (<?php echo round((($monthlyStats['total'] ?? 0) / $newAffiliationsGoal) * 100); ?>%)
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Activities Today -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Actividades Hoy</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo count($todayActivities); ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>/agenda" class="mt-4 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                Ver agenda →
            </a>
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
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Activities -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Actividades de Hoy</h3>
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
                                       'bg-gray-100 text-gray-800'); ?>">
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
        
        <!-- Expiring Affiliations -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Membresías por Vencer (30 días)</h3>
            </div>
            <div class="p-6">
                <?php if (empty($expiringAffiliations)): ?>
                <p class="text-gray-500 text-center py-8">No hay membresías próximas a vencer</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($expiringAffiliations, 0, 5) as $affiliation): ?>
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
    </div>
    
    <!-- Goals Progress -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Progreso de Metas Mensuales</h3>
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
