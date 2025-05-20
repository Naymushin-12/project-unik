<!DOCTYPE html>
<html lang="ru">
<?php
session_start();
require_once 'auth.php';
checkAuth();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сообщить об ошибке</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="sidebar">
        <a href="main.html" class="sidebar-item">
            <span class="icon">🏠</span>
            Главная
        </a>
        <a href="yvedomleniya.html" class="sidebar-item">
            <span class="icon">🔔</span>
            Уведомления
        </a>
        <a href="groups.html" class="sidebar-item">
            <span class="icon">👥</span>
            Группы
        </a>
        <a href="opros.html" class="sidebar-item">
            <span class="icon">📝</span>
            Опросы
        </a>
        <a href="rezultat.html" class="sidebar-item">
            <span class="icon">📊</span>
            Результаты
        </a>
        <a href="report.php" class="sidebar-item active">
            <span class="icon">⚠️</span>
            Сообщить об ошибке
        </a>
    </div>

    <div class="content">
        <div class="top-icons">
            <span class="icon" onclick="showProfile()">👤</span>
            <span class="icon" onclick="toggleEditProfile()">⚙️</span>
        </div>

        <h1 style="text-align: center;">Сообщить об ошибке или предложить улучшение</h1>
        
        <div class="report-section">
            <form id="report-form" method="POST" action="process_report.php">
                <div class="form-group">
                    <label for="report-type">Тип сообщения:</label>
                    <select id="report-type" name="report-type" class="form-input" required>
                        <option value="" disabled selected>Выберите тип</option>
                        <option value="bug">Ошибка на сайте</option>
                        <option value="suggestion">Предложение по улучшению</option>
                        <option value="other">Другое</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">Сообщение:</label>
                    <textarea id="message" name="message" class="form-textarea" rows="6" required placeholder="Опишите проблему или ваше предложение..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Отправить</button>
                    <button type="reset" class="cancel-btn">Очистить</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Обработка отправки формы
        document.getElementById('report-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Здесь можно добавить AJAX-запрос или оставить обычную отправку
            alert('Ваше сообщение отправлено. Спасибо за обратную связь!');
            this.reset();
            
            // Для реального использования раскомментируйте следующую строку:
            // this.submit();
        });
    </script>
</body>
</html>