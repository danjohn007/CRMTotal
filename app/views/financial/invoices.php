<!-- Invoices View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Facturación</h2>
            <p class="mt-1 text-sm text-gray-500">Control de facturas emitidas y pendientes</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/financiero" class="text-blue-600 hover:text-blue-800">
            ← Volver al Módulo Financiero
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Estado de Factura</label>
                <select id="status" name="status" onchange="this.form.submit()"
                        class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>Todos</option>
                    <option value="invoiced" <?php echo $status === 'invoiced' ? 'selected' : ''; ?>>Facturados</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pendientes</option>
                    <option value="not_required" <?php echo $status === 'not_required' ? 'selected' : ''; ?>>No requiere</option>
                </select>
            </div>
        </form>
    </div>
    
    <!-- Invoices Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membresía</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Factura</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($invoices as $invoice): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($invoice['business_name']); ?></p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($invoice['rfc'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($invoice['membership_name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($invoice['invoice_number'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $invoice['invoice_status'] === 'invoiced' ? 'bg-green-100 text-green-800' : 
                                          ($invoice['invoice_status'] === 'not_required' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                <?php 
                                $statusLabels = ['invoiced' => 'Facturado', 'pending' => 'Pendiente', 'not_required' => 'No requiere'];
                                echo $statusLabels[$invoice['invoice_status']] ?? ucfirst($invoice['invoice_status']);
                                ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                            $<?php echo number_format($invoice['amount'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <?php if ($invoice['invoice_status'] === 'pending'): ?>
                            <button onclick="openInvoiceModal(<?php echo $invoice['id']; ?>, '<?php echo htmlspecialchars($invoice['business_name']); ?>', '<?php echo htmlspecialchars($invoice['rfc'] ?? ''); ?>')"
                                    class="text-blue-600 hover:text-blue-900">Generar Factura</button>
                            <?php else: ?>
                            <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div id="invoiceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Registrar Factura</h3>
            <p class="text-sm text-gray-500 mb-2">Empresa: <span id="invoiceBusinessName" class="font-medium"></span></p>
            <p class="text-sm text-gray-500 mb-4">RFC: <span id="invoiceRfc" class="font-medium"></span></p>
            
            <form method="POST" action="<?php echo BASE_URL; ?>/financiero/generar-factura">
                <input type="hidden" name="csrf_token" value="<?php echo $this->csrfToken(); ?>">
                <input type="hidden" name="affiliation_id" id="invoiceAffiliationId">
                
                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700">Número de Factura</label>
                    <input type="text" id="invoice_number" name="invoice_number" required
                           placeholder="FAC-2025-0001"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeInvoiceModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Registrar Factura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openInvoiceModal(id, businessName, rfc) {
    document.getElementById('invoiceAffiliationId').value = id;
    document.getElementById('invoiceBusinessName').textContent = businessName;
    document.getElementById('invoiceRfc').textContent = rfc || 'Sin RFC';
    document.getElementById('invoiceModal').classList.remove('hidden');
}

function closeInvoiceModal() {
    document.getElementById('invoiceModal').classList.add('hidden');
}
</script>
