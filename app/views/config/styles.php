<!-- Styles Configuration View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Configuración de Estilos</h2>
            <p class="mt-1 text-sm text-gray-500">Personaliza los colores del sistema</p>
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
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Primary Color -->
            <div>
                <label for="primary_color" class="block text-sm font-medium text-gray-700">Color Primario</label>
                <div class="mt-1 flex items-center">
                    <input type="color" id="primary_color" name="primary_color" 
                           value="<?php echo htmlspecialchars($config['primary_color'] ?? '#1e40af'); ?>"
                           class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                    <input type="text" id="primary_color_text" 
                           value="<?php echo htmlspecialchars($config['primary_color'] ?? '#1e40af'); ?>"
                           class="ml-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                           oninput="document.getElementById('primary_color').value = this.value">
                </div>
                <p class="text-xs text-gray-500 mt-1">Color principal del sistema (botones, enlaces)</p>
            </div>
            
            <!-- Secondary Color -->
            <div>
                <label for="secondary_color" class="block text-sm font-medium text-gray-700">Color Secundario</label>
                <div class="mt-1 flex items-center">
                    <input type="color" id="secondary_color" name="secondary_color" 
                           value="<?php echo htmlspecialchars($config['secondary_color'] ?? '#3b82f6'); ?>"
                           class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                    <input type="text" id="secondary_color_text" 
                           value="<?php echo htmlspecialchars($config['secondary_color'] ?? '#3b82f6'); ?>"
                           class="ml-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                           oninput="document.getElementById('secondary_color').value = this.value">
                </div>
                <p class="text-xs text-gray-500 mt-1">Color secundario (elementos destacados)</p>
            </div>
            
            <!-- Accent Color -->
            <div>
                <label for="accent_color" class="block text-sm font-medium text-gray-700">Color de Acento</label>
                <div class="mt-1 flex items-center">
                    <input type="color" id="accent_color" name="accent_color" 
                           value="<?php echo htmlspecialchars($config['accent_color'] ?? '#10b981'); ?>"
                           class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                    <input type="text" id="accent_color_text" 
                           value="<?php echo htmlspecialchars($config['accent_color'] ?? '#10b981'); ?>"
                           class="ml-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                           oninput="document.getElementById('accent_color').value = this.value">
                </div>
                <p class="text-xs text-gray-500 mt-1">Color de acento (éxito, confirmaciones)</p>
            </div>
        </div>
        
        <!-- Preview -->
        <div class="mt-8">
            <h3 class="text-sm font-medium text-gray-700 mb-4">Vista Previa</h3>
            <div class="p-6 border rounded-lg bg-gray-50">
                <div class="flex flex-wrap gap-4">
                    <button type="button" id="preview_primary" 
                            style="background-color: <?php echo htmlspecialchars($config['primary_color'] ?? '#1e40af'); ?>"
                            class="px-4 py-2 text-white rounded-lg">
                        Botón Primario
                    </button>
                    <button type="button" id="preview_secondary" 
                            style="background-color: <?php echo htmlspecialchars($config['secondary_color'] ?? '#3b82f6'); ?>"
                            class="px-4 py-2 text-white rounded-lg">
                        Botón Secundario
                    </button>
                    <button type="button" id="preview_accent" 
                            style="background-color: <?php echo htmlspecialchars($config['accent_color'] ?? '#10b981'); ?>"
                            class="px-4 py-2 text-white rounded-lg">
                        Botón Acento
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Estilos
            </button>
        </div>
    </form>
</div>

<script>
// Sync color pickers with text inputs
document.getElementById('primary_color').addEventListener('input', function() {
    document.getElementById('primary_color_text').value = this.value;
    document.getElementById('preview_primary').style.backgroundColor = this.value;
});
document.getElementById('secondary_color').addEventListener('input', function() {
    document.getElementById('secondary_color_text').value = this.value;
    document.getElementById('preview_secondary').style.backgroundColor = this.value;
});
document.getElementById('accent_color').addEventListener('input', function() {
    document.getElementById('accent_color_text').value = this.value;
    document.getElementById('preview_accent').style.backgroundColor = this.value;
});
</script>
