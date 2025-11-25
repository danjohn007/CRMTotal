<!-- Search Results -->
<?php $content = ob_start(); ?>

<div class="min-h-screen bg-gray-50">
    <!-- Search Header -->
    <div class="bg-blue-600 py-8">
        <div class="max-w-6xl mx-auto px-4">
            <form action="<?php echo BASE_URL; ?>/buscador/resultados" method="GET" class="flex shadow-lg rounded-lg overflow-hidden max-w-2xl mx-auto">
                <input type="text" name="q" value="<?php echo htmlspecialchars($term); ?>"
                       class="flex-1 px-6 py-4 text-lg border-0 focus:ring-0" required>
                <button type="submit" class="px-8 py-4 bg-blue-800 text-white font-medium hover:bg-blue-900 transition">
                    Buscar
                </button>
            </form>
        </div>
    </div>
    
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Results Count -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                <?php echo count($results); ?> resultados para "<?php echo htmlspecialchars($term); ?>"
            </h1>
        </div>
        
        <?php if (empty($results)): ?>
        <!-- No Results -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">No encontramos resultados</h2>
            <p class="text-gray-600 mb-6">
                No hay proveedores afiliados que ofrezcan "<?php echo htmlspecialchars($term); ?>" en este momento.
            </p>
            
            <?php if ($searcherType === 'publico'): ?>
            <div class="bg-white rounded-lg p-6 max-w-md mx-auto">
                <h3 class="font-semibold text-gray-900 mb-2">¿Ofreces este producto o servicio?</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Afíliate a la Cámara de Comercio y aparece en nuestro buscador
                </p>
                <a href="<?php echo BASE_URL; ?>/register" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Conocer Membresías
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php else: ?>
        <!-- Results Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($results as $result): 
                $products = json_decode($result['products_sells'] ?? '[]', true) ?: [];
            ?>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-lg">
                                <?php echo substr($result['business_name'] ?? 'E', 0, 1); ?>
                            </span>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            Afiliado
                        </span>
                    </div>
                    
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        <?php echo htmlspecialchars($result['commercial_name'] ?? $result['business_name']); ?>
                    </h3>
                    
                    <?php if ($result['industry']): ?>
                    <p class="text-sm text-gray-500 mt-1"><?php echo htmlspecialchars($result['industry']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($products)): ?>
                    <div class="mt-4">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-2">Productos/Servicios</p>
                        <div class="flex flex-wrap gap-1">
                            <?php foreach (array_slice($products, 0, 3) as $product): ?>
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                <?php echo htmlspecialchars($product); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($searcherType === 'afiliado'): ?>
                    <!-- Full info for affiliates -->
                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                        <?php if ($result['phone']): ?>
                        <a href="tel:<?php echo $result['phone']; ?>" class="flex items-center text-sm text-gray-600 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <?php echo htmlspecialchars($result['phone']); ?>
                        </a>
                        <?php endif; ?>
                        <?php if ($result['website']): ?>
                        <a href="<?php echo $result['website']; ?>" target="_blank" class="flex items-center text-sm text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            Visitar sitio web
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <!-- Limited info for public -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500">
                            <?php echo htmlspecialchars($result['city'] ?? 'Querétaro'); ?>
                        </p>
                        <a href="<?php echo BASE_URL; ?>/register" class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                            Ver información completa →
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Back to Search -->
        <div class="mt-8 text-center">
            <a href="<?php echo BASE_URL; ?>/buscador" class="text-blue-600 hover:text-blue-800">
                ← Volver al buscador
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); echo $content; ?>
