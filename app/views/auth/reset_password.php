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
                Nueva Contraseña
            </h2>
            <p class="mt-2 text-sm text-blue-200">
                Ingresa tu nueva contraseña
            </p>
        </div>
        
        <!-- Reset Password Form -->
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
            
            <form class="space-y-6" action="<?php echo BASE_URL; ?>/restablecer-password?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Nueva contraseña
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                               class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent sm:text-sm"
                               placeholder="Mínimo 8 caracteres">
                    </div>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                        Confirmar contraseña
                    </label>
                    <div class="mt-1">
                        <input id="confirm_password" name="confirm_password" type="password" required
                               class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent sm:text-sm"
                               placeholder="Repite la contraseña">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition"
                            style="background-color: <?php echo htmlspecialchars($primaryColor); ?>;">
                        Restablecer Contraseña
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
