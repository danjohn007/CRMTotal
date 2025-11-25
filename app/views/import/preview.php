<!-- Import Preview View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Vista Previa de Importación</h2>
            <p class="mt-1 text-sm text-gray-500">Revisa los datos antes de confirmar la importación</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/importar" class="text-blue-600 hover:text-blue-800">
            ← Volver
        </a>
    </div>
    
    <!-- Summary -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-800">
                    Se importarán <strong><?php echo $total; ?></strong> registros como 
                    <strong><?php echo $contactType === 'prospecto' ? 'Prospectos' : 'Afiliados'; ?></strong>
                </p>
                <p class="text-sm text-blue-600 mt-1">Mostrando los primeros 10 registros</p>
            </div>
        </div>
    </div>
    
    <!-- Preview Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <?php 
                        $columns = array_keys($data[0] ?? []);
                        foreach ($columns as $col): 
                        ?>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <?php echo htmlspecialchars($col); ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($data as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <?php foreach ($row as $value): ?>
                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                            <?php echo htmlspecialchars(mb_substr($value, 0, 50)); ?>
                            <?php echo strlen($value) > 50 ? '...' : ''; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        <a href="<?php echo BASE_URL; ?>/importar" 
           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
            Cancelar
        </a>
        <form method="POST" action="<?php echo BASE_URL; ?>/importar/procesar" class="inline">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="contact_type" value="<?php echo htmlspecialchars($contactType); ?>">
            <!-- Note: In production, you'd store the file temporarily and reference it here -->
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Confirmar Importación
            </button>
        </form>
    </div>
</div>
