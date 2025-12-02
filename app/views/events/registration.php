<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - Registro</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- PayPal SDK (loaded only for paid events) -->
    <?php if ($event['is_paid'] && !empty($paypalClientId)): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($paypalClientId); ?>&currency=MXN"></script>
    <?php endif; ?>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <?php if (!empty($event['image'])): ?>
            <div class="w-full aspect-square relative overflow-hidden">
                <img src="<?php echo BASE_URL; ?>/uploads/events/<?php echo htmlspecialchars($event['image']); ?>" 
                     alt="<?php echo htmlspecialchars($event['title']); ?>"
                     class="absolute inset-0 w-full h-full object-cover">
            </div>
            <?php endif; ?>
            <div class="p-6">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                        <?php echo $event['category'] ?? 'Evento'; ?>
                    </span>
                    <?php if ($event['is_paid']): ?>
                    <?php
                        // Calculate current price based on presale period
                        $isPresalePeriod = false;
                        if (!empty($event['promo_end_date'])) {
                            $promoEndDate = strtotime($event['promo_end_date']);
                            $isPresalePeriod = (time() <= $promoEndDate);
                        }
                        $displayPrice = ($isPresalePeriod && (float)($event['promo_price'] ?? 0) > 0) 
                            ? (float)$event['promo_price'] 
                            : (float)$event['price'];
                    ?>
                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                        <?php if ($isPresalePeriod && (float)($event['promo_price'] ?? 0) > 0): ?>
                        <span class="line-through text-gray-500">$<?php echo number_format($event['price'], 0); ?></span>
                        $<?php echo number_format($displayPrice, 0); ?> MXN
                        <span class="text-green-700">(Preventa)</span>
                        <?php else: ?>
                        $<?php echo number_format($event['price'], 0); ?> MXN
                        <?php endif; ?>
                    </span>
                    <?php else: ?>
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                        Gratuito
                    </span>
                    <?php endif; ?>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($event['title']); ?></h1>
                
                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
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
                
                <?php if (!empty($event['description'])): ?>
                <p class="mt-4 text-gray-600"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Messages -->
        <?php if (isset($error)): ?>
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
        <?php 
            // Determine the type of message based on payment status
            $isPendingPayment = ($event['is_paid'] && isset($registrationId) && isset($totalAmount) && $totalAmount > 0);
            $messageClass = $isPendingPayment ? 'bg-yellow-100 border-yellow-400 text-yellow-800' : 'bg-green-100 border-green-400 text-green-700';
        ?>
        <div class="mb-6 p-4 <?php echo $messageClass; ?> border rounded-lg">
            <?php if ($isPendingPayment): ?>
            <div class="flex items-center">
                <span class="text-2xl mr-2">⚠️</span>
                <div>
                    <p class="font-bold text-lg">Registro Pendiente</p>
                    <p>Tu registro ha sido recibido. Por favor, completa el pago para confirmar tu asistencia.</p>
                    <?php if (!empty($registrationEmail)): ?>
                    <p class="mt-2 text-sm">📧 Notificación enviada a: <strong><?php echo htmlspecialchars($registrationEmail); ?></strong></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <?php echo htmlspecialchars($success); ?>
            <?php endif; ?>
        </div>
        
        <!-- QR Code Display (for free registrations) -->
        <?php if (isset($registrationId) && isset($qrCode) && (!$event['is_paid'] || (isset($totalAmount) && $totalAmount == 0))): ?>
        <div id="printable-ticket" class="bg-white rounded-lg shadow-sm p-6 mb-6 text-center">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🎉 ¡Registro Exitoso!</h2>
            <p class="text-gray-600 mb-4">Tu código QR de acceso al evento:</p>
            <div class="flex justify-center mb-4">
                <img src="<?php echo BASE_URL; ?>/uploads/qr/<?php echo htmlspecialchars($qrCode); ?>" 
                     alt="Código QR de Acceso" class="w-64 h-64 border-4 border-gray-200 rounded-lg" id="qr-image">
            </div>
            <p class="text-sm text-gray-500 mb-2">Código de registro: <strong><?php echo htmlspecialchars($registrationCode ?? ''); ?></strong></p>
            <p class="text-sm text-gray-500">Presenta este código en la entrada del evento.</p>
            <div class="mt-4 flex justify-center gap-3 flex-wrap">
                <a href="<?php echo BASE_URL; ?>/uploads/qr/<?php echo htmlspecialchars($qrCode); ?>" 
                   download="qr-evento.png"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar QR
                </a>
                <button type="button" onclick="printTicket()" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimir Boleto
                </button>
            </div>
        </div>
        
        <!-- Print Styles (hidden on screen) -->
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }
                #printable-ticket, #printable-ticket * {
                    visibility: visible;
                }
                #printable-ticket {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    padding: 20px;
                }
                #printable-ticket button, 
                #printable-ticket a[download] {
                    display: none !important;
                }
                #qr-image {
                    width: 200px !important;
                    height: 200px !important;
                }
            }
        </style>
        
        <script>
        function printTicket() {
            window.print();
        }
        </script>
        <?php endif; ?>
        
        <!-- PayPal Payment Section (only shown after successful registration for paid events) -->
        <?php if ($event['is_paid'] && isset($registrationId) && !empty($paypalClientId) && isset($totalAmount) && $totalAmount > 0): ?>
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Completar Pago</h2>
            <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                <p class="text-blue-800">
                    <strong>Total a pagar:</strong> $<?php echo number_format($totalAmount, 2); ?> MXN
                </p>
            </div>
            <div id="paypal-button-container"></div>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        description: <?php echo json_encode($event['title'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                        amount: {
                            value: <?php echo json_encode((string)$totalAmount); ?>,
                            currency_code: 'MXN'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Send payment confirmation to server
                    fetch(<?php echo json_encode(BASE_URL . '/api/eventos/confirmar-pago'); ?>, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            registration_id: <?php echo (int)$registrationId; ?>,
                            order_id: data.orderID,
                            payer_email: details.payer.email_address
                        })
                    }).then(function(response) {
                        alert('¡Pago completado exitosamente! Recibirás un correo de confirmación.');
                        window.location.reload();
                    });
                });
            },
            onError: function(err) {
                console.error(err);
                alert('Hubo un error al procesar el pago. Por favor, intenta de nuevo.');
            }
        }).render('#paypal-button-container');
    </script>
        </div>
        <?php endif; ?>
        <?php else: ?>
        
        <!-- Registration Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Formulario de Registro</h2>
            
            <form method="POST" id="registration-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="contact_id" id="contact_id" value="">
                <input type="hidden" name="is_active_affiliate" id="is_active_affiliate" value="0">
                
                <!-- Company Lookup (hidden in guest mode) -->
                <div id="company-lookup-section" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-3">
                        ¿Ya eres empresa afiliada o registrada? Ingresa tu RFC, WhatsApp, Email o Razón Social para autocompletar tus datos.
                    </p>
                    <div class="flex space-x-2">
                        <input type="text" id="lookup-identifier" 
                               placeholder="RFC, WhatsApp, Email o Razón Social"
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <button type="button" onclick="lookupCompany()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Buscar
                        </button>
                    </div>
                    <p id="lookup-result" class="text-sm mt-2 hidden"></p>
                </div>
                
                <!-- Main Registration Fields - Orden: RFC, Razón Social, Nombre Empresario/Representante, WhatsApp, Email -->
                <div id="main-fields-section" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- RFC field (FIRST - hidden in guest mode, required when not guest) -->
                    <div id="rfc-field" class="md:col-span-2">
                        <label for="rfc" class="block text-sm font-medium text-gray-700">
                            RFC <span id="rfc-required-indicator">*</span>
                            <span id="rfc-type-indicator" class="text-sm text-gray-500 ml-2"></span>
                        </label>
                        <input type="text" id="rfc" name="rfc" required
                               maxlength="13" 
                               pattern="^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$"
                               placeholder="12 caracteres (Moral) o 13 caracteres (Física)"
                               oninput="validateRFC()"
                               class="mt-1 block w-full uppercase rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <p class="text-xs text-gray-500 mt-1">Persona Moral: 12 caracteres | Persona Física: 13 caracteres</p>
                    </div>
                    
                    <!-- Business Name / Razón Social (SECOND) -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            <span id="name-label">Razón Social</span> *
                        </label>
                        <input type="text" id="name" name="name" required
                               placeholder="Nombre de la empresa o razón social"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <p class="text-xs text-gray-500 mt-1" id="name-help">
                            Para Persona Física, este campo se repetirá automáticamente
                        </p>
                    </div>
                    
                    <!-- Owner/Representative Name field (THIRD - preloaded from search or manual, hidden in guest mode) -->
                    <div id="owner-name-field" class="md:col-span-2">
                        <label for="owner_name" class="block text-sm font-medium text-gray-700">
                            Nombre del Empresario / Representante Legal *
                        </label>
                        <input type="text" id="owner_name" name="owner_name" required
                               placeholder="Nombre completo del dueño o representante legal"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    
                    <!-- WhatsApp (FOURTH) -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">WhatsApp *</label>
                        <input type="tel" id="phone" name="phone" required
                               maxlength="10" pattern="[0-9]{10}"
                               placeholder="10 dígitos"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    
                    <!-- Corporate Email (FIFTH) -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Corporativo *</label>
                        <input type="email" id="email" name="email" required
                               placeholder="correo@empresa.com"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    
                    <!-- Tickets field (hidden in guest mode - guests can only register 1 ticket) -->
                    <div id="tickets-field" class="md:col-span-2">
                        <label for="tickets" class="block text-sm font-medium text-gray-700">Número de Boletos</label>
                        <select id="tickets" name="tickets" onchange="updateTicketFields()"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
                
                <!-- Guest Mode Selection (below tickets field) -->
                <div id="guest-mode-section" class="mt-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="is_guest" name="is_guest" value="1"
                               class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                               onchange="toggleGuestMode()">
                        <span class="ml-3 text-sm font-medium text-gray-900">Asisto como Invitado</span>
                    </label>
                    <p class="text-xs text-gray-600 mt-2 ml-6">
                        Selecciona esta opción si no eres empresa afiliada y deseas asistir como invitado al evento.
                    </p>
                    
                    <!-- Guest Type Dropdown (shown only when guest mode is active) -->
                    <div id="guest-type-section" class="hidden mt-4">
                        <label for="guest_type" class="block text-sm font-medium text-gray-700">Tipo de Invitado *</label>
                        <select id="guest_type" name="guest_type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 p-2 border">
                            <option value="">-- Selecciona una opción --</option>
                            <option value="INVITADO">INVITADO</option>
                            <option value="FUNCIONARIO PÚBLICO">FUNCIONARIO PÚBLICO</option>
                            <option value="OTRO">OTRO</option>
                        </select>
                    </div>
                </div>
                
                <!-- Attendee Information (for affiliate registrations) -->
                <div id="attendee-section" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="text-md font-semibold text-gray-900 mb-3">Información del Asistente al Evento</h3>
                    
                    <div class="mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="is_owner_representative" name="is_owner_representative" value="1" checked
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   onchange="toggleOwnerRepresentative()">
                            <span class="ml-3 text-sm font-medium text-gray-900">¿Dueño, Socio o Representante Legal?</span>
                        </label>
                        <p class="text-xs text-gray-600 mt-1 ml-6">
                            Las empresas afiliadas activas tienen 1 boleto de cortesía. Desmarca esta casilla si el asistente es colaborador o invitado de la empresa.
                        </p>
                    </div>
                    
                    <!-- Attendee details (shown when NOT owner/representative) -->
                    <div id="attendee-details" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 p-4 bg-white rounded-lg border border-blue-100">
                        <div class="md:col-span-2">
                            <label for="attendee_name" class="block text-sm font-medium text-gray-700">Nombre del Asistente *</label>
                            <input type="text" id="attendee_name" name="attendee_name"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        </div>
                        <div>
                            <label for="attendee_position" class="block text-sm font-medium text-gray-700">Cargo</label>
                            <input type="text" id="attendee_position" name="attendee_position"
                                   placeholder="Ej: Gerente, Director, Asistente"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        </div>
                        <div>
                            <label for="attendee_phone" class="block text-sm font-medium text-gray-700">WhatsApp del Asistente *</label>
                            <input type="tel" id="attendee_phone" name="attendee_phone"
                                   maxlength="10" pattern="[0-9]{10}"
                                   placeholder="10 dígitos"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        </div>
                        <div class="md:col-span-2">
                            <label for="attendee_email" class="block text-sm font-medium text-gray-700">Correo del Asistente *</label>
                            <input type="email" id="attendee_email" name="attendee_email"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        </div>
                    </div>
                </div>
                
                <!-- Additional Tickets Information -->
                <div id="additional-tickets-section" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h3 class="text-md font-semibold text-gray-900 mb-3">Información de Boletos Adicionales</h3>
                    <p class="text-xs text-gray-600 mb-4">Por favor, proporciona la información de cada asistente adicional.</p>
                    <div id="additional-attendees-container"></div>
                </div>
                
                <!-- Cost Summary -->
                <div id="cost-summary" class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h3 class="text-md font-semibold text-gray-900 mb-2">Resumen de Costos</h3>
                    <div id="cost-details" class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Boletos:</span>
                            <span id="summary-tickets">1</span>
                        </div>
                        <div class="flex justify-between" id="courtesy-row">
                            <span>Boleto de cortesía (afiliado activo):</span>
                            <span class="text-green-600">-$0.00</span>
                        </div>
                        <div class="flex justify-between font-bold border-t border-yellow-300 pt-2">
                            <span>Total a Pagar:</span>
                            <span id="total-amount" class="text-lg">$0.00 MXN</span>
                        </div>
                    </div>
                    <p id="free-event-note" class="text-sm text-green-600 mt-2 hidden">
                        ✓ Este es un evento gratuito
                    </p>
                </div>
                
                <!-- Anti-Spam Verification -->
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Verificación Anti-Spam *
                    </label>
                    <?php 
                    $num1 = rand(1, 9);
                    $num2 = rand(1, 9);
                    $expectedSum = $num1 + $num2;
                    ?>
                    <input type="hidden" name="expected_sum" value="<?php echo base64_encode($expectedSum); ?>">
                    <div class="flex items-center space-x-3">
                        <span class="text-lg font-medium text-gray-700">
                            ¿Cuánto es <?php echo $num1; ?> + <?php echo $num2; ?>?
                        </span>
                        <input type="number" id="spam_check" name="spam_check" required
                               min="0" max="18"
                               class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Por favor, responde esta simple operación matemática.</p>
                </div>
                
                <!-- Submit -->
                <div class="mt-6">
                    <button type="submit" id="submit-button" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-lg font-medium">
                        Registrarme y Proceder al Pago
                    </button>
                </div>
                
                <p id="payment-note" class="text-center text-sm text-gray-500 mt-3">
                    Al registrarte serás redirigido a PayPal para completar el pago
                </p>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-500">
            <p>Organizado por Cámara de Comercio de Querétaro</p>
            <p class="mt-1">
                <a href="https://www.camaradecomercioqro.mx/" class="text-blue-600 hover:text-blue-800" target="_blank" rel="noopener noreferrer">Ir al sitio principal</a>
            </p>
        </div>
    </div>
    
    <script>
    // Ticket limit constants (must match server-side)
    const GUEST_TICKET_LIMIT = 1;
    const MAX_TICKETS_PER_REGISTRATION = 5;
    
    // Event configuration with presale pricing
    <?php
        // Calculate if we're in presale period for frontend
        $jsPresalePeriod = false;
        if (!empty($event['promo_end_date'])) {
            $promoEndDate = strtotime($event['promo_end_date']);
            $jsPresalePeriod = (time() <= $promoEndDate);
        }
    ?>
    const eventConfig = {
        isPaid: <?php echo $event['is_paid'] ? 'true' : 'false'; ?>,
        price: <?php echo (float)($event['price'] ?? 0); ?>,
        promoPrice: <?php echo (float)($event['promo_price'] ?? 0); ?>,
        memberPrice: <?php echo (float)($event['member_price'] ?? 0); ?>,
        promoMemberPrice: <?php echo (float)($event['promo_member_price'] ?? 0); ?>,
        isPresalePeriod: <?php echo $jsPresalePeriod ? 'true' : 'false'; ?>,
        freeForAffiliates: <?php echo ($event['free_for_affiliates'] ?? 1) ? 'true' : 'false'; ?>
    };
    
    let isActiveAffiliate = false;
    
    function toggleGuestMode() {
        const isGuest = document.getElementById('is_guest').checked;
        const companyLookup = document.getElementById('company-lookup-section');
        const rfcField = document.getElementById('rfc-field');
        const ownerNameField = document.getElementById('owner-name-field');
        const attendeeSection = document.getElementById('attendee-section');
        const ticketsField = document.getElementById('tickets-field');
        const nameLabel = document.getElementById('name-label');
        const guestTypeSection = document.getElementById('guest-type-section');
        const guestTypeSelect = document.getElementById('guest_type');
        const rfcInput = document.getElementById('rfc');
        const rfcRequiredIndicator = document.getElementById('rfc-required-indicator');
        
        if (isGuest) {
            companyLookup.classList.add('hidden');
            rfcField.classList.add('hidden');
            ownerNameField.classList.add('hidden');
            attendeeSection.classList.add('hidden');
            ticketsField.classList.add('hidden'); // Guests cannot request additional tickets
            guestTypeSection.classList.remove('hidden'); // Show guest type dropdown
            nameLabel.textContent = 'Nombre Completo';
            document.getElementById('contact_id').value = '';
            document.getElementById('owner_name').value = '';
            document.getElementById('tickets').value = String(GUEST_TICKET_LIMIT); // Reset to max tickets for guests
            isActiveAffiliate = false;
            document.getElementById('is_active_affiliate').value = '0';
            // RFC is not required for guests
            rfcInput.removeAttribute('required');
            rfcInput.value = '';
            // Owner name not required for guests
            document.getElementById('owner_name').removeAttribute('required');
            // Guest type is required
            guestTypeSelect.setAttribute('required', 'required');
        } else {
            companyLookup.classList.remove('hidden');
            rfcField.classList.remove('hidden');
            ownerNameField.classList.remove('hidden');
            attendeeSection.classList.remove('hidden');
            ticketsField.classList.remove('hidden');
            guestTypeSection.classList.add('hidden'); // Hide guest type dropdown
            nameLabel.textContent = 'Nombre Completo / Empresa';
            // RFC is required for non-guests
            rfcInput.setAttribute('required', 'required');
            rfcRequiredIndicator.classList.remove('hidden');
            // Owner name required for non-guests
            document.getElementById('owner_name').setAttribute('required', 'required');
            // Guest type is not required
            guestTypeSelect.removeAttribute('required');
            guestTypeSelect.value = '';
        }
        
        updateTicketFields();
        updateCostSummary();
    }
    
    /**
     * Validate RFC format and detect person type
     * RFC format:
     * - Persona Moral: 12 characters (3-4 letters + 6 digits + 3 alphanumeric)
     * - Persona Física: 13 characters (4 letters + 6 digits + 3 alphanumeric)
     */
    function validateRFC() {
        const rfcInput = document.getElementById('rfc');
        const nameInput = document.getElementById('name');
        const ownerNameInput = document.getElementById('owner_name');
        const rfcTypeIndicator = document.getElementById('rfc-type-indicator');
        const nameHelp = document.getElementById('name-help');
        
        if (!rfcInput || !rfcInput.value) {
            rfcTypeIndicator.textContent = '';
            return;
        }
        
        const rfc = rfcInput.value.toUpperCase().trim();
        rfcInput.value = rfc; // Auto-uppercase
        
        // RFC format validation regex (corrected patterns)
        // Persona Moral: 3-4 letters + 6 digits + 3 alphanumeric
        // Persona Física: 4 letters + 6 digits + 3 alphanumeric
        const rfcMoralPattern = /^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/;
        const rfcFisicaPattern = /^[A-ZÑ&]{4}[0-9]{6}[A-Z0-9]{3}$/;
        
        if (rfc.length === 12 && rfcMoralPattern.test(rfc)) {
            // Persona Moral (12 characters)
            rfcTypeIndicator.textContent = '(Persona Moral)';
            rfcTypeIndicator.className = 'text-sm text-blue-600 ml-2 font-medium';
            rfcInput.classList.remove('border-red-500');
            rfcInput.classList.add('border-green-500');
            nameHelp.textContent = 'Razón social de la empresa';
            
            // For moral persons, owner name is separate and editable
            if (ownerNameInput) {
                ownerNameInput.removeAttribute('readonly');
                ownerNameInput.classList.remove('bg-gray-100');
                ownerNameInput.placeholder = 'Ingrese nombre del representante legal';
            }
            
        } else if (rfc.length === 13 && rfcFisicaPattern.test(rfc)) {
            // Persona Física (13 characters)
            rfcTypeIndicator.textContent = '(Persona Física)';
            rfcTypeIndicator.className = 'text-sm text-green-600 ml-2 font-medium';
            rfcInput.classList.remove('border-red-500');
            rfcInput.classList.add('border-green-500');
            nameHelp.textContent = 'Para Persona Física, nombre y razón social son el mismo';
            
            // For física, sync name automatically but allow manual override if needed
            if (ownerNameInput) {
                ownerNameInput.classList.add('bg-gray-100');
                // Copy value if name field has content
                if (nameInput && nameInput.value) {
                    ownerNameInput.value = nameInput.value;
                }
            }
            
        } else {
            // Invalid or incomplete RFC
            rfcTypeIndicator.textContent = '(Formato inválido)';
            rfcTypeIndicator.className = 'text-sm text-red-600 ml-2 font-medium';
            rfcInput.classList.remove('border-green-500');
            if (rfc.length >= 12) {
                rfcInput.classList.add('border-red-500');
            }
            nameHelp.textContent = 'Ingrese un RFC válido de 12 o 13 caracteres';
            // Remove readonly to allow editing
            if (ownerNameInput) {
                ownerNameInput.removeAttribute('readonly');
                ownerNameInput.classList.remove('bg-gray-100');
            }
        }
        
        // Auto-lookup if RFC is complete and valid
        if ((rfc.length === 12 && rfcMoralPattern.test(rfc)) || 
            (rfc.length === 13 && rfcFisicaPattern.test(rfc))) {
            // Try to lookup company by RFC
            const lookupInput = document.getElementById('lookup-identifier');
            if (lookupInput) {
                lookupInput.value = rfc;
                lookupCompany();
            }
        }
    }
    
    // Sync name with owner_name for Persona Física
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const ownerNameInput = document.getElementById('owner_name');
        const rfcInput = document.getElementById('rfc');
        
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                const rfc = rfcInput ? rfcInput.value : '';
                // Only sync if Persona Física (13 chars) and field has bg-gray-100 class (auto-sync indicator)
                if (rfc.length === 13 && ownerNameInput && ownerNameInput.classList.contains('bg-gray-100')) {
                    const rfcFisicaPattern = /^[A-ZÑ&]{4}[0-9]{6}[A-Z0-9]{3}$/;
                    if (rfcFisicaPattern.test(rfc.toUpperCase())) {
                        ownerNameInput.value = nameInput.value;
                    }
                }
            });
        }
    });
    
    function toggleOwnerRepresentative() {
        const isOwner = document.getElementById('is_owner_representative').checked;
        const attendeeDetails = document.getElementById('attendee-details');
        
        if (isOwner) {
            attendeeDetails.classList.add('hidden');
            // Clear required attributes and values
            document.querySelectorAll('#attendee-details input').forEach(input => {
                input.removeAttribute('required');
                input.value = '';
            });
        } else {
            attendeeDetails.classList.remove('hidden');
            // Add required attributes
            document.getElementById('attendee_name').setAttribute('required', 'required');
            document.getElementById('attendee_phone').setAttribute('required', 'required');
            document.getElementById('attendee_email').setAttribute('required', 'required');
        }
        
        updateCostSummary();
    }
    
    function updateTicketFields() {
        const tickets = parseInt(document.getElementById('tickets').value) || 1;
        const isGuest = document.getElementById('is_guest').checked;
        const additionalSection = document.getElementById('additional-tickets-section');
        const container = document.getElementById('additional-attendees-container');
        
        // Calculate how many additional attendees we need forms for
        let additionalCount = tickets - 1;
        
        if (additionalCount > 0) {
            additionalSection.classList.remove('hidden');
            container.innerHTML = '';
            
            for (let i = 1; i <= additionalCount; i++) {
                // Create attendee form using DOM methods for safety
                const attendeeDiv = document.createElement('div');
                attendeeDiv.className = 'p-4 bg-white rounded-lg border border-green-100 mb-4';
                
                const title = document.createElement('h4');
                title.className = 'text-sm font-medium text-gray-900 mb-3';
                title.textContent = 'Asistente ' + (i + 1);
                attendeeDiv.appendChild(title);
                
                const gridDiv = document.createElement('div');
                gridDiv.className = 'grid grid-cols-1 md:grid-cols-2 gap-4';
                
                // Name field
                const nameDiv = document.createElement('div');
                nameDiv.className = 'md:col-span-2';
                const nameLabel = document.createElement('label');
                nameLabel.className = 'block text-sm font-medium text-gray-700';
                nameLabel.textContent = 'Nombre *';
                const nameInput = document.createElement('input');
                nameInput.type = 'text';
                nameInput.name = 'additional_attendees[' + i + '][name]';
                nameInput.required = true;
                nameInput.className = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border';
                nameDiv.appendChild(nameLabel);
                nameDiv.appendChild(nameInput);
                gridDiv.appendChild(nameDiv);
                
                // Phone field
                const phoneDiv = document.createElement('div');
                const phoneLabel = document.createElement('label');
                phoneLabel.className = 'block text-sm font-medium text-gray-700';
                phoneLabel.textContent = 'WhatsApp *';
                const phoneInput = document.createElement('input');
                phoneInput.type = 'tel';
                phoneInput.name = 'additional_attendees[' + i + '][phone]';
                phoneInput.required = true;
                phoneInput.maxLength = 10;
                phoneInput.pattern = '[0-9]{10}';
                phoneInput.placeholder = '10 dígitos';
                phoneInput.className = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border';
                phoneDiv.appendChild(phoneLabel);
                phoneDiv.appendChild(phoneInput);
                gridDiv.appendChild(phoneDiv);
                
                // Email field
                const emailDiv = document.createElement('div');
                const emailLabel = document.createElement('label');
                emailLabel.className = 'block text-sm font-medium text-gray-700';
                emailLabel.textContent = 'Correo *';
                const emailInput = document.createElement('input');
                emailInput.type = 'email';
                emailInput.name = 'additional_attendees[' + i + '][email]';
                emailInput.required = true;
                emailInput.className = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border';
                emailDiv.appendChild(emailLabel);
                emailDiv.appendChild(emailInput);
                gridDiv.appendChild(emailDiv);
                
                attendeeDiv.appendChild(gridDiv);
                container.appendChild(attendeeDiv);
            }
        } else {
            additionalSection.classList.add('hidden');
            container.innerHTML = '';
        }
        
        updateCostSummary();
    }
    
    function updateCostSummary() {
        const tickets = parseInt(document.getElementById('tickets').value) || 1;
        const isGuest = document.getElementById('is_guest').checked;
        const isOwner = document.getElementById('is_owner_representative')?.checked ?? true;
        const courtesyRow = document.getElementById('courtesy-row');
        const freeEventNote = document.getElementById('free-event-note');
        const submitButton = document.getElementById('submit-button');
        const paymentNote = document.getElementById('payment-note');
        
        document.getElementById('summary-tickets').textContent = tickets;
        
        let total = 0;
        let freeTickets = 0;
        
        // Determine price per ticket with presale pricing logic
        // Priority: 1. Promo Member Price, 2. Member Price, 3. Promo Price, 4. Regular Price
        let pricePerTicket = eventConfig.price;
        
        if (isActiveAffiliate && !isGuest) {
            // Affiliate pricing
            if (eventConfig.isPresalePeriod && eventConfig.promoMemberPrice > 0) {
                pricePerTicket = eventConfig.promoMemberPrice;
            } else if (eventConfig.memberPrice > 0) {
                pricePerTicket = eventConfig.memberPrice;
            }
        } else {
            // Non-affiliate pricing
            if (eventConfig.isPresalePeriod && eventConfig.promoPrice > 0) {
                pricePerTicket = eventConfig.promoPrice;
            }
        }
        
        if (!eventConfig.isPaid) {
            // Free event
            total = 0;
            courtesyRow.classList.add('hidden');
            freeEventNote.classList.remove('hidden');
        } else {
            freeEventNote.classList.add('hidden');
            
            // Check for courtesy ticket eligibility
            if (!isGuest && isActiveAffiliate && eventConfig.freeForAffiliates && isOwner) {
                freeTickets = 1;
                courtesyRow.classList.remove('hidden');
                courtesyRow.querySelector('span:last-child').textContent = `-$${pricePerTicket.toFixed(2)}`;
            } else {
                courtesyRow.classList.add('hidden');
            }
            
            total = (tickets - freeTickets) * pricePerTicket;
            if (total < 0) total = 0;
        }
        
        document.getElementById('total-amount').textContent = `$${total.toFixed(2)} MXN`;
        
        // Update submit button text
        if (total > 0) {
            submitButton.textContent = 'Registrarme y Proceder al Pago';
            paymentNote.classList.remove('hidden');
            paymentNote.textContent = `Al registrarte serás redirigido a PayPal para completar el pago de $${total.toFixed(2)} MXN`;
        } else {
            submitButton.textContent = 'Completar Registro';
            paymentNote.classList.add('hidden');
        }
    }
    
    function lookupCompany() {
        const identifier = document.getElementById('lookup-identifier').value.trim();
        const resultEl = document.getElementById('lookup-result');
        
        if (!identifier) {
            resultEl.textContent = 'Por favor, ingresa un Email, WhatsApp, RFC o Teléfono.';
            resultEl.className = 'text-sm mt-2 text-red-600';
            resultEl.classList.remove('hidden');
            return;
        }
        
        fetch('<?php echo BASE_URL; ?>/api/buscar-empresa?q=' + encodeURIComponent(identifier))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.company) {
                    // Autocomplete form fields
                    document.getElementById('name').value = data.company.business_name || data.company.owner_name || '';
                    document.getElementById('email').value = data.company.corporate_email || '';
                    document.getElementById('phone').value = data.company.phone || data.company.whatsapp || '';
                    document.getElementById('rfc').value = data.company.rfc || '';
                    document.getElementById('contact_id').value = data.company.id || '';
                    
                    // Preload owner_name field if available
                    document.getElementById('owner_name').value = data.company.owner_name || '';
                    
                    // Check if active affiliate
                    isActiveAffiliate = data.is_active_affiliate || false;
                    document.getElementById('is_active_affiliate').value = isActiveAffiliate ? '1' : '0';
                    
                    let message = '✓ Empresa encontrada: ' + (data.company.business_name || data.company.owner_name);
                    if (isActiveAffiliate) {
                        message += ' (Afiliado Activo)';
                    }
                    
                    resultEl.textContent = message;
                    resultEl.className = 'text-sm mt-2 text-green-600';
                    
                    updateCostSummary();
                } else {
                    resultEl.textContent = 'No se encontró ninguna empresa. Puedes registrarte como invitado.';
                    resultEl.className = 'text-sm mt-2 text-yellow-600';
                    isActiveAffiliate = false;
                    document.getElementById('is_active_affiliate').value = '0';
                    updateCostSummary();
                }
                resultEl.classList.remove('hidden');
            })
            .catch(err => {
                resultEl.textContent = 'Error al buscar. Por favor, intenta de nuevo.';
                resultEl.className = 'text-sm mt-2 text-red-600';
                resultEl.classList.remove('hidden');
            });
    }
    
    // Form validation
    document.getElementById('registration-form')?.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        
        const isGuest = document.getElementById('is_guest').checked;
        
        // Validate guest type if guest mode is active
        if (isGuest) {
            const guestType = document.getElementById('guest_type').value;
            if (!guestType) {
                e.preventDefault();
                alert('Por favor, selecciona un tipo de invitado.');
                document.getElementById('guest_type').focus();
                return false;
            }
        }
        
        // Validate phone
        const phone = document.getElementById('phone').value;
        if (phone && !/^\d{10}$/.test(phone)) {
            e.preventDefault();
            document.getElementById('phone').focus();
            alert('El teléfono debe tener exactamente 10 dígitos.');
            return false;
        }
        
        // Validate attendee details if not owner/representative and not guest
        if (!isGuest) {
            const isOwner = document.getElementById('is_owner_representative').checked;
            if (!isOwner) {
                const attendeeName = document.getElementById('attendee_name').value.trim();
                const attendeePhone = document.getElementById('attendee_phone').value.trim();
                const attendeeEmail = document.getElementById('attendee_email').value.trim();
                
                if (!attendeeName || !attendeePhone || !attendeeEmail) {
                    e.preventDefault();
                    alert('Por favor, completa la información del asistente (nombre, teléfono y correo).');
                    return false;
                }
                
                if (attendeePhone && !/^\d{10}$/.test(attendeePhone)) {
                    e.preventDefault();
                    alert('El teléfono del asistente debe tener exactamente 10 dígitos.');
                    document.getElementById('attendee_phone').focus();
                    return false;
                }
            }
        }
        
        // Validate additional attendees
        const additionalInputs = document.querySelectorAll('[name^="additional_attendees"]');
        for (let input of additionalInputs) {
            if (input.type === 'tel' && input.value && !/^\d{10}$/.test(input.value)) {
                e.preventDefault();
                input.focus();
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                alert('Todos los teléfonos deben tener exactamente 10 dígitos.');
                return false;
            }
        }
        
        // Validate spam check
        const spamCheck = document.getElementById('spam_check').value;
        if (!spamCheck || spamCheck.trim() === '') {
            e.preventDefault();
            alert('Por favor, responde la verificación anti-spam.');
            document.getElementById('spam_check').focus();
            return false;
        }
        
        console.log('Form validation passed, submitting...');
        
        // Show loading state
        const submitButton = document.getElementById('submit-button');
        submitButton.disabled = true;
        submitButton.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Procesando...';
        
        // Form will submit normally if we reach here
    });
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCostSummary();
    });
    </script>
</body>
</html>
