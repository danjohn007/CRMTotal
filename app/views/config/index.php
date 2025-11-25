<!-- Configuration Index -->
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Configuración del Sistema</h2>
        <p class="mt-1 text-sm text-gray-500">Administración global del CRM</p>
    </div>
    
    <!-- Config Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Site Settings -->
        <a href="<?php echo BASE_URL; ?>/configuracion/sitio" 
           class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Sitio y Logotipo</h3>
                    <p class="text-sm text-gray-500">Nombre, logo, teléfonos y horarios</p>
                </div>
            </div>
        </a>
        
        <!-- Email Settings -->
        <a href="<?php echo BASE_URL; ?>/configuracion/correo" 
           class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Correo Electrónico</h3>
                    <p class="text-sm text-gray-500">Configuración SMTP para envío de correos</p>
                </div>
            </div>
        </a>
        
        <!-- Styles -->
        <a href="<?php echo BASE_URL; ?>/configuracion/estilos" 
           class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Estilos y Colores</h3>
                    <p class="text-sm text-gray-500">Personaliza los colores del sistema</p>
                </div>
            </div>
        </a>
        
        <!-- Payment Settings -->
        <a href="<?php echo BASE_URL; ?>/configuracion/pagos" 
           class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Pasarela de Pagos</h3>
                    <p class="text-sm text-gray-500">Configuración de PayPal</p>
                </div>
            </div>
        </a>
        
        <!-- API Settings -->
        <a href="<?php echo BASE_URL; ?>/configuracion/api" 
           class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">APIs Externas</h3>
                    <p class="text-sm text-gray-500">WhatsApp, Google Maps, QR</p>
                </div>
            </div>
        </a>
        
        <!-- Users -->
        <a href="<?php echo BASE_URL; ?>/usuarios" 
           class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Usuarios del Sistema</h3>
                    <p class="text-sm text-gray-500">Gestión de usuarios y roles</p>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Current Config Summary -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración Actual</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Nombre del Sitio</dt>
                <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($config['site_name'] ?? 'No configurado'); ?></dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($config['contact_phone'] ?? 'No configurado'); ?></dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($config['contact_email'] ?? 'No configurado'); ?></dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Color Primario</dt>
                <dd class="mt-1 flex items-center">
                    <span class="w-6 h-6 rounded border" style="background-color: <?php echo $config['primary_color'] ?? '#1e40af'; ?>"></span>
                    <span class="ml-2 text-sm text-gray-900"><?php echo $config['primary_color'] ?? '#1e40af'; ?></span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">PayPal</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    <?php echo !empty($config['paypal_client_id']) ? 'Configurado (' . ($config['paypal_mode'] ?? 'sandbox') . ')' : 'No configurado'; ?>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">SMTP</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    <?php echo !empty($config['smtp_host']) ? 'Configurado' : 'No configurado'; ?>
                </dd>
            </div>
        </div>
    </div>
</div>
