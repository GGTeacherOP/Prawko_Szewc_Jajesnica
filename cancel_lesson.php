<?php
header('Content-Type: application/json');
include 'db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

// Walidacja danych wejściowych
if (!isset($input['lessonId'], $input['userId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Brakujące dane (ID jazdy lub ID użytkownika).']);
    exit();
}

$lessonId = $input['lessonId'];
$userId = $input['userId']; // ID ucznia, który próbuje odwołać

// Sprawdź, czy jazda istnieje, należy do tego ucznia i ma status 'Zaplanowana'
$stmtCheck = $conn->prepare("SELECT id FROM jazdy WHERE id = ? AND id_ucznia = ? AND status = 'Zaplanowana'");
$stmtCheck->bind_param("ii", $lessonId, $userId);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Nie można odwołać tej jazdy. Może już się odbyła, została anulowana lub nie należysz do niej.']);
    $stmtCheck->close();
    $conn->close();
    exit();
}
$stmtCheck->close();

// Zmień status jazdy na 'Anulowana'
$stmtUpdate = $conn->prepare("UPDATE jazdy SET status = 'Anulowana' WHERE id = ?");
$stmtUpdate->bind_param("i", $lessonId);

if ($stmtUpdate->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Jazda została pomyślnie odwołana.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Błąd podczas odwoływania jazdy: ' . $stmtUpdate->error]);
}

$stmtUpdate->close();
$conn->close();
?>
