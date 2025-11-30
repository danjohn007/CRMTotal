<!-- Customer Journey - Upselling Opportunities (Stage 5) -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/journey" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al Customer Journey
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">
                📈 Oportunidades de Up-Selling
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Etapa 5 del Customer Journey: Upgrade de membresía (mínimo 2 invitaciones por año)
            </p>
        </div>
    </div>
    
    <!-- Stage 5 Info -->
    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl shadow-sm p-6 border border-yellow-200">
        <div class="flex items-start">
            <span class="text-4xl mr-4">📈</span>
            <div>
                <h3 class="text-lg font-semibold text-yellow-800">Etapa 5: Up-Selling de Membresías</h3>
                <p class="text-yellow-700 mt-1">
                    Todo afiliado deberá recibir la invitación a escalar su compra al menos <strong>2 veces por año</strong>, 
                    documentando fecha y hora del envío de liga para el pago en línea.
                </p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <div class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm">
                        <span class="font-medium text-gray-700">Pyme</span>
                        <span class="mx-2 text-gray-400">→</span>
                        <span class="font-medium text-blue-600">Visionario</span>
                    </div>
                    <div class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm">
                        <span class="font-medium text-blue-600">Visionario</span>
                        <span class="mx-2 text-gray-400">→</span>
                        <span class="font-medium text-purple-600">Premier</span>
                    </div>
                    <div class="inline-flex items-center px-3 py-1 bg-white rounded-full text-sm">
                        <span class="font-medium text-purple-600">Premier</span>
                        <span class="mx-2 text-gray-400">→</span>
                        <span class="font-medium text-amber-600">Patrocinador</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <span class="text-xl">🎯</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Oportunidades</p>
                    <p class="text-2xl font-bold text-yellow-600"><?php echo count($opportunities); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <span class="text-xl">⚠️</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Sin Invitaciones</p>
                    <p class="text-2xl font-bold text-red-600">
                        <?php 
                        $noInvitations = count(array_filter($opportunities, function($o) use ($invitationsThisYear) {
                            return !isset($invitationsThisYear[$o['id']]) || $invitationsThisYear[$o['id']] === 0;
                        }));
                        echo $noInvitations;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <span class="text-xl">📬</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Con 1 Invitación</p>
                    <p class="text-2xl font-bold text-orange-600">
                        <?php 
                        $oneInvitation = count(array_filter($opportunities, function($o) use ($invitationsThisYear) {
                            return isset($invitationsThisYear[$o['id']]) && $invitationsThisYear[$o['id']] === 1;
                        }));
                        echo $oneInvitation;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <span class="text-xl">✓</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">2+ Invitaciones</p>
                    <p class="text-2xl font-bold text-green-600">
                        <?php 
                        $twoInvitations = count(array_filter($opportunities, function($o) use ($invitationsThisYear) {
                            return isset($invitationsThisYear[$o['id']]) && $invitationsThisYear[$o['id']] >= 2;
                        }));
                        echo $twoInvitations;
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Membership Types -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jerarquía de Membresías</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php 
            $membershipColors = [
                'PYME' => 'bg-gray-100 border-gray-400',
                'VISIONARIO' => 'bg-blue-100 border-blue-400',
                'PREMIER' => 'bg-purple-100 border-purple-400',
                'PATROCINADOR' => 'bg-amber-100 border-amber-400'
            ];
            foreach ($membershipTypes as $membership): 
                if (($membership['upsell_order'] ?? 0) < 1) continue;
                $colorClass = $membershipColors[$membership['code']] ?? 'bg-gray-100 border-gray-400';
            ?>
            <div class="p-4 rounded-lg border-2 <?php echo $colorClass; ?>">
                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($membership['name']); ?></p>
                <p class="text-lg font-bold text-gray-900 mt-1">$<?php echo number_format($membership['price'], 0); ?></p>
                <p class="text-xs text-gray-500">Nivel <?php echo $membership['upsell_order']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Opportunities Table -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Oportunidades de Up-Selling</h3>
        </div>
        
        <?php if (empty($opportunities)): ?>
        <div class="p-12 text-center">
            <span class="text-6xl">🎉</span>
            <p class="text-gray-500 mt-4">¡No hay oportunidades de up-selling pendientes!</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membresía Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invitaciones (Año)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afiliador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($opportunities as $opp): 
                        $invCount = $invitationsThisYear[$opp['id']] ?? 0;
                    ?>
                    <tr class="hover:bg-gray-50 <?php echo $invCount < 2 ? 'bg-yellow-50' : ''; ?>">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($opp['business_name']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($opp['rfc'] ?? '-'); ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                <?php echo $membershipColors[$opp['membership_code']] ?? 'bg-gray-100'; ?>">
                                <?php echo htmlspecialchars($opp['current_membership']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo date('d/m/Y', strtotime($opp['expiration_date'])); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($invCount >= 2): ?>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                ✓ <?php echo $invCount; ?>/2 completo
                            </span>
                            <?php elseif ($invCount === 1): ?>
                            <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
                                ⚠ <?php echo $invCount; ?>/2 falta 1
                            </span>
                            <?php else: ?>
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                ❌ 0/2 pendiente
                            </span>
                            <?php endif; ?>
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
                                <?php if ($invCount < 2): ?>
                                <button onclick="openInvitationModal(<?php echo $opp['id']; ?>, '<?php echo addslashes($opp['business_name']); ?>', <?php echo $opp['current_membership_type_id'] ?? 0; ?>)" 
                                        class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                                    Enviar Invitación
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Invitation Modal with WhatsApp and Email -->
<div id="invitationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" onclick="closeInvitationModal(event)">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">📤 Enviar Invitación de Upgrade</h3>
                <button onclick="closeInvitationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form action="<?php echo BASE_URL; ?>/journey/sendUpsellingInvitation" method="POST" id="upsellingForm">
                <input type="hidden" name="csrf_token" value="<?php echo $this->csrfToken(); ?>">
                <input type="hidden" name="contact_id" id="modal_contact_id">
                <input type="hidden" name="current_membership_id" id="modal_current_membership_id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <p id="modal_business_name" class="text-gray-900 font-medium"></p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="target_membership_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Membresía Objetivo
                            </label>
                            <select name="target_membership_id" id="target_membership_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <?php foreach ($membershipTypes as $membership): 
                                    if (($membership['upsell_order'] ?? 0) < 1) continue;
                                ?>
                                <option value="<?php echo $membership['id']; ?>">
                                    <?php echo htmlspecialchars($membership['name']); ?> - $<?php echo number_format($membership['price'], 0); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="invitation_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipo de Invitación
                            </label>
                            <select name="invitation_type" id="invitation_type" required
                                    onchange="toggleMessageFields()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="whatsapp">💬 WhatsApp</option>
                                <option value="email">📧 Email</option>
                                <option value="payment_link">🔗 Liga de pago</option>
                                <option value="phone">📞 Teléfono</option>
                                <option value="in_person">🤝 Presencial</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- WhatsApp Fields -->
                    <div id="whatsappFields" class="space-y-3 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h4 class="font-medium text-green-800">💬 Mensaje de WhatsApp</h4>
                        <div>
                            <label for="contact_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">
                                Número de WhatsApp
                            </label>
                            <input type="text" name="contact_whatsapp" id="contact_whatsapp" 
                                   placeholder="10 dígitos"
                                   maxlength="10" pattern="[0-9]{10}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="whatsapp_message" class="block text-sm font-medium text-gray-700 mb-1">
                                Mensaje
                            </label>
                            <textarea name="whatsapp_message" id="whatsapp_message" rows="4" 
                                      placeholder="Escriba el mensaje de invitación..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">¡Hola! 👋

Te invitamos a conocer los beneficios de nuestra membresía superior. Con el upgrade podrás disfrutar de mayores descuentos, acceso a eventos exclusivos y más.

¿Te gustaría conocer más detalles? Puedes realizar tu pago en línea aquí:</textarea>
                        </div>
                        <button type="button" onclick="openWhatsApp()" 
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Abrir WhatsApp
                        </button>
                    </div>
                    
                    <!-- Email Fields -->
                    <div id="emailFields" class="space-y-3 p-4 bg-blue-50 rounded-lg border border-blue-200 hidden">
                        <h4 class="font-medium text-blue-800">📧 Mensaje de Email</h4>
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Correo Electrónico
                            </label>
                            <input type="email" name="contact_email" id="contact_email" 
                                   placeholder="correo@empresa.com"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="email_subject" class="block text-sm font-medium text-gray-700 mb-1">
                                Asunto
                            </label>
                            <input type="text" name="email_subject" id="email_subject" 
                                   value="Invitación exclusiva: Upgrade de membresía - Cámara de Comercio de Querétaro"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="email_message" class="block text-sm font-medium text-gray-700 mb-1">
                                Mensaje
                            </label>
                            <textarea name="email_message" id="email_message" rows="6" 
                                      placeholder="Escriba el contenido del email..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">Estimado(a) empresario(a),

Es un placer saludarte en nombre de la Cámara de Comercio de Querétaro.

Queremos invitarte a conocer los beneficios exclusivos de nuestra membresía superior, diseñada para potenciar el crecimiento de tu empresa.

Con el upgrade podrás disfrutar de:
• Mayores descuentos en eventos y capacitaciones
• Acceso a networking exclusivo
• Asesoría personalizada
• Mayor visibilidad en nuestra plataforma

Te invitamos a realizar tu upgrade en el siguiente enlace de pago seguro:

¡Contamos contigo!

Atentamente,
Cámara de Comercio de Querétaro</textarea>
                        </div>
                        <button type="button" onclick="openEmail()" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Abrir Cliente de Email
                        </button>
                    </div>
                    
                    <!-- Payment Link -->
                    <div id="paymentLinkFields">
                        <label for="payment_link_url" class="block text-sm font-medium text-gray-700 mb-1">
                            URL de Liga de Pago
                        </label>
                        <input type="url" name="payment_link_url" id="payment_link_url" placeholder="https://..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Notas (opcional)
                        </label>
                        <textarea name="notes" id="notes" rows="2" placeholder="Notas adicionales..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <!-- Documentation Info -->
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-600">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Se documentará automáticamente: fecha, hora, tipo de envío y contenido del mensaje
                        </p>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-3">
                    <button type="button" onclick="closeInvitationModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                        Documentar y Guardar Invitación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openInvitationModal(contactId, businessName, affiliationId) {
    document.getElementById('modal_contact_id').value = contactId;
    document.getElementById('modal_business_name').textContent = businessName;
    document.getElementById('modal_current_membership_id').value = affiliationId;
    document.getElementById('invitationModal').classList.remove('hidden');
    toggleMessageFields();
}

function closeInvitationModal(event) {
    if (!event || event.target === document.getElementById('invitationModal')) {
        document.getElementById('invitationModal').classList.add('hidden');
    }
}

function toggleMessageFields() {
    const type = document.getElementById('invitation_type').value;
    const whatsappFields = document.getElementById('whatsappFields');
    const emailFields = document.getElementById('emailFields');
    const paymentLinkFields = document.getElementById('paymentLinkFields');
    
    // Hide all first
    whatsappFields.classList.add('hidden');
    emailFields.classList.add('hidden');
    paymentLinkFields.classList.add('hidden');
    
    // Show based on type
    if (type === 'whatsapp') {
        whatsappFields.classList.remove('hidden');
        paymentLinkFields.classList.remove('hidden');
    } else if (type === 'email') {
        emailFields.classList.remove('hidden');
        paymentLinkFields.classList.remove('hidden');
    } else if (type === 'payment_link') {
        paymentLinkFields.classList.remove('hidden');
    }
}

function openWhatsApp() {
    const phone = document.getElementById('contact_whatsapp').value.replace(/\D/g, '');
    const message = document.getElementById('whatsapp_message').value;
    const paymentLink = document.getElementById('payment_link_url').value;
    
    if (!phone || phone.length !== 10) {
        alert('Por favor ingrese un número de WhatsApp válido de 10 dígitos');
        return;
    }
    
    let fullMessage = message;
    if (paymentLink) {
        fullMessage += '\n\n' + paymentLink;
    }
    
    const encodedMessage = encodeURIComponent(fullMessage);
    window.open('https://wa.me/52' + phone + '?text=' + encodedMessage, '_blank');
}

function openEmail() {
    const email = document.getElementById('contact_email').value;
    const subject = document.getElementById('email_subject').value;
    const body = document.getElementById('email_message').value;
    const paymentLink = document.getElementById('payment_link_url').value;
    
    if (!email) {
        alert('Por favor ingrese un correo electrónico válido');
        return;
    }
    
    let fullBody = body;
    if (paymentLink) {
        fullBody += '\n\n' + paymentLink;
    }
    
    const mailtoLink = 'mailto:' + email + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(fullBody);
    window.location.href = mailtoLink;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleMessageFields();
});
</script>
