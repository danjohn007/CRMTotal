<!-- Reports Index -->
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Reportes</h2>
        <p class="mt-1 text-sm text-gray-500">Centro de reportes y analíticas del CRM</p>
    </div>
    
    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Commercial Reports -->
        <a href="<?php echo BASE_URL; ?>/reportes/comerciales" 
           class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition border-t-4 border-blue-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Comerciales</h3>
            </div>
            <p class="text-gray-500 text-sm">Métricas de ventas, conversión por canal, efectividad de afiliadores y tendencias.</p>
            <ul class="mt-4 space-y-2 text-sm text-gray-600">
                <li class="flex items-center">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                    Tasa de conversión por canal
                </li>
                <li class="flex items-center">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                    Desempeño por afiliador
                </li>
                <li class="flex items-center">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                    Tendencias de renovación
                </li>
            </ul>
        </a>
        
        <!-- Financial Reports -->
        <a href="<?php echo BASE_URL; ?>/reportes/financieros" 
           class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition border-t-4 border-green-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Financieros</h3>
            </div>
            <p class="text-gray-500 text-sm">Ingresos por período, desglose por fuente, comisiones y proyecciones.</p>
            <ul class="mt-4 space-y-2 text-sm text-gray-600">
                <li class="flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    Ingresos por afiliaciones
                </li>
                <li class="flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    Ingresos por servicios
                </li>
                <li class="flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    Rentabilidad por membresía
                </li>
            </ul>
        </a>
        
        <!-- Operational Reports -->
        <a href="<?php echo BASE_URL; ?>/reportes/operativos" 
           class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition border-t-4 border-purple-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Operativos</h3>
            </div>
            <p class="text-gray-500 text-sm">Métricas de operación, completitud de expedientes, eventos y buscador.</p>
            <ul class="mt-4 space-y-2 text-sm text-gray-600">
                <li class="flex items-center">
                    <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                    Completitud de expedientes
                </li>
                <li class="flex items-center">
                    <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                    Asistencia a eventos
                </li>
                <li class="flex items-center">
                    <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                    Impacto del buscador
                </li>
            </ul>
        </a>
    </div>
    
    <!-- Quick Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen Rápido del Mes</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600">-</p>
                <p class="text-sm text-gray-500">Nuevas Afiliaciones</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-green-600">-</p>
                <p class="text-sm text-gray-500">Renovaciones</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-purple-600">$-</p>
                <p class="text-sm text-gray-500">Ingresos Totales</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-yellow-600">-%</p>
                <p class="text-sm text-gray-500">Tasa de Renovación</p>
            </div>
        </div>
    </div>
</div>
