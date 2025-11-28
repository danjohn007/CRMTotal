<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleto de Acceso - <?php echo htmlspecialchars($event['title']); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
        }
        .print-only {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Print Button Bar (non-printable) -->
    <div class="no-print bg-green-700 text-white p-4 flex justify-between items-center">
        <span class="text-lg font-semibold">Boleto de Acceso - <?php echo htmlspecialchars($event['title']); ?></span>
        <button onclick="window.print()" 
                class="inline-flex items-center px-6 py-2 bg-white text-green-700 rounded-lg hover:bg-gray-100 transition font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Imprimir Boleto
        </button>
    </div>
    
    <!-- Ticket Content -->
    <div class="max-w-2xl mx-auto my-8 bg-white shadow-lg rounded-lg overflow-hidden" id="ticket">
        <!-- Header -->
        <div class="bg-green-700 p-6 flex justify-between items-center">
            <div class="bg-white rounded-lg p-3 text-center">
                <span class="text-green-700 font-bold text-xs block">CÁMARA</span>
                <span class="text-green-700 font-bold text-xs block">DE COMERCIO</span>
                <span class="text-green-700 font-bold text-xs block">DE QUERÉTARO</span>
            </div>
            <div class="text-right text-white">
                <h1 class="text-2xl font-bold">BOLETO DE ACCESO</h1>
                <p class="text-green-200 text-sm">Personal e Intransferible</p>
            </div>
        </div>
        
        <!-- Event Title Banner -->
        <div class="bg-gray-100 border-t-4 border-b-4 border-green-700 py-4 px-6 text-center">
            <h2 class="text-2xl font-bold text-green-700"><?php echo htmlspecialchars($event['title']); ?></h2>
        </div>
        
        <!-- Event Details -->
        <div class="p-6">
            <div class="flex flex-wrap gap-6 mb-6 text-gray-700">
                <div class="flex items-center">
                    <span class="text-green-700 mr-2">📅</span>
                    <strong><?php echo date('d/m/Y', strtotime($event['start_date'])); ?></strong>
                </div>
                <div class="flex items-center">
                    <span class="text-green-700 mr-2">🕐</span>
                    <?php echo date('H:i', strtotime($event['start_date'])); ?> - <?php echo date('H:i', strtotime($event['end_date'])); ?>
                </div>
            </div>
            
            <div class="mb-6 text-gray-600">
                <span class="text-green-700 mr-2">📍</span>
                <?php echo $event['is_online'] ? 'Evento en línea' : htmlspecialchars($event['address'] ?? $event['location'] ?? ''); ?>
            </div>
            
            <!-- Attendee Info and QR Code -->
            <div class="flex flex-col md:flex-row gap-6 border-t pt-6">
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-gray-800 uppercase border-b pb-2 mb-4">ASISTENTE</h3>
                    
                    <div class="space-y-2 text-sm">
                        <p><strong>Nombre:</strong><br><?php echo htmlspecialchars($registration['guest_name'] ?? '-'); ?></p>
                        
                        <?php if (!empty($contact['owner_name'])): ?>
                        <p><strong>Dueño/Representante:</strong><br><?php echo htmlspecialchars($contact['owner_name']); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($contact['legal_representative'])): ?>
                        <p><strong>Representante Legal:</strong><br><?php echo htmlspecialchars($contact['legal_representative']); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($contact['business_name']) || !empty($contact['commercial_name'])): ?>
                        <p><strong>Empresa:</strong><br><?php echo htmlspecialchars($contact['business_name'] ?? $contact['commercial_name'] ?? '-'); ?></p>
                        <?php endif; ?>
                        
                        <p><strong>Boletos:</strong> <?php echo (int)($registration['tickets'] ?? 1); ?></p>
                    </div>
                </div>
                
                <div class="text-center">
                    <h3 class="text-sm font-bold text-gray-800 uppercase mb-4">CÓDIGO QR</h3>
                    
                    <?php if (!empty($registration['qr_code'])): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/qr/<?php echo htmlspecialchars($registration['qr_code']); ?>" 
                         alt="Código QR" 
                         class="w-48 h-48 border border-gray-300 mx-auto">
                    <?php else: ?>
                    <div class="w-48 h-48 border border-gray-300 flex items-center justify-center text-gray-400 mx-auto">
                        QR no disponible
                    </div>
                    <?php endif; ?>
                    
                    <p class="text-green-700 text-xs font-mono mt-2"><?php echo htmlspecialchars($registration['registration_code']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Contact Info Footer -->
        <div class="bg-gray-50 border-t p-4 text-center text-gray-500 text-sm">
            <p>✉️ <?php echo htmlspecialchars($configModel->get('contact_email', 'contacto@camaradecomercioqro.mx')); ?> | 📞 <?php echo htmlspecialchars($configModel->get('contact_phone', '4425375301')); ?></p>
            <p class="mt-1">🔒 Política de Privacidad</p>
        </div>
        
        <!-- Instructions -->
        <div class="bg-blue-50 border-t border-blue-100 p-6">
            <h3 class="text-blue-800 font-semibold mb-3">ℹ️ Instrucciones</h3>
            <ul class="text-blue-700 text-sm space-y-1 list-disc list-inside">
                <li>Imprime este boleto o guárdalo en tu dispositivo móvil</li>
                <li>Llega con 15 minutos de anticipación</li>
                <li>Presenta tu código QR en la entrada del evento</li>
                <li>Si tienes problemas, contacta al organizador</li>
            </ul>
        </div>
    </div>
    
    <!-- Footer (non-printable) -->
    <div class="no-print text-center py-6 text-gray-500 text-sm">
        <p>Organizado por Cámara de Comercio de Querétaro</p>
        <p class="mt-2">
            <a href="<?php echo BASE_URL; ?>" class="text-blue-600 hover:text-blue-800">Ir al sitio principal</a>
        </p>
    </div>
    
    <!-- Print-only Footer -->
    <div class="print-only text-center py-4 text-gray-500 text-sm mt-4">
        <p>Estrategia Digital desarrollada por ID - www.impactosdigitales.com</p>
    </div>
</body>
</html>
