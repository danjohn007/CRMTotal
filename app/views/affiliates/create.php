<!-- Affiliate Create View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Nuevo Afiliado</h2>
            <p class="mt-1 text-sm text-gray-500">Registra un nuevo afiliado en el sistema</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/afiliados" class="text-blue-600 hover:text-blue-800">
            ← Volver a Afiliados
        </a>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <!-- Create Form -->
    <form method="POST" class="bg-white rounded-lg shadow-sm p-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <!-- Membership Section -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Información de Membresía</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="membership_type_id" class="block text-sm font-medium text-gray-700">Tipo de Membresía *</label>
                    <select id="membership_type_id" name="membership_type_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($membershipTypes as $type): ?>
                        <option value="<?php echo $type['id']; ?>">
                            <?php echo htmlspecialchars($type['name']); ?> - $<?php echo number_format($type['price'], 2); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700">Estado de Pago</label>
                    <select id="payment_status" name="payment_status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="pending">Pendiente</option>
                        <option value="paid">Pagado</option>
                        <option value="partial">Parcial</option>
                    </select>
                </div>
                
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de Pago</label>
                    <select id="payment_method" name="payment_method"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="">Seleccionar...</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Basic Info -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Información Básica</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="rfc" class="block text-sm font-medium text-gray-700">RFC</label>
                    <input type="text" id="rfc" name="rfc" 
                           maxlength="13"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp Principal *</label>
                    <input type="text" id="whatsapp" name="whatsapp" required
                           maxlength="10" pattern="[0-9]{10}" title="Ingrese exactamente 10 dígitos"
                           placeholder="10 dígitos"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700">Razón Social *</label>
                    <input type="text" id="business_name" name="business_name" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="commercial_name" class="block text-sm font-medium text-gray-700">Nombre Comercial</label>
                    <input type="text" id="commercial_name" name="commercial_name" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="owner_name" class="block text-sm font-medium text-gray-700">Propietario / Representante</label>
                    <input type="text" id="owner_name" name="owner_name" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="legal_representative" class="block text-sm font-medium text-gray-700">Representante Legal</label>
                    <input type="text" id="legal_representative" name="legal_representative" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="corporate_email" class="block text-sm font-medium text-gray-700">Correo Corporativo</label>
                    <input type="email" id="corporate_email" name="corporate_email" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" id="phone" name="phone" 
                           maxlength="10" pattern="[0-9]{10}" title="Ingrese exactamente 10 dígitos"
                           placeholder="10 dígitos"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Business Info -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Información Comercial</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="industry" class="block text-sm font-medium text-gray-700">Giro / Industria</label>
                    <input type="text" id="industry" name="industry" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="niza_classification" class="block text-sm font-medium text-gray-700">Clasificación NIZA</label>
                    <input type="text" id="niza_classification" name="niza_classification" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="assigned_affiliate_id" class="block text-sm font-medium text-gray-700">Afiliador Asignado</label>
                    <select id="assigned_affiliate_id" name="assigned_affiliate_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($affiliators as $affiliator): ?>
                        <option value="<?php echo $affiliator['id']; ?>">
                            <?php echo htmlspecialchars($affiliator['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="discount_percentage" class="block text-sm font-medium text-gray-700">Porcentaje de Descuento</label>
                    <input type="number" id="discount_percentage" name="discount_percentage" 
                           value="0" min="0" max="100" step="0.01"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Address Info -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Direcciones</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="commercial_address" class="block text-sm font-medium text-gray-700">Dirección Comercial</label>
                    <textarea id="commercial_address" name="commercial_address" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label for="fiscal_address" class="block text-sm font-medium text-gray-700">Dirección Fiscal</label>
                    <textarea id="fiscal_address" name="fiscal_address" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
                </div>
                
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                    <input type="text" id="city" name="city" 
                           value="Santiago de Querétaro"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700">Estado</label>
                    <input type="text" id="state" name="state" 
                           value="Querétaro"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Código Postal</label>
                    <input type="text" id="postal_code" name="postal_code" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="google_maps_url" class="block text-sm font-medium text-gray-700">URL Google Maps</label>
                    <input type="url" id="google_maps_url" name="google_maps_url" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Web & Social -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Web y Redes Sociales</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700">Sitio Web</label>
                    <input type="url" id="website" name="website" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="facebook" class="block text-sm font-medium text-gray-700">Facebook</label>
                    <input type="url" id="facebook" name="facebook" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="instagram" class="block text-sm font-medium text-gray-700">Instagram</label>
                    <input type="url" id="instagram" name="instagram" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Notes -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Notas</h3>
            <div>
                <textarea id="notes" name="notes" rows="4"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"></textarea>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>/afiliados" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Crear Afiliado
            </button>
        </div>
    </form>
</div>
