<?php
// get-question.php - Получение вопроса из базы данных
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include 'dbcon.php';

if (isset($_GET['id']) && isset($_GET['roomId'])) {
    $questionId = (int)$_GET['id'];
    $roomId = (int)$_GET['roomId'];
    
    try {
        $stmt = $pdo->prepare("SELECT id, question, answer, hint FROM questions WHERE id = ? AND roomId = ?");
        $stmt->execute([$questionId, $roomId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Question not found'
            ]);
        }
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Missing parameters'
    ]);
}
?>