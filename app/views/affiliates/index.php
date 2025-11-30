<!-- Affiliates Index -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Afiliados</h2>
            <p class="mt-1 text-sm text-gray-500">Gestión del Expediente Digital Afiliado (EDA)</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/afiliados/vencimientos" 
               class="inline-flex items-center px-4 py-2 border border-yellow-300 text-yellow-700 bg-yellow-50 rounded-lg hover:bg-yellow-100">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Vencimientos (<?php echo $expiringCount; ?>)
            </a>
            <a href="<?php echo BASE_URL; ?>/afiliados/nuevo" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Afiliado
            </a>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="<?php echo BASE_URL; ?>/afiliados" class="p-4 bg-green-50 border border-green-200 rounded-lg text-center hover:bg-green-100">
            <p class="text-2xl font-bold text-green-600"><?php echo count($affiliates); ?></p>
            <p class="text-sm text-gray-600">Afiliados Activos</p>
        </a>
        <a href="<?php echo BASE_URL; ?>/afiliados/vencimientos" class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-center hover:bg-yellow-100">
            <p class="text-2xl font-bold text-yellow-600"><?php echo $expiringCount; ?></p>
            <p class="text-sm text-gray-600">Por Vencer</p>
        </a>
        <a href="<?php echo BASE_URL; ?>/afiliados/exafiliados" class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center hover:bg-gray-100">
            <p class="text-2xl font-bold text-gray-600">-</p>
            <p class="text-sm text-gray-600">Exafiliados</p>
        </a>
        <a href="<?php echo BASE_URL; ?>/journey/upselling" class="p-4 bg-purple-50 border border-purple-200 rounded-lg text-center hover:bg-purple-100">
            <p class="text-2xl font-bold text-purple-600">↑</p>
            <p class="text-sm text-gray-600">Oportunidades</p>
        </a>
    </div>
    
    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Buscar por nombre, RFC, teléfono, WhatsApp o email..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <select name="membership" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Todas las membresías</option>
                <option value="BASICA" <?php echo ($_GET['membership'] ?? '') === 'BASICA' ? 'selected' : ''; ?>>Básica</option>
                <option value="PYME" <?php echo ($_GET['membership'] ?? '') === 'PYME' ? 'selected' : ''; ?>>PYME</option>
                <option value="PREMIER" <?php echo ($_GET['membership'] ?? '') === 'PREMIER' ? 'selected' : ''; ?>>Premier</option>
                <option value="PATROCINADOR" <?php echo ($_GET['membership'] ?? '') === 'PATROCINADOR' ? 'selected' : ''; ?>>Patrocinador</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Buscar
            </button>
        </form>
    </div>
    
    <!-- Affiliates Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membresía</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perfil</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($affiliates)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No hay afiliados registrados
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($affiliates as $affiliate): 
                        $daysToExpire = $affiliate['expiration_date'] ? 
                            floor((strtotime($affiliate['expiration_date']) - time()) / 86400) : null;
                        $expirationStatus = 'active';
                        if ($daysToExpire !== null) {
                            if ($daysToExpire < 0) $expirationStatus = 'expired';
                            elseif ($daysToExpire <= 30) $expirationStatus = 'warning';
                        }
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 font-medium text-sm">
                                        <?php echo substr($affiliate['business_name'] ?? 'A', 0, 1); ?>
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($affiliate['business_name'] ?? ''); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($affiliate['commercial_name'] ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($affiliate['rfc'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                <?php echo ($affiliate['membership_code'] ?? '') === 'PATROCINADOR' ? 'bg-purple-100 text-purple-800' : 
                                          (($affiliate['membership_code'] ?? '') === 'PREMIER' ? 'bg-blue-100 text-blue-800' : 
                                          'bg-gray-100 text-gray-800'); ?>">
                                <?php echo htmlspecialchars($affiliate['membership_name'] ?? 'Sin membresía'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if ($affiliate['expiration_date']): ?>
                            <span class="<?php echo $expirationStatus === 'expired' ? 'text-red-600' : 
                                                   ($expirationStatus === 'warning' ? 'text-yellow-600' : 'text-gray-900'); ?>">
                                <?php echo date('d/m/Y', strtotime($affiliate['expiration_date'])); ?>
                                <?php if ($daysToExpire !== null && $daysToExpire > 0 && $daysToExpire <= 30): ?>
                                <span class="text-xs">(<?php echo $daysToExpire; ?> días)</span>
                                <?php endif; ?>
                            </span>
                            <?php else: ?>
                            <span class="text-gray-500">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo ($affiliate['affiliation_status'] ?? '') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo ($affiliate['affiliation_status'] ?? '') === 'active' ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $affiliate['profile_completion']; ?>%"></div>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo $affiliate['profile_completion']; ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $affiliate['id']; ?>" 
                               class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $affiliate['id']; ?>/expediente" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">EDA</a>
                            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $affiliate['id']; ?>/editar" 
                               class="text-gray-600 hover:text-gray-900">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
