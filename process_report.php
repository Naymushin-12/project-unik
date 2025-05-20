<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connectDB();
    
    $reportType = $_POST['report-type'] ?? '';
    $message = $_POST['message'] ?? '';
    
    try {
        $stmt = $conn->prepare("INSERT INTO reports (type, message, created_at) 
                               VALUES (:type, :message, NOW())");
        $stmt->bindParam(':type', $reportType);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
        
        header('Location: report.php?success=1');
        exit;
    } catch(PDOException $e) {
        die("Ошибка при сохранении: " . $e->getMessage());
    }
} else {
    header('Location: report.php');
    exit;
}
?>