<!-- API Configuration View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Configuración de APIs</h2>
            <p class="mt-1 text-sm text-gray-500">Configuración de integraciones externas</p>
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
    <form method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <!-- QR Code Configuration -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Configuración de Códigos QR</h3>
                    <p class="text-sm text-gray-500">Para generación de códigos QR en eventos</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="qr_api_provider" class="block text-sm font-medium text-gray-700">API para Generación de QR</label>
                    <select id="qr_api_provider" name="qr_api_provider"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="google" <?php echo ($config['qr_api_provider'] ?? 'google') === 'google' ? 'selected' : ''; ?>>Google Charts API</option>
                        <option value="qrserver" <?php echo ($config['qr_api_provider'] ?? '') === 'qrserver' ? 'selected' : ''; ?>>QR Server API</option>
                        <option value="local" <?php echo ($config['qr_api_provider'] ?? '') === 'local' ? 'selected' : ''; ?>>Generación Local (PHP)</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Seleccione el proveedor de API para generar códigos QR</p>
                </div>
                <div>
                    <label for="qr_size" class="block text-sm font-medium text-gray-700">Tamaño de QR (píxeles)</label>
                    <input type="number" id="qr_size" name="qr_size" 
                           value="<?php echo htmlspecialchars($config['qr_size'] ?? '350'); ?>"
                           min="100" max="1000" step="50"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <p class="mt-1 text-xs text-gray-500">Tamaño del código QR para impresión (recomendado: 400px)</p>
                </div>
            </div>
            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-blue-700">
                        <strong>Nota:</strong> La configuración de API de QR permite cambiar el proveedor de generación de códigos QR. Un tamaño mayor mejora la calidad de impresión.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Shelly Relay Configuration -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Shelly Relay API - Control de Acceso</h3>
                    <p class="text-sm text-gray-500">Permite controlar el acceso a eventos mediante dispositivos Shelly Relay</p>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" id="shelly_enabled" name="shelly_enabled" value="1"
                           <?php echo ($config['shelly_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Habilitar integración con Shelly Relay API</span>
                </label>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="shelly_url" class="block text-sm font-medium text-gray-700">URL de la API de Shelly</label>
                    <input type="text" id="shelly_url" name="shelly_url" 
                           value="<?php echo htmlspecialchars($config['shelly_url'] ?? ''); ?>"
                           placeholder="http://192.168.1.100/relay"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <p class="mt-1 text-xs text-gray-500">Ejemplo: http://192.168.1.100/relay</p>
                </div>
                <div>
                    <label for="shelly_channel" class="block text-sm font-medium text-gray-700">Canal del Relay</label>
                    <select id="shelly_channel" name="shelly_channel"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="0" <?php echo ($config['shelly_channel'] ?? '0') === '0' ? 'selected' : ''; ?>>Canal 0</option>
                        <option value="1" <?php echo ($config['shelly_channel'] ?? '') === '1' ? 'selected' : ''; ?>>Canal 1</option>
                        <option value="2" <?php echo ($config['shelly_channel'] ?? '') === '2' ? 'selected' : ''; ?>>Canal 2</option>
                        <option value="3" <?php echo ($config['shelly_channel'] ?? '') === '3' ? 'selected' : ''; ?>>Canal 3</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Seleccione el canal del relay a controlar</p>
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm text-yellow-700">
                        <strong>Importante:</strong> Asegúrese de que la URL del dispositivo Shelly sea accesible desde el servidor. Esta función permite activar el relay para el acceso físico a eventos.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- QR API Key (for legacy/mass generation) -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">API Key para QR Masivo</h3>
                    <p class="text-sm text-gray-500">Para generación masiva de códigos QR (opcional)</p>
                </div>
            </div>
            <div>
                <label for="qr_api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                <input type="text" id="qr_api_key" name="qr_api_key" 
                       value="<?php echo htmlspecialchars($config['qr_api_key'] ?? ''); ?>"
                       placeholder="Ingrese su API Key"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
        </div>
        
        <!-- WhatsApp API -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">WhatsApp Business API</h3>
                    <p class="text-sm text-gray-500">Para envío de mensajes automatizados</p>
                </div>
            </div>
            <div>
                <label for="whatsapp_api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                <input type="text" id="whatsapp_api_key" name="whatsapp_api_key" 
                       value="<?php echo htmlspecialchars($config['whatsapp_api_key'] ?? ''); ?>"
                       placeholder="Ingrese su API Key"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
        </div>
        
        <!-- Google Maps API -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-red-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Google Maps API</h3>
                    <p class="text-sm text-gray-500">Para visualización de mapas y ubicaciones</p>
                </div>
            </div>
            <div>
                <label for="google_maps_api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                <input type="text" id="google_maps_api_key" name="google_maps_api_key" 
                       value="<?php echo htmlspecialchars($config['google_maps_api_key'] ?? ''); ?>"
                       placeholder="AIzaSy..."
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
