<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4 print-container">
        <!-- Print Button -->
        <div class="mb-4 text-center no-print">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Boleto
            </button>
        </div>
        
        <!-- Ticket Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border-4 border-green-700">
            <!-- Header -->
            <div class="p-6 border-b-2 border-green-700">
                <div class="flex justify-between items-start">
                    <div>
                        <?php if (!empty($siteLogo) && strpos($siteLogo, '/') === 0): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($siteLogo); ?>" alt="Logo" class="h-12 mb-2" onerror="this.style.display='none'">
                        <?php endif; ?>
                    </div>
                    <div class="text-right">
                        <h1 class="text-2xl font-bold text-green-800">BOLETO DE ACCESO</h1>
                        <p class="text-sm text-gray-600">Personal e Intransferible</p>
                    </div>
                </div>
            </div>
            
            <!-- Event Title -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 text-center">
                    <?php echo htmlspecialchars($event['title']); ?>
                </h2>
            </div>
            
            <!-- Event Details -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Date -->
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-700 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-gray-700"><?php echo date('d/m/Y', strtotime($event['start_date'])); ?></span>
                    </div>
                    
                    <!-- Time -->
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-700 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-gray-700">
                            <?php echo date('H:i', strtotime($event['start_date'])); ?> 
                            <?php if (!empty($event['end_date'])): ?>
                            - <?php echo date('H:i', strtotime($event['end_date'])); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                
                <!-- Location -->
                <div class="mt-4 flex items-start">
                    <svg class="w-6 h-6 text-green-700 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-gray-700">
                        <?php if ($event['is_online']): ?>
                            Evento en línea
                        <?php else: ?>
                            <?php echo htmlspecialchars($event['location'] ?? ''); ?>
                            <?php if (!empty($event['address'])): ?>
                                <br><span class="text-sm text-gray-500"><?php echo htmlspecialchars($event['address']); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <!-- Attendee Info and QR Code -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Attendee Details -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 uppercase">ASISTENTE</h3>
                        
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-500">Nombre:</span>
                                <p class="font-semibold text-gray-900">
                                    <?php echo htmlspecialchars(strtoupper($registration['nombre_asistente'] ?? $registration['guest_name'] ?? '')); ?>
                                </p>
                            </div>
                            
                            <div>
                                <span class="text-sm text-gray-500">Empresa:</span>
                                <p class="font-semibold text-gray-900">
                                    <?php echo htmlspecialchars(strtoupper($registration['razon_social'] ?? $registration['nombre_empresario'] ?? '')); ?>
                                </p>
                            </div>
                            
                            <div>
                                <span class="text-sm text-gray-500">Boletos:</span>
                                <p class="font-semibold text-red-600 text-xl">
                                    <?php echo (int)($registration['tickets'] ?? 1); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- QR Code -->
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 uppercase">CÓDIGO QR</h3>
                        
                        <div class="inline-block p-3 border-4 border-green-700 rounded-lg">
                            <?php if (!empty($registration['qr_code'])): ?>
                                <img src="<?php echo BASE_URL; ?>/uploads/qr/<?php echo htmlspecialchars($registration['qr_code']); ?>" 
                                     alt="QR Code" 
                                     class="w-48 h-48 mx-auto">
                            <?php else: ?>
                                <!-- Generate QR on the fly using QR Server API -->
                                <?php 
                                $qrData = BASE_URL . '/evento/boleto/' . htmlspecialchars($registration['registration_code']);
                                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);
                                ?>
                                <img src="<?php echo htmlspecialchars($qrUrl); ?>" 
                                     alt="QR Code" 
                                     class="w-48 h-48 mx-auto">
                            <?php endif; ?>
                        </div>
                        
                        <p class="mt-2 text-sm font-mono text-gray-600">
                            <?php echo htmlspecialchars($registration['registration_code']); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center text-sm text-gray-600">
                <div class="flex flex-wrap justify-center items-center gap-4">
                    <?php if (!empty($contactEmail)): ?>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <?php echo htmlspecialchars($contactEmail); ?>
                    </span>
                    <?php endif; ?>
                    
                    <span class="text-gray-300">|</span>
                    
                    <?php if (!empty($contactPhone)): ?>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <?php echo htmlspecialchars($contactPhone); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Back to home link -->
        <div class="text-center mt-6 no-print">
            <a href="<?php echo BASE_URL; ?>" class="text-blue-600 hover:text-blue-800">
                ← Volver al sitio principal
            </a>
        </div>
    </div>
</body>
</html>
