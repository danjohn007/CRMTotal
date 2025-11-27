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
    
    <!-- QR Validation Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Validar Código QR</h3>
        <p class="text-gray-500 text-center mb-6">Escanea o ingresa el código de registro del visitante</p>
        
        <div class="max-w-2xl mx-auto">
            <div class="flex space-x-4 mb-6">
                <button type="button" id="btn-scan-qr" onclick="startQRScanner()"
                        class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-lg font-medium">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Escanear QR
                </button>
                <button type="button" id="btn-manual-entry" onclick="toggleManualEntry()"
                        class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-lg font-medium">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Validar Código QR
                </button>
            </div>
            
            <!-- QR Scanner Container -->
            <div id="qr-scanner-container" class="hidden mb-6">
                <div id="qr-reader" class="w-full max-w-md mx-auto"></div>
                <button type="button" onclick="stopQRScanner()" class="mt-4 w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancelar Escaneo
                </button>
            </div>
            
            <!-- Manual Entry -->
            <div id="manual-entry-container" class="hidden">
                <div class="mb-4">
                    <label for="qr-code-input" class="block text-sm font-medium text-gray-700 mb-2">Código de Registro</label>
                    <input type="text" id="qr-code-input" 
                           placeholder="REG-20251127-ABCD12"
                           class="w-full rounded-md border-2 border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 text-lg uppercase">
                </div>
                <button type="button" onclick="validateQRCode()" 
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-lg font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Validar y Registrar Asistencia
                </button>
            </div>
            
            <!-- Validation Result -->
            <div id="validation-result" class="hidden mt-6"></div>
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
// QR Scanner variables
let qrScanner = null;

function toggleManualEntry() {
    const container = document.getElementById('manual-entry-container');
    const scannerContainer = document.getElementById('qr-scanner-container');
    
    scannerContainer.classList.add('hidden');
    container.classList.toggle('hidden');
    
    if (!container.classList.contains('hidden')) {
        document.getElementById('qr-code-input').focus();
    }
}

function startQRScanner() {
    const container = document.getElementById('qr-scanner-container');
    const manualContainer = document.getElementById('manual-entry-container');
    
    manualContainer.classList.add('hidden');
    container.classList.remove('hidden');
    
    // Check if html5-qrcode is available (would need to be loaded)
    // For now, show a message suggesting manual entry
    const resultDiv = document.getElementById('validation-result');
    resultDiv.innerHTML = '<div class="p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg">' +
        '<p class="font-medium">📷 Escaneo de Cámara</p>' +
        '<p class="mt-2">Para habilitar el escaneo de QR con cámara, es necesario:</p>' +
        '<ul class="mt-2 list-disc list-inside text-sm">' +
        '<li>Acceder desde un dispositivo con cámara (teléfono, tablet o laptop)</li>' +
        '<li>Permitir el acceso a la cámara cuando el navegador lo solicite</li>' +
        '<li>Usar HTTPS para conexión segura</li>' +
        '</ul>' +
        '<p class="mt-3 text-sm">Alternativamente, use la opción <strong>"Ingresar Manual"</strong> para escribir o pegar el código QR del asistente.</p>' +
        '</div>';
    resultDiv.classList.remove('hidden');
}

function stopQRScanner() {
    const container = document.getElementById('qr-scanner-container');
    container.classList.add('hidden');
    
    if (qrScanner) {
        qrScanner.stop();
        qrScanner = null;
    }
}

function validateQRCode() {
    const code = document.getElementById('qr-code-input').value.trim();
    const resultDiv = document.getElementById('validation-result');
    
    if (!code) {
        resultDiv.innerHTML = '<div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">Por favor, ingrese un código QR.</div>';
        resultDiv.classList.remove('hidden');
        return;
    }
    
    resultDiv.innerHTML = '<div class="p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">Validando código...</div>';
    resultDiv.classList.remove('hidden');
    
    fetch('<?php echo BASE_URL; ?>/api/eventos/validar-qr', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            code: code,
            event_id: <?php echo (int)$event['id']; ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">' +
                '<p class="font-bold text-lg">✓ Código Válido</p>' +
                '<p class="mt-2"><strong>Nombre:</strong> ' + (data.registration.guest_name || 'N/A') + '</p>' +
                '<p><strong>Email:</strong> ' + (data.registration.guest_email || 'N/A') + '</p>' +
                '<p><strong>Boletos:</strong> ' + (data.registration.tickets || 1) + '</p>' +
                (data.registration.already_attended ? '<p class="mt-2 text-yellow-600">⚠ Ya registró asistencia previamente</p>' : '<p class="mt-2 font-bold text-green-600">✓ Asistencia registrada</p>') +
                '</div>';
            document.getElementById('qr-code-input').value = '';
            
            // Reload page after 2 seconds to update list
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        } else {
            resultDiv.innerHTML = '<div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">' +
                '<p class="font-bold">✗ Código No Válido</p>' +
                '<p class="mt-2">' + (data.message || 'El código QR no corresponde a un registro de este evento.') + '</p>' +
                '</div>';
        }
    })
    .catch(err => {
        console.error(err);
        resultDiv.innerHTML = '<div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">Error al validar el código. Intente de nuevo.</div>';
    });
}

// Handle Enter key on QR input
document.getElementById('qr-code-input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        validateQRCode();
    }
});

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
