<?php
/**
 * Файл конфигурации базы данных и основных настроек сайта
 */

// Настройки базы данных MyAMPP (значения по умолчанию)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'survey_system');
define('DB_USER', 'survey_app');
define('DB_PASS', 'secure_password_123');

// Настройки сессии
define('SESSION_NAME', 'SURVEY_SYSTEM');
define('SESSION_LIFETIME', 86400);

// Функция для подключения к базе данных
function connectDB() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        
        $conn = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true
        ]);
        
        return $conn;
    } catch (PDOException $e) {
        error_log('Database connection error: ' . $e->getMessage());
        die('Произошла ошибка подключения к базе данных. Пожалуйста, попробуйте позже.');
    }
}

// Функция для инициализации сессии
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    
    
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}

// Инициализируем сессию
initSession();

// Функция для безопасного вывода данных
function safeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Функция для перенаправления
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Функции для работы с группами
function createGroup($name, $creatorId) {
    $conn = connectDB();
    try {
        $stmt = $conn->prepare("INSERT INTO groups (name, created_by, created_at) VALUES (:name, :creator_id, NOW())");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':creator_id', $creatorId);
        $stmt->execute();
        return $conn->lastInsertId();
    } catch(PDOException $e) {
        error_log('Error creating group: ' . $e->getMessage());
        return false;
    }
}

function getGroupsByUser($userId) {
    $conn = connectDB();
    try {
        $stmt = $conn->prepare("
            SELECT g.* FROM groups g
            JOIN group_members gm ON g.id = gm.group_id
            WHERE gm.user_id = :user_id
            ORDER BY g.created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log('Error fetching groups: ' . $e->getMessage());
        return [];
    }
}

function getGroupMembers($groupId) {
    $conn = connectDB();
    try {
        $stmt = $conn->prepare("
            SELECT u.email FROM users u
            JOIN group_members gm ON u.id = gm.user_id
            WHERE gm.group_id = :group_id
        ");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } catch(PDOException $e) {
        error_log('Error fetching group members: ' . $e->getMessage());
        return [];
    }
}

function addMemberToGroup($groupId, $email) {
    $conn = connectDB();
    try {
        // Проверяем существование пользователя
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $userId = $stmt->fetchColumn();
        
        if (!$userId) {
            return false; // Пользователь не найден
        }
        
        // Проверяем, не состоит ли уже пользователь в группе
        $stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = :group_id AND user_id = :user_id");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        if ($stmt->fetchColumn()) {
            return false; // Уже состоит в группе
        }
        
        // Добавляем пользователя в группу
        $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id, joined_at) VALUES (:group_id, :user_id, NOW())");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log('Error adding member to group: ' . $e->getMessage());
        return false;
    }
}

function removeMemberFromGroup($groupId, $email) {
    $conn = connectDB();
    try {
        $stmt = $conn->prepare("
            DELETE gm FROM group_members gm
            JOIN users u ON gm.user_id = u.id
            WHERE gm.group_id = :group_id AND u.email = :email
        ");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log('Error removing member from group: ' . $e->getMessage());
        return false;
    }
}
?>