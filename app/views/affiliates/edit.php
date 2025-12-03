<!-- Affiliate Edit View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Editar Afiliado</h2>
            <p class="mt-1 text-sm text-gray-500"><?php echo htmlspecialchars($contact['business_name'] ?? ''); ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>" class="text-blue-600 hover:text-blue-800">
            ← Volver al detalle
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
        
        <!-- Basic Info -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Información Básica</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="rfc" class="block text-sm font-medium text-gray-700">RFC</label>
                    <input type="text" id="rfc" name="rfc" 
                           value="<?php echo htmlspecialchars($contact['rfc'] ?? ''); ?>"
                           maxlength="13"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp Principal *</label>
                    <input type="text" id="whatsapp" name="whatsapp" required
                           value="<?php echo htmlspecialchars($contact['whatsapp'] ?? ''); ?>"
                           maxlength="10" pattern="[0-9]{10}" title="Ingrese exactamente 10 dígitos"
                           placeholder="10 dígitos"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700">Razón Social *</label>
                    <input type="text" id="business_name" name="business_name" required
                           value="<?php echo htmlspecialchars($contact['business_name'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="commercial_name" class="block text-sm font-medium text-gray-700">Nombre Comercial</label>
                    <input type="text" id="commercial_name" name="commercial_name" 
                           value="<?php echo htmlspecialchars($contact['commercial_name'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="owner_name" class="block text-sm font-medium text-gray-700">Propietario / Representante</label>
                    <input type="text" id="owner_name" name="owner_name" 
                           value="<?php echo htmlspecialchars($contact['owner_name'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="legal_representative" class="block text-sm font-medium text-gray-700">Representante Legal</label>
                    <input type="text" id="legal_representative" name="legal_representative" 
                           value="<?php echo htmlspecialchars($contact['legal_representative'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="corporate_email" class="block text-sm font-medium text-gray-700">Correo Corporativo</label>
                    <input type="email" id="corporate_email" name="corporate_email" 
                           value="<?php echo htmlspecialchars($contact['corporate_email'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($contact['phone'] ?? ''); ?>"
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
                           value="<?php echo htmlspecialchars($contact['industry'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div x-data="{ showCustomNiza: <?php echo ($contact['niza_classification'] ?? '') === '99' ? 'true' : 'false'; ?> }">
                    <label for="niza_classification" class="block text-sm font-medium text-gray-700">Clasificación NIZA</label>
                    <select id="niza_classification" name="niza_classification" 
                            @change="showCustomNiza = ($event.target.value === '99')"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="">Seleccionar clasificación...</option>
                        <option value="1" <?php echo ($contact['niza_classification'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Productos químicos</option>
                        <option value="2" <?php echo ($contact['niza_classification'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Pinturas, barnices, lacas</option>
                        <option value="3" <?php echo ($contact['niza_classification'] ?? '') === '3' ? 'selected' : ''; ?>>3 - Productos de limpieza y cosméticos</option>
                        <option value="4" <?php echo ($contact['niza_classification'] ?? '') === '4' ? 'selected' : ''; ?>>4 - Aceites y combustibles industriales</option>
                        <option value="5" <?php echo ($contact['niza_classification'] ?? '') === '5' ? 'selected' : ''; ?>>5 - Productos farmacéuticos</option>
                        <option value="6" <?php echo ($contact['niza_classification'] ?? '') === '6' ? 'selected' : ''; ?>>6 - Metales comunes</option>
                        <option value="7" <?php echo ($contact['niza_classification'] ?? '') === '7' ? 'selected' : ''; ?>>7 - Máquinas y máquinas herramientas</option>
                        <option value="8" <?php echo ($contact['niza_classification'] ?? '') === '8' ? 'selected' : ''; ?>>8 - Herramientas e instrumentos manuales</option>
                        <option value="9" <?php echo ($contact['niza_classification'] ?? '') === '9' ? 'selected' : ''; ?>>9 - Aparatos científicos y electrónicos</option>
                        <option value="10" <?php echo ($contact['niza_classification'] ?? '') === '10' ? 'selected' : ''; ?>>10 - Aparatos médicos</option>
                        <option value="11" <?php echo ($contact['niza_classification'] ?? '') === '11' ? 'selected' : ''; ?>>11 - Aparatos de iluminación y calefacción</option>
                        <option value="12" <?php echo ($contact['niza_classification'] ?? '') === '12' ? 'selected' : ''; ?>>12 - Vehículos</option>
                        <option value="13" <?php echo ($contact['niza_classification'] ?? '') === '13' ? 'selected' : ''; ?>>13 - Armas de fuego</option>
                        <option value="14" <?php echo ($contact['niza_classification'] ?? '') === '14' ? 'selected' : ''; ?>>14 - Metales preciosos y joyería</option>
                        <option value="15" <?php echo ($contact['niza_classification'] ?? '') === '15' ? 'selected' : ''; ?>>15 - Instrumentos musicales</option>
                        <option value="16" <?php echo ($contact['niza_classification'] ?? '') === '16' ? 'selected' : ''; ?>>16 - Papel y artículos de papelería</option>
                        <option value="17" <?php echo ($contact['niza_classification'] ?? '') === '17' ? 'selected' : ''; ?>>17 - Caucho y plásticos</option>
                        <option value="18" <?php echo ($contact['niza_classification'] ?? '') === '18' ? 'selected' : ''; ?>>18 - Artículos de cuero</option>
                        <option value="19" <?php echo ($contact['niza_classification'] ?? '') === '19' ? 'selected' : ''; ?>>19 - Materiales de construcción no metálicos</option>
                        <option value="20" <?php echo ($contact['niza_classification'] ?? '') === '20' ? 'selected' : ''; ?>>20 - Muebles</option>
                        <option value="21" <?php echo ($contact['niza_classification'] ?? '') === '21' ? 'selected' : ''; ?>>21 - Utensilios domésticos</option>
                        <option value="22" <?php echo ($contact['niza_classification'] ?? '') === '22' ? 'selected' : ''; ?>>22 - Cuerdas y fibras textiles</option>
                        <option value="23" <?php echo ($contact['niza_classification'] ?? '') === '23' ? 'selected' : ''; ?>>23 - Hilos para uso textil</option>
                        <option value="24" <?php echo ($contact['niza_classification'] ?? '') === '24' ? 'selected' : ''; ?>>24 - Productos textiles</option>
                        <option value="25" <?php echo ($contact['niza_classification'] ?? '') === '25' ? 'selected' : ''; ?>>25 - Prendas de vestir</option>
                        <option value="26" <?php echo ($contact['niza_classification'] ?? '') === '26' ? 'selected' : ''; ?>>26 - Mercería y accesorios</option>
                        <option value="27" <?php echo ($contact['niza_classification'] ?? '') === '27' ? 'selected' : ''; ?>>27 - Alfombras y tapices</option>
                        <option value="28" <?php echo ($contact['niza_classification'] ?? '') === '28' ? 'selected' : ''; ?>>28 - Juegos y juguetes</option>
                        <option value="29" <?php echo ($contact['niza_classification'] ?? '') === '29' ? 'selected' : ''; ?>>29 - Carne, pescado, productos lácteos</option>
                        <option value="30" <?php echo ($contact['niza_classification'] ?? '') === '30' ? 'selected' : ''; ?>>30 - Café, té, productos de panadería</option>
                        <option value="31" <?php echo ($contact['niza_classification'] ?? '') === '31' ? 'selected' : ''; ?>>31 - Productos agrícolas y semillas</option>
                        <option value="32" <?php echo ($contact['niza_classification'] ?? '') === '32' ? 'selected' : ''; ?>>32 - Bebidas no alcohólicas</option>
                        <option value="33" <?php echo ($contact['niza_classification'] ?? '') === '33' ? 'selected' : ''; ?>>33 - Bebidas alcohólicas</option>
                        <option value="34" <?php echo ($contact['niza_classification'] ?? '') === '34' ? 'selected' : ''; ?>>34 - Tabaco y artículos para fumadores</option>
                        <option value="35" <?php echo ($contact['niza_classification'] ?? '') === '35' ? 'selected' : ''; ?>>35 - Publicidad y negocios</option>
                        <option value="36" <?php echo ($contact['niza_classification'] ?? '') === '36' ? 'selected' : ''; ?>>36 - Seguros y finanzas</option>
                        <option value="37" <?php echo ($contact['niza_classification'] ?? '') === '37' ? 'selected' : ''; ?>>37 - Construcción y reparaciones</option>
                        <option value="38" <?php echo ($contact['niza_classification'] ?? '') === '38' ? 'selected' : ''; ?>>38 - Telecomunicaciones</option>
                        <option value="39" <?php echo ($contact['niza_classification'] ?? '') === '39' ? 'selected' : ''; ?>>39 - Transporte y almacenamiento</option>
                        <option value="40" <?php echo ($contact['niza_classification'] ?? '') === '40' ? 'selected' : ''; ?>>40 - Tratamiento de materiales</option>
                        <option value="41" <?php echo ($contact['niza_classification'] ?? '') === '41' ? 'selected' : ''; ?>>41 - Educación y entretenimiento</option>
                        <option value="42" <?php echo ($contact['niza_classification'] ?? '') === '42' ? 'selected' : ''; ?>>42 - Servicios científicos y tecnológicos</option>
                        <option value="43" <?php echo ($contact['niza_classification'] ?? '') === '43' ? 'selected' : ''; ?>>43 - Servicios de restauración y hospedaje</option>
                        <option value="44" <?php echo ($contact['niza_classification'] ?? '') === '44' ? 'selected' : ''; ?>>44 - Servicios médicos y de belleza</option>
                        <option value="45" <?php echo ($contact['niza_classification'] ?? '') === '45' ? 'selected' : ''; ?>>45 - Servicios jurídicos y de seguridad</option>
                        <option value="99" <?php echo ($contact['niza_classification'] ?? '') === '99' ? 'selected' : ''; ?>>99 - OTRA CATEGORÍA</option>
                    </select>
                    <div x-show="showCustomNiza" x-cloak class="mt-2">
                        <input type="text" name="niza_custom_category" id="niza_custom_category"
                               value="<?php echo htmlspecialchars($contact['niza_custom_category'] ?? ''); ?>"
                               placeholder="Especifique la categoría personalizada..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                </div>
                
                <div>
                    <label for="products_sells" class="block text-sm font-medium text-gray-700">Productos que Vende</label>
                    <input type="text" id="products_sells" name="products_sells" 
                           value="<?php $productsSellsArray = json_decode($contact['products_sells'] ?? '[]', true); echo htmlspecialchars(is_array($productsSellsArray) && !empty($productsSellsArray) ? implode(', ', $productsSellsArray) : ''); ?>"
                           placeholder="Separados por coma"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="products_buys" class="block text-sm font-medium text-gray-700">Productos que Compra</label>
                    <input type="text" id="products_buys" name="products_buys" 
                           value="<?php $productsBuysArray = json_decode($contact['products_buys'] ?? '[]', true); echo htmlspecialchars(is_array($productsBuysArray) && !empty($productsBuysArray) ? implode(', ', $productsBuysArray) : ''); ?>"
                           placeholder="Separados por coma"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="discount_percentage" class="block text-sm font-medium text-gray-700">Porcentaje de Descuento</label>
                    <input type="number" id="discount_percentage" name="discount_percentage" 
                           value="<?php echo htmlspecialchars($contact['discount_percentage'] ?? 0); ?>"
                           min="0" max="100" step="0.01"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="assigned_affiliate_id" class="block text-sm font-medium text-gray-700">Afiliador Asignado</label>
                    <select id="assigned_affiliate_id" name="assigned_affiliate_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($affiliators as $affiliator): ?>
                        <option value="<?php echo $affiliator['id']; ?>" 
                            <?php echo ($contact['assigned_affiliate_id'] ?? '') == $affiliator['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($affiliator['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
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
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"><?php echo htmlspecialchars($contact['commercial_address'] ?? ''); ?></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label for="fiscal_address" class="block text-sm font-medium text-gray-700">Dirección Fiscal</label>
                    <textarea id="fiscal_address" name="fiscal_address" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"><?php echo htmlspecialchars($contact['fiscal_address'] ?? ''); ?></textarea>
                </div>
                
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                    <input type="text" id="city" name="city" 
                           value="<?php echo htmlspecialchars($contact['city'] ?? 'Santiago de Querétaro'); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700">Estado</label>
                    <input type="text" id="state" name="state" 
                           value="<?php echo htmlspecialchars($contact['state'] ?? 'Querétaro'); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Código Postal</label>
                    <input type="text" id="postal_code" name="postal_code" 
                           value="<?php echo htmlspecialchars($contact['postal_code'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="google_maps_url" class="block text-sm font-medium text-gray-700">URL Google Maps</label>
                    <input type="url" id="google_maps_url" name="google_maps_url" 
                           value="<?php echo htmlspecialchars($contact['google_maps_url'] ?? ''); ?>"
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
                           value="<?php echo htmlspecialchars($contact['website'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="facebook" class="block text-sm font-medium text-gray-700">Facebook</label>
                    <input type="url" id="facebook" name="facebook" 
                           value="<?php echo htmlspecialchars($contact['facebook'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="instagram" class="block text-sm font-medium text-gray-700">Instagram</label>
                    <input type="url" id="instagram" name="instagram" 
                           value="<?php echo htmlspecialchars($contact['instagram'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" 
                           value="<?php echo htmlspecialchars($contact['linkedin'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="twitter" class="block text-sm font-medium text-gray-700">Twitter / X</label>
                    <input type="url" id="twitter" name="twitter" 
                           value="<?php echo htmlspecialchars($contact['twitter'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- WhatsApp Contacts -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Contactos WhatsApp Adicionales</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="whatsapp_sales" class="block text-sm font-medium text-gray-700">WhatsApp Ventas</label>
                    <input type="text" id="whatsapp_sales" name="whatsapp_sales" 
                           value="<?php echo htmlspecialchars($contact['whatsapp_sales'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="whatsapp_purchases" class="block text-sm font-medium text-gray-700">WhatsApp Compras</label>
                    <input type="text" id="whatsapp_purchases" name="whatsapp_purchases" 
                           value="<?php echo htmlspecialchars($contact['whatsapp_purchases'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div>
                    <label for="whatsapp_admin" class="block text-sm font-medium text-gray-700">WhatsApp Administración</label>
                    <input type="text" id="whatsapp_admin" name="whatsapp_admin" 
                           value="<?php echo htmlspecialchars($contact['whatsapp_admin'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
            </div>
        </div>
        
        <!-- Notes -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Notas</h3>
            <div>
                <textarea id="notes" name="notes" rows="4"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"><?php echo htmlspecialchars($contact['notes'] ?? ''); ?></textarea>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
