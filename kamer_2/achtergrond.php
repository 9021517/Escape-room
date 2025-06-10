<?php
// achtergrond.php - Обработка фонового изображения
header('Content-Type: application/json');

// Проверяем существование изображения
$imagePath = 'img/hospital-corridor.jpg';
$imageExists = file_exists($imagePath);

if ($imageExists) {
    $imageInfo = getimagesize($imagePath);
    echo json_encode([
        'success' => true,
        'path' => $imagePath,
        'width' => $imageInfo[0],
        'height' => $imageInfo[1],
        'type' => $imageInfo['mime']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Background image not found',
        'fallback' => true
    ]);
}
?>