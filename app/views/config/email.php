<!-- Email Configuration View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Configuración de Correo</h2>
            <p class="mt-1 text-sm text-gray-500">Configuración SMTP para envío de correos electrónicos</p>
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
    <form method="POST" class="bg-white rounded-lg shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- SMTP Host -->
            <div>
                <label for="smtp_host" class="block text-sm font-medium text-gray-700">Servidor SMTP</label>
                <input type="text" id="smtp_host" name="smtp_host" 
                       value="<?php echo htmlspecialchars($config['smtp_host'] ?? ''); ?>"
                       placeholder="smtp.gmail.com"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- SMTP Port -->
            <div>
                <label for="smtp_port" class="block text-sm font-medium text-gray-700">Puerto SMTP</label>
                <input type="number" id="smtp_port" name="smtp_port" 
                       value="<?php echo htmlspecialchars($config['smtp_port'] ?? '587'); ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                <p class="text-xs text-gray-500 mt-1">Común: 587 (TLS) o 465 (SSL)</p>
            </div>
            
            <!-- SMTP User -->
            <div>
                <label for="smtp_user" class="block text-sm font-medium text-gray-700">Usuario SMTP</label>
                <input type="text" id="smtp_user" name="smtp_user" 
                       value="<?php echo htmlspecialchars($config['smtp_user'] ?? ''); ?>"
                       placeholder="correo@dominio.com"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- SMTP Password -->
            <div>
                <label for="smtp_password" class="block text-sm font-medium text-gray-700">Contraseña SMTP</label>
                <input type="password" id="smtp_password" name="smtp_password" 
                       placeholder="<?php echo !empty($config['smtp_password']) ? '••••••••' : 'Ingrese contraseña'; ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                <p class="text-xs text-gray-500 mt-1">Dejar en blanco para mantener la contraseña actual</p>
            </div>
            
            <!-- From Name -->
            <div class="md:col-span-2">
                <label for="smtp_from_name" class="block text-sm font-medium text-gray-700">Nombre del Remitente</label>
                <input type="text" id="smtp_from_name" name="smtp_from_name" 
                       value="<?php echo htmlspecialchars($config['smtp_from_name'] ?? ''); ?>"
                       placeholder="CRM Cámara de Comercio"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
        </div>
        
        <!-- Test Email Button -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Probar configuración</h3>
            <p class="text-xs text-gray-500 mb-3">Envía un correo de prueba para verificar la configuración.</p>
            <button type="button" onclick="testEmail()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                Enviar Correo de Prueba
            </button>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>

<script>
function testEmail() {
    alert('Funcionalidad de prueba de correo - Por implementar');
}
</script>
