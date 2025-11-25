<!-- Change Password View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Cambiar Contraseña</h2>
            <p class="mt-1 text-sm text-gray-500">Actualiza tu contraseña de acceso al sistema</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/perfil" class="text-blue-600 hover:text-blue-800">
            ← Volver a Mi Perfil
        </a>
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
    
    <div class="max-w-xl">
        <form method="POST" class="bg-white rounded-lg shadow-sm p-6">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="space-y-6">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Contraseña Actual</label>
                    <input type="password" id="current_password" name="current_password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                    <input type="password" id="new_password" name="new_password" required minlength="8"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
                </div>
                
                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
            
            <!-- Security Tips -->
            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <h4 class="text-sm font-medium text-yellow-800 mb-2">Consejos de seguridad:</h4>
                <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                    <li>Usa al menos 8 caracteres</li>
                    <li>Combina letras mayúsculas y minúsculas</li>
                    <li>Incluye números y símbolos</li>
                    <li>No uses información personal</li>
                    <li>No reutilices contraseñas de otros sitios</li>
                </ul>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?php echo BASE_URL; ?>/perfil" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Cambiar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>
