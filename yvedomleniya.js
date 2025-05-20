document.addEventListener('DOMContentLoaded', function() {
    const notificationsList = document.getElementById('notifications-list');
    const notificationBadge = document.getElementById('notification-badge');
    let notifications = JSON.parse(localStorage.getItem('notifications')) || [];

    // Функция для обновления бейджа с количеством уведомлений
    function updateNotificationBadge() {
        const unreadCount = notifications.filter(n => !n.read).length;
        
        if (unreadCount > 0) {
            notificationBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
            notificationBadge.style.display = 'inline-block';
        } else {
            notificationBadge.style.display = 'none';
        }
        
        // Обновляем бейдж на всех страницах
        localStorage.setItem('unreadNotifications', unreadCount);
    }

    // Функция для отображения уведомлений
    function renderNotifications() {
        notificationsList.innerHTML = '';
        
        if (notifications.length === 0) {
            notificationsList.innerHTML = '<p>Нет уведомлений</p>';
            return;
        }
        
        // Сортируем уведомления (новые сверху)
        notifications.sort((a, b) => new Date(b.date) - new Date(a.date));
        
        notifications.forEach(notification => {
            const notificationItem = document.createElement('div');
            notificationItem.className = `notification-item ${notification.read ? '' : 'unread'}`;
            
            const notificationIcon = document.createElement('span');
            notificationIcon.className = 'notification-icon';
            notificationIcon.textContent = getNotificationIcon(notification.type);
            
            const notificationContent = document.createElement('div');
            notificationContent.className = 'notification-content';
            
            const notificationText = document.createElement('p');
            notificationText.className = 'notification-text';
            notificationText.textContent = notification.message;
            
            const notificationDate = document.createElement('span');
            notificationDate.className = 'notification-date';
            notificationDate.textContent = notification.date;
            
            notificationContent.appendChild(notificationText);
            notificationContent.appendChild(notificationDate);
            
            notificationItem.appendChild(notificationIcon);
            notificationItem.appendChild(notificationContent);
            
            // Помечаем как прочитанное при клике
            notificationItem.addEventListener('click', () => {
                if (!notification.read) {
                    notification.read = true;
                    localStorage.setItem('notifications', JSON.stringify(notifications));
                    notificationItem.classList.remove('unread');
                    updateNotificationBadge();
                }
            });
            
            notificationsList.appendChild(notificationItem);
        });
    }

    // Функция для получения иконки в зависимости от типа уведомления
    function getNotificationIcon(type) {
        const icons = {
            'new-poll': '📝',
            'poll-completed': '✅',
            'system': 'ℹ️',
            'warning': '⚠️',
            'error': '❌'
        };
        return icons[type] || '🔔';
    }

    // Пометить все уведомления как прочитанные при загрузке страницы
    function markAllAsRead() {
        let updated = false;
        notifications = notifications.map(n => {
            if (!n.read) {
                updated = true;
                return {...n, read: true};
            }
            return n;
        });
        
        if (updated) {
            localStorage.setItem('notifications', JSON.stringify(notifications));
            updateNotificationBadge();
        }
    }

    // Инициализация
    markAllAsRead();
    renderNotifications();
    updateNotificationBadge();
});