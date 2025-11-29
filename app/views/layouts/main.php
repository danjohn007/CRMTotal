<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? APP_NAME; ?></title>
    
    <?php
    // Load system configuration for styles
    $configModel = new Config();
    $sysConfig = $configModel->getAll();
    $primaryColor = $sysConfig['primary_color'] ?? '#1e40af';
    $secondaryColor = $sysConfig['secondary_color'] ?? '#3b82f6';
    $accentColor = $sysConfig['accent_color'] ?? '#10b981';
    $siteLogo = $sysConfig['site_logo'] ?? null;
    $siteName = $sysConfig['site_name'] ?? 'CRM CCQ';
    ?>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link.active { background-color: <?php echo htmlspecialchars($primaryColor); ?>20; color: <?php echo htmlspecialchars($primaryColor); ?>; }
        .sidebar-header { background-color: <?php echo htmlspecialchars($primaryColor); ?>; }
        .btn-primary { background-color: <?php echo htmlspecialchars($primaryColor); ?> !important; color: white !important; }
        .btn-primary:hover { background-color: <?php echo htmlspecialchars($secondaryColor); ?> !important; }
        .btn-accent { background-color: <?php echo htmlspecialchars($accentColor); ?>; }
        .text-primary { color: <?php echo htmlspecialchars($primaryColor); ?>; }
        .bg-primary { background-color: <?php echo htmlspecialchars($primaryColor); ?>; }
        /* Override hardcoded blue buttons with primary color */
        .bg-blue-600 { background-color: <?php echo htmlspecialchars($primaryColor); ?> !important; }
        .bg-blue-600:hover, .hover\:bg-blue-700:hover { background-color: <?php echo htmlspecialchars($secondaryColor); ?> !important; }
    </style>
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Authenticated Layout -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto"
            :class="{'translate-x-0': mobileMenuOpen, '-translate-x-full': !mobileMenuOpen}"
        >
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 sidebar-header">
                <a href="<?php echo BASE_URL; ?>/dashboard" class="flex items-center">
                    <?php if ($siteLogo): ?>
                    <img src="<?php echo BASE_URL . $siteLogo; ?>" alt="Logo" class="h-10 object-contain">
                    <?php else: ?>
                    <span class="text-white font-bold text-xl"><?php echo htmlspecialchars($siteName); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            
            <!-- User Info -->
            <div class="p-4 border-b">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                        <span class="text-primary-700 font-semibold">
                            <?php echo mb_substr($_SESSION['user_name'] ?? 'U', 0, 1, 'UTF-8'); ?>
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></p>
                        <p class="text-xs text-gray-500"><?php echo ucfirst($_SESSION['user_role'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="p-4 space-y-1 overflow-y-auto" style="height: calc(100vh - 160px);">
                <a href="<?php echo BASE_URL; ?>/dashboard" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Dashboard
                </a>
                
                <a href="<?php echo BASE_URL; ?>/prospectos" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'prospectos' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Prospectos
                </a>
                
                <a href="<?php echo BASE_URL; ?>/afiliados" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'afiliados' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Afiliados
                </a>
                
                <?php if (in_array($_SESSION['user_role'] ?? '', ['afiliador', 'jefe_comercial', 'superadmin', 'direccion'])): ?>
                <a href="<?php echo BASE_URL; ?>/expedientes" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'expedientes' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Expedientes Digitales
                </a>
                <?php endif; ?>
                
                <a href="<?php echo BASE_URL; ?>/eventos" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'eventos' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Eventos
                </a>
                
                <a href="<?php echo BASE_URL; ?>/agenda" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'agenda' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Agenda
                </a>
                
                <a href="<?php echo BASE_URL; ?>/journey" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'journey' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Customer Journey
                </a>
                
                <a href="<?php echo BASE_URL; ?>/buscador" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'buscador' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscador
                </a>
                
                <a href="<?php echo BASE_URL; ?>/notificaciones" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'notificaciones' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Notificaciones
                </a>
                
                <a href="<?php echo BASE_URL; ?>/reportes" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'reportes' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reportes
                </a>
                
                <!-- New Modules Section -->
                <div class="pt-4 mt-4 border-t">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Módulos</p>
                </div>
                
                <a href="<?php echo BASE_URL; ?>/membresias" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'membresias' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a2 2 0 10-4 0v5a2 2 0 01-2 2h6m-6-4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Membresías
                </a>
                
                <?php if (in_array($_SESSION['user_role'] ?? '', ['superadmin', 'direccion', 'contabilidad'])): ?>
                <a href="<?php echo BASE_URL; ?>/financiero" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'financiero' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Financiero
                </a>
                <?php endif; ?>
                
                <a href="<?php echo BASE_URL; ?>/requerimientos" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'requerimientos' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Requerimientos
                </a>
                
                <?php if (in_array($_SESSION['user_role'] ?? '', ['superadmin', 'direccion'])): ?>
                <div class="pt-4 mt-4 border-t">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administración</p>
                </div>
                
                <a href="<?php echo BASE_URL; ?>/importar" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'importar' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Importar
                </a>
                
                <a href="<?php echo BASE_URL; ?>/auditoria" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'auditoria' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Auditoría
                </a>
                
                <a href="<?php echo BASE_URL; ?>/usuarios" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'usuarios' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Usuarios
                </a>
                
                <a href="<?php echo BASE_URL; ?>/configuracion" 
                   class="sidebar-link flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition <?php echo ($currentPage ?? '') === 'configuracion' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configuración
                </a>
                <?php endif; ?>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm z-40">
                <div class="flex items-center justify-between h-16 px-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <!-- Page Title -->
                    <h1 class="text-xl font-semibold text-gray-800">
                        <?php echo $pageTitle ?? 'Dashboard'; ?>
                    </h1>
                    
                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <a href="<?php echo BASE_URL; ?>/notificaciones" class="relative p-2 text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <?php if (isset($notificationCount) && $notificationCount > 0): ?>
                            <span class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                <?php echo $notificationCount; ?>
                            </span>
                            <?php endif; ?>
                        </a>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <span class="hidden sm:inline"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                                <a href="<?php echo BASE_URL; ?>/perfil" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Mi Perfil</a>
                                <a href="<?php echo BASE_URL; ?>/logout" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Cerrar Sesión</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                </div>
                <?php endif; ?>
                
                <?php echo $content ?? ''; ?>
                
                <!-- Footer -->
                <div class="mt-8 pt-4 border-t border-gray-200 text-center text-sm text-gray-500">
                    <p>Solución Digital desarrollada por&nbsp;<a href="https://www.impactosdigitales.com/" class="text-blue-600 hover:underline" target="_blank">ID</a></p>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Mobile sidebar overlay -->
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>
    
    <?php else: ?>
    <!-- Guest Layout -->
    <?php echo $content ?? ''; ?>
    <?php endif; ?>
    
    <!-- Common Scripts -->
    <script>
        // Flash message auto-hide
        document.querySelectorAll('[class*="flash"]').forEach(el => {
            setTimeout(() => el.remove(), 5000);
        });
    </script>
</body>
</html>
