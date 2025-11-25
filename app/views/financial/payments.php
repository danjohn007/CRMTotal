<!-- Payments View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Gestión de Pagos</h2>
            <p class="mt-1 text-sm text-gray-500">Registro y seguimiento de pagos de membresías</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/financiero" class="text-blue-600 hover:text-blue-800">
            ← Volver al Módulo Financiero
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Estado de Pago</label>
                <select id="status" name="status" onchange="this.form.submit()"
                        class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>Todos</option>
                    <option value="paid" <?php echo $status === 'paid' ? 'selected' : ''; ?>>Pagados</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pendientes</option>
                    <option value="partial" <?php echo $status === 'partial' ? 'selected' : ''; ?>>Parciales</option>
                </select>
            </div>
        </form>
    </div>
    
    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membresía</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Afiliación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($payments as $payment): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($payment['business_name']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($payment['rfc'] ?? ''); ?></p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($payment['membership_name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('d/m/Y', strtotime($payment['affiliation_date'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($payment['payment_method'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $payment['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 
                                          ($payment['payment_status'] === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                <?php echo ucfirst($payment['payment_status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                            $<?php echo number_format($payment['amount'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <?php if ($payment['payment_status'] !== 'paid'): ?>
                            <button onclick="openPaymentModal(<?php echo $payment['id']; ?>, '<?php echo htmlspecialchars($payment['business_name']); ?>', <?php echo $payment['amount']; ?>)"
                                    class="text-green-600 hover:text-green-900">Registrar Pago</button>
                            <?php else: ?>
                            <span class="text-gray-400">Pagado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Registrar Pago</h3>
            <p class="text-sm text-gray-500 mb-4">Empresa: <span id="modalBusinessName" class="font-medium"></span></p>
            <p class="text-sm text-gray-500 mb-4">Monto: <span id="modalAmount" class="font-medium"></span></p>
            
            <form method="POST" action="<?php echo BASE_URL; ?>/financiero/registrar-pago">
                <input type="hidden" name="csrf_token" value="<?php echo $this->csrfToken(); ?>">
                <input type="hidden" name="affiliation_id" id="modalAffiliationId">
                
                <div class="space-y-4">
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de Pago</label>
                        <select id="payment_method" name="payment_method" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                            <option value="">Seleccionar...</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Cheque">Cheque</option>
                            <option value="PayPal">PayPal</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700">Referencia (opcional)</label>
                        <input type="text" id="payment_reference" name="payment_reference"
                               placeholder="Número de transacción"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closePaymentModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPaymentModal(id, businessName, amount) {
    document.getElementById('modalAffiliationId').value = id;
    document.getElementById('modalBusinessName').textContent = businessName;
    document.getElementById('modalAmount').textContent = '$' + amount.toLocaleString('es-MX', {minimumFractionDigits: 2});
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}
</script>
