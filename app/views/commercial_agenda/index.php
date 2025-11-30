<!-- Agenda y Acciones Comerciales - Main Index -->
<div class="space-y-6" x-data="{ 
    activeTab: 'today', 
    showQuickActions: false,
    selectedContact: null
}">
    
    <!-- Motivational Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4">
                <div class="text-4xl"><?php echo $motivationalMessage['icon']; ?></div>
                <div>
                    <h2 class="text-2xl font-bold"><?php echo $motivationalMessage['title']; ?></h2>
                    <p class="mt-1 text-blue-100"><?php echo $motivationalMessage['message']; ?></p>
                </div>
            </div>
            <?php if (!$isWorkingHours): ?>
            <div class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-sm font-medium">
                🌙 Fuera de horario laboral
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
            <div class="bg-white/20 rounded-lg p-3 backdrop-blur-sm">
                <p class="text-3xl font-bold"><?php echo count($todayActivities); ?></p>
                <p class="text-sm text-blue-100">Acciones Hoy</p>
            </div>
            <div class="bg-white/20 rounded-lg p-3 backdrop-blur-sm">
                <p class="text-3xl font-bold"><?php echo count($overdueActivities); ?></p>
                <p class="text-sm text-blue-100">Vencidas</p>
            </div>
            <div class="bg-white/20 rounded-lg p-3 backdrop-blur-sm">
                <p class="text-3xl font-bold"><?php echo $unreadCount; ?></p>
                <p class="text-sm text-blue-100">Notificaciones</p>
            </div>
            <div class="bg-white/20 rounded-lg p-3 backdrop-blur-sm">
                <p class="text-3xl font-bold"><?php echo count($prioritizedProspects); ?></p>
                <p class="text-sm text-blue-100">Prospectos Prioritarios</p>
            </div>
            <div class="bg-white/20 rounded-lg p-3 backdrop-blur-sm">
                <p class="text-3xl font-bold"><?php echo $performanceMetrics['completion_rate']; ?>%</p>
                <p class="text-sm text-blue-100">Tasa de Éxito</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation Tabs & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between bg-white rounded-lg shadow-sm p-4">
        <div class="flex space-x-1 bg-gray-100 rounded-lg p-1">
            <button @click="activeTab = 'today'" 
                    :class="activeTab === 'today' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-sm font-medium rounded-md transition">
                📅 Hoy
            </button>
            <button @click="activeTab = 'week'" 
                    :class="activeTab === 'week' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-sm font-medium rounded-md transition">
                📆 Semana
            </button>
            <button @click="activeTab = 'month'" 
                    :class="activeTab === 'month' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-sm font-medium rounded-md transition">
                🗓️ Mes
            </button>
            <button @click="activeTab = 'prospects'" 
                    :class="activeTab === 'prospects' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-sm font-medium rounded-md transition">
                👥 Prospectos
            </button>
            <button @click="activeTab = 'notifications'" 
                    :class="activeTab === 'notifications' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-sm font-medium rounded-md transition relative">
                🔔 Notificaciones
                <?php if ($unreadCount > 0): ?>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                    <?php echo $unreadCount > 9 ? '9+' : $unreadCount; ?>
                </span>
                <?php endif; ?>
            </button>
            <button @click="activeTab = 'metrics'" 
                    :class="activeTab === 'metrics' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-sm font-medium rounded-md transition">
                📊 Métricas
            </button>
        </div>
        
        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Actividad
            </a>
        </div>
    </div>
    
    <!-- Today's Actions Tab -->
    <div x-show="activeTab === 'today'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Calendar & Activities -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Calendar -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Calendario de Actividades</h3>
                <div id="calendar"></div>
            </div>
            
            <!-- Today's Activities List -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Actividades de Hoy</h3>
                    <span class="text-sm text-gray-500"><?php echo date('d/m/Y'); ?></span>
                </div>
                
                <?php if (empty($todayActivities)): ?>
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-gray-500">¡No hay actividades pendientes para hoy!</p>
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva" class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Crear nueva actividad
                    </a>
                </div>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($todayActivities as $activity): ?>
                    <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                <?php echo match($activity['activity_type']) {
                                    'llamada' => 'bg-blue-100 text-blue-600',
                                    'whatsapp' => 'bg-green-100 text-green-600',
                                    'email' => 'bg-purple-100 text-purple-600',
                                    'visita' => 'bg-orange-100 text-orange-600',
                                    'reunion' => 'bg-indigo-100 text-indigo-600',
                                    default => 'bg-gray-100 text-gray-600'
                                }; ?>">
                                <?php echo match($activity['activity_type']) {
                                    'llamada' => '📞',
                                    'whatsapp' => '💬',
                                    'email' => '✉️',
                                    'visita' => '🚗',
                                    'reunion' => '👥',
                                    default => '📋'
                                }; ?>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                                <div class="flex items-center text-sm text-gray-500 space-x-2">
                                    <span><?php echo date('H:i', strtotime($activity['scheduled_date'])); ?></span>
                                    <?php if ($activity['business_name']): ?>
                                    <span>•</span>
                                    <span class="text-blue-600"><?php echo htmlspecialchars($activity['business_name']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $activity['priority'] === 'urgente' ? 'bg-red-100 text-red-800' : 
                                          ($activity['priority'] === 'alta' ? 'bg-orange-100 text-orange-800' : 
                                          ($activity['priority'] === 'media' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')); ?>">
                                <?php echo ucfirst($activity['priority']); ?>
                            </span>
                            <a href="<?php echo BASE_URL; ?>/agenda-comercial/<?php echo $activity['id']; ?>/editar" 
                               class="p-2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Column: Quick Actions & Overdue -->
        <div class="space-y-6">
            <!-- Quick Contact Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva?type=invitacion" 
                       class="p-4 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition text-center">
                        <div class="text-2xl mb-1">📨</div>
                        <p class="text-sm font-medium text-gray-900">Enviar Invitación</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva?type=whatsapp" 
                       class="p-4 rounded-lg border border-gray-200 hover:border-green-500 hover:bg-green-50 transition text-center">
                        <div class="text-2xl mb-1">💬</div>
                        <p class="text-sm font-medium text-gray-900">WhatsApp</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva?type=email" 
                       class="p-4 rounded-lg border border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition text-center">
                        <div class="text-2xl mb-1">✉️</div>
                        <p class="text-sm font-medium text-gray-900">Email</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva?type=visita" 
                       class="p-4 rounded-lg border border-gray-200 hover:border-orange-500 hover:bg-orange-50 transition text-center">
                        <div class="text-2xl mb-1">🚗</div>
                        <p class="text-sm font-medium text-gray-900">Visita</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva?type=prospectacion" 
                       class="p-4 rounded-lg border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 transition text-center">
                        <div class="text-2xl mb-1">🎯</div>
                        <p class="text-sm font-medium text-gray-900">Prospectación</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/prospectos/nuevo" 
                       class="p-4 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50 transition text-center">
                        <div class="text-2xl mb-1">👤</div>
                        <p class="text-sm font-medium text-gray-900">Nuevo Prospecto</p>
                    </a>
                </div>
            </div>
            
            <!-- Overdue Activities -->
            <?php if (!empty($overdueActivities)): ?>
            <div class="bg-red-50 rounded-lg shadow-sm p-6 border border-red-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-red-800">⚠️ Actividades Vencidas</h3>
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded-full"><?php echo count($overdueActivities); ?></span>
                </div>
                <div class="space-y-3">
                    <?php foreach (array_slice($overdueActivities, 0, 5) as $activity): ?>
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/<?php echo $activity['id']; ?>/editar" 
                       class="block p-3 rounded-lg bg-white border border-red-200 hover:bg-red-50 transition">
                        <p class="font-medium text-gray-900 text-sm"><?php echo htmlspecialchars($activity['title']); ?></p>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-red-600">
                                Vencida: <?php echo date('d/m/Y', strtotime($activity['scheduled_date'])); ?>
                            </span>
                            <?php if ($activity['business_name']): ?>
                            <span class="text-xs text-gray-500"><?php echo htmlspecialchars($activity['business_name']); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Pending Requirements -->
            <?php if (!empty($pendingRequirements)): ?>
            <div class="bg-yellow-50 rounded-lg shadow-sm p-6 border border-yellow-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-yellow-800">📋 Requerimientos Pendientes</h3>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-sm rounded-full"><?php echo count($pendingRequirements); ?></span>
                </div>
                <div class="space-y-3">
                    <?php foreach (array_slice($pendingRequirements, 0, 5) as $req): ?>
                    <div class="p-3 rounded-lg bg-white border border-yellow-200">
                        <p class="font-medium text-gray-900 text-sm"><?php echo htmlspecialchars($req['title']); ?></p>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-yellow-600">
                                <?php echo $req['due_date'] ? date('d/m/Y', strtotime($req['due_date'])) : 'Sin fecha'; ?>
                            </span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                <?php echo ucfirst($req['category'] ?? 'General'); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Week Tab -->
    <div x-show="activeTab === 'week'" x-cloak class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividades de la Semana</h3>
        <?php if (empty($weekActivities)): ?>
        <p class="text-gray-500 text-center py-8">No hay actividades programadas para esta semana.</p>
        <?php else: ?>
        <div class="space-y-4">
            <?php 
            $groupedByDate = [];
            foreach ($weekActivities as $act) {
                $date = date('Y-m-d', strtotime($act['scheduled_date']));
                if (!isset($groupedByDate[$date])) {
                    $groupedByDate[$date] = [];
                }
                $groupedByDate[$date][] = $act;
            }
            foreach ($groupedByDate as $date => $activities): 
            ?>
            <div class="border-l-4 border-blue-500 pl-4">
                <h4 class="font-medium text-gray-900 mb-2">
                    <?php 
                    $dateObj = new DateTime($date);
                    $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                    echo $days[(int)$dateObj->format('w')] . ' ' . $dateObj->format('d/m');
                    ?>
                </h4>
                <div class="space-y-2">
                    <?php foreach ($activities as $activity): ?>
                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/<?php echo $activity['id']; ?>/editar" 
                       class="block p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></span>
                            <span class="text-sm text-gray-500"><?php echo date('H:i', strtotime($activity['scheduled_date'])); ?></span>
                        </div>
                        <?php if ($activity['business_name']): ?>
                        <p class="text-sm text-blue-600 mt-1"><?php echo htmlspecialchars($activity['business_name']); ?></p>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Month Tab -->
    <div x-show="activeTab === 'month'" x-cloak class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Resumen del Mes</h3>
            <span class="text-gray-500"><?php echo strftime('%B %Y', time()); ?></span>
        </div>
        
        <!-- Monthly Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-blue-600"><?php echo $activityStats['total'] ?? 0; ?></p>
                <p class="text-sm text-gray-600">Total Actividades</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-green-600"><?php echo $activityStats['completed'] ?? 0; ?></p>
                <p class="text-sm text-gray-600">Completadas</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-yellow-600"><?php echo $activityStats['pending'] ?? 0; ?></p>
                <p class="text-sm text-gray-600">Pendientes</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-red-600"><?php echo $activityStats['overdue'] ?? 0; ?></p>
                <p class="text-sm text-gray-600">Vencidas</p>
            </div>
        </div>
        
        <!-- Activity Type Distribution -->
        <?php if (!empty($typeStats)): ?>
        <h4 class="font-medium text-gray-900 mb-3">Distribución por Tipo</h4>
        <div class="space-y-2">
            <?php foreach ($typeStats as $type): ?>
            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50">
                <span class="text-sm text-gray-600"><?php echo $activityTypes[$type['activity_type']] ?? ucfirst($type['activity_type']); ?></span>
                <span class="font-medium text-gray-900"><?php echo $type['count']; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Prospects Tab -->
    <div x-show="activeTab === 'prospects'" x-cloak class="space-y-6">
        <!-- Priority 1: New Prospects with RFC + WhatsApp -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center space-x-2 mb-4">
                <span class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                <h3 class="text-lg font-semibold text-gray-900">Prospectos Prioritarios</h3>
                <span class="text-sm text-gray-500">(RFC + WhatsApp)</span>
            </div>
            
            <?php 
            $priority1 = array_filter($prioritizedProspects, fn($p) => ($p['priority_type'] ?? '') === 'new_prospect');
            if (empty($priority1)): 
            ?>
            <p class="text-gray-500 text-center py-4">No hay prospectos con alta prioridad.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">WhatsApp</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Canal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Días</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($priority1 as $prospect): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="<?php echo BASE_URL; ?>/prospectos/<?php echo $prospect['id']; ?>" class="font-medium text-blue-600 hover:text-blue-800">
                                    <?php echo htmlspecialchars($prospect['business_name'] ?? $prospect['commercial_name'] ?? 'Sin nombre'); ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo htmlspecialchars($prospect['rfc'] ?? ''); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo htmlspecialchars($prospect['whatsapp'] ?? ''); ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    <?php echo match($prospect['source_channel'] ?? '') {
                                        'chatbot' => 'bg-purple-100 text-purple-800',
                                        'evento_gratuito' => 'bg-green-100 text-green-800',
                                        'evento_pagado' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    }; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $prospect['source_channel'] ?? 'Directo')); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo $prospect['days_since_creation'] ?? 0; ?> días</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <?php if ($prospect['whatsapp']): ?>
                                    <a href="https://wa.me/52<?php echo preg_replace('/[^0-9]/', '', $prospect['whatsapp']); ?>" 
                                       target="_blank"
                                       class="p-1 text-green-600 hover:text-green-800">
                                        💬
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($prospect['corporate_email']): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($prospect['corporate_email']); ?>" 
                                       class="p-1 text-blue-600 hover:text-blue-800">
                                        ✉️
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva?contact_id=<?php echo $prospect['id']; ?>" 
                                       class="p-1 text-indigo-600 hover:text-indigo-800">
                                        📅
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Cross-Selling Opportunities -->
        <?php if (!empty($crossSellingOpportunities)): ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center space-x-2 mb-4">
                <span class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-sm font-bold">🔄</span>
                <h3 class="text-lg font-semibold text-gray-900">Oportunidades de Cross-Selling</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($crossSellingOpportunities as $opp): ?>
                <div class="p-4 rounded-lg border border-gray-200 hover:border-indigo-300 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($opp['business_name'] ?? ''); ?></p>
                            <p class="text-sm text-gray-500">Membresía: <?php echo htmlspecialchars($opp['membership_name'] ?? ''); ?></p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                            <?php echo $opp['days_until_expiration'] ?? 0; ?> días
                        </span>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <a href="<?php echo BASE_URL; ?>/agenda-comercial/nueva?contact_id=<?php echo $opp['id']; ?>&type=seguimiento" 
                           class="text-xs text-indigo-600 hover:text-indigo-800">
                            + Agendar seguimiento
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Up-Selling Opportunities -->
        <?php if (!empty($upsellingOpportunities)): ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center space-x-2 mb-4">
                <span class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-sm font-bold">📈</span>
                <h3 class="text-lg font-semibold text-gray-900">Oportunidades de Up-Selling</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($upsellingOpportunities as $opp): ?>
                <div class="p-4 rounded-lg border border-gray-200 hover:border-emerald-300 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($opp['business_name'] ?? ''); ?></p>
                            <p class="text-sm text-gray-500">
                                <?php echo htmlspecialchars($opp['current_membership'] ?? ''); ?> → 
                                <span class="text-emerald-600 font-medium"><?php echo htmlspecialchars($opp['suggested_upgrade'] ?? ''); ?></span>
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-800">
                            Renovación en <?php echo $opp['days_until_expiration'] ?? 0; ?> días
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Notifications Tab -->
    <div x-show="activeTab === 'notifications'" x-cloak class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Centro de Notificaciones</h3>
                <p class="text-sm text-gray-500"><?php echo $unreadCount; ?> sin leer</p>
            </div>
            <?php if ($unreadCount > 0): ?>
            <form action="<?php echo BASE_URL; ?>/agenda-comercial/notificaciones/marcar-todas" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                    Marcar todas como leídas
                </button>
            </form>
            <?php endif; ?>
        </div>
        
        <!-- Notification Type Filters -->
        <div class="flex flex-wrap gap-2 mb-6">
            <?php foreach ($notificationTypes as $type => $info): 
                $count = count($notificationsByType[$type] ?? []);
                if ($count === 0) continue;
            ?>
            <button class="px-3 py-1 text-sm rounded-full bg-<?php echo $info['color']; ?>-100 text-<?php echo $info['color']; ?>-800 hover:bg-<?php echo $info['color']; ?>-200 transition">
                <?php echo $info['icon']; ?> <?php echo $info['label']; ?> (<?php echo $count; ?>)
            </button>
            <?php endforeach; ?>
        </div>
        
        <!-- Notifications List -->
        <?php if (empty($notifications)): ?>
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-gray-500">No tienes notificaciones</p>
        </div>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach (array_slice($notifications, 0, 20) as $notification): 
                $typeInfo = $notificationTypes[$notification['type']] ?? ['icon' => '📌', 'color' => 'gray', 'label' => 'General'];
            ?>
            <div class="p-4 rounded-lg border <?php echo !$notification['is_read'] ? 'border-blue-300 bg-blue-50' : 'border-gray-200'; ?> hover:bg-gray-50 transition">
                <div class="flex items-start space-x-3">
                    <div class="text-2xl"><?php echo $typeInfo['icon']; ?></div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($notification['title']); ?></p>
                            <span class="text-xs text-gray-500">
                                <?php echo date('d/m H:i', strtotime($notification['created_at'])); ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                        <div class="mt-2 flex items-center space-x-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-<?php echo $typeInfo['color']; ?>-100 text-<?php echo $typeInfo['color']; ?>-800">
                                <?php echo $typeInfo['label']; ?>
                            </span>
                            <?php if ($notification['link']): ?>
                            <a href="<?php echo BASE_URL . $notification['link']; ?>" class="text-xs text-blue-600 hover:text-blue-800">
                                Ver detalle →
                            </a>
                            <?php endif; ?>
                            <?php if (!$notification['is_read']): ?>
                            <a href="<?php echo BASE_URL; ?>/agenda-comercial/notificacion/<?php echo $notification['id']; ?>/leida" 
                               class="text-xs text-gray-500 hover:text-gray-700">
                                Marcar como leída
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Metrics Tab -->
    <div x-show="activeTab === 'metrics'" x-cloak class="space-y-6">
        <!-- Performance Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl">📋</span>
                </div>
                <p class="text-3xl font-bold text-gray-900"><?php echo $performanceMetrics['activities_completed']; ?></p>
                <p class="text-sm text-gray-500">Actividades Completadas</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl">👥</span>
                </div>
                <p class="text-3xl font-bold text-gray-900"><?php echo $performanceMetrics['contacts_created']; ?></p>
                <p class="text-sm text-gray-500">Contactos Creados</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl">🤝</span>
                </div>
                <p class="text-3xl font-bold text-gray-900"><?php echo $performanceMetrics['affiliations_closed']; ?></p>
                <p class="text-sm text-gray-500">Afiliaciones Cerradas</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl">💰</span>
                </div>
                <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($performanceMetrics['revenue_generated'], 0); ?></p>
                <p class="text-sm text-gray-500">Ingresos Generados</p>
            </div>
        </div>
        
        <!-- Completion Rate Gauge -->
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
        </div>
        
        <!-- Off-Hours Activity (For Managers) -->
        <?php if (in_array($userRole, ['jefe_comercial', 'direccion', 'superadmin', 'mesa_directiva', 'consejero']) && !empty($offHoursActivity)): ?>
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg shadow-sm p-6 border border-purple-200">
            <div class="flex items-center space-x-2 mb-4">
                <span class="text-2xl">🌟</span>
                <h3 class="text-lg font-semibold text-purple-900">Compromiso Excepcional del Equipo</h3>
            </div>
            <p class="text-sm text-purple-700 mb-4">Colaboradores que han trabajado fuera del horario laboral este mes:</p>
            <div class="space-y-3">
                <?php foreach ($offHoursActivity as $user): ?>
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-purple-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 font-semibold"><?php echo mb_substr($user['name'], 0, 1, 'UTF-8'); ?></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-purple-600"><?php echo $user['off_hours_actions']; ?></p>
                        <p class="text-xs text-gray-500">acciones extra</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Calendar Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: '<?php echo BASE_URL; ?>/agenda-comercial/api/eventos',
            eventClick: function(info) {
                window.location.href = '<?php echo BASE_URL; ?>/agenda-comercial/' + info.event.id + '/editar';
            },
            eventDidMount: function(info) {
                if (info.event.extendedProps.status === 'completada') {
                    info.el.style.opacity = '0.6';
                    info.el.style.textDecoration = 'line-through';
                }
            }
        });
        calendar.render();
    }
});
</script>
