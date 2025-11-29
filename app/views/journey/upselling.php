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
                                <button onclick="openInvitationModal(<?php echo $opp['id']; ?>, '<?php echo addslashes($opp['business_name']); ?>', <?php echo $opp['affiliation_id']; ?>)" 
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

<!-- Invitation Modal -->
<div id="invitationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" onclick="closeInvitationModal(event)">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">📤 Enviar Invitación de Upgrade</h3>
                <button onclick="closeInvitationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form action="<?php echo BASE_URL; ?>/journey/sendUpsellingInvitation" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $this->csrfToken(); ?>">
                <input type="hidden" name="contact_id" id="modal_contact_id">
                <input type="hidden" name="current_membership_id" id="modal_current_membership_id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <p id="modal_business_name" class="text-gray-900 font-medium"></p>
                    </div>
                    
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="payment_link">🔗 Liga de pago en línea</option>
                            <option value="email">📧 Email</option>
                            <option value="whatsapp">💬 WhatsApp</option>
                            <option value="phone">📞 Teléfono</option>
                            <option value="in_person">🤝 Presencial</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="payment_link_url" class="block text-sm font-medium text-gray-700 mb-1">
                            URL de Liga de Pago (opcional)
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
                </div>
                
                <div class="mt-6 flex space-x-3">
                    <button type="button" onclick="closeInvitationModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                        Enviar Invitación
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
}

function closeInvitationModal(event) {
    if (!event || event.target === document.getElementById('invitationModal')) {
        document.getElementById('invitationModal').classList.add('hidden');
    }
}
</script>
