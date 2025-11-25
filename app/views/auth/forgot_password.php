<?php $content = ob_start(); ?>
<?php 
$siteLogo = $config['site_logo'] ?? null;
$siteName = $config['site_name'] ?? 'CRM Total';
$primaryColor = $config['primary_color'] ?? '#1e40af';
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($primaryColor); ?> 0%, <?php echo htmlspecialchars($config['secondary_color'] ?? '#3b82f6'); ?> 100%);">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo and Title -->
        <div class="text-center">
            <?php if ($siteLogo): ?>
            <div class="mx-auto h-20 w-auto flex items-center justify-center">
                <img src="<?php echo BASE_URL . $siteLogo; ?>" alt="Logo" class="h-20 object-contain">
            </div>
            <?php else: ?>
            <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                <span class="text-3xl font-bold" style="color: <?php echo htmlspecialchars($primaryColor); ?>;">CCQ</span>
            </div>
            <?php endif; ?>
            <h2 class="mt-6 text-3xl font-extrabold text-white">
                Recuperar Contraseña
            </h2>
            <p class="mt-2 text-sm text-blue-200">
                Ingresa tu correo para recibir instrucciones
            </p>
        </div>
        
        <!-- Forgot Password Form -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            <?php if (!empty($error)): ?>
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <form class="space-y-6" action="<?php echo BASE_URL; ?>/recuperar-password" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Correo electrónico
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent sm:text-sm"
                               style="--tw-ring-color: <?php echo htmlspecialchars($primaryColor); ?>;"
                               placeholder="usuario@ejemplo.com">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition"
                            style="background-color: <?php echo htmlspecialchars($primaryColor); ?>;">
                        Enviar Instrucciones
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>/login" class="text-sm font-medium hover:underline" style="color: <?php echo htmlspecialchars($primaryColor); ?>;">
                        ← Volver al inicio de sesión
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <p class="text-center text-sm text-blue-200">
            © <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>
        </p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php echo $content; ?>
