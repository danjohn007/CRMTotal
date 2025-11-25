<!-- Affiliate Show View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($contact['business_name'] ?? 'Detalle de Afiliado'); ?></h2>
            <p class="mt-1 text-sm text-gray-500">
                <?php if (!empty($contact['commercial_name'])): ?>
                    <?php echo htmlspecialchars($contact['commercial_name']); ?> |
                <?php endif; ?>
                RFC: <?php echo htmlspecialchars($contact['rfc'] ?? 'No registrado'); ?>
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>/editar" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Editar
            </a>
            <a href="<?php echo BASE_URL; ?>/afiliados/<?php echo $contact['id']; ?>/expediente" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Ver Expediente Digital
            </a>
            <a href="<?php echo BASE_URL; ?>/afiliados" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                ← Volver
            </a>
        </div>
    </div>
    
    <!-- Profile Completion -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Completitud del Perfil</span>
            <span class="text-sm font-bold text-gray-900"><?php echo $contact['profile_completion'] ?? 0; ?>%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full <?php echo ($contact['profile_completion'] ?? 0) >= 70 ? 'bg-green-500' : (($contact['profile_completion'] ?? 0) >= 35 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
                 style="width: <?php echo $contact['profile_completion'] ?? 0; ?>%"></div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de Contacto</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500">Razón Social</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['business_name'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Nombre Comercial</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['commercial_name'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Propietario / Representante</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['owner_name'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Representante Legal</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['legal_representative'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Correo Corporativo</label>
                        <p class="text-gray-900">
                            <?php if (!empty($contact['corporate_email'])): ?>
                            <a href="mailto:<?php echo htmlspecialchars($contact['corporate_email']); ?>" class="text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($contact['corporate_email']); ?>
                            </a>
                            <?php else: ?>-<?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Teléfono</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['phone'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">WhatsApp</label>
                        <p class="text-gray-900">
                            <?php if (!empty($contact['whatsapp'])): ?>
                            <a href="https://wa.me/52<?php echo preg_replace('/[^0-9]/', '', $contact['whatsapp']); ?>" target="_blank" class="text-green-600 hover:underline">
                                <?php echo htmlspecialchars($contact['whatsapp']); ?>
                            </a>
                            <?php else: ?>-<?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Giro / Industria</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['industry'] ?? '-'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Address Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Direcciones</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500">Dirección Comercial</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['commercial_address'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Dirección Fiscal</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['fiscal_address'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Ciudad</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['city'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Estado</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['state'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Código Postal</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($contact['postal_code'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Google Maps</label>
                        <p class="text-gray-900">
                            <?php if (!empty($contact['google_maps_url'])): ?>
                            <a href="<?php echo htmlspecialchars($contact['google_maps_url']); ?>" target="_blank" class="text-blue-600 hover:underline">Ver en mapa</a>
                            <?php else: ?>-<?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividades Recientes</h3>
                <?php if (!empty($activities)): ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($activities, 0, 5) as $activity): ?>
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo date('d/m/Y H:i', strtotime($activity['scheduled_date'])); ?></p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?php echo $activity['status'] === 'completada' ? 'bg-green-100 text-green-800' : 
                                ($activity['status'] === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo ucfirst($activity['status']); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-center py-4">No hay actividades registradas</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Current Affiliation -->
            <?php if (!empty($affiliations)): $affiliation = $affiliations[0]; ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Afiliación Actual</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-500">Membresía</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($affiliation['membership_name'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Fecha de Afiliación</label>
                        <p class="text-gray-900"><?php echo date('d/m/Y', strtotime($affiliation['affiliation_date'])); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Fecha de Vencimiento</label>
                        <p class="text-gray-900 <?php echo strtotime($affiliation['expiration_date']) < strtotime('+30 days') ? 'text-red-600 font-bold' : ''; ?>">
                            <?php echo date('d/m/Y', strtotime($affiliation['expiration_date'])); ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Estado</label>
                        <span class="inline-flex px-2 py-1 text-xs rounded-full 
                            <?php echo $affiliation['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                ($affiliation['status'] === 'pending_payment' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                            <?php echo $affiliation['status'] === 'active' ? 'Activo' : 
                                ($affiliation['status'] === 'pending_payment' ? 'Pendiente de Pago' : ucfirst($affiliation['status'])); ?>
                        </span>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Afiliador</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($affiliation['affiliate_name'] ?? '-'); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Web & Social -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Web y Redes Sociales</h3>
                <div class="space-y-3">
                    <?php if (!empty($contact['website'])): ?>
                    <a href="<?php echo htmlspecialchars($contact['website']); ?>" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        Sitio Web
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($contact['facebook']); ?>" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($contact['instagram']); ?>" target="_blank" class="flex items-center text-gray-700 hover:text-pink-600">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        Instagram
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['linkedin'])): ?>
                    <a href="<?php echo htmlspecialchars($contact['linkedin']); ?>" target="_blank" class="flex items-center text-gray-700 hover:text-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                        LinkedIn
                    </a>
                    <?php endif; ?>
                    <?php if (empty($contact['website']) && empty($contact['facebook']) && empty($contact['instagram']) && empty($contact['linkedin'])): ?>
                    <p class="text-gray-500 text-sm">No hay información registrada</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-2">
                    <a href="<?php echo BASE_URL; ?>/agenda/nueva?contact_id=<?php echo $contact['id']; ?>" 
                       class="block w-full px-4 py-2 text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Nueva Actividad
                    </a>
                    <?php if (!empty($contact['whatsapp'])): ?>
                    <a href="https://wa.me/52<?php echo preg_replace('/[^0-9]/', '', $contact['whatsapp']); ?>" target="_blank"
                       class="block w-full px-4 py-2 text-center bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Enviar WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
