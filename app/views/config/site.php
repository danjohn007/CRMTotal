<!-- Site Configuration View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Configuración del Sitio</h2>
            <p class="mt-1 text-sm text-gray-500">Nombre, logo, teléfonos y horarios del sistema</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/configuracion" class="text-blue-600 hover:text-blue-800">
            ← Volver a Configuración
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
    
    <!-- Config Form -->
    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Site Name -->
            <div>
                <label for="site_name" class="block text-sm font-medium text-gray-700">Nombre del Sitio</label>
                <input type="text" id="site_name" name="site_name" 
                       value="<?php echo htmlspecialchars($config['site_name'] ?? ''); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Contact Phone -->
            <div>
                <label for="contact_phone" class="block text-sm font-medium text-gray-700">Teléfono de Contacto</label>
                <input type="text" id="contact_phone" name="contact_phone" 
                       value="<?php echo htmlspecialchars($config['contact_phone'] ?? ''); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Contact Email -->
            <div>
                <label for="contact_email" class="block text-sm font-medium text-gray-700">Email de Contacto</label>
                <input type="email" id="contact_email" name="contact_email" 
                       value="<?php echo htmlspecialchars($config['contact_email'] ?? ''); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Office Hours -->
            <div>
                <label for="office_hours" class="block text-sm font-medium text-gray-700">Horario de Atención</label>
                <input type="text" id="office_hours" name="office_hours" 
                       value="<?php echo htmlspecialchars($config['office_hours'] ?? ''); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Address -->
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                <textarea id="address" name="address" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"><?php echo htmlspecialchars($config['address'] ?? ''); ?></textarea>
            </div>
            
            <!-- Logo Upload -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Logo del Sitio</label>
                <?php if (!empty($config['site_logo'])): ?>
                <div class="mt-2 mb-4">
                    <img src="<?php echo BASE_URL . $config['site_logo']; ?>" alt="Logo actual" class="h-16 object-contain">
                    <p class="text-xs text-gray-500 mt-1">Logo actual</p>
                </div>
                <?php endif; ?>
                <input type="file" id="site_logo" name="site_logo" accept="image/*"
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB</p>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
