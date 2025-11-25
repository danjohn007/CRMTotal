<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | CRM CCQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <h1 class="text-9xl font-bold text-blue-600">404</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mt-4">Página no encontrada</h2>
        <p class="text-gray-600 mt-2 max-w-md mx-auto">
            Lo sentimos, la página que buscas no existe o ha sido movida.
        </p>
        <div class="mt-8 space-x-4">
            <a href="javascript:history.back()" 
               class="inline-block px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                ← Volver
            </a>
            <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/" 
               class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Ir al Inicio
            </a>
        </div>
    </div>
</body>
</html>
