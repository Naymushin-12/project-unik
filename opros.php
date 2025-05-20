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
    <title>Управление опросами</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Боковое меню -->
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
        <a href="opros.html" class="sidebar-item active">
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
        <!-- Иконки профиля -->
        <div class="top-icons">
            <span class="icon" onclick="showProfile()">👤</span>
            <span class="icon" onclick="toggleEditProfile()">⚙️</span>
        </div>

        <!-- Главный вид опросов -->
        <div id="main-poll-view">
            <h1 style="text-align: center;">Управление опросами</h1>
            
            <div class="poll-section">
                <button id="create-poll-btn" class="create-btn">+ Создать новый опрос</button>
                
                <h2>Доступные опросы:</h2>
                <ul id="poll-list" class="poll-list"></ul>
            </div>
        </div>

        <!-- Вид создания опроса -->
        <div id="create-poll-view" style="display: none;">
            <h1 style="text-align: center;">Создание нового опроса</h1>
            
            <div class="poll-form">
                <input type="text" id="poll-title" placeholder="Название опроса" class="poll-input">
                
                <div id="questions-container">
                    <!-- Вопросы будут добавляться здесь -->
                </div>
                
                <button id="add-question-btn" class="add-btn">+ Добавить вопрос</button>
                
                <div class="answer-options">
                    <h3>Варианты ответов (фиксированные):</h3>
                    <ul>
                        <li>1 - Отлично</li>
                        <li>2 - Хорошо</li>
                        <li>3 - Не очень</li>
                        <li>4 - Затрудняюсь с ответом</li>
                        <li>5 - Другой ответ (текстовое поле)</li>
                    </ul>
                </div>
                
                <div class="poll-actions">
                    <button id="save-poll-btn" class="save-btn">Сохранить опрос</button>
                    <button id="cancel-poll-btn" class="cancel-btn">Отмена</button>
                </div>
            </div>
        </div>

        <!-- Вид прохождения опроса -->
        <div id="take-poll-view" style="display: none;">
            <h1 id="poll-view-title" style="text-align: center;"></h1>
            
            <div class="poll-form">
                <div id="poll-questions-container">
                    <!-- Вопросы опроса будут здесь -->
                </div>
                
                <div class="poll-actions">
                    <button id="submit-poll-btn" class="save-btn">Отправить ответы</button>
                    <button id="back-to-polls-btn" class="cancel-btn">Назад к опросам</button>
                </div>
            </div>
        </div>
    </div>

    <script src="opros.js"></script>
</body>
</html>