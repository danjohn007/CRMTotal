<!-- Edit Prospect -->
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Editar Prospecto</h2>
            <p class="mt-1 text-sm text-gray-500"><?php echo htmlspecialchars($prospect['business_name'] ?? $prospect['owner_name'] ?? ''); ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>/prospectos/<?php echo $prospect['id']; ?>" class="text-blue-600 hover:text-blue-800">
            ← Volver al detalle
        </a>
    </div>
    
    <?php if (!empty($error)): ?>
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
    </div>
    <?php endif; ?>
    
    <form action="<?php echo BASE_URL; ?>/prospectos/<?php echo $prospect['id']; ?>/editar" method="POST" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <!-- Identification -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Identificación</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="rfc" class="block text-sm font-medium text-gray-700">RFC *</label>
                    <input type="text" name="rfc" id="rfc" maxlength="13" required
                           value="<?php echo htmlspecialchars($prospect['rfc'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="RFC de la empresa">
                </div>
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp *</label>
                    <input type="text" name="whatsapp" id="whatsapp" required
                           value="<?php echo htmlspecialchars($prospect['whatsapp'] ?? ''); ?>"
                           maxlength="10" pattern="[0-9]{10}" title="Ingrese exactamente 10 dígitos"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="10 dígitos">
                </div>
            </div>
        </div>
        
        <!-- Company Info -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Empresa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700">Razón Social *</label>
                    <input type="text" name="business_name" id="business_name" required
                           value="<?php echo htmlspecialchars($prospect['business_name'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="commercial_name" class="block text-sm font-medium text-gray-700">Nombre Comercial *</label>
                    <input type="text" name="commercial_name" id="commercial_name" required
                           value="<?php echo htmlspecialchars($prospect['commercial_name'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="owner_name" class="block text-sm font-medium text-gray-700">Nombre del Encargado/Recepcionista/Representante Legal *</label>
                    <input type="text" name="owner_name" id="owner_name" required
                           value="<?php echo htmlspecialchars($prospect['owner_name'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="industry" class="block text-sm font-medium text-gray-700">Industria / Giro (Clasificación Niza) *</label>
                    <input type="text" name="industry" id="industry" required
                           value="<?php echo htmlspecialchars($prospect['industry'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="corporate_email" class="block text-sm font-medium text-gray-700">Email (Opcional)</label>
                    <input type="email" name="corporate_email" id="corporate_email"
                           value="<?php echo htmlspecialchars($prospect['corporate_email'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono Oficina (Opcional)</label>
                    <input type="text" name="phone" id="phone"
                           value="<?php echo htmlspecialchars($prospect['phone'] ?? ''); ?>"
                           maxlength="10" pattern="[0-9]{10}" title="Ingrese exactamente 10 dígitos"
                           placeholder="10 dígitos"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="legal_representative" class="block text-sm font-medium text-gray-700">Representante Legal (Opcional)</label>
                    <input type="text" name="legal_representative" id="legal_representative"
                           value="<?php echo htmlspecialchars($prospect['legal_representative'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700">Sitio Web (Opcional)</label>
                    <input type="url" name="website" id="website"
                           value="<?php echo htmlspecialchars($prospect['website'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://...">
                </div>
            </div>
        </div>
        
        <!-- Source and Assignment -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Canal y Asignación</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="source_channel" class="block text-sm font-medium text-gray-700">Canal de Obtención *</label>
                    <select name="source_channel" id="source_channel" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($channels as $key => $label): ?>
                        <option value="<?php echo $key; ?>" <?php echo ($prospect['source_channel'] ?? '') === $key ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="assigned_affiliate_id" class="block text-sm font-medium text-gray-700">Afiliador Asignado</label>
                    <select name="assigned_affiliate_id" id="assigned_affiliate_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($affiliators as $affiliator): ?>
                        <option value="<?php echo $affiliator['id']; ?>" 
                                <?php echo ($prospect['assigned_affiliate_id'] ?? 0) == $affiliator['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($affiliator['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Address -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Dirección (Opcional)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="fiscal_address" class="block text-sm font-medium text-gray-700">Dirección Fiscal (Opcional)</label>
                    <input type="text" name="fiscal_address" id="fiscal_address"
                           value="<?php echo htmlspecialchars($prospect['fiscal_address'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label for="commercial_address" class="block text-sm font-medium text-gray-700">Dirección Comercial (Opcional)</label>
                    <input type="text" name="commercial_address" id="commercial_address"
                           value="<?php echo htmlspecialchars($prospect['commercial_address'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                    <input type="text" name="city" id="city" 
                           value="<?php echo htmlspecialchars($prospect['city'] ?? 'Santiago de Querétaro'); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700">Estado</label>
                    <input type="text" name="state" id="state" 
                           value="<?php echo htmlspecialchars($prospect['state'] ?? 'Querétaro'); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Código Postal</label>
                    <input type="text" name="postal_code" id="postal_code" maxlength="5"
                           value="<?php echo htmlspecialchars($prospect['postal_code'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
        
        <!-- Notes -->
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">Notas (Opcional)</label>
            <textarea name="notes" id="notes" rows="3"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Notas adicionales sobre el prospecto..."><?php echo htmlspecialchars($prospect['notes'] ?? ''); ?></textarea>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-4">
            <a href="<?php echo BASE_URL; ?>/prospectos/<?php echo $prospect['id']; ?>" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
