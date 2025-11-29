<!-- Customer Journey - Council Eligibility (Stage 6) -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/journey" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al Customer Journey
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                🏛️ Elegibilidad para Consejo
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Etapa 6 del Customer Journey: Afiliados con 2+ años de afiliación ininterrumpida
            </p>
        </div>
    </div>
    
    <!-- Stage 6 Info -->
    <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl shadow-sm p-6 border border-amber-200">
        <div class="flex items-start">
            <span class="text-4xl mr-4">🏛️</span>
            <div>
                <h3 class="text-lg font-semibold text-amber-800">Etapa 6: Consejo de la Cámara</h3>
                <p class="text-amber-700 mt-1">
                    La solicitud de ingreso al consejo de la cámara está disponible únicamente para personas físicas o morales 
                    con al menos <strong>2 años de afiliación ininterrumpida</strong> a la cámara.
                </p>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3 bg-white rounded-lg">
                        <p class="text-sm font-medium text-gray-700">👤 Consejero Propietario</p>
                        <p class="text-xs text-gray-500">Miembro con voz y voto en las sesiones del consejo</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg">
                        <p class="text-sm font-medium text-gray-700">👥 Consejero Invitado</p>
                        <p class="text-xs text-gray-500">Miembro con voz pero sin voto en las sesiones del consejo</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Stats Cards -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-amber-100 rounded-full">
                    <span class="text-2xl">⭐</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Afiliados Elegibles</p>
                    <p class="text-2xl font-bold text-amber-600"><?php echo count($eligibleAffiliates); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <span class="text-2xl">✓</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Consejeros Activos</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo count($currentCouncilMembers); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <span class="text-2xl">📅</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Requisito</p>
                    <p class="text-2xl font-bold text-blue-600">2+ años</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Eligible Affiliates -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200 bg-amber-50">
                <h3 class="text-lg font-semibold text-gray-900">⭐ Afiliados Elegibles para el Consejo</h3>
                <p class="text-sm text-gray-500">Afiliados con 2+ años de afiliación continua</p>
            </div>
            <div class="p-6">
                <?php if (empty($eligibleAffiliates)): ?>
                <p class="text-gray-500 text-center py-8">No hay afiliados que cumplan con el requisito de 2 años</p>
                <?php else: ?>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    <?php foreach ($eligibleAffiliates as $affiliate): ?>
                    <div class="flex items-center justify-between p-4 bg-amber-50 rounded-lg border border-amber-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-amber-700 font-semibold">
                                    <?php echo mb_substr($affiliate['business_name'] ?? 'E', 0, 1, 'UTF-8'); ?>
                                </span>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($affiliate['business_name']); ?></p>
                                <p class="text-sm text-gray-500">
                                    <?php echo number_format($affiliate['years_affiliated'], 1); ?> años de afiliación
                                </p>
                                <p class="text-xs text-gray-400">
                                    Desde: <?php echo date('d/m/Y', strtotime($affiliate['first_affiliation_date'])); ?>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <?php if ($affiliate['council_status'] === 'active'): ?>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                En Consejo
                            </span>
                            <?php elseif ($affiliate['council_status'] === 'pending_approval'): ?>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                Pendiente
                            </span>
                            <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/journey/<?php echo $affiliate['id']; ?>" 
                               class="text-sm text-amber-600 hover:text-amber-800 font-medium">
                                Ver Journey →
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Current Council Members -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200 bg-green-50">
                <h3 class="text-lg font-semibold text-gray-900">✓ Consejeros Activos</h3>
                <p class="text-sm text-gray-500">Miembros actuales del consejo de la cámara</p>
            </div>
            <div class="p-6">
                <?php if (empty($currentCouncilMembers)): ?>
                <p class="text-gray-500 text-center py-8">No hay consejeros activos registrados</p>
                <?php else: ?>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    <?php foreach ($currentCouncilMembers as $member): ?>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <?php if ($member['member_type'] === 'propietario'): ?>
                                <span class="text-green-700 font-semibold">👤</span>
                                <?php else: ?>
                                <span class="text-green-700 font-semibold">👥</span>
                                <?php endif; ?>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($member['business_name']); ?></p>
                                <p class="text-sm text-gray-600">
                                    <?php echo htmlspecialchars($member['owner_name'] ?? $member['legal_representative'] ?? '-'); ?>
                                </p>
                                <?php if (!empty($member['position'])): ?>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($member['position']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                <?php echo $member['member_type'] === 'propietario' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                <?php echo $member['member_type'] === 'propietario' ? 'Propietario' : 'Invitado'; ?>
                            </span>
                            <p class="text-xs text-gray-400 mt-1">
                                Desde: <?php echo date('d/m/Y', strtotime($member['start_date'])); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Council Membership Requirements -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Requisitos para el Consejo</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center mb-2">
                    <span class="text-xl mr-2">📅</span>
                    <h4 class="font-medium text-gray-900">Antigüedad</h4>
                </div>
                <p class="text-sm text-gray-600">Mínimo 2 años de afiliación ininterrumpida a la cámara</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center mb-2">
                    <span class="text-xl mr-2">💳</span>
                    <h4 class="font-medium text-gray-900">Pagos al Corriente</h4>
                </div>
                <p class="text-sm text-gray-600">Sin adeudos pendientes en membresía o servicios</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center mb-2">
                    <span class="text-xl mr-2">🏢</span>
                    <h4 class="font-medium text-gray-900">Negocio Activo</h4>
                </div>
                <p class="text-sm text-gray-600">Empresa o persona física con actividad comercial vigente</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center mb-2">
                    <span class="text-xl mr-2">📝</span>
                    <h4 class="font-medium text-gray-900">Solicitud Formal</h4>
                </div>
                <p class="text-sm text-gray-600">Carta de interés y compromiso con la cámara</p>
            </div>
        </div>
    </div>
</div>
