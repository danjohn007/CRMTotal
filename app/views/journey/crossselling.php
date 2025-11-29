<!-- Customer Journey - Cross-selling Opportunities (Stage 4) -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/journey" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al Customer Journey
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                🎯 Oportunidades de Cross-Selling
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Etapa 4 del Customer Journey: Servicios adicionales de la cámara
            </p>
        </div>
    </div>
    
    <!-- Stage 4 Info -->
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl shadow-sm p-6 border border-purple-200">
        <div class="flex items-start">
            <span class="text-4xl mr-4">🎯</span>
            <div>
                <h3 class="text-lg font-semibold text-purple-800">Etapa 4: Cross-Selling de Servicios</h3>
                <p class="text-purple-700 mt-1">
                    Invitación a la contratación de servicios adicionales de la cámara. Se inicia un contador 
                    de todos los pagos y servicios contratados que la persona moral o física realiza.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm text-purple-700">
                        🏛️ Renta de Salones
                    </span>
                    <span class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm text-purple-700">
                        📣 Servicios de Marketing
                    </span>
                    <span class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm text-purple-700">
                        📚 Cursos y Talleres
                    </span>
                    <span class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm text-purple-700">
                        🎪 Expo's y Eventos Pagados
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <span class="text-xl">🎯</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Oportunidades</p>
                    <p class="text-2xl font-bold text-purple-600"><?php echo count($opportunities); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <span class="text-xl">💼</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Servicios Disponibles</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo count($services); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <span class="text-xl">📁</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Categorías</p>
                    <p class="text-2xl font-bold text-blue-600"><?php echo count($serviceCategories); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Categories -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Categorías de Servicios Disponibles</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <?php 
            $categoryIcons = [
                'salon_rental' => '🏛️',
                'event_organization' => '🎪',
                'course' => '📚',
                'conference' => '🎤',
                'training' => '👨‍🏫',
                'marketing_email' => '📧',
                'marketing_videowall' => '📺',
                'marketing_social' => '📱',
                'marketing_platform' => '💻',
                'gestoria' => '📋',
                'tramites' => '📑',
                'otros' => '📦'
            ];
            foreach ($serviceCategories as $code => $name): 
            ?>
            <div class="p-3 bg-purple-50 rounded-lg text-center border border-purple-200">
                <span class="text-2xl"><?php echo $categoryIcons[$code] ?? '📦'; ?></span>
                <p class="text-xs text-gray-700 mt-1"><?php echo $name; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Opportunities Table -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Afiliados Sin Servicios Contratados</h3>
                <p class="text-sm text-gray-500">Oportunidades de cross-selling identificadas</p>
            </div>
        </div>
        
        <?php if (empty($opportunities)): ?>
        <div class="p-12 text-center">
            <span class="text-6xl">🎉</span>
            <p class="text-gray-500 mt-4">¡Todos los afiliados han contratado servicios adicionales!</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membresía</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Industria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afiliador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($opportunities as $opp): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($opp['business_name']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($opp['commercial_name'] ?? '-'); ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                <?php echo htmlspecialchars($opp['current_membership']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo htmlspecialchars($opp['industry'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo htmlspecialchars($opp['affiliator_name'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="<?php echo BASE_URL; ?>/journey/<?php echo $opp['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver Journey
                                </a>
                                <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $opp['id']; ?>" 
                                   class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                    Ofrecer Servicios
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
    
    <!-- Available Services -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Servicios Disponibles para Ofrecer</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($services as $service): ?>
            <div class="p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($service['name']); ?></p>
                        <p class="text-xs text-gray-500 mt-1">
                            <?php echo $serviceCategories[$service['category']] ?? $service['category']; ?>
                        </p>
                    </div>
                    <span class="text-xl"><?php echo $categoryIcons[$service['category']] ?? '📦'; ?></span>
                </div>
                <?php if (!empty($service['description'])): ?>
                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars(substr($service['description'], 0, 100)); ?>...</p>
                <?php endif; ?>
                <div class="mt-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Precio público</p>
                        <p class="font-bold text-gray-900">$<?php echo number_format($service['price'] ?? 0, 0); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Precio afiliado</p>
                        <p class="font-bold text-green-600">$<?php echo number_format($service['member_price'] ?? 0, 0); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
