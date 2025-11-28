<!-- Event Attendance Control View -->
<!-- html5-qrcode library for camera scanning - v2.3.8 from cdnjs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
        <p class="text-gray-500 text-center mb-6">Escanea o ingresa el código QR del visitante</p>
        
        <div class="max-w-2xl mx-auto">
            <div class="flex space-x-4 mb-6">
                <button type="button" id="btn-scan-qr" onclick="startQRScanner()"
                        class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-lg font-medium">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Escanear QR
                </button>
                <button type="button" id="btn-manual-entry" onclick="toggleManualEntry()"
                        class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-lg font-medium">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Ingresar Manual
                </button>
            </div>
            
            <!-- Info Alert -->
            <div id="qr-info-alert" class="hidden mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-blue-700">Apunta la cámara hacia el código QR del visitante</p>
                </div>
            </div>
            
            <!-- QR Scanner Container -->
            <div id="qr-scanner-container" class="hidden mb-6">
                <div id="qr-reader" class="w-full" style="min-height: 300px;"></div>
                <button type="button" onclick="stopQRScanner()" 
                        class="mt-4 w-full inline-flex items-center justify-center px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                    </svg>
                    Detener Cámara
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa / Representante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Boletos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asistentes Adicionales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($registrations as $reg): ?>
                    <?php 
                        // Parse additional attendees JSON
                        $additionalAttendees = [];
                        if (!empty($reg['additional_attendees'])) {
                            $additionalAttendees = json_decode($reg['additional_attendees'], true) ?: [];
                        }
                    ?>
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
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <div class="font-semibold">
                                <?php echo htmlspecialchars($reg['guest_name'] ?? $reg['business_name'] ?? '-'); ?>
                            </div>
                            <?php if (!empty($reg['owner_name'])): ?>
                            <div class="text-xs text-gray-600 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-green-800">
                                    👤 Dueño: <?php echo htmlspecialchars($reg['owner_name']); ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($reg['legal_representative'])): ?>
                            <div class="text-xs text-gray-600 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-purple-800">
                                    📋 Rep. Legal: <?php echo htmlspecialchars($reg['legal_representative']); ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($reg['attendee_name']) && empty($reg['is_owner_representative'])): ?>
                            <div class="text-xs text-gray-500 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                                    👤 Asiste: <?php echo htmlspecialchars($reg['attendee_name']); ?>
                                    <?php if (!empty($reg['attendee_position'])): ?>
                                    <span class="ml-1 text-gray-600">(<?php echo htmlspecialchars($reg['attendee_position']); ?>)</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php elseif (!empty($reg['is_owner_representative'])): ?>
                            <div class="text-xs text-green-600 mt-1">
                                ✓ Dueño/Representante
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($reg['guest_rfc'])): ?>
                            <div class="text-xs text-gray-400 mt-1">RFC: <?php echo htmlspecialchars($reg['guest_rfc']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($reg['guest_email'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($reg['guest_phone'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <?php echo (int)($reg['tickets'] ?? 1); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php if (!empty($additionalAttendees)): ?>
                            <div class="space-y-1">
                                <?php foreach ($additionalAttendees as $index => $attendee): ?>
                                <div class="text-xs bg-gray-50 rounded p-1">
                                    <span class="font-medium"><?php echo htmlspecialchars($attendee['name'] ?? 'Sin nombre'); ?></span>
                                    <?php if (!empty($attendee['email'])): ?>
                                    <br><span class="text-gray-400"><?php echo htmlspecialchars($attendee['email']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($attendee['phone'])): ?>
                                    <br><span class="text-gray-400">📞 <?php echo htmlspecialchars($attendee['phone']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <span class="text-gray-400">-</span>
                            <?php endif; ?>
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
let html5QrCode = null;

// Html5QrcodeScannerState enum values (from library)
const Html5QrcodeScannerState = {
    UNKNOWN: 0,
    NOT_STARTED: 1,
    SCANNING: 2,
    PAUSED: 3
};

function toggleManualEntry() {
    const container = document.getElementById('manual-entry-container');
    const scannerContainer = document.getElementById('qr-scanner-container');
    const infoAlert = document.getElementById('qr-info-alert');
    
    // Stop scanner if running
    stopQRScanner();
    
    scannerContainer.classList.add('hidden');
    infoAlert.classList.add('hidden');
    container.classList.toggle('hidden');
    
    if (!container.classList.contains('hidden')) {
        document.getElementById('qr-code-input').focus();
    }
}

// Initialize and start the QR scanner camera
function initializeScanner() {
    const container = document.getElementById('qr-scanner-container');
    const infoAlert = document.getElementById('qr-info-alert');
    const resultDiv = document.getElementById('validation-result');
    
    // Create new scanner instance
    html5QrCode = new Html5Qrcode("qr-reader");
    
    // Configuration for the scanner
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };
    
    // Start the camera
    html5QrCode.start(
        { facingMode: "environment" }, // Use back camera on mobile
        config,
        (decodedText, decodedResult) => {
            // QR code successfully scanned
            onQRCodeScanned(decodedText);
        },
        (errorMessage) => {
            // QR code scanning error (this is called frequently, ignore)
        }
    ).catch((err) => {
        // Camera access error
        console.error("Camera error:", err);
        infoAlert.classList.add('hidden');
        resultDiv.innerHTML = '<div class="p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg">' +
            '<p class="font-medium">📷 No se pudo acceder a la cámara</p>' +
            '<p class="mt-2">Posibles causas:</p>' +
            '<ul class="mt-2 list-disc list-inside text-sm">' +
            '<li>El navegador no tiene permisos para acceder a la cámara</li>' +
            '<li>El dispositivo no tiene cámara disponible</li>' +
            '<li>La conexión no es segura (se requiere HTTPS)</li>' +
            '<li>Otra aplicación está usando la cámara</li>' +
            '</ul>' +
            '<p class="mt-3 text-sm">Usa la opción <strong>"Ingresar Manual"</strong> para escribir el código QR.</p>' +
            '</div>';
        resultDiv.classList.remove('hidden');
        container.classList.add('hidden');
    });
}

function startQRScanner() {
    const container = document.getElementById('qr-scanner-container');
    const manualContainer = document.getElementById('manual-entry-container');
    const infoAlert = document.getElementById('qr-info-alert');
    const resultDiv = document.getElementById('validation-result');
    
    manualContainer.classList.add('hidden');
    container.classList.remove('hidden');
    infoAlert.classList.remove('hidden');
    resultDiv.classList.add('hidden');
    
    // Check if html5QrCode library is available
    if (typeof Html5Qrcode === 'undefined') {
        resultDiv.innerHTML = '<div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">' +
            '<p class="font-medium">❌ Error al cargar el escáner</p>' +
            '<p class="mt-2">No se pudo cargar la librería de escaneo. Por favor, recarga la página e intenta de nuevo.</p>' +
            '</div>';
        resultDiv.classList.remove('hidden');
        container.classList.add('hidden');
        return;
    }
    
    // Always create a new scanner instance to avoid state issues
    try {
        if (html5QrCode) {
            // Try to stop and clear existing scanner
            const state = html5QrCode.getState();
            if (state === Html5QrcodeScannerState.SCANNING || state === Html5QrcodeScannerState.PAUSED) {
                html5QrCode.stop().then(() => {
                    html5QrCode = null;
                    initializeScanner();
                }).catch(() => {
                    html5QrCode = null;
                    initializeScanner();
                });
                return;
            }
        }
    } catch (e) {
        html5QrCode = null;
    }
    
    initializeScanner();
}

function stopQRScanner() {
    const container = document.getElementById('qr-scanner-container');
    const infoAlert = document.getElementById('qr-info-alert');
    
    container.classList.add('hidden');
    infoAlert.classList.add('hidden');
    
    if (html5QrCode) {
        try {
            // Try to get the state - if scanner is running, stop it
            const state = html5QrCode.getState();
            if (state === Html5QrcodeScannerState.SCANNING || state === Html5QrcodeScannerState.PAUSED) {
                html5QrCode.stop().then(() => {
                    console.log("QR Scanner stopped");
                }).catch((err) => {
                    console.error("Error stopping scanner:", err);
                });
            }
        } catch (e) {
            // State check failed, try to stop anyway
            html5QrCode.stop().catch(() => {});
        }
    }
}

function onQRCodeScanned(code) {
    // Stop the scanner after successful scan
    stopQRScanner();
    
    // Extract the registration code from URL if it's a URL
    let registrationCode = code;
    if (code.includes('/evento/verificar/')) {
        const parts = code.split('/evento/verificar/');
        if (parts.length > 1) {
            registrationCode = parts[1].split(/[?#]/)[0]; // Remove query params if any
        }
    }
    
    // Validate the QR code
    validateQRCodeValue(registrationCode);
}

function validateQRCode() {
    const code = document.getElementById('qr-code-input').value.trim();
    validateQRCodeValue(code);
}

function validateQRCodeValue(code) {
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
