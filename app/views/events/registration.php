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
                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                        $<?php echo number_format($event['price'], 0); ?> MXN
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
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <?php echo htmlspecialchars($success); ?>
            <?php if ($event['is_paid'] && isset($registrationId)): ?>
            <p class="mt-2 font-medium">Por favor, completa el pago para confirmar tu registro.</p>
            <?php endif; ?>
        </div>
        
        <!-- PayPal Payment Section (only shown after successful registration for paid events) -->
        <?php if ($event['is_paid'] && isset($registrationId) && !empty($paypalClientId)): ?>
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Completar Pago</h2>
            <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                <p class="text-blue-800">
                    <strong>Total a pagar:</strong> $<?php echo number_format($event['price'], 2); ?> MXN
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
                            value: <?php echo json_encode((string)$event['price']); ?>,
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
            
            <!-- Company Lookup -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-3">
                    ¿Ya eres empresa afiliada o registrada? Ingresa tu WhatsApp o RFC para autocompletar tus datos.
                </p>
                <div class="flex space-x-2">
                    <input type="text" id="lookup-identifier" 
                           placeholder="WhatsApp o RFC"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <button type="button" onclick="lookupCompany()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Buscar
                    </button>
                </div>
                <p id="lookup-result" class="text-sm mt-2 hidden"></p>
            </div>
            
            <form method="POST" id="registration-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="contact_id" id="contact_id" value="">
                
                <div class="space-y-6">
                    <!-- Company/Business Information Section -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de la Empresa</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="rfc" class="block text-sm font-medium text-gray-700">
                                    RFC *
                                    <span class="text-xs text-gray-500">(12 caracteres para Persona Moral, 13 para Persona Física)</span>
                                </label>
                                <input type="text" id="rfc" name="rfc" required
                                       maxlength="13" 
                                       placeholder="Ejemplo: SMM040902AD3 o FOBL910724G35"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border uppercase"
                                       oninput="this.value = this.value.toUpperCase(); validateRFC();">
                                <p id="rfc-feedback" class="text-xs mt-1 hidden"></p>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="razon_social" class="block text-sm font-medium text-gray-700">Razón Social</label>
                                <input type="text" id="razon_social" name="razon_social"
                                       placeholder="Nombre oficial de la empresa"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="nombre_empresario_representante" class="block text-sm font-medium text-gray-700">
                                    Nombre del Empresario / Representante Legal
                                </label>
                                <input type="text" id="nombre_empresario_representante" name="nombre_empresario_representante"
                                       placeholder="Nombre completo del propietario o representante"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                                       onblur="checkAttendeeMatch();">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico de la Empresa *</label>
                                <input type="email" id="email" name="email" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono de la Empresa *</label>
                                <input type="tel" id="phone" name="phone" required
                                       maxlength="10" pattern="[0-9]{10}"
                                       placeholder="10 dígitos"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Attendee Information Section -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Asistente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="nombre_asistente" class="block text-sm font-medium text-gray-700">
                                    Nombre del Asistente *
                                    <span class="text-xs text-gray-500">(Requerido para emisión del boleto)</span>
                                </label>
                                <input type="text" id="nombre_asistente" name="nombre_asistente" required
                                       placeholder="Nombre completo de quien asistirá al evento"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                                       onblur="checkAttendeeMatch();">
                                <p id="payment-notice" class="text-xs mt-1 hidden"></p>
                            </div>
                            
                            <!-- Additional fields shown only if attendee is different from owner -->
                            <div id="guest-fields" class="md:col-span-2 hidden">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                    <p class="text-sm text-yellow-800">
                                        <strong>⚠️ Importante:</strong> El asistente es diferente del propietario/representante. 
                                        Se requieren datos adicionales del asistente.
                                    </p>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label for="categoria_asistente" class="block text-sm font-medium text-gray-700">
                                            Categoría del Asistente *
                                        </label>
                                        <select id="categoria_asistente" name="categoria_asistente"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                                            <option value="">Seleccione una categoría</option>
                                            <option value="socio">Socio</option>
                                            <option value="empleado">Empleado</option>
                                            <option value="publico_general">Público General</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="email_asistente" class="block text-sm font-medium text-gray-700">
                                            Email del Asistente
                                        </label>
                                        <input type="email" id="email_asistente" name="email_asistente"
                                               placeholder="Email personal del asistente"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                                    </div>
                                    
                                    <div>
                                        <label for="whatsapp_asistente" class="block text-sm font-medium text-gray-700">
                                            WhatsApp del Asistente
                                        </label>
                                        <input type="tel" id="whatsapp_asistente" name="whatsapp_asistente"
                                               maxlength="10" pattern="[0-9]{10}"
                                               placeholder="10 dígitos"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre para Contacto *</label>
                                <input type="text" id="name" name="name" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                                <p class="text-xs text-gray-500 mt-1">Usado para comunicaciones generales</p>
                            </div>
                            
                            <div>
                                <label for="tickets" class="block text-sm font-medium text-gray-700">Número de Boletos</label>
                                <select id="tickets" name="tickets"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                    </div>
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
                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-lg font-medium">
                        <?php echo $event['is_paid'] ? 'Registrarme y Proceder al Pago' : 'Completar Registro'; ?>
                    </button>
                </div>
                
                <?php if ($event['is_paid']): ?>
                <p class="text-center text-sm text-gray-500 mt-3">
                    Al registrarte serás redirigido a PayPal para completar el pago de $<?php echo number_format($event['price'], 2); ?> MXN
                </p>
                <?php endif; ?>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-500">
            <p>Organizado por Cámara de Comercio de Querétaro</p>
            <p class="mt-1">
                <a href="<?php echo BASE_URL; ?>" class="text-blue-600 hover:text-blue-800">Ir al sitio principal</a>
            </p>
        </div>
    </div>
    
    <script>
    function lookupCompany() {
        const identifier = document.getElementById('lookup-identifier').value.trim();
        const resultEl = document.getElementById('lookup-result');
        
        if (!identifier) {
            resultEl.textContent = 'Por favor, ingresa un WhatsApp o RFC.';
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
                    document.getElementById('razon_social').value = data.company.business_name || '';
                    document.getElementById('nombre_empresario_representante').value = data.company.owner_name || '';
                    document.getElementById('email').value = data.company.corporate_email || '';
                    document.getElementById('phone').value = data.company.phone || data.company.whatsapp || '';
                    document.getElementById('rfc').value = data.company.rfc || '';
                    document.getElementById('contact_id').value = data.company.id || '';
                    validateRFC();
                    
                    resultEl.textContent = '✓ Empresa encontrada: ' + (data.company.business_name || data.company.owner_name);
                    resultEl.className = 'text-sm mt-2 text-green-600';
                } else {
                    resultEl.textContent = 'No se encontró ninguna empresa. Puedes registrarte como invitado.';
                    resultEl.className = 'text-sm mt-2 text-yellow-600';
                }
                resultEl.classList.remove('hidden');
            })
            .catch(err => {
                resultEl.textContent = 'Error al buscar. Por favor, intenta de nuevo.';
                resultEl.className = 'text-sm mt-2 text-red-600';
                resultEl.classList.remove('hidden');
            });
    }
    
    function validateRFC() {
        const rfcInput = document.getElementById('rfc');
        const feedback = document.getElementById('rfc-feedback');
        const rfc = rfcInput.value.toUpperCase().trim();
        
        if (!rfc) {
            feedback.textContent = '';
            feedback.classList.add('hidden');
            rfcInput.classList.remove('border-red-500', 'border-green-500');
            return;
        }
        
        const length = rfc.length;
        let isValid = false;
        let message = '';
        
        if (length === 13) {
            // Persona Física: 4 letters + 6 digits + 3 alphanumeric
            isValid = /^[A-Z]{4}[0-9]{6}[A-Z0-9]{3}$/.test(rfc);
            message = isValid 
                ? '✓ RFC válido (Persona Física)' 
                : '✗ Formato inválido. Debe ser: 4 letras + 6 dígitos + 3 caracteres';
        } else if (length === 12) {
            // Persona Moral: 3 letters + 6 digits + 3 alphanumeric
            isValid = /^[A-Z]{3}[0-9]{6}[A-Z0-9]{3}$/.test(rfc);
            message = isValid 
                ? '✓ RFC válido (Persona Moral)' 
                : '✗ Formato inválido. Debe ser: 3 letras + 6 dígitos + 3 caracteres';
        } else {
            message = '✗ RFC debe tener 12 caracteres (Persona Moral) o 13 (Persona Física)';
        }
        
        feedback.textContent = message;
        feedback.className = 'text-xs mt-1 ' + (isValid ? 'text-green-600' : 'text-red-600');
        feedback.classList.remove('hidden');
        
        rfcInput.classList.remove('border-red-500', 'border-green-500');
        rfcInput.classList.add(isValid ? 'border-green-500' : 'border-red-500');
    }
    
    function checkAttendeeMatch() {
        const attendeeName = document.getElementById('nombre_asistente').value.trim().toLowerCase();
        const ownerName = document.getElementById('nombre_empresario_representante').value.trim().toLowerCase();
        const guestFields = document.getElementById('guest-fields');
        const paymentNotice = document.getElementById('payment-notice');
        const categoriaSelect = document.getElementById('categoria_asistente');
        
        if (!attendeeName || !ownerName) {
            guestFields.classList.add('hidden');
            paymentNotice.classList.add('hidden');
            categoriaSelect.removeAttribute('required');
            return;
        }
        
        const isDifferent = attendeeName !== ownerName;
        
        if (isDifferent) {
            // Show guest fields and payment notice
            guestFields.classList.remove('hidden');
            categoriaSelect.setAttribute('required', 'required');
            
            <?php if ($event['is_paid']): ?>
            paymentNotice.textContent = '💳 El asistente deberá pagar su boleto ($<?php echo number_format($event['price'], 2); ?> MXN)';
            paymentNotice.className = 'text-xs mt-1 text-orange-600 font-medium';
            <?php else: ?>
            paymentNotice.textContent = 'ℹ️ El asistente es diferente del representante';
            paymentNotice.className = 'text-xs mt-1 text-blue-600';
            <?php endif; ?>
            paymentNotice.classList.remove('hidden');
        } else {
            // Hide guest fields
            guestFields.classList.add('hidden');
            categoriaSelect.removeAttribute('required');
            
            <?php if ($event['is_paid'] && $event['free_for_affiliates']): ?>
            paymentNotice.textContent = '🎉 El propietario/representante tiene acceso gratuito';
            paymentNotice.className = 'text-xs mt-1 text-green-600 font-medium';
            paymentNotice.classList.remove('hidden');
            <?php else: ?>
            paymentNotice.classList.add('hidden');
            <?php endif; ?>
        }
    }
    
    // Form validation - consolidated validation on submit
    document.getElementById('registration-form')?.addEventListener('submit', function(e) {
        // Validate phone
        const phone = document.getElementById('phone').value;
        if (phone && !/^\d{10}$/.test(phone)) {
            e.preventDefault();
            alert('El teléfono debe tener exactamente 10 dígitos.');
            return false;
        }
        
        // Validate WhatsApp asistente
        const whatsappAsistente = document.getElementById('whatsapp_asistente').value;
        if (whatsappAsistente && !/^\d{10}$/.test(whatsappAsistente)) {
            e.preventDefault();
            alert('El WhatsApp del asistente debe tener exactamente 10 dígitos.');
            return false;
        }
        
        // Validate RFC using same logic as validateRFC() for consistency
        const rfcInput = document.getElementById('rfc');
        const rfc = rfcInput.value.toUpperCase().trim();
        
        if (!rfc) {
            e.preventDefault();
            alert('El RFC es obligatorio.');
            return false;
        }
        
        const length = rfc.length;
        let isValid = false;
        
        if (length === 13) {
            isValid = /^[A-Z]{4}[0-9]{6}[A-Z0-9]{3}$/.test(rfc);
            if (!isValid) {
                e.preventDefault();
                alert('RFC de Persona Física inválido. Formato: 4 letras + 6 dígitos + 3 caracteres');
                return false;
            }
        } else if (length === 12) {
            isValid = /^[A-Z]{3}[0-9]{6}[A-Z0-9]{3}$/.test(rfc);
            if (!isValid) {
                e.preventDefault();
                alert('RFC de Persona Moral inválido. Formato: 3 letras + 6 dígitos + 3 caracteres');
                return false;
            }
        } else {
            e.preventDefault();
            alert('El RFC debe tener 12 caracteres (Persona Moral) o 13 caracteres (Persona Física).');
            return false;
        }
        
        // Validate nombre asistente
        const nombreAsistente = document.getElementById('nombre_asistente').value.trim();
        if (!nombreAsistente) {
            e.preventDefault();
            alert('El nombre del asistente es obligatorio para la emisión del boleto.');
            return false;
        }
        
        // Validate categoria asistente if guest fields are visible
        const guestFields = document.getElementById('guest-fields');
        if (!guestFields.classList.contains('hidden')) {
            const categoria = document.getElementById('categoria_asistente').value;
            if (!categoria) {
                e.preventDefault();
                alert('Debe seleccionar la categoría del asistente.');
                return false;
            }
        }
    });
    </script>
</body>
</html>
