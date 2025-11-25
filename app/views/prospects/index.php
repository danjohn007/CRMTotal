<!-- Prospects Index -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Prospectos</h2>
            <p class="mt-1 text-sm text-gray-500">Gestión de prospectos por los 6 canales de obtención</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/prospectos/nuevo" 
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nuevo Prospecto
        </a>
    </div>
    
    <!-- Channel Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <?php foreach ($channels as $key => $label): 
            $count = 0;
            foreach ($channelStats as $stat) {
                if ($stat['source_channel'] === $key) {
                    $count = $stat['count'];
                    break;
                }
            }
        ?>
        <a href="<?php echo BASE_URL; ?>/prospectos/canal/<?php echo $key; ?>" 
           class="p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition text-center">
            <p class="text-2xl font-bold text-gray-900"><?php echo $count; ?></p>
            <p class="text-xs text-gray-500"><?php echo $label; ?></p>
        </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Filter Bar -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Buscar por nombre, RFC o teléfono..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <select name="channel" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todos los canales</option>
                <?php foreach ($channels as $key => $label): ?>
                <option value="<?php echo $key; ?>" <?php echo ($_GET['channel'] ?? '') === $key ? 'selected' : ''; ?>>
                    <?php echo $label; ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Filtrar
            </button>
        </form>
    </div>
    
    <!-- Prospects Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Canal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Afiliador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($prospects)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No hay prospectos registrados
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <?php echo $channels[$prospect['source_channel']] ?? $prospect['source_channel']; ?>
                            </span>
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
