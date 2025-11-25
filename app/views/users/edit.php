<!-- User Edit View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Editar Usuario</h2>
            <p class="mt-1 text-sm text-gray-500">Modifica la información del usuario</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/usuarios" class="text-blue-600 hover:text-blue-800">
            ← Volver a Usuarios
        </a>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <!-- Edit Form -->
    <form method="POST" class="bg-white rounded-lg shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <!-- Account Info -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Información de Cuenta</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico *</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($user['email']); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">Rol *</label>
                    <select id="role_id" name="role_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>" <?php echo $user['role_id'] == $role['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($role['display_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                    <input type="password" id="password" name="password"
                           minlength="8"
                           placeholder="Dejar vacío para no cambiar"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           minlength="8"
                           placeholder="Confirmar nueva contraseña"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" 
                               <?php echo $user['is_active'] ? 'checked' : ''; ?>
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Usuario activo</span>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Personal Info -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Información Personal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                    <input type="text" id="name" name="name" required
                           value="<?php echo htmlspecialchars($user['name']); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" id="phone" name="phone"
                           maxlength="10" pattern="\d{10}"
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                           placeholder="10 dígitos"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <p class="mt-1 text-xs text-gray-500">Solo 10 dígitos, sin espacios ni guiones</p>
                </div>
                
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
                    <input type="text" id="whatsapp" name="whatsapp"
                           maxlength="10" pattern="\d{10}"
                           value="<?php echo htmlspecialchars($user['whatsapp'] ?? ''); ?>"
                           placeholder="10 dígitos"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <p class="mt-1 text-xs text-gray-500">Solo 10 dígitos, sin espacios ni guiones</p>
                </div>
                
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                    <textarea id="address" name="address" rows="2"
                              placeholder="Dirección completa (opcional)"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>/usuarios" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    if (password && this.value !== password) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});

// Restrict phone and whatsapp to only digits, max 10
['phone', 'whatsapp'].forEach(function(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', function(e) {
            // Remove non-digits
            this.value = this.value.replace(/\D/g, '');
            // Limit to 10 digits
            if (this.value.length > 10) {
                this.value = this.value.substring(0, 10);
            }
        });
    }
});
</script>
