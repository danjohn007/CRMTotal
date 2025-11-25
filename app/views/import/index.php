<!-- Import Index View -->
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Importar Empresas desde Excel</h2>
        <p class="mt-1 text-sm text-gray-500">Carga masiva de prospectos o afiliados desde archivo CSV o Excel</p>
    </div>
    
    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-blue-800 mb-3">Instrucciones</h3>
        <ol class="list-decimal list-inside space-y-2 text-blue-700">
            <li>Descarga la plantilla de ejemplo</li>
            <li>Llena los datos de las empresas en el formato indicado</li>
            <li>Guarda el archivo en formato CSV (separado por comas)</li>
            <li>Sube el archivo y selecciona el tipo de contacto</li>
            <li>Revisa la vista previa antes de confirmar la importación</li>
        </ol>
        <div class="mt-4">
            <a href="<?php echo BASE_URL; ?>/importar/plantilla" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar Plantilla CSV
            </a>
        </div>
    </div>
    
    <!-- Upload Form -->
    <form method="POST" action="<?php echo BASE_URL; ?>/importar/procesar" enctype="multipart/form-data" 
          class="bg-white rounded-lg shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- File Upload -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo CSV/Excel</label>
                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="excel_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                <span>Selecciona un archivo</span>
                                <input id="excel_file" name="excel_file" type="file" accept=".csv,.xls,.xlsx" required class="sr-only">
                            </label>
                            <p class="pl-1">o arrastra y suelta</p>
                        </div>
                        <p class="text-xs text-gray-500">CSV, XLS, XLSX hasta 5MB</p>
                    </div>
                </div>
                <p id="file-name" class="mt-2 text-sm text-gray-600"></p>
            </div>
            
            <!-- Contact Type -->
            <div>
                <label for="contact_type" class="block text-sm font-medium text-gray-700">Tipo de Contacto</label>
                <select id="contact_type" name="contact_type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="prospecto">Prospectos</option>
                    <option value="afiliado">Afiliados</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Los contactos se crearán con este tipo</p>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <button type="submit" name="preview" value="1" 
                    class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition">
                Vista Previa
            </button>
            <button type="submit" 
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Importar Directamente
            </button>
        </div>
    </form>
    
    <!-- Expected Format -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Formato Esperado</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Columna</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Requerido</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr><td class="px-4 py-2 font-mono">rfc</td><td class="px-4 py-2 text-gray-500">RFC de la empresa</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">business_name</td><td class="px-4 py-2 text-gray-500">Razón social</td><td class="px-4 py-2 text-green-600 font-medium">Sí</td></tr>
                    <tr><td class="px-4 py-2 font-mono">commercial_name</td><td class="px-4 py-2 text-gray-500">Nombre comercial</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">owner_name</td><td class="px-4 py-2 text-gray-500">Nombre del propietario</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">corporate_email</td><td class="px-4 py-2 text-gray-500">Correo corporativo</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">phone</td><td class="px-4 py-2 text-gray-500">Teléfono</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">whatsapp</td><td class="px-4 py-2 text-gray-500">WhatsApp</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">industry</td><td class="px-4 py-2 text-gray-500">Giro/Industria</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">commercial_address</td><td class="px-4 py-2 text-gray-500">Dirección comercial</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">city</td><td class="px-4 py-2 text-gray-500">Ciudad</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">state</td><td class="px-4 py-2 text-gray-500">Estado</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">postal_code</td><td class="px-4 py-2 text-gray-500">Código postal</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                    <tr><td class="px-4 py-2 font-mono">website</td><td class="px-4 py-2 text-gray-500">Sitio web</td><td class="px-4 py-2 text-gray-500">No</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('excel_file').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || '';
    document.getElementById('file-name').textContent = fileName ? 'Archivo seleccionado: ' + fileName : '';
});
</script>
