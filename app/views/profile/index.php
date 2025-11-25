<!-- Profile Index View -->
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Mi Perfil</h2>
        <p class="mt-1 text-sm text-gray-500">Administra tu información personal y configuración de cuenta</p>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
    <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <!-- Avatar -->
                <div class="mb-4">
                    <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo BASE_URL . $user['avatar']; ?>" 
                         alt="Avatar" 
                         class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-blue-100">
                    <?php else: ?>
                    <div class="w-32 h-32 rounded-full mx-auto bg-blue-100 flex items-center justify-center border-4 border-blue-200">
                        <span class="text-4xl font-bold text-blue-600">
                            <?php echo mb_substr($user['name'] ?? 'U', 0, 1, 'UTF-8'); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <h3 class="text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($user['name']); ?></h3>
                <p class="text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                <span class="inline-block mt-2 px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                    <?php echo htmlspecialchars($user['role_display'] ?? $user['role_name']); ?>
                </span>
                
                <!-- Quick Stats -->
                <div class="mt-6 pt-6 border-t">
                    <div class="text-sm text-gray-500">
                        <p>Último acceso:</p>
                        <p class="font-medium text-gray-900">
                            <?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca'; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="mt-4 bg-white rounded-lg shadow-sm p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Acciones Rápidas</h4>
                <div class="space-y-2">
                    <a href="<?php echo BASE_URL; ?>/perfil/password" 
                       class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Cambiar Contraseña
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="<?php echo BASE_URL; ?>/perfil/actualizar" enctype="multipart/form-data" 
                  class="bg-white rounded-lg shadow-sm p-6">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <h3 class="text-lg font-medium text-gray-900 mb-6">Información Personal</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                        <input type="text" id="name" name="name" required
                               value="<?php echo htmlspecialchars($user['name']); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    
                    <!-- Email (readonly) -->
                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" id="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2 border cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">El correo electrónico no puede ser modificado. Contacta al administrador.</p>
                    </div>
                    
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                               placeholder="442 123 4567"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    
                    <!-- WhatsApp -->
                    <div>
                        <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
                        <input type="tel" id="whatsapp" name="whatsapp" 
                               value="<?php echo htmlspecialchars($user['whatsapp'] ?? ''); ?>"
                               placeholder="4421234567"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    
                    <!-- Avatar -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Foto de Perfil</label>
                        <div class="mt-2 flex items-center space-x-4">
                            <?php if (!empty($user['avatar'])): ?>
                            <img src="<?php echo BASE_URL . $user['avatar']; ?>" 
                                 alt="Avatar actual" 
                                 class="w-16 h-16 rounded-full object-cover">
                            <?php else: ?>
                            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <input type="file" id="avatar" name="avatar" accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF o WEBP. Máximo 2MB.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
