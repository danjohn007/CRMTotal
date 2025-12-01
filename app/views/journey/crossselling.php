<!-- Customer Journey - Cross-selling Opportunities (Stage 4) -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/journey" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al Customer Journey
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                🎯 Oportunidades de Cross-Selling
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Etapa 4 del Customer Journey: Servicios adicionales de la cámara
            </p>
        </div>
    </div>
    
    <!-- Stage 4 Info -->
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl shadow-sm p-6 border border-purple-200">
        <div class="flex items-start">
            <span class="text-4xl mr-4">🎯</span>
            <div>
                <h3 class="text-lg font-semibold text-purple-800">Etapa 4: Cross-Selling de Servicios</h3>
                <p class="text-purple-700 mt-1">
                    Invitación a la contratación de servicios adicionales de la cámara. Se inicia un contador 
                    de todos los pagos y servicios contratados que la persona moral o física realiza.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm text-purple-700">
                        🏛️ Renta de Salones
                    </span>
                    <span class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm text-purple-700">
                        📣 Servicios de Marketing
                    </span>
                    <span class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm text-purple-700">
                        📚 Cursos y Talleres
                    </span>
                    <span class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm text-purple-700">
                        🎪 Expo's y Eventos Pagados
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <span class="text-xl">🎯</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Oportunidades</p>
                    <p class="text-2xl font-bold text-purple-600"><?php echo count($opportunities); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <span class="text-xl">💼</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Servicios Disponibles</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo count($services); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <span class="text-xl">📁</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Categorías</p>
                    <p class="text-2xl font-bold text-blue-600"><?php echo count($serviceCategories); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Categories -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Categorías de Servicios Disponibles</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <?php 
            $categoryIcons = [
                'salon_rental' => '🏛️',
                'event_organization' => '🎪',
                'course' => '📚',
                'conference' => '🎤',
                'training' => '👨‍🏫',
                'marketing_email' => '📧',
                'marketing_videowall' => '📺',
                'marketing_social' => '📱',
                'marketing_platform' => '💻',
                'gestoria' => '📋',
                'tramites' => '📑',
                'otros' => '📦'
            ];
            foreach ($serviceCategories as $code => $name): 
            ?>
            <div class="p-3 bg-purple-50 rounded-lg text-center border border-purple-200">
                <span class="text-2xl"><?php echo $categoryIcons[$code] ?? '📦'; ?></span>
                <p class="text-xs text-gray-700 mt-1"><?php echo $name; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Opportunities Table -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Afiliados Sin Servicios Contratados</h3>
                <p class="text-sm text-gray-500">Oportunidades de cross-selling identificadas</p>
            </div>
        </div>
        
        <?php if (empty($opportunities)): ?>
        <div class="p-12 text-center">
            <span class="text-6xl">🎉</span>
            <p class="text-gray-500 mt-4">¡Todos los afiliados han contratado servicios adicionales!</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membresía</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Industria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afiliador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($opportunities as $opp): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($opp['business_name']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($opp['commercial_name'] ?? '-'); ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                <?php echo htmlspecialchars($opp['current_membership']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo htmlspecialchars($opp['industry'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo htmlspecialchars($opp['affiliator_name'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="<?php echo BASE_URL; ?>/journey/<?php echo $opp['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver Journey
                                </a>
                                <button type="button" 
                                        onclick="openServiceInvitationModal(<?php echo $opp['id']; ?>, '<?php echo htmlspecialchars($opp['business_name']); ?>', <?php echo $opp['affiliation_id']; ?>)"
                                        class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                    📤 Enviar Invitación
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Available Services -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Servicios Disponibles para Ofrecer</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($services as $service): ?>
            <div class="p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($service['name']); ?></p>
                        <p class="text-xs text-gray-500 mt-1">
                            <?php echo $serviceCategories[$service['category']] ?? $service['category']; ?>
                        </p>
                    </div>
                    <span class="text-xl"><?php echo $categoryIcons[$service['category']] ?? '📦'; ?></span>
                </div>
                <?php if (!empty($service['description'])): ?>
                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars(substr($service['description'], 0, 100)); ?>...</p>
                <?php endif; ?>
                <div class="mt-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Precio público</p>
                        <p class="font-bold text-gray-900">$<?php echo number_format($service['price'] ?? 0, 0); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Precio afiliado</p>
                        <p class="font-bold text-green-600">$<?php echo number_format($service['member_price'] ?? 0, 0); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Service Invitation Modal -->
<div id="serviceInvitationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" onclick="closeServiceModal(event)">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">📤 Enviar Invitación de Servicios</h3>
                <button onclick="closeServiceModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form action="<?php echo BASE_URL; ?>/journey/sendServiceInvitation" method="POST" id="serviceForm">
                <input type="hidden" name="csrf_token" value="<?php echo $this->csrfToken(); ?>">
                <input type="hidden" name="contact_id" id="service_contact_id">
                <input type="hidden" name="affiliation_id" id="service_affiliation_id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <p id="service_business_name" class="text-gray-900 font-medium"></p>
                    </div>
                    
                    <!-- Service Selection -->
                    <div>
                        <label for="service_ids" class="block text-sm font-medium text-gray-700 mb-2">
                            Servicios a Ofrecer <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            <?php 
                            $groupedServices = [];
                            foreach ($services as $service) {
                                $category = $service['category'];
                                if (!isset($groupedServices[$category])) {
                                    $groupedServices[$category] = [];
                                }
                                $groupedServices[$category][] = $service;
                            }
                            
                            foreach ($groupedServices as $category => $categoryServices): 
                            ?>
                            <div class="mb-3">
                                <p class="text-sm font-semibold text-purple-700 mb-2">
                                    <?php echo $categoryIcons[$category] ?? '📦'; ?> 
                                    <?php echo $serviceCategories[$category] ?? $category; ?>
                                </p>
                                <?php foreach ($categoryServices as $service): ?>
                                <label class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox" name="service_ids[]" value="<?php echo $service['id']; ?>" 
                                           class="mt-1 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($service['name']); ?></p>
                                        <p class="text-xs text-gray-500">
                                            Público: $<?php echo number_format($service['price'] ?? 0, 0); ?> | 
                                            Afiliado: $<?php echo number_format($service['member_price'] ?? 0, 0); ?>
                                        </p>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="service_invitation_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipo de Invitación <span class="text-red-500">*</span>
                            </label>
                            <select name="invitation_type" id="service_invitation_type" required
                                    onchange="toggleServiceMessageFields()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                <option value="whatsapp">💬 WhatsApp</option>
                                <option value="email">📧 Email</option>
                                <option value="phone">📞 Teléfono</option>
                                <option value="in_person">🤝 Presencial</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- WhatsApp Fields -->
                    <div id="serviceWhatsappFields" class="space-y-3 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h4 class="font-medium text-green-800">💬 Mensaje de WhatsApp</h4>
                        <div>
                            <label for="service_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">
                                WhatsApp (10 dígitos)
                            </label>
                            <input type="tel" name="contact_whatsapp" id="service_whatsapp" 
                                   placeholder="4421234567" maxlength="10"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="service_whatsapp_message" class="block text-sm font-medium text-gray-700 mb-1">
                                Mensaje
                            </label>
                            <textarea name="whatsapp_message" id="service_whatsapp_message" rows="4" 
                                      placeholder="Escriba el mensaje..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">¡Hola! 👋

Te invitamos a conocer nuestros servicios exclusivos. Como afiliado tienes descuentos especiales en:
• Renta de salones 🏛️
• Servicios de marketing 📣
• Gestorías y trámites 📋
• Cursos y capacitaciones 📚

¿Te interesa conocer más detalles?</textarea>
                        </div>
                        <button type="button" onclick="openServiceWhatsApp()" 
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Abrir WhatsApp
                        </button>
                    </div>
                    
                    <!-- Email Fields -->
                    <div id="serviceEmailFields" class="space-y-3 p-4 bg-blue-50 rounded-lg border border-blue-200 hidden">
                        <h4 class="font-medium text-blue-800">📧 Mensaje de Email</h4>
                        <div>
                            <label for="service_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Correo Electrónico
                            </label>
                            <input type="email" name="contact_email" id="service_email" 
                                   placeholder="correo@empresa.com"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="service_email_subject" class="block text-sm font-medium text-gray-700 mb-1">
                                Asunto
                            </label>
                            <input type="text" name="email_subject" id="service_email_subject" 
                                   value="Servicios exclusivos para afiliados - Cámara de Comercio"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="service_email_message" class="block text-sm font-medium text-gray-700 mb-1">
                                Mensaje
                            </label>
                            <textarea name="email_message" id="service_email_message" rows="6" 
                                      placeholder="Escriba el contenido del email..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">Estimado(a) empresario(a),

Es un placer saludarte en nombre de la Cámara de Comercio de Querétaro.

Como afiliado valioso, queremos que conozcas los servicios exclusivos que tenemos para impulsar tu negocio:

🏛️ Renta de Salones - Espacios profesionales para tus eventos
📣 Servicios de Marketing - Promociona tu negocio
📋 Gestorías y Trámites - Te ayudamos con procesos gubernamentales  
📚 Cursos y Capacitaciones - Desarrollo profesional
🎪 Eventos y Expo's - Networking y oportunidades comerciales

¡Contamos contigo!

Atentamente,
Cámara de Comercio de Querétaro</textarea>
                        </div>
                        <button type="button" onclick="openServiceEmail()" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Abrir Cliente de Email
                        </button>
                    </div>
                    
                    <div>
                        <label for="service_notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Notas (opcional)
                        </label>
                        <textarea name="notes" id="service_notes" rows="2" placeholder="Notas adicionales..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-600">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Se documentará automáticamente: fecha, hora, servicios ofrecidos y tipo de contacto
                        </p>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-3">
                    <button type="button" onclick="closeServiceModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Enviar Invitación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openServiceInvitationModal(contactId, businessName, affiliationId) {
    document.getElementById('service_contact_id').value = contactId;
    document.getElementById('service_business_name').textContent = businessName;
    document.getElementById('service_affiliation_id').value = affiliationId;
    document.getElementById('serviceInvitationModal').classList.remove('hidden');
    toggleServiceMessageFields();
}

function closeServiceModal(event) {
    if (!event || event.target === document.getElementById('serviceInvitationModal')) {
        document.getElementById('serviceInvitationModal').classList.add('hidden');
    }
}

function toggleServiceMessageFields() {
    const type = document.getElementById('service_invitation_type').value;
    const whatsappFields = document.getElementById('serviceWhatsappFields');
    const emailFields = document.getElementById('serviceEmailFields');
    
    whatsappFields.classList.add('hidden');
    emailFields.classList.add('hidden');
    
    if (type === 'whatsapp') {
        whatsappFields.classList.remove('hidden');
    } else if (type === 'email') {
        emailFields.classList.remove('hidden');
    }
}

function openServiceWhatsApp() {
    const phone = document.getElementById('service_whatsapp').value.replace(/\D/g, '');
    const message = document.getElementById('service_whatsapp_message').value;
    
    if (!phone || phone.length !== 10) {
        alert('Por favor ingrese un número de WhatsApp válido de 10 dígitos');
        return;
    }
    
    const encodedMessage = encodeURIComponent(message);
    window.open('https://wa.me/52' + phone + '?text=' + encodedMessage, '_blank');
}

function openServiceEmail() {
    const email = document.getElementById('service_email').value;
    const subject = document.getElementById('service_email_subject').value;
    const body = document.getElementById('service_email_message').value;
    
    if (!email) {
        alert('Por favor ingrese un correo electrónico válido');
        return;
    }
    
    const mailtoLink = 'mailto:' + email + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
    window.location.href = mailtoLink;
}
</script>
