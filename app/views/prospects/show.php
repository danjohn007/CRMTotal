<!-- Show Prospect -->
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <a href="<?php echo BASE_URL; ?>/prospectos" class="text-gray-500 hover:text-gray-700 mr-2">← </a>
                <h2 class="text-2xl font-bold text-gray-900">
                    <?php echo htmlspecialchars($prospect['business_name'] ?? $prospect['owner_name']); ?>
                </h2>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                <?php echo htmlspecialchars($prospect['commercial_name'] ?? ''); ?>
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/agenda/nueva?contact_id=<?php echo $prospect['id']; ?>" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Nueva Actividad
            </a>
            <a href="<?php echo BASE_URL; ?>/prospectos/<?php echo $prospect['id']; ?>/editar" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                Editar
            </a>
            <a href="<?php echo BASE_URL; ?>/afiliados/nuevo?prospect_id=<?php echo $prospect['id']; ?>" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Convertir a Afiliado
            </a>
        </div>
    </div>
    
    <!-- Profile Completion -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Completitud del Perfil</h3>
            <span class="text-2xl font-bold text-<?php echo $prospect['profile_completion'] >= 70 ? 'green' : ($prospect['profile_completion'] >= 35 ? 'yellow' : 'red'); ?>-600">
                <?php echo $prospect['profile_completion']; ?>%
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-<?php echo $prospect['profile_completion'] >= 70 ? 'green' : ($prospect['profile_completion'] >= 35 ? 'yellow' : 'red'); ?>-500 h-4 rounded-full transition-all" 
                 style="width: <?php echo $prospect['profile_completion']; ?>%"></div>
        </div>
        <div class="mt-2 flex justify-between text-xs text-gray-500">
            <span>Etapa A (25%)</span>
            <span>Etapa B (35%)</span>
            <span>Etapa C (70%)</span>
            <span>Completo (100%)</span>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Company Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Empresa</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">RFC</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['rfc'] ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">WhatsApp</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['whatsapp'] ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Razón Social</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['business_name'] ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nombre Comercial</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['commercial_name'] ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Propietario/Representante</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['owner_name'] ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Industria</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['industry'] ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Correo Corporativo</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="mailto:<?php echo htmlspecialchars($prospect['corporate_email'] ?? ''); ?>" class="text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($prospect['corporate_email'] ?? '-'); ?>
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['phone'] ?? '-'); ?></dd>
                    </div>
                </dl>
            </div>
            
            <!-- Address -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dirección</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Dirección Comercial</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['commercial_address'] ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ciudad</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['city'] ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Código Postal</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($prospect['postal_code'] ?? '-'); ?></dd>
                    </div>
                </dl>
            </div>
            
            <!-- Activities -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Historial de Actividades</h3>
                    <a href="<?php echo BASE_URL; ?>/agenda/nueva?contact_id=<?php echo $prospect['id']; ?>" 
                       class="text-sm text-blue-600 hover:text-blue-800">+ Nueva</a>
                </div>
                <?php if (empty($activities)): ?>
                <p class="text-gray-500 text-center py-8">No hay actividades registradas</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($activities as $activity): ?>
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                <?php echo $activity['status'] === 'completada' ? 'bg-green-100' : 'bg-gray-200'; ?>">
                                <?php if ($activity['status'] === 'completada'): ?>
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <?php else: ?>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['title']); ?></p>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php echo date('d/m/Y H:i', strtotime($activity['scheduled_date'])); ?> 
                                - <?php echo ucfirst($activity['activity_type']); ?>
                                <?php if ($activity['user_name']): ?>
                                por <?php echo htmlspecialchars($activity['user_name']); ?>
                                <?php endif; ?>
                            </p>
                            <?php if ($activity['result']): ?>
                            <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($activity['result']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Estado</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                Prospecto
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Canal</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo $channels[$prospect['source_channel']] ?? $prospect['source_channel']; ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Etapa</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            Etapa <?php echo $prospect['completion_stage']; ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Registrado</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo date('d/m/Y', strtotime($prospect['created_at'])); ?>
                        </dd>
                    </div>
                </dl>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                    <?php if ($prospect['whatsapp']): ?>
                    <a href="https://wa.me/52<?php echo $prospect['whatsapp']; ?>" target="_blank"
                       class="flex items-center p-3 bg-green-50 rounded-lg text-green-700 hover:bg-green-100">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        </svg>
                        Enviar WhatsApp
                    </a>
                    <?php endif; ?>
                    <?php if ($prospect['corporate_email']): ?>
                    <a href="mailto:<?php echo $prospect['corporate_email']; ?>"
                       class="flex items-center p-3 bg-blue-50 rounded-lg text-blue-700 hover:bg-blue-100">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Enviar Correo
                    </a>
                    <?php endif; ?>
                    <?php if ($prospect['phone']): ?>
                    <a href="tel:<?php echo $prospect['phone']; ?>"
                       class="flex items-center p-3 bg-purple-50 rounded-lg text-purple-700 hover:bg-purple-100">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Llamar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Notes -->
            <?php if ($prospect['notes']): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notas</h3>
                <p class="text-sm text-gray-600"><?php echo nl2br(htmlspecialchars($prospect['notes'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
