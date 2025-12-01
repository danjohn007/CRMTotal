<!-- Prospects by Channel -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="<?php echo BASE_URL; ?>/prospectos" 
                   class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-900">
                    Prospectos - <?php echo htmlspecialchars($channels[$channel] ?? $channel); ?>
                </h2>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Total de prospectos: <?php echo count($prospects); ?>
            </p>
        </div>
        <a href="<?php echo BASE_URL; ?>/prospectos/nuevo" 
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nuevo Prospecto
        </a>
    </div>

    <!-- Channel Selection -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex flex-wrap gap-2">
            <?php foreach ($channels as $key => $label): ?>
            <a href="<?php echo BASE_URL; ?>/prospectos/canal/<?php echo $key; ?>" 
               class="px-4 py-2 rounded-lg transition <?php echo $key === $channel ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                <?php echo htmlspecialchars($label); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Prospects Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Afiliador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($prospects)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-1">No hay prospectos en este canal</p>
                                <p class="text-sm text-gray-500">Crea un nuevo prospecto para comenzar</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($prospects as $prospect): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-purple-600 font-medium text-sm">
                                        <?php echo substr($prospect['business_name'] ?? 'P', 0, 1); ?>
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($prospect['business_name'] ?? $prospect['owner_name'] ?? 'Sin nombre'); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($prospect['rfc'] ?? '-'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($prospect['owner_name'] ?? '-'); ?></div>
                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($prospect['phone'] ?? $prospect['whatsapp'] ?? '-'); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($prospect['affiliator_name'] ?? 'Sin asignar'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-<?php echo $prospect['profile_completion'] >= 70 ? 'green' : ($prospect['profile_completion'] >= 35 ? 'yellow' : 'red'); ?>-500 h-2 rounded-full" 
                                         style="width: <?php echo $prospect['profile_completion']; ?>%"></div>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo $prospect['profile_completion']; ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('d/m/Y', strtotime($prospect['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?php echo BASE_URL; ?>/prospectos/<?php echo $prospect['id']; ?>" 
                               class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                            <a href="<?php echo BASE_URL; ?>/prospectos/<?php echo $prospect['id']; ?>/editar" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                            <a href="<?php echo BASE_URL; ?>/afiliados/nuevo?prospect_id=<?php echo $prospect['id']; ?>" 
                               class="text-green-600 hover:text-green-900">Afiliar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
