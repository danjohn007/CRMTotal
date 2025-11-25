<!-- Payments Configuration View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Configuración de Pagos</h2>
            <p class="mt-1 text-sm text-gray-500">Configuración de pasarela de pagos PayPal</p>
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
        
        <!-- PayPal Section -->
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.72c.048-.289.301-.51.593-.51h7.878c2.09 0 3.642.606 4.617 1.804.9 1.105 1.184 2.476.844 4.073-.36 1.69-1.347 3.075-2.937 4.116-1.517.993-3.386 1.497-5.555 1.497H8.123l-.888 5.145a.634.634 0 0 1-.627.53zm.684-1.27l.796-4.616a.64.64 0 0 1 .633-.54h2.385c1.853 0 3.454-.427 4.76-1.269 1.354-.874 2.183-2.031 2.464-3.44.263-1.233.056-2.237-.613-2.98-.72-.8-1.94-1.184-3.625-1.184H6.973L4.372 20.067h3.388z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900">PayPal</h3>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- PayPal Client ID -->
            <div class="md:col-span-2">
                <label for="paypal_client_id" class="block text-sm font-medium text-gray-700">Client ID</label>
                <input type="text" id="paypal_client_id" name="paypal_client_id" 
                       value="<?php echo htmlspecialchars($config['paypal_client_id'] ?? ''); ?>"
                       placeholder="AZDxjDSCLn..."
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border font-mono text-sm">
            </div>
            
            <!-- PayPal Secret -->
            <div class="md:col-span-2">
                <label for="paypal_secret" class="block text-sm font-medium text-gray-700">Secret</label>
                <input type="password" id="paypal_secret" name="paypal_secret" 
                       placeholder="<?php echo !empty($config['paypal_secret']) ? '••••••••••••••••' : 'Ingrese el secret'; ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border font-mono text-sm">
                <p class="text-xs text-gray-500 mt-1">Dejar en blanco para mantener el secret actual</p>
            </div>
            
            <!-- PayPal Mode -->
            <div>
                <label for="paypal_mode" class="block text-sm font-medium text-gray-700">Modo</label>
                <select id="paypal_mode" name="paypal_mode" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="sandbox" <?php echo ($config['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : ''; ?>>
                        Sandbox (Pruebas)
                    </option>
                    <option value="live" <?php echo ($config['paypal_mode'] ?? '') === 'live' ? 'selected' : ''; ?>>
                        Live (Producción)
                    </option>
                </select>
                <p class="text-xs text-gray-500 mt-1">⚠️ Usar "Live" solo cuando esté listo para recibir pagos reales</p>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="text-sm font-medium text-blue-800 mb-2">¿Cómo obtener las credenciales de PayPal?</h4>
            <ol class="text-sm text-blue-700 list-decimal list-inside space-y-1">
                <li>Accede a <a href="https://developer.paypal.com" target="_blank" class="underline">developer.paypal.com</a></li>
                <li>Inicia sesión con tu cuenta de PayPal Business</li>
                <li>Ve a "Dashboard" → "My Apps & Credentials"</li>
                <li>Crea una nueva app o selecciona una existente</li>
                <li>Copia el Client ID y Secret</li>
            </ol>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
