<!-- Contabilidad Dashboard -->
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-600 to-green-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Dashboard Contabilidad</h2>
        <p class="mt-1 text-emerald-100">Control financiero y facturación</p>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-gray-500">Ingresos del Mes</p>
            <p class="text-3xl font-bold text-green-600">$<?php echo number_format($affiliationRevenueMonth + $servicesRevenueMonth, 0); ?></p>
            <div class="mt-2 flex text-xs text-gray-500">
                <span>Afiliaciones: $<?php echo number_format($affiliationRevenueMonth, 0); ?></span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-gray-500">Afiliaciones Activas</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo count($activeAffiliations); ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-gray-500">Facturas Pendientes</p>
            <p class="text-3xl font-bold text-yellow-600"><?php echo $pendingInvoicesCount; ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-gray-500">Servicios del Mes</p>
            <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($servicesRevenueMonth, 0); ?></p>
        </div>
    </div>
    
    <!-- Monthly Revenue Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ingresos Mensuales <?php echo date('Y'); ?></h3>
        <canvas id="revenueChart" height="100"></canvas>
    </div>
    
    <!-- Affiliations Table -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Afiliaciones Activas</h3>
            <a href="<?php echo BASE_URL; ?>/afiliados" class="text-sm text-blue-600 hover:text-blue-800">Ver todas →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membresía</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pago</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Factura</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach (array_slice($activeAffiliations, 0, 10) as $affiliation): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($affiliation['business_name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($affiliation['rfc']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($affiliation['membership_name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $<?php echo number_format($affiliation['amount'], 0); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $affiliation['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 
                                          ($affiliation['payment_status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                <?php echo $affiliation['payment_status'] === 'paid' ? 'Pagado' : 
                                          ($affiliation['payment_status'] === 'pending' ? 'Pendiente' : 'Parcial'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $affiliation['invoice_status'] === 'invoiced' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $affiliation['invoice_status'] === 'invoiced' ? 'Facturado' : 'Pendiente'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyData = <?php echo json_encode($monthlyStats ?? []); ?>;
    const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
    const revenueData = new Array(12).fill(0);
    monthlyData.forEach(item => {
        revenueData[parseInt(item.month) - 1] = parseFloat(item.total);
    });
    
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Ingresos',
                data: revenueData,
                backgroundColor: 'rgba(16, 185, 129, 0.5)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
