<!DOCTYPE html>
<html lang="ru">
<?php
session_start();
require 'auth.php';
checkAuth();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 200px;
            background-color: #f4f4f4;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            text-decoration: none;
            color: #333;
            background: #e0e0e0;
            width: 100%;
            margin: 10px 0;
            border-radius: 8px;
            transition: background 0.3s;
            font-size: 18px;
        }
        .sidebar-item:hover {
            background-color: #ddd;
        }
        .icon {
            margin-right: 10px;
            font-size: 20px;
        }
        .content {
            padding: 20px;
            flex-grow: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
        .welcome-container {
            text-align: center;
            width: 100%;
            max-width: 800px;
            margin-top: 50px;
        }
        .profile-info {
            display: none;
            text-align: center;
            margin-top: 50px;
            width: 100%;
        }
        .profile-info input {
            display: block;
            text-align: center;
            margin: 10px auto;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 4px;
            width: 300px;
            max-width: 80%;
        }
        .profile-display {
            display: none;
            text-align: center;
            margin-top: 50px;
            width: 100%;
        }
        .profile-display p {
            font-size: 18px;
            margin: 15px 0;
        }
        .top-icons {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .top-icons .icon {
            font-size: 24px;
            margin-left: 15px;
            cursor: pointer;
        }
        .save-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 16px;
        }
        .save-btn:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: space-around;
                padding: 10px;
            }
            .sidebar-item {
                width: 45%;
                margin: 5px;
                font-size: 16px;
                padding: 10px;
            }
            .content {
                padding: 10px;
            }
            .welcome-container {
                margin-top: 20px;
            }
        }
    </style>
    <script>
        // Загрузка сохранённых данных при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            const savedProfile = localStorage.getItem('userProfile');
            if (savedProfile) {
                const profile = JSON.parse(savedProfile);
                document.getElementById('firstName').value = profile.firstName || '';
                document.getElementById('lastName').value = profile.lastName || '';
                document.getElementById('email').value = profile.email || '';
                
                // Обновляем отображаемые данные
                document.getElementById('displayFirstName').textContent = profile.firstName || '';
                document.getElementById('displayLastName').textContent = profile.lastName || '';
                document.getElementById('displayEmail').textContent = profile.email || '';
            }
            updateNotificationBadge();
        });

        function showProfile() {
            const editMode = document.querySelector('.profile-info').style.display === 'block';
            if (!editMode) {
                // Показываем только просмотр, если не в режиме редактирования
                document.querySelector('.profile-display').style.display = 'block';
                document.querySelector('.profile-info').style.display = 'none';
            }
        }

        function toggleEditProfile() {
            const profileInfo = document.querySelector('.profile-info');
            const profileDisplay = document.querySelector('.profile-display');
            
            if (profileInfo.style.display === 'block') {
                // Сохраняем данные при выходе из режима редактирования
                saveProfile();
                profileInfo.style.display = 'none';
                profileDisplay.style.display = 'block';
            } else {
                // Включаем режим редактирования
                profileInfo.style.display = 'block';
                profileDisplay.style.display = 'none';
            }
        }

        function saveProfile() {
            const profile = {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                email: document.getElementById('email').value
            };
            
            // Сохраняем в localStorage
            localStorage.setItem('userProfile', JSON.stringify(profile));
            
            // Обновляем отображаемые данные
            document.getElementById('displayFirstName').textContent = profile.firstName;
            document.getElementById('displayLastName').textContent = profile.lastName;
            document.getElementById('displayEmail').textContent = profile.email;
            
            // Показываем сохранённые данные
            document.querySelector('.profile-info').style.display = 'none';
            document.querySelector('.profile-display').style.display = 'block';
        }
        
        function updateNotificationBadge() {
            const unreadCount = localStorage.getItem('unreadNotifications') || 0;
            const badge = document.getElementById('notification-badge');
        
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    </script>
</head>
<body>

<div class="sidebar">
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
    <a href="res.html" class="sidebar-item">
        <span class="icon">📊</span>
        Результаты
    </a>
    <a href="report.html" class="sidebar-item">
        <span class="icon">⚠️</span>
        Сообщить об ошибке
    </a>
</div>

<div class="content">
    <div class="welcome-container">
        <h1>Добро пожаловать в систему опросов</h1>
        <p>Выберите пункт в меню слева для навигации.</p>
    </div>

    <div class="top-icons">
        <span class="icon" onclick="showProfile()">👤</span>
        <span class="icon" onclick="toggleEditProfile()">⚙️</span>
    </div>

    <div class="profile-info">
        <h2>Редактирование профиля</h2>
        <input type="text" id="firstName" placeholder="Имя">
        <input type="text" id="lastName" placeholder="Фамилия">
        <input type="email" id="email" placeholder="Почта">
        <button class="save-btn" onclick="saveProfile()">Сохранить</button>
    </div>

    <div class="profile-display">
        <h2>Мой профиль</h2>
        <p>Имя: <span id="displayFirstName"></span></p>
        <p>Фамилия: <span id="displayLastName"></span></p>
        <p>Почта: <span id="displayEmail"></span></p>
    </div>
</div>
<script src="main.js"></script>
</body>
</html>