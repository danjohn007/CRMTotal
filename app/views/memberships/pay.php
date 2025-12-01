<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - CRM Total CCQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php if (!empty($paypalClientId)): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($paypalClientId); ?>&currency=MXN"></script>
    <?php endif; ?>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <img src="<?php echo BASE_URL; ?>/img/logo.png" alt="CCQ" class="h-16 mx-auto mb-4" onerror="this.style.display='none'">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Cámara de Comercio de Querétaro</h1>
                <p class="text-gray-600">Membresía Empresarial</p>
            </div>

            <!-- Membership Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header with gradient -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-8 py-6 text-white">
                    <h2 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($membership['name']); ?></h2>
                    <div class="flex items-baseline">
                        <span class="text-5xl font-bold">$<?php echo number_format($membership['price'], 2); ?></span>
                        <span class="ml-2 text-blue-100">MXN</span>
                    </div>
                    <p class="mt-2 text-blue-100">Vigencia: <?php echo $membership['duration_days']; ?> días</p>
                </div>

                <!-- Benefits -->
                <div class="px-8 py-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Beneficios Incluidos</h3>
                    <?php 
                    $benefits = json_decode($membership['benefits'] ?? '{}', true);
                    if (!empty($benefits)):
                    ?>
                    <ul class="space-y-3">
                        <?php foreach ($benefits as $key => $value): ?>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-700">
                                <?php 
                                $label = str_replace('_', ' ', ucfirst($key));
                                echo htmlspecialchars($label);
                                if ($value !== true && $value !== 1) {
                                    echo ': <strong>' . htmlspecialchars($value) . '</strong>';
                                }
                                ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p class="text-gray-500">Consulta beneficios con nuestro equipo comercial.</p>
                    <?php endif; ?>
                </div>

                <!-- Payment Section -->
                <div class="px-8 py-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Completa tu suscripción</h3>
                    
                    <?php if (!empty($paypalClientId) && !empty($membership['paypal_product_id'])): ?>
                    <!-- PayPal Button -->
                    <div id="paypal-button-container" class="max-w-md mx-auto"></div>
                    
                    <div class="mt-6 text-center text-sm text-gray-500">
                        <p>Pago seguro procesado por PayPal</p>
                        <p class="mt-2">Al completar el pago, tu membresía será activada inmediatamente.</p>
                    </div>
                    <?php else: ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                        <p class="text-yellow-800">Contacta a nuestro equipo para completar tu afiliación:</p>
                        <a href="tel:+524421234567" class="text-blue-600 font-semibold hover:underline">+52 (442) 123-4567</a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Share Link Section -->
                <div class="px-8 py-6 bg-gray-50 border-t">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Compartir este enlace</h4>
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               id="share-url" 
                               value="<?php echo htmlspecialchars($shareUrl); ?>" 
                               readonly
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                        <button onclick="copyShareUrl()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span>Copiar</span>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Comparte este enlace para que otros puedan suscribirse a esta membresía</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-sm text-gray-500">
                <p>¿Necesitas ayuda? Contáctanos</p>
                <div class="mt-2 space-x-4">
                    <a href="mailto:info@camaraqueretaro.com" class="text-blue-600 hover:underline">Email</a>
                    <a href="tel:+524421234567" class="text-blue-600 hover:underline">Teléfono</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    function copyShareUrl() {
        const input = document.getElementById('share-url');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>¡Copiado!</span>';
        
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    }

    <?php if (!empty($paypalClientId) && !empty($membership['paypal_product_id'])): ?>
    // Check if returning from PayPal
    const urlParams = new URLSearchParams(window.location.search);
    const subscriptionId = urlParams.get('subscription_id');
    const success = urlParams.get('success');
    const cancelled = urlParams.get('cancelled');
    
    if (cancelled === 'true') {
        document.body.innerHTML = `
            <div class="min-h-screen flex items-center justify-center bg-gray-50">
                <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Suscripción Cancelada</h2>
                    <p class="text-gray-600 mb-6">Has cancelado el proceso de suscripción.</p>
                    <a href="<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>/pagar" 
                       class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Intentar de nuevo
                    </a>
                </div>
            </div>
        `;
    } else if (subscriptionId || success === 'true') {
        // Success - show success message
        document.body.innerHTML = `
            <div class="min-h-screen flex items-center justify-center bg-gray-50">
                <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">¡Suscripción Exitosa!</h2>
                    <p class="text-gray-600 mb-6">Tu suscripción a la membresía ha sido activada correctamente.</p>
                    <p class="text-sm text-gray-500 mb-6">ID de suscripción: ${subscriptionId || 'Procesando...'}</p>
                    <p class="text-sm text-gray-600 mb-4">Recibirás un correo de confirmación con los detalles de tu membresía.</p>
                    <div class="bg-blue-50 rounded-lg p-4 mt-4">
                        <p class="text-sm text-blue-800">Tu suscripción se renovará automáticamente cada año.</p>
                    </div>
                </div>
            </div>
        `;
    } else {
        // Show PayPal subscription button
        paypal.Buttons({
            createSubscription: function(data, actions) {
                return fetch('<?php echo BASE_URL; ?>/membresias/crear-suscripcion', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'membership_id': '<?php echo $membership['id']; ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    return data.subscriptionId;
                })
                .catch(err => {
                    alert('Error al crear la suscripción: ' + err.message);
                });
            },
            onApprove: function(data, actions) {
                // Redirect to success page with subscription ID
                window.location.href = '<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>/pagar?success=true&subscription_id=' + data.subscriptionID;
            },
            onError: function(err) {
                console.error('PayPal error:', err);
                alert('Ocurrió un error con PayPal. Por favor intenta nuevamente.');
            },
            onCancel: function(data) {
                window.location.href = '<?php echo BASE_URL; ?>/membresias/<?php echo $membership['id']; ?>/pagar?cancelled=true';
            },
            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'rect',
                label: 'subscribe',
                height: 45
            }
        }).render('#paypal-button-container');
    }
    <?php endif; ?>
    </script>
</body>
</html>
