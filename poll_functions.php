<?php
require_once 'connect.php';

function savePollToDB($title, $questions) {
    $conn = connectDB();
    
    try {
        $conn->beginTransaction();
        
        // Сохраняем опрос
        $stmt = $conn->prepare("INSERT INTO polls (title, created_at) VALUES (:title, NOW())");
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        $pollId = $conn->lastInsertId();
        
        // Сохраняем вопросы
        foreach ($questions as $question) {
            $stmt = $conn->prepare("INSERT INTO questions (poll_id, text) VALUES (:poll_id, :text)");
            $stmt->bindParam(':poll_id', $pollId);
            $stmt->bindParam(':text', $question);
            $stmt->execute();
        }
        
        $conn->commit();
        return $pollId;
    } catch(PDOException $e) {
        $conn->rollBack();
        die("Ошибка при сохранении опроса: " . $e->getMessage());
    }
}

function getPollsFromDB() {
    $conn = connectDB();
    
    try {
        $stmt = $conn->query("SELECT * FROM polls ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("Ошибка при загрузке опросов: " . $e->getMessage());
    }
}
?>