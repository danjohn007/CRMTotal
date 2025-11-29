<!-- Expedientes Digitales Únicos - Index -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">📁 Expedientes Digitales Únicos</h2>
            <p class="mt-1 text-sm text-gray-500">Gestión del perfil completo de afiliados</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/afiliados/nuevo" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Afiliado
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Expedientes</p>
                    <p class="text-3xl font-bold text-green-600"><?php echo $totalAffiliates; ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Afiliados registrados</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Por Completar</p>
                    <p class="text-3xl font-bold text-yellow-600"><?php echo $incompleteExpedientes; ?></p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Expedientes incompletos</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completados</p>
                    <p class="text-3xl font-bold text-blue-600"><?php echo $totalAffiliates - $incompleteExpedientes; ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Expedientes al 100%</p>
        </div>
    </div>
    
    <!-- Expedientes List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Listado de Expedientes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WhatsApp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membresía</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avance Expediente</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($affiliates)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No hay expedientes registrados
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($affiliates as $affiliate): 
                        $completion = $affiliate['profile_completion'] ?? 0;
                        $progressColor = $completion < 25 ? 'bg-red-500' : ($completion < 60 ? 'bg-yellow-500' : ($completion < 100 ? 'bg-blue-500' : 'bg-green-500'));
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-medium text-sm">
                                        <?php echo mb_substr($affiliate['business_name'] ?? 'E', 0, 1, 'UTF-8'); ?>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php 
                            $affiliateWhatsapp = preg_replace('/[^0-9]/', '', $affiliate['whatsapp'] ?? '');
                            if (!empty($affiliateWhatsapp) && strlen($affiliateWhatsapp) === 10): 
                            ?>
                            <a href="https://wa.me/52<?php echo htmlspecialchars($affiliateWhatsapp); ?>" target="_blank" class="text-green-600 hover:underline">
                                <?php echo htmlspecialchars($affiliate['whatsapp']); ?>
                            </a>
                            <?php elseif (!empty($affiliate['whatsapp'])): ?>
                            <span class="text-gray-900"><?php echo htmlspecialchars($affiliate['whatsapp']); ?></span>
                            <?php else: ?>
                            <span class="text-gray-500">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                <?php echo ($affiliate['membership_code'] ?? '') === 'PATROCINADOR' ? 'bg-purple-100 text-purple-800' : 
                                          (($affiliate['membership_code'] ?? '') === 'PREMIER' ? 'bg-blue-100 text-blue-800' : 
                                          'bg-gray-100 text-gray-800'); ?>">
                                <?php echo htmlspecialchars($affiliate['membership_name'] ?? 'Sin membresía'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-3 mr-3">
                                    <div class="<?php echo $progressColor; ?> h-3 rounded-full transition-all duration-300" style="width: <?php echo $completion; ?>%"></div>
                                </div>
                                <span class="text-sm font-medium <?php echo $completion < 100 ? 'text-yellow-600' : 'text-green-600'; ?>">
                                    <?php echo $completion; ?>%
                                </span>
                            </div>
                            <?php if ($completion < 100): ?>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php 
                                if ($completion < 25) echo "Falta: Etapa 1, 2 y 3";
                                elseif ($completion < 60) echo "Falta: Etapa 2 y 3";
                                else echo "Falta: Etapa 3";
                                ?>
                            </p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $affiliate['id']; ?>" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">Ver Expediente</a>
                            <?php if ($completion < 100): ?>
                            <a href="<?php echo BASE_URL; ?>/expedientes/<?php echo $affiliate['id']; ?>/etapa-a" 
                               class="text-green-600 hover:text-green-900">Continuar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
