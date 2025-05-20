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
    <title>Результаты опросов</title>
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
        <a href="rezultat.html" class="sidebar-item active">
            <span class="icon">📊</span>
            Результаты
        </a>
        <a href="report.html" class="sidebar-item">
            <span class="icon">⚠️</span>
            Сообщить об ошибке
        </a>
    </div>

    <div class="content">
        <div class="top-icons">
            <span class="icon" onclick="showProfile()">👤</span>
            <span class="icon" onclick="toggleEditProfile()">⚙️</span>
        </div>

        <h1 style="text-align: center;">Результаты опросов</h1>
        
        <div id="results-container" class="results-container">
            <!-- Результаты будут загружены через JavaScript -->
        </div>
    </div>

    <script src="rezultat.js"></script>
</body>
</html>