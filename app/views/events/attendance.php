<!-- Event Attendance Control View -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>" class="text-blue-600 hover:text-blue-800">
                ← Volver al Evento
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Control de Asistencia</h2>
            <p class="mt-1 text-sm text-gray-500"><?php echo htmlspecialchars($event['title']); ?></p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600"><?php echo count($registrations); ?></p>
                <p class="text-xs text-gray-500">Registrados</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600"><?php echo count(array_filter($registrations, fn($r) => $r['attended'])); ?></p>
                <p class="text-xs text-gray-500">Asistieron</p>
            </div>
        </div>
    </div>
    
    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <input type="text" id="search-input" placeholder="Buscar por nombre, email o RFC..."
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
    </div>
    
    <!-- Registrations Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($registrations)): ?>
        <p class="text-gray-500 text-center py-12">No hay registros para este evento</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="attendance-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asistencia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($registrations as $reg): ?>
                    <tr class="hover:bg-gray-50 attendance-row" 
                        data-name="<?php echo strtolower($reg['guest_name'] ?? ''); ?>"
                        data-email="<?php echo strtolower($reg['guest_email'] ?? ''); ?>"
                        data-rfc="<?php echo strtolower($reg['guest_rfc'] ?? ''); ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer attendance-toggle" 
                                       data-id="<?php echo $reg['id']; ?>"
                                       <?php echo $reg['attended'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($reg['guest_name'] ?? $reg['business_name'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($reg['guest_email'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($reg['guest_phone'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($reg['guest_rfc'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 attendance-time">
                            <?php echo $reg['attendance_time'] ? date('H:i', strtotime($reg['attendance_time'])) : '-'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Search functionality
document.getElementById('search-input').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('.attendance-row').forEach(row => {
        const name = row.dataset.name || '';
        const email = row.dataset.email || '';
        const rfc = row.dataset.rfc || '';
        const matches = name.includes(searchTerm) || email.includes(searchTerm) || rfc.includes(searchTerm);
        row.style.display = matches ? '' : 'none';
    });
});

// Attendance toggle
document.querySelectorAll('.attendance-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const registrationId = this.dataset.id;
        const attended = this.checked;
        const row = this.closest('tr');
        const timeCell = row.querySelector('.attendance-time');
        
        fetch('<?php echo BASE_URL; ?>/eventos/<?php echo $event['id']; ?>/asistencia', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `csrf_token=<?php echo $csrf_token; ?>&registration_id=${registrationId}&attended=${attended ? 1 : 0}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                timeCell.textContent = attended ? new Date().toLocaleTimeString('es-MX', {hour: '2-digit', minute:'2-digit'}) : '-';
            }
        })
        .catch(err => {
            console.error(err);
            this.checked = !attended; // Revert
            alert('Error al guardar la asistencia');
        });
    });
});
</script>
