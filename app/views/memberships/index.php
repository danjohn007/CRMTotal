<!-- Memberships Index View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Tipos de Membresía</h2>
            <p class="mt-1 text-sm text-gray-500">Administra los tipos de membresía disponibles</p>
        </div>
        <?php if (($_SESSION['user_role'] ?? '') === 'superadmin'): ?>
        <a href="<?php echo BASE_URL; ?>/membresias/nuevo" 
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nueva Membresía
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Membresías</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo count($memberships); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Ingresos Totales</p>
                    <p class="text-2xl font-bold text-gray-900">$<?php echo number_format($totalRevenue, 2); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Afiliaciones Activas</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php echo array_sum(array_column($memberships, 'active_affiliations')); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Memberships Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($memberships as $membership): ?>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 <?php echo $membership['is_active'] ? '' : 'opacity-50'; ?>">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        <?php echo htmlspecialchars($membership['code']); ?>
                    </span>
                    <span class="px-2 py-1 text-xs rounded-full <?php echo $membership['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                        <?php echo $membership['is_active'] ? 'Activo' : 'Inactivo'; ?>
                    </span>
                </div>
                
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    <?php echo htmlspecialchars($membership['name']); ?>
                </h3>
                
                <p class="text-3xl font-bold text-blue-600 mb-4">
                    $<?php echo number_format($membership['price'], 2); ?>
                    <span class="text-sm font-normal text-gray-500">/ <?php echo $membership['duration_days']; ?> días</span>
                </p>
                
                <?php 
                $benefits = json_decode($membership['benefits'] ?? '{}', true);
                if (!empty($benefits)):
                ?>
                <ul class="space-y-2 mb-4">
                    <?php foreach ($benefits as $key => $value): ?>
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <?php endif; ?>
                
                <div class="flex justify-between items-center pt-4 border-t">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold"><?php echo $membership['active_affiliations'] ?? 0; ?></span> afiliados
                    </div>
                    <div class="space-x-2">
                        <a href="<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm">Ver</a>
                        <?php if (($_SESSION['user_role'] ?? '') === 'superadmin'): ?>
                        <a href="<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>/editar" 
                           class="text-indigo-600 hover:text-indigo-800 text-sm">Editar</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Payment Link -->
                <?php if (!empty($membership['paypal_product_id'])): ?>
                <div class="mt-4 pt-4 border-t bg-blue-50 -mx-6 px-6 py-3">
                    <p class="text-xs font-medium text-gray-700 mb-2">Enlace de Suscripción</p>
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               id="link-<?php echo $membership['id']; ?>" 
                               value="<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>/pagar" 
                               readonly
                               class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded bg-white truncate">
                        <button onclick="copyLink(<?php echo $membership['id']; ?>)" 
                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copiar
                        </button>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>/pagar" 
                       target="_blank"
                       class="block mt-2 text-xs text-blue-600 hover:text-blue-800">
                        Ver página de suscripción →
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function copyLink(membershipId) {
    const input = document.getElementById('link-' + membershipId);
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>¡Copiado!';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-blue-600');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-blue-600');
    }, 2000);
}
</script>
