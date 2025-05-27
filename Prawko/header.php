<?php
session_start();
if (!isset($conn)) {
    require_once 'config.php';
}

// Check if user has an active course
$has_active_course = false;
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("
        SELECT 1 FROM zapisy z 
        WHERE z.uzytkownik_id = ? 
        AND z.status = 'Zatwierdzony'
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $has_active_course = $stmt->get_result()->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linia Nauka Jazdy</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AOS Library -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <!-- Custom styles -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="scroll-up">
        <nav>
            <div class="logo">
                <img src="logo.png" alt="Linia Nauka Jazdy Logo">
            </div>
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if(isset($_SESSION['rola']) && $_SESSION['rola'] === 'instruktor'): ?>
                        <li><a href="panel_instruktora.php">Panel Instruktora</a></li>
                        <li><a href="harmonogram_jazd.php">Harmonogram Jazd</a></li>
                        <li><a href="moi_kursanci.php">Moi Kursanci</a></li>
                        <li><a href="ustaw_dostepnosc.php">Ustaw Dostępność</a></li>
                    <?php else: ?>
                        <li><a href="dashboard.php">Panel Kursanta</a></li>
                        <li><a href="planowanie_jazd.php">Zaplanuj Jazdy</a></li>
                        <li><a href="moje_jazdy.php">Moje Jazdy</a></li>
                        <li><a href="badania.php">Badania</a></li>
                        <li><a href="oplaty.php">Opłaty</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn-logout">Wyloguj się</a></li>
                <?php else: ?>
                    <li><a href="kurs_prawa_jazdy.php">Kurs Prawa Jazdy</a></li>
                    <li><a href="kurs_instruktorow.php">Kursy dla Instruktorów</a></li>
                    <li><a href="kurs_kierowcow.php">Kursy Kierowców Zawodowych</a></li>
                    <li><a href="kurs_operatorow.php">Kursy Operatorów Maszyn</a></li>
                    <li><a href="badania.php">Badania</a></li>
                    <li><a href="oplaty.php">Opłaty</a></li>
                    <li><a href="login.php">Zaloguj się</a></li>
                    <li><a href="rejestracja.php">Zarejestruj się</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </header>

    <!-- AOS Library -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <!-- Custom scripts -->
    <script src="js/script.js"></script>
</body>
</html> 