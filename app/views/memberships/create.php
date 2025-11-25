<!-- Create Membership View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Nueva Membresía</h2>
            <p class="mt-1 text-sm text-gray-500">Crear un nuevo tipo de membresía</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/membresias" class="text-blue-600 hover:text-blue-800">
            ← Volver a Membresías
        </a>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <!-- Form -->
    <form method="POST" class="bg-white rounded-lg shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" id="name" name="name" required
                       placeholder="Membresía Premium"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Code -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">Código</label>
                <input type="text" id="code" name="code" required maxlength="20"
                       placeholder="PREMIUM"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border uppercase">
                <p class="text-xs text-gray-500 mt-1">Código único para identificar la membresía</p>
            </div>
            
            <!-- Price -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Precio (MXN)</label>
                <input type="number" id="price" name="price" required min="0" step="0.01"
                       placeholder="5000.00"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Duration -->
            <div>
                <label for="duration_days" class="block text-sm font-medium text-gray-700">Duración (días)</label>
                <input type="number" id="duration_days" name="duration_days" required min="1" value="360"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>
            
            <!-- Status -->
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700">Estado</label>
                <select id="is_active" name="is_active"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
        </div>
        
        <!-- Benefits -->
        <div class="mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Beneficios</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="benefit_descuento_eventos" class="block text-sm font-medium text-gray-700">Descuento en Eventos (%)</label>
                    <input type="number" id="benefit_descuento_eventos" name="benefit_descuento_eventos" min="0" max="100"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                <div>
                    <label for="benefit_capacitaciones" class="block text-sm font-medium text-gray-700">Capacitaciones</label>
                    <input type="text" id="benefit_capacitaciones" name="benefit_capacitaciones"
                           placeholder="2 o 'ilimitadas'"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                <div class="flex items-center pt-6">
                    <input type="checkbox" id="benefit_buscador" name="benefit_buscador" value="true"
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="benefit_buscador" class="ml-2 text-sm text-gray-700">Acceso al Buscador</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="benefit_networking" name="benefit_networking" value="true"
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="benefit_networking" class="ml-2 text-sm text-gray-700">Eventos de Networking</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="benefit_asesoria" name="benefit_asesoria" value="true"
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="benefit_asesoria" class="ml-2 text-sm text-gray-700">Asesoría</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="benefit_marketing" name="benefit_marketing" value="true"
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="benefit_marketing" class="ml-2 text-sm text-gray-700">Marketing Incluido</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="benefit_publicidad" name="benefit_publicidad" value="true"
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="benefit_publicidad" class="ml-2 text-sm text-gray-700">Publicidad</label>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>/membresias" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Crear Membresía
            </button>
        </div>
    </form>
</div>
