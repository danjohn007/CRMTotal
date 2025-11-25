<!-- User Show View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/usuarios" class="text-blue-600 hover:text-blue-800">
                ← Volver a Usuarios
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2"><?php echo htmlspecialchars($user['name']); ?></h2>
            <div class="mt-1 flex items-center space-x-3">
                <span class="px-2 py-1 text-xs rounded-full 
                    <?php echo $user['role_name'] === 'superadmin' ? 'bg-red-100 text-red-800' : 
                              ($user['role_name'] === 'direccion' ? 'bg-purple-100 text-purple-800' : 
                              'bg-blue-100 text-blue-800'); ?>">
                    <?php echo htmlspecialchars($user['role_display']); ?>
                </span>
                <span class="px-2 py-1 text-xs rounded-full <?php echo $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $user['is_active'] ? 'Activo' : 'Inactivo'; ?>
                </span>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?php echo BASE_URL; ?>/usuarios/<?php echo $user['id']; ?>/editar" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar Usuario
            </a>
        </div>
    </div>
    
    <!-- User Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de Contacto</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Correo Electrónico</p>
                        <p class="text-gray-900"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Teléfono</p>
                        <p class="text-gray-900"><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : '-'; ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">WhatsApp</p>
                        <p class="text-gray-900"><?php echo !empty($user['whatsapp']) ? htmlspecialchars($user['whatsapp']) : '-'; ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Último Acceso</p>
                        <p class="text-gray-900"><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca'; ?></p>
                    </div>
                    <?php if (!empty($user['address'])): ?>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Dirección</p>
                        <p class="text-gray-900"><?php echo htmlspecialchars($user['address']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Stats -->
            <?php if (in_array($user['role_name'], ['afiliador', 'jefe_comercial'])): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas de Desempeño</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600"><?php echo $affiliationStats ?? 0; ?></p>
                        <p class="text-sm text-gray-600">Afiliaciones (Año)</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600"><?php echo $activityStats['completadas'] ?? 0; ?></p>
                        <p class="text-sm text-gray-600">Actividades Completadas</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <p class="text-2xl font-bold text-yellow-600"><?php echo $activityStats['pendientes'] ?? 0; ?></p>
                        <p class="text-sm text-gray-600">Actividades Pendientes</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600"><?php echo $activityStats['total'] ?? 0; ?></p>
                        <p class="text-sm text-gray-600">Total Actividades</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cuenta</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID Usuario</span>
                        <span class="text-gray-900"><?php echo $user['id']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rol</span>
                        <span class="text-gray-900"><?php echo htmlspecialchars($user['role_display']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado</span>
                        <span class="<?php echo $user['is_active'] ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo $user['is_active'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Creado</span>
                        <span class="text-gray-900"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
