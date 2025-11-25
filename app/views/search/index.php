<!-- Search Index -->
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900">Buscador Inteligente de Proveedores</h2>
        <p class="mt-2 text-gray-500">Encuentra proveedores afiliados a la Cámara de Comercio de Querétaro</p>
    </div>
    
    <!-- Search Form -->
    <div class="max-w-2xl mx-auto">
        <form action="<?php echo BASE_URL; ?>/buscador/resultados" method="GET" class="flex shadow-lg rounded-lg overflow-hidden">
            <input type="text" name="q" placeholder="¿Qué producto o servicio buscas?"
                   class="flex-1 px-6 py-4 text-lg border-0 focus:ring-0"
                   required>
            <button type="submit" class="px-8 py-4 bg-blue-600 text-white font-medium hover:bg-blue-700 transition">
                Buscar
            </button>
        </form>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <p class="text-3xl font-bold text-blue-600"><?php echo $stats['total_searches'] ?? 0; ?></p>
            <p class="text-gray-500">Búsquedas este mes</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <p class="text-3xl font-bold text-green-600"><?php echo ($stats['total_searches'] ?? 0) - ($stats['no_match_count'] ?? 0); ?></p>
            <p class="text-gray-500">Con resultados</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <p class="text-3xl font-bold text-yellow-600"><?php echo $stats['no_match_count'] ?? 0; ?></p>
            <p class="text-gray-500">Sin resultados (Oportunidades)</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Popular Searches -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Búsquedas Populares</h3>
            <?php if (empty($popularSearches)): ?>
            <p class="text-gray-500 text-center py-4">No hay datos de búsquedas</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($popularSearches as $search): ?>
                <a href="<?php echo BASE_URL; ?>/buscador/resultados?q=<?php echo urlencode($search['search_term']); ?>" 
                   class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <span class="text-gray-900"><?php echo htmlspecialchars($search['search_term']); ?></span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full"><?php echo $search['count']; ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- No Match (Opportunities) -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">NO MATCH - Oportunidades</h3>
                <a href="<?php echo BASE_URL; ?>/buscador/no-match" class="text-sm text-blue-600 hover:text-blue-800">Ver todos →</a>
            </div>
            <p class="text-sm text-gray-500 mb-4">Búsquedas sin resultados = posibles nuevos afiliados</p>
            <?php if (empty($noMatchList)): ?>
            <p class="text-gray-500 text-center py-4">No hay búsquedas sin resultados</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach (array_slice($noMatchList, 0, 8) as $noMatch): ?>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div>
                        <span class="text-gray-900 font-medium"><?php echo htmlspecialchars($noMatch['search_term']); ?></span>
                        <span class="ml-2 text-xs text-gray-500"><?php echo $noMatch['count']; ?> búsquedas</span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/prospectos/nuevo?industry=<?php echo urlencode($noMatch['search_term']); ?>" 
                       class="text-xs text-blue-600 hover:text-blue-800">Crear prospecto →</a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
