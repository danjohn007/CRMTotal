<!-- Customer Journey Index - 6 Stage Visualization -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Customer Journey</h2>
            <p class="mt-1 text-sm text-gray-500">Visualización del proceso comercial de 6 etapas</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/journey/upselling" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-sm">
                📈 Up-Selling
            </a>
            <a href="<?php echo BASE_URL; ?>/journey/crossselling" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                🎯 Cross-Selling
            </a>
            <a href="<?php echo BASE_URL; ?>/journey/council" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition text-sm">
                🏛️ Consejo
            </a>
        </div>
    </div>
    
    <!-- 6 Journey Stages Visual -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Las 6 Etapas del Customer Journey</h3>
        
        <!-- Desktop View - Horizontal Stages -->
        <div class="hidden lg:block">
            <div class="relative">
                <!-- Progress Line -->
                <div class="absolute top-8 left-0 right-0 h-1 bg-gray-200 z-0"></div>
                
                <!-- Stages -->
                <div class="relative z-10 flex justify-between">
                    <?php foreach ($journeyStages as $num => $stage): ?>
                    <div class="flex flex-col items-center" style="width: 16%;">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl shadow-lg
                            <?php 
                            $count = $journeyStageStats[$num] ?? 0;
                            echo $count > 0 ? 'bg-' . $stage['color'] . '-100 border-2 border-' . $stage['color'] . '-500' : 'bg-gray-100 border-2 border-gray-300';
                            ?>">
                            <?php echo $stage['icon']; ?>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-gray-900 text-center"><?php echo $stage['name']; ?></p>
                        <p class="text-2xl font-bold text-<?php echo $stage['color']; ?>-600"><?php echo $journeyStageStats[$num] ?? 0; ?></p>
                        <p class="text-xs text-gray-500 text-center mt-1 px-2"><?php echo substr($stage['description'], 0, 60); ?>...</p>
                    </div>
                    <?php if ($num < 6): ?>
                    <div class="flex items-start pt-6">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Mobile/Tablet View - Vertical Stages -->
        <div class="lg:hidden grid grid-cols-2 md:grid-cols-3 gap-4">
            <?php foreach ($journeyStages as $num => $stage): ?>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center text-xl
                    <?php 
                    $count = $journeyStageStats[$num] ?? 0;
                    echo $count > 0 ? 'bg-blue-100' : 'bg-gray-100';
                    ?>">
                    <?php echo $stage['icon']; ?>
                </div>
                <p class="mt-2 text-sm font-semibold text-gray-900"><?php echo $num; ?>. <?php echo $stage['name']; ?></p>
                <p class="text-2xl font-bold text-blue-600"><?php echo $journeyStageStats[$num] ?? 0; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Stage Details Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($journeyStages as $num => $stage): ?>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-<?php echo $stage['color']; ?>-500">
            <div class="flex items-center justify-between mb-2">
                <span class="text-2xl"><?php echo $stage['icon']; ?></span>
                <span class="text-sm font-medium text-gray-500">Etapa <?php echo $num; ?></span>
            </div>
            <h4 class="font-semibold text-gray-900"><?php echo $stage['name']; ?></h4>
            <p class="text-sm text-gray-600 mt-1"><?php echo $stage['description']; ?></p>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-2xl font-bold text-<?php echo $stage['color']; ?>-600"><?php echo $journeyStageStats[$num] ?? 0; ?></span>
                <span class="text-xs text-gray-500">contactos</span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upselling Opportunities (Stage 5) -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between bg-yellow-50">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">📈</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Oportunidades de Up-Selling</h3>
                        <p class="text-sm text-gray-500">Etapa 5: Upgrade de membresía</p>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/journey/upselling" class="text-sm text-yellow-600 hover:text-yellow-800 font-medium">Ver todas →</a>
            </div>
            <div class="p-6">
                <?php if (empty($upsellingOpportunities)): ?>
                <p class="text-gray-500 text-center py-4">No hay oportunidades de up-selling pendientes</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($upsellingOpportunities, 0, 5) as $opp): ?>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($opp['business_name']); ?></p>
                            <p class="text-sm text-gray-500">
                                Actual: <?php echo htmlspecialchars($opp['current_membership']); ?>
                                <?php 
                                $invCount = $opp['invitations_this_year'] ?? 0;
                                if ($invCount < 2):
                                ?>
                                <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">
                                    <?php echo $invCount; ?>/2 invitaciones
                                </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/journey/<?php echo $opp['id']; ?>" class="text-sm text-yellow-600 hover:text-yellow-800 font-medium">
                            Ver Journey →
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Cross-selling Opportunities (Stage 4) -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between bg-purple-50">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🎯</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Oportunidades de Cross-Selling</h3>
                        <p class="text-sm text-gray-500">Etapa 4: Servicios adicionales</p>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/journey/crossselling" class="text-sm text-purple-600 hover:text-purple-800 font-medium">Ver todas →</a>
            </div>
            <div class="p-6">
                <?php if (empty($crosssellingOpportunities)): ?>
                <p class="text-gray-500 text-center py-4">No hay oportunidades de cross-selling</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($crosssellingOpportunities, 0, 5) as $opp): ?>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($opp['business_name']); ?></p>
                            <p class="text-sm text-gray-500">Sin servicios contratados</p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/journey/<?php echo $opp['id']; ?>" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                            Ver Journey →
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Council Eligible (Stage 6) -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between bg-amber-50">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🏛️</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Elegibles para Consejo</h3>
                        <p class="text-sm text-gray-500">Etapa 6: 2+ años de afiliación</p>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/journey/council" class="text-sm text-amber-600 hover:text-amber-800 font-medium">Ver todos →</a>
            </div>
            <div class="p-6">
                <?php if (empty($councilEligible)): ?>
                <p class="text-gray-500 text-center py-4">No hay afiliados elegibles para el consejo</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($councilEligible, 0, 5) as $eligible): ?>
                    <div class="flex items-center justify-between p-4 bg-amber-50 rounded-lg border border-amber-200">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($eligible['business_name']); ?></p>
                            <p class="text-sm text-gray-500">
                                <?php echo number_format($eligible['years_affiliated'], 1); ?> años de afiliación
                                <?php if ($eligible['council_status'] === 'active'): ?>
                                <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">En consejo</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/journey/<?php echo $eligible['id']; ?>" class="text-sm text-amber-600 hover:text-amber-800 font-medium">
                            Ver Journey →
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Pending Upselling Invitations -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">📬</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Invitaciones de Upgrade Pendientes</h3>
                        <p class="text-sm text-gray-500">Seguimiento de up-selling enviados</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($pendingUpsellingInvitations)): ?>
                <p class="text-gray-500 text-center py-4">No hay invitaciones pendientes de respuesta</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($pendingUpsellingInvitations, 0, 5) as $inv): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($inv['business_name']); ?></p>
                            <p class="text-sm text-gray-500">
                                <?php echo htmlspecialchars($inv['current_membership']); ?> → <?php echo htmlspecialchars($inv['target_membership']); ?>
                            </p>
                            <p class="text-xs text-gray-400">Enviado: <?php echo date('d/m/Y H:i', strtotime($inv['invitation_date'])); ?></p>
                        </div>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pendiente</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Channel Performance -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rendimiento por Canal de Adquisición</h3>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <?php
            $channelLabels = [
                'chatbot' => ['label' => 'Chatbot', 'icon' => '🤖'],
                'alta_directa' => ['label' => 'Alta Directa', 'icon' => '📝'],
                'evento_gratuito' => ['label' => 'Evento Gratuito', 'icon' => '🎉'],
                'evento_pagado' => ['label' => 'Evento Pagado', 'icon' => '🎟️'],
                'buscador' => ['label' => 'Buscador', 'icon' => '🔍'],
                'jefatura_comercial' => ['label' => 'Reasignaciones', 'icon' => '🔄'],
            ];
            foreach ($channelStats as $channel):
                $info = $channelLabels[$channel['source_channel']] ?? ['label' => $channel['source_channel'], 'icon' => '📊'];
            ?>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <span class="text-2xl"><?php echo $info['icon']; ?></span>
                <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo $channel['count']; ?></p>
                <p class="text-xs text-gray-500"><?php echo $info['label']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Free Event Attendance Stats (Stage 3) -->
    <?php if (!empty($freeEventStats)): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center mb-4">
            <span class="text-2xl mr-3">🎟️</span>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Asistencia a Eventos Gratuitos</h3>
                <p class="text-sm text-gray-500">Etapa 3: Registro de eventos gratuitos (desayunos, open days, conferencias, ferias, exposiciones)</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php 
            $categoryLabels = [
                'desayuno' => ['label' => 'Desayunos', 'icon' => '☕'],
                'open_day' => ['label' => 'Open Days', 'icon' => '🚪'],
                'conferencia' => ['label' => 'Conferencias', 'icon' => '🎤'],
                'feria' => ['label' => 'Ferias', 'icon' => '🎪'],
                'exposicion' => ['label' => 'Exposiciones', 'icon' => '🖼️'],
                'networking' => ['label' => 'Networking', 'icon' => '🤝'],
            ];
            foreach ($freeEventStats as $stat): 
                $catInfo = $categoryLabels[$stat['category']] ?? ['label' => ucfirst($stat['category']), 'icon' => '📅'];
            ?>
            <div class="p-4 border border-gray-200 rounded-lg text-center">
                <span class="text-2xl"><?php echo $catInfo['icon']; ?></span>
                <p class="text-lg font-bold text-gray-900 mt-2"><?php echo $stat['total_attended']; ?></p>
                <p class="text-sm text-gray-500"><?php echo $catInfo['label']; ?></p>
                <p class="text-xs text-gray-400"><?php echo $stat['total_registrations']; ?> registros</p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Services by Category (Stage 4) -->
    <?php if (!empty($servicesByCategory)): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center mb-4">
            <span class="text-2xl mr-3">💼</span>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Servicios Contratados por Categoría</h3>
                <p class="text-sm text-gray-500">Etapa 4: Cross-selling de servicios adicionales</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach ($servicesByCategory as $category): ?>
            <div class="p-4 border border-gray-200 rounded-lg">
                <p class="text-lg font-bold text-gray-900"><?php echo $category['count']; ?></p>
                <p class="text-sm text-gray-500"><?php echo $serviceCategories[$category['category']] ?? $category['category']; ?></p>
                <p class="text-sm font-medium text-green-600">$<?php echo number_format($category['total'], 0); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
