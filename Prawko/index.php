<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linia Nauka Jazdy - Profesjonalna Szkoła Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                once: true,
                offset: 100
            });
        });
    </script>
</head>
<body>
    <header class="scroll-up">
        <nav>
            <div class="logo">
                <img src="logo.png" alt="Linia Nauka Jazdy Logo">
            </div>
            <ul>
                <li><a href="index.php" class="active">Strona Główna</a></li>
                <li><a href="kurs_prawa_jazdy.php">Kurs Prawa Jazdy</a></li>
                <li><a href="kurs_instruktorow.php">Kursy dla Instruktorów</a></li>
                <li><a href="kurs_kierowcow.php">Kursy Kierowców Zawodowych</a></li>
                <li><a href="kurs_operatorow.php">Kursy Operatorów Maszyn</a></li>
                <li><a href="badania.php">Badania</a></li>
                <li><a href="oplaty.php">Opłaty</a></li>
                <li><a href="kontakt.php">Kontakt</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Panel</a></li>
                    <li><a href="logout.php">Wyloguj</a></li>
                <?php else: ?>
                    <li><a href="login.php">Zaloguj</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
</body>
</html> 