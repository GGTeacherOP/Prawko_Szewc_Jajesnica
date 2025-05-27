<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kursy Kierowców Zawodowych - Linia Nauka Jazdy</title>
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
    <?php include 'header.php'; ?>

    <main>
        <section class="page-header" data-aos="fade-down">
            <h1>Kursy Kierowców Zawodowych</h1>
            <p>Profesjonalne szkolenia dla kierowców zawodowych</p>
        </section>

        <section class="course-content">
            <div class="course-intro" data-aos="fade-up">
                <h2>Oferta Kursów dla Kierowców Zawodowych</h2>
                <p>Oferujemy kompleksowe szkolenia dla kierowców zawodowych, spełniające wszystkie wymogi prawne i standardy branżowe.</p>
            </div>

            <div class="courses-grid">
                <!-- Kwalifikacja Wstępna -->
                <div class="course-card" data-aos="fade-up">
                    <h3>Kurs Kierowcy Zawodowego</h3>
                    <p>Kwalifikacja wstępna dla kierowców zawodowych</p>
                    <ul>
                        <li>280 godzin zajęć teoretycznych</li>
                        <li>20 godzin zajęć praktycznych</li>
                        <li>Egzamin państwowy</li>
                        <li>Materiały szkoleniowe</li>
                    </ul>
                    <?php
                    require_once 'config.php';
                    require_once 'course_enrollment_button.php';
                    displayEnrollmentButton('Kierowcy Zawodowi', $conn, 'Kurs Kierowcy Zawodowego');
                    ?>
                </div>

                <!-- Kwalifikacja Okresowa -->
                <div class="course-card" data-aos="fade-up" data-aos-delay="100">
                    <h3>Kwalifikacja Okresowa</h3>
                    <p>Szkolenie okresowe dla kierowców zawodowych</p>
                    <ul>
                        <li>35 godzin zajęć</li>
                        <li>Aktualizacja wiedzy</li>
                        <li>Certyfikat ukończenia</li>
                        <li>Materiały szkoleniowe</li>
                    </ul>
                    <?php
                    displayEnrollmentButton('Kierowcy Zawodowi', $conn, 'Kwalifikacja Okresowa');
                    ?>
                </div>

                <!-- Kurs ADR -->
                <div class="course-card" data-aos="fade-up" data-aos-delay="200">
                    <h3>Kurs ADR</h3>
                    <p>Przewóz materiałów niebezpiecznych</p>
                    <ul>
                        <li>24 godziny zajęć</li>
                        <li>Praktyczne ćwiczenia</li>
                        <li>Egzamin ADR</li>
                        <li>Certyfikat międzynarodowy</li>
                    </ul>
                    <?php
                    displayEnrollmentButton('Kierowcy Zawodowi', $conn, 'Kurs ADR');
                    ?>
                </div>
            </div>

            <div class="course-requirements" data-aos="fade-up">
                <h3>Wymagania dla kandydatów</h3>
                <ul>
                    <li>Ukończone 21 lat (18 lat w przypadku kwalifikacji wstępnej przyspieszonej)</li>
                    <li>Prawo jazdy odpowiedniej kategorii</li>
                    <li>Aktualne badania lekarskie i psychologiczne</li>
                    <li>Niekaralność za określone przestępstwa</li>
                </ul>
            </div>

            <div class="course-benefits" data-aos="fade-up">
                <h3>Korzyści z ukończenia kursu</h3>
                <ul>
                    <li>Możliwość podjęcia pracy jako kierowca zawodowy</li>
                    <li>Wyższe kwalifikacje zawodowe</li>
                    <li>Lepsze możliwości zatrudnienia</li>
                    <li>Certyfikaty uznawane w całej UE</li>
                </ul>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content" data-aos="zoom-in">
                <h2>Rozpocznij Karierę Kierowcy Zawodowego</h2>
                <p>Zdobądź kwalifikacje i rozpocznij pracę w branży transportowej!</p>
                <a href="kontakt.php" class="hero-btn primary">Skontaktuj się z Nami</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="contact-info" data-aos="fade-right">
                <h4>Kontakt</h4>
                <p>Telefon: +48 123 456 789</p>
                <p>Email: biuro@linianauka.pl</p>
                <p>Adres: ul. Przykładowa 123, 00-000 Warszawa</p>
            </div>
            <div class="social-links" data-aos="fade-up">
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="copyright" data-aos="fade-left">
                <p>&copy; 2024 Linia Nauka Jazdy. Wszystkie prawa zastrzeżone.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>