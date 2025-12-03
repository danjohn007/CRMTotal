<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago - <?php echo htmlspecialchars($event['title']); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- PayPal SDK -->
    <?php if (!empty($paypalClientId)): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($paypalClientId); ?>&currency=MXN"></script>
    <?php endif; ?>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-2xl mx-auto py-8 px-4">
        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <?php if (!empty($event['image'])): ?>
            <div class="w-full h-48 relative overflow-hidden">
                <img src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($event['image']); ?>" 
                     alt="<?php echo htmlspecialchars($event['title']); ?>"
                     class="w-full h-full object-cover">
            </div>
            <?php endif; ?>
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($event['title']); ?></h1>
                
                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mt-4">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?php echo date('d/m/Y', strtotime($event['start_date'])); ?>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <?php echo date('H:i', strtotime($event['start_date'])); ?> hrs
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <?php echo $event['is_online'] ? 'Evento en línea' : htmlspecialchars($event['location'] ?? ''); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Registration Info -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del Registro</h2>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Nombre:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($registration['guest_name'] ?? '-'); ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($registration['guest_email'] ?? '-'); ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Boletos:</span>
                    <span class="font-medium"><?php echo (int)($registration['tickets'] ?? 1); ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Código de Registro:</span>
                    <span class="font-medium font-mono"><?php echo htmlspecialchars($registration['registration_code']); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Payment Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Completar Pago</h2>
            
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-yellow-800 font-medium">Total a pagar:</span>
                    <span class="text-2xl font-bold text-yellow-800">
                        $<?php echo number_format((float)($registration['total_amount'] ?? 0), 2); ?> MXN
                    </span>
                </div>
            </div>
            
            <?php if (!empty($paypalClientId)): ?>
            <div id="paypal-button-container"></div>
            
            <script>
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                description: <?php echo json_encode($event['title'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                                amount: {
                                    value: <?php echo json_encode((string)($registration['total_amount'] ?? 0)); ?>,
                                    currency_code: 'MXN'
                                }
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            // Show loading message
                            var paypalContainer = document.getElementById('paypal-button-container');
                            paypalContainer.innerHTML = '<div style="text-align:center; padding:20px;"><p style="font-size:18px; color:#10b981;">✓ Pago completado</p><p>Generando tu boleto...</p><div style="margin-top:10px;"><div style="border:3px solid #f3f3f3; border-top:3px solid #3498db; border-radius:50%; width:40px; height:40px; animation:spin 1s linear infinite; margin:0 auto;"></div></div></div><style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>';
                            
                            // Send payment confirmation to server
                            fetch(<?php echo json_encode(BASE_URL . '/api/eventos/confirmar-pago'); ?>, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    registration_id: <?php echo (int)$registration['id']; ?>,
                                    order_id: data.orderID,
                                    payer_email: details.payer.email_address
                                })
                            }).then(function(response) {
                                if (!response.ok) {
                                    throw new Error('Error en la respuesta del servidor');
                                }
                                return response.json();
                            }).then(function(result) {
                                console.log('Payment confirmation result:', result);
                                if (result && result.success) {
                                    var ticketCode = result.registration_code || <?php echo json_encode($registration['registration_code']); ?>;
                                    console.log('Redirecting to ticket:', ticketCode);
                                    // Redirect to ticket page immediately
                                    setTimeout(function() {
                                        window.location.href = <?php echo json_encode(BASE_URL); ?> + '/evento/boleto/' + ticketCode;
                                    }, 500);
                                } else {
                                    throw new Error('Payment confirmation returned unsuccessful');
                                }
                            }).catch(function(error) {
                                console.error('Error confirmando pago:', error);
                                alert('Pago procesado correctamente. Serás redirigido a tu boleto.');
                                // Even if confirmation fails, redirect to ticket (payment was already captured)
                                setTimeout(function() {
                                    window.location.href = <?php echo json_encode(BASE_URL . '/evento/boleto/' . $registration['registration_code']); ?>;
                                }, 500);
                            });
                        });
                    },
                    onError: function(err) {
                        console.error(err);
                        alert('Hubo un error al procesar el pago. Por favor, intenta de nuevo.');
                    }
                }).render('#paypal-button-container');
            </script>
            <?php else: ?>
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                <p class="font-medium">Sistema de pagos no configurado</p>
                <p class="text-sm mt-1">Por favor contacta al organizador para completar el pago.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            <p>Organizado por Cámara de Comercio de Querétaro</p>
            <p class="mt-1">
                <a href="<?php echo BASE_URL; ?>" class="text-blue-600 hover:text-blue-800">Ir al sitio principal</a>
            </p>
        </div>
    </div>
</body>
</html>
