<?php
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
<header class="scroll-up">
    <nav>
        <div class="logo">
            <img src="logo.png" alt="Linia Nauka Jazdy Logo">
        </div>
        <ul>
            <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Strona Główna</a></li>
            <li><a href="kurs_prawa_jazdy.php" <?php echo basename($_SERVER['PHP_SELF']) == 'kurs_prawa_jazdy.php' ? 'class="active"' : ''; ?>>Kurs Prawa Jazdy</a></li>
            <li><a href="kurs_instruktorow.php" <?php echo basename($_SERVER['PHP_SELF']) == 'kurs_instruktorow.php' ? 'class="active"' : ''; ?>>Kursy dla Instruktorów</a></li>
            <li><a href="kurs_kierowcow.php" <?php echo basename($_SERVER['PHP_SELF']) == 'kurs_kierowcow.php' ? 'class="active"' : ''; ?>>Kursy Kierowców Zawodowych</a></li>
            <li><a href="kurs_operatorow.php" <?php echo basename($_SERVER['PHP_SELF']) == 'kurs_operatorow.php' ? 'class="active"' : ''; ?>>Kursy Operatorów Maszyn</a></li>
            <li><a href="badania.php" <?php echo basename($_SERVER['PHP_SELF']) == 'badania.php' ? 'class="active"' : ''; ?>>Badania</a></li>
            <?php if($has_active_course): ?>
                <li><a href="planowanie_jazd.php" <?php echo basename($_SERVER['PHP_SELF']) == 'planowanie_jazd.php' ? 'class="active"' : ''; ?>>Planowanie Jazd</a></li>
            <?php endif; ?>
            <li><a href="oplaty.php" <?php echo basename($_SERVER['PHP_SELF']) == 'oplaty.php' ? 'class="active"' : ''; ?>>Opłaty</a></li>
            <li><a href="kontakt.php" <?php echo basename($_SERVER['PHP_SELF']) == 'kontakt.php' ? 'class="active"' : ''; ?>>Kontakt</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if(isset($_SESSION['rola']) && $_SESSION['rola'] === 'instruktor'): ?>
                    <li><a href="panel_instruktora.php">Panel Instruktora</a></li>
                <?php else: ?>
                    <li><a href="dashboard.php">Panel Kursanta</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Wyloguj</a></li>
            <?php else: ?>
                <li><a href="login.php" <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'class="active"' : ''; ?>>Zaloguj</a></li>
                <li><a href="register.php">Zarejestruj</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header> 