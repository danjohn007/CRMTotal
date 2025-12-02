<!-- Membership Detail View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($membership['name']); ?></h2>
            <p class="mt-1 text-sm text-gray-500">Código: <?php echo htmlspecialchars($membership['code']); ?></p>
        </div>
        <div class="flex space-x-3">
            <a href="<?php echo BASE_URL; ?>/membresias" class="text-blue-600 hover:text-blue-800">
                ← Volver a Membresías
            </a>
            <?php if (($_SESSION['user_role'] ?? '') === 'superadmin'): ?>
            <a href="<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>/editar" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Editar
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Membership Details -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-center mb-6">
                    <span class="px-3 py-1 text-sm rounded-full <?php echo $membership['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                        <?php echo $membership['is_active'] ? 'Activo' : 'Inactivo'; ?>
                    </span>
                </div>
                
                <p class="text-4xl font-bold text-blue-600 text-center mb-2">
                    $<?php echo number_format($membership['price'], 2); ?>
                </p>
                <p class="text-center text-gray-500 mb-6">
                    Duración: <?php echo $membership['duration_days']; ?> días
                </p>
                
                <!-- Share Payment Link -->
                <?php if (!empty($membership['paypal_product_id'])): ?>
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-2 text-center">Enlace de Pago Público</p>
                    <div class="flex items-center space-x-2 mb-2">
                        <input type="text" 
                               id="payment-link-<?php echo $membership['id']; ?>" 
                               value="<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>/pagar" 
                               readonly
                               class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded bg-white">
                        <button onclick="copyPaymentLink(<?php echo $membership['id']; ?>)" 
                                class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>/pagar" 
                       target="_blank"
                       class="block text-center text-xs text-blue-600 hover:text-blue-800">
                        Ver página de pago →
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="border-t pt-4">
                    <h4 class="font-medium text-gray-900 mb-3">Beneficios</h4>
                    <?php 
                    $benefits = json_decode($membership['benefits'] ?? '{}', true);
                    if (!empty($benefits)):
                    ?>
                    <ul class="space-y-2">
                        <?php foreach ($benefits as $key => $value): ?>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <?php 
                            $label = str_replace('_', ' ', ucfirst($key));
                            echo htmlspecialchars($label);
                            if ($value !== true && $value !== 1) {
                                echo ': ' . htmlspecialchars($value);
                            }
                            ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p class="text-sm text-gray-500">No hay beneficios configurados</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="mt-4 bg-white rounded-lg shadow-sm p-6">
                <h4 class="font-medium text-gray-900 mb-4">Estadísticas</h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total de afiliaciones:</span>
                        <span class="font-semibold"><?php echo count($affiliations); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ingresos generados:</span>
                        <span class="font-semibold text-green-600">$<?php echo number_format($revenue, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Affiliations -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Afiliaciones con esta Membresía</h3>
                </div>
                <?php if (!empty($affiliations)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pago</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($affiliations as $affiliation): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('d/m/Y', strtotime($affiliation['affiliation_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d/m/Y', strtotime($affiliation['expiration_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?php echo $affiliation['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                                  ($affiliation['status'] === 'expired' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                        <?php echo ucfirst($affiliation['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?php echo $affiliation['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo ucfirst($affiliation['payment_status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    $<?php echo number_format($affiliation['amount'], 2); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-6 text-center text-gray-500">
                    No hay afiliaciones con esta membresía
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function copyPaymentLink(membershipId) {
    const input = document.getElementById('payment-link-' + membershipId);
    if (!input) {
        console.error('Input not found for membership:', membershipId);
        return;
    }
    
    // Usar navigator.clipboard para copiar (más moderno)
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value)
            .then(() => {
                // Obtener el botón que disparó el evento
                const button = event.target.closest('button');
                if (!button) return;
                
                const originalHTML = button.innerHTML;
                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                button.classList.add('bg-green-600');
                button.classList.remove('bg-blue-600');
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-blue-600');
                }, 2000);
            })
            .catch(err => {
                console.error('Error al copiar:', err);
                // Fallback al método antiguo
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');
            });
    } else {
        // Fallback para navegadores antiguos
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        const button = event.target.closest('button');
        if (!button) return;
        
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        button.classList.add('bg-green-600');
        button.classList.remove('bg-blue-600');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('bg-green-600');
            button.classList.add('bg-blue-600');
        }, 2000);
    }
}
</script>
