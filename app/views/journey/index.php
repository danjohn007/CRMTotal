<!-- Customer Journey Index -->
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Customer Journey</h2>
        <p class="mt-1 text-sm text-gray-500">Visualización del proceso comercial y oportunidades</p>
    </div>
    
    <!-- Journey Stages -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Etapas del Journey</h3>
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <?php
            $stages = [
                ['name' => 'Prospectación', 'icon' => '🎯', 'count' => 0],
                ['name' => 'Atención', 'icon' => '💬', 'count' => 0],
                ['name' => 'Facturación', 'icon' => '📄', 'count' => 0],
                ['name' => 'Servicio Postventa', 'icon' => '🤝', 'count' => 0],
                ['name' => 'Upselling', 'icon' => '📈', 'count' => 0],
            ];
            foreach ($typeStats as $stat) {
                if ($stat['contact_type'] === 'prospecto') $stages[0]['count'] = $stat['count'];
                if ($stat['contact_type'] === 'afiliado') $stages[1]['count'] = $stat['count'];
            }
            
            foreach ($stages as $index => $stage):
            ?>
            <div class="flex items-center">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-2xl mx-auto">
                        <?php echo $stage['icon']; ?>
                    </div>
                    <p class="mt-2 text-sm font-medium text-gray-900"><?php echo $stage['name']; ?></p>
                    <p class="text-lg font-bold text-blue-600"><?php echo $stage['count']; ?></p>
                </div>
                <?php if ($index < count($stages) - 1): ?>
                <div class="hidden md:block mx-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upselling Opportunities -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Oportunidades de Upselling</h3>
                <a href="<?php echo BASE_URL; ?>/journey/upselling" class="text-sm text-blue-600 hover:text-blue-800">Ver todas →</a>
            </div>
            <div class="p-6">
                <?php if (empty($upsellingOpportunities)): ?>
                <p class="text-gray-500 text-center py-4">No hay oportunidades de upselling</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($upsellingOpportunities, 0, 5) as $opp): ?>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($opp['business_name']); ?></p>
                            <p class="text-sm text-gray-500">
                                Membresía: <?php echo htmlspecialchars($opp['current_membership']); ?> 
                                (vence <?php echo date('d/m/Y', strtotime($opp['expiration_date'])); ?>)
                            </p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $opp['id']; ?>" class="text-sm text-green-600 hover:text-green-800">
                            Upgrade →
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Cross-selling Opportunities -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Oportunidades de Cross-selling</h3>
                <a href="<?php echo BASE_URL; ?>/journey/crossselling" class="text-sm text-blue-600 hover:text-blue-800">Ver todas →</a>
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
                        <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $opp['id']; ?>" class="text-sm text-purple-600 hover:text-purple-800">
                            Ofrecer servicios →
                        </a>
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
                'jefatura_comercial' => ['label' => 'Jefatura', 'icon' => '👔'],
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
    
    <!-- Services by Category -->
    <?php if (!empty($servicesByCategory)): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Servicios Contratados por Categoría</h3>
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
