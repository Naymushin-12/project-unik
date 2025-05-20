<?php
require 'connect.php'; 

class Auth {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    /**
     * Проверяет авторизацию пользователя
     * Перенаправляет на страницу входа если не авторизован
     */
    public function checkAuth() {
        session_start();
        
        // Если нет user_id в сессии - перенаправляем
        if (empty($_SESSION['user_id'])) {
            $this->redirectWithError('ay.php', 'Для доступа требуется авторизация');
        }

        // Дополнительная проверка в БД
        if (!$this->validateSession()) {
            $this->logout();
            $this->redirectWithError('ay.php', 'Ваша сессия устарела');
        }

        // Обновляем время последней активности
        $_SESSION['last_activity'] = time();
    }

    /**
     * Аутентификация пользователя
     */
    public function authenticate($email, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT id, email, password FROM users WHERE email = :email AND is_active = 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password'])) {
                    // Успешная авторизация
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                    
                    // Обновляем время последнего входа в БД
                    $this->updateLastLogin($user['id']);
                    
                    return true;
                }
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Auth error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Выход из системы
     */
    public function logout() {
        session_start();
        
        // Запись в лог о выходе
        if (!empty($_SESSION['user_id'])) {
            $this->logUserActivity($_SESSION['user_id'], 'logout');
        }
        
        // Очистка сессии
        session_unset();
        session_destroy();
        session_write_close();
        
        // Удаляем куки сессии
        setcookie(session_name(), '', time()-3600, '/');
    }

    /**
     * Проверяет валидность сессии в БД
     */
    private function validateSession() {
        if (empty($_SESSION['user_id']) || empty($_SESSION['ip_address']) || empty($_SESSION['user_agent'])) {
            return false;
        }

        // Проверка IP и User-Agent
        if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] || 
            $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            return false;
        }

        // Проверка времени бездействия (30 минут)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            return false;
        }

        // Дополнительная проверка в БД
        try {
            $stmt = $this->conn->prepare("SELECT 1 FROM users WHERE id = :id AND is_active = 1");
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch(PDOException $e) {
            error_log("Session validation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Обновляет время последнего входа пользователя
     */
    private function updateLastLogin($userId) {
        try {
            $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
        } catch(PDOException $e) {
            error_log("Last login update error: " . $e->getMessage());
        }
    }

    /**
     * Логирование действий пользователя
     */
    private function logUserActivity($userId, $action) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO user_logs (user_id, action, ip_address, user_agent) 
                VALUES (:user_id, :action, :ip, :ua)
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
            $stmt->bindParam(':ua', $_SERVER['HTTP_USER_AGENT']);
            $stmt->execute();
        } catch(PDOException $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Перенаправление с сообщением об ошибке
     */
    private function redirectWithError($url, $message) {
        $_SESSION['error_message'] = $message;
        header("Location: $url");
        exit;
    }
}

// Создаем экземпляр класса Auth
$auth = new Auth();

// Функция для обратной совместимости
function checkAuth() {
    if (!isset($_SESSION['user_id'])){
        header('Location: ay.php');
        exit;
    }
}
?>