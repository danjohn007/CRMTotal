<!-- Notifications Index -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Notificaciones</h2>
            <p class="mt-1 text-sm text-gray-500"><?php echo $unreadCount; ?> sin leer</p>
        </div>
        <?php if ($unreadCount > 0): ?>
        <form action="<?php echo BASE_URL; ?>/notificaciones/marcar-todas-leidas" method="POST">
            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                Marcar todas como leídas
            </button>
        </form>
        <?php endif; ?>
    </div>
    
    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow-sm">
        <?php if (empty($groupedNotifications)): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-gray-500">No tienes notificaciones</p>
        </div>
        <?php else: ?>
        <?php foreach ($groupedNotifications as $date => $notifications): ?>
        <div class="border-b border-gray-200 last:border-0">
            <div class="px-6 py-3 bg-gray-50">
                <h3 class="text-sm font-medium text-gray-700">
                    <?php 
                    $dateObj = new DateTime($date);
                    $today = new DateTime('today');
                    $yesterday = new DateTime('yesterday');
                    
                    if ($dateObj->format('Y-m-d') === $today->format('Y-m-d')) {
                        echo 'Hoy';
                    } elseif ($dateObj->format('Y-m-d') === $yesterday->format('Y-m-d')) {
                        echo 'Ayer';
                    } else {
                        echo $dateObj->format('d/m/Y');
                    }
                    ?>
                </h3>
            </div>
            
            <div class="divide-y divide-gray-100">
                <?php foreach ($notifications as $notification): 
                    $typeInfo = $notificationTypes[$notification['type']] ?? ['icon' => '📌', 'color' => 'gray', 'label' => 'General'];
                ?>
                <div class="p-4 hover:bg-gray-50 <?php echo !$notification['is_read'] ? 'bg-blue-50' : ''; ?>">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-2xl">
                            <?php echo $typeInfo['icon']; ?>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($notification['title']); ?>
                                </p>
                                <span class="text-xs text-gray-500">
                                    <?php echo date('H:i', strtotime($notification['created_at'])); ?>
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                <?php echo htmlspecialchars($notification['message']); ?>
                            </p>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-<?php echo $typeInfo['color']; ?>-100 text-<?php echo $typeInfo['color']; ?>-800">
                                    <?php echo $typeInfo['label']; ?>
                                </span>
                                <?php if ($notification['link']): ?>
                                <a href="<?php echo BASE_URL . $notification['link']; ?>" 
                                   class="text-xs text-blue-600 hover:text-blue-800">
                                    Ver detalle →
                                </a>
                                <?php endif; ?>
                                <?php if (!$notification['is_read']): ?>
                                <a href="<?php echo BASE_URL; ?>/notificaciones/marcar-leida/<?php echo $notification['id']; ?>" 
                                   class="text-xs text-gray-500 hover:text-gray-700">
                                    Marcar como leída
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
