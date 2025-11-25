<!-- Mesa Directiva Dashboard -->
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Dashboard Mesa Directiva</h2>
        <p class="mt-1 text-slate-300">Métricas mensuales de la Cámara de Comercio</p>
    </div>
    
    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-4xl font-bold text-gray-900"><?php echo $affiliatesCount; ?></p>
            <p class="text-gray-500 mt-2">Afiliados Activos</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-4xl font-bold text-gray-900"><?php echo $prospectsCount; ?></p>
            <p class="text-gray-500 mt-2">Prospectos</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <p class="text-4xl font-bold text-<?php echo $renewalRate >= 80 ? 'green' : 'yellow'; ?>-600"><?php echo $renewalRate; ?>%</p>
            <p class="text-gray-500 mt-2">Tasa de Renovación</p>
        </div>
    </div>
    
    <!-- Affiliation Status -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado de Afiliaciones</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php 
            $statusLabels = [
                'active' => ['label' => 'Activas', 'color' => 'green'],
                'expired' => ['label' => 'Vencidas', 'color' => 'red'],
                'pending_payment' => ['label' => 'Pago Pendiente', 'color' => 'yellow'],
                'cancelled' => ['label' => 'Canceladas', 'color' => 'gray']
            ];
            foreach ($affiliationStats as $stat):
                $info = $statusLabels[$stat['status']] ?? ['label' => $stat['status'], 'color' => 'gray'];
            ?>
            <div class="p-4 bg-<?php echo $info['color']; ?>-50 rounded-lg text-center border border-<?php echo $info['color']; ?>-200">
                <p class="text-2xl font-bold text-<?php echo $info['color']; ?>-600"><?php echo $stat['count']; ?></p>
                <p class="text-sm text-gray-600"><?php echo $info['label']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Goal Progress -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cumplimiento de Objetivos del Área Comercial</h3>
        <div class="space-y-6">
            <div>
                <div class="flex justify-between mb-2">
                    <span class="font-medium text-gray-700">Afiliaciones Nuevas</span>
                    <span class="text-gray-500">Meta: 100 / año</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-blue-600 h-4 rounded-full" style="width: 65%"></div>
                </div>
                <p class="text-sm text-gray-500 mt-1">65 afiliaciones (65% de la meta anual)</p>
            </div>
            
            <div>
                <div class="flex justify-between mb-2">
                    <span class="font-medium text-gray-700">Tasa de Renovación</span>
                    <span class="text-gray-500">Meta: 80%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-<?php echo $renewalRate >= 80 ? 'green' : 'yellow'; ?>-500 h-4 rounded-full" style="width: <?php echo min(100, ($renewalRate / 80) * 100); ?>%"></div>
                </div>
                <p class="text-sm text-gray-500 mt-1"><?php echo $renewalRate; ?>% de renovación actual</p>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <a href="<?php echo BASE_URL; ?>/reportes" class="p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition text-center">
            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="text-sm font-medium text-gray-700">Ver Reportes</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/eventos" class="p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition text-center">
            <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-sm font-medium text-gray-700">Eventos</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/afiliados" class="p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition text-center">
            <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="text-sm font-medium text-gray-700">Afiliados</span>
        </a>
    </div>
</div>
