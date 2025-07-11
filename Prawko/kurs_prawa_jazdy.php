<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurs Prawa Jazdy - Linia Nauka Jazdy</title>
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
            <h1>Kurs Prawa Jazdy</h1>
            <p>Profesjonalne szkolenie przygotowujące do egzaminu na prawo jazdy</p>
            <p class="highlight">Możliwość dofinansowania nawet do 80% wartości kursu!</p>
        </section>

        <section class="course-details">
            <div class="course-info" data-aos="fade-right">
                <h2>Kategorie Prawa Jazdy</h2>
                <ul>
                    <li>Kategoria B - samochody osobowe (30 godz. teorii + 30 godz. praktyki)</li>
                    <li>Kategoria A - motocykle (20 godz. teorii + 20 godz. praktyki)</li>
                    <li>Kategoria C - samochody ciężarowe (20 godz. teorii + 30 godz. praktyki)</li>
                    <li>Kategoria D - autobusy (20 godz. teorii + 40 godz. praktyki)</li>
                    <li>Kategoria A2 - motocykle do 35kW (20 godz. teorii + 20 godz. praktyki)</li>
                    <li>Kategoria B+E - samochód z przyczepą (20 godz. teorii + 15 godz. praktyki)</li>
                </ul>
            </div>

            <div class="course-benefits" data-aos="fade-left">
                <h2>Korzyści z Naszego Kursu</h2>
                <ul>
                    <li>Doświadczeni instruktorzy z min. 10-letnim stażem</li>
                    <li>Nowoczesne pojazdy szkoleniowe (nie starsze niż 2 lata)</li>
                    <li>Elastyczny harmonogram zajęć (również weekendy)</li>
                    <li>Materiały szkoleniowe w cenie kursu</li>
                    <li>Bezpłatne konsultacje z instruktorem</li>
                    <li>Możliwość płatności w ratach</li>
                    <li>Wsparcie w załatwianiu formalności</li>
                    <li>Darmowe materiały do nauki online</li>
                </ul>
            </div>
        </section>

        <section class="learning-process">
            <h2 data-aos="fade-up">Proces Nauki</h2>
            <div class="process-grid">
                <div class="process-item" data-aos="fade-up" data-aos-delay="100">
                    <h3><i class="fas fa-book"></i> Część Teoretyczna</h3>
                    <ul>
                        <li>Przepisy ruchu drogowego</li>
                        <li>Zasady bezpieczeństwa</li>
                        <li>Pierwsza pomoc</li>
                        <li>Technika kierowania pojazdem</li>
                        <li>Multimedia i materiały interaktywne</li>
                        <li>Testy egzaminacyjne online</li>
                    </ul>
                </div>
                <div class="process-item" data-aos="fade-up" data-aos-delay="200">
                    <h3><i class="fas fa-car"></i> Zajęcia Praktyczne</h3>
                    <ul>
                        <li>Nauka na placu manewrowym</li>
                        <li>Jazda w ruchu miejskim</li>
                        <li>Jazda w różnych warunkach</li>
                        <li>Eco-driving</li>
                        <li>Parkowanie i manewry</li>
                        <li>Symulacja trasy egzaminacyjnej</li>
                    </ul>
                </div>
                <div class="process-item" data-aos="fade-up" data-aos-delay="300">
                    <h3><i class="fas fa-graduation-cap"></i> Przygotowanie do Egzaminu</h3>
                    <ul>
                        <li>Egzaminy próbne</li>
                        <li>Analiza błędów</li>
                        <li>Konsultacje indywidualne</li>
                        <li>Materiały dodatkowe</li>
                        <li>Wsparcie psychologiczne</li>
                        <li>Jazdy uzupełniające</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="exam-info">
            <h2 data-aos="fade-up">Informacje o Egzaminie Państwowym</h2>
            <div class="exam-grid">
                <div class="exam-item" data-aos="fade-right">
                    <h3>Egzamin Teoretyczny</h3>
                    <p>32 pytania, 20 minut, minimum 68 punktów do zdania</p>
                    <ul>
                        <li>Pytania podstawowe: 20</li>
                        <li>Pytania specjalistyczne: 12</li>
                        <li>Dostępne w 7 językach</li>
                        <li>Możliwość próbnego testu</li>
                    </ul>
                </div>
                <div class="exam-item" data-aos="fade-left">
                    <h3>Egzamin Praktyczny</h3>
                    <p>Około 40 minut jazdy, ocena umiejętności praktycznych</p>
                    <ul>
                        <li>Plac manewrowy</li>
                        <li>Jazda w ruchu miejskim</li>
                        <li>Wykonywanie manewrów</li>
                        <li>Eco-driving</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="additional-services">
            <h2 data-aos="fade-up">Usługi Dodatkowe</h2>
            <div class="services-grid">
                <div class="service-item" data-aos="fade-up" data-aos-delay="100">
                    <h3>Jazdy Doszkalające</h3>
                    <ul>
                        <li>Jazda w nocy</li>
                        <li>Jazda autostradowa</li>
                        <li>Parkowanie równoległe</li>
                        <li>Jazda w trudnych warunkach</li>
                    </ul>
                </div>
                <div class="service-item" data-aos="fade-up" data-aos-delay="200">
                    <h3>Kursy Specjalistyczne</h3>
                    <ul>
                        <li>Eco-driving</li>
                        <li>Jazda defensywna</li>
                        <li>Pierwsza pomoc</li>
                        <li>Obsługa techniczna pojazdu</li>
                    </ul>
                </div>
                <div class="service-item" data-aos="fade-up" data-aos-delay="300">
                    <h3>Wsparcie Dodatkowe</h3>
                    <ul>
                        <li>Konsultacje online</li>
                        <li>Materiały video</li>
                        <li>Aplikacja mobilna</li>
                        <li>Pomoc w dokumentach</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="funding-section">
            <h2 data-aos="fade-up">Dostępne Dofinansowania</h2>
            <div class="funding-options">
                <div class="funding-option" data-aos="fade-up" data-aos-delay="100">
                    <h3>Dofinansowanie z Urzędu Pracy</h3>
                    <p>Dla osób bezrobotnych i poszukujących pracy:</p>
                    <ul>
                        <li>Do 80% wartości kursu</li>
                        <li>Wymagane zaświadczenie z UP</li>
                        <li>Pomoc w wypełnieniu wniosku</li>
                    </ul>
                </div>

                <div class="funding-option" data-aos="fade-up" data-aos-delay="200">
                    <h3>Dofinansowanie z Funduszy EU</h3>
                    <p>W ramach projektów unijnych:</p>
                    <ul>
                        <li>Do 70% wartości kursu</li>
                        <li>Dla osób do 30 roku życia</li>
                        <li>Dostępne w ramach projektu "Młodzi kierowcy"</li>
                    </ul>
                </div>

                <div class="funding-option" data-aos="fade-up" data-aos-delay="300">
                    <h3>Bon Szkoleniowy</h3>
                    <p>Dla pracujących:</p>
                    <ul>
                        <li>Do 50% wartości kursu</li>
                        <li>Z Krajowego Funduszu Szkoleniowego</li>
                        <li>Dla firm i pracowników</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="pricing">
            <h2 data-aos="fade-up">Cennik Kursów</h2>
            <div class="price-cards">
                <div class="price-card" data-aos="fade-up" data-aos-delay="100">
                    <h3>Kategoria B</h3>
                    <p class="price">2500 PLN</p>
                    <ul>
                        <li>30 godzin teorii</li>
                        <li>30 godzin praktyki</li>
                        <li>Materiały szkoleniowe</li>
                        <li>E-learning</li>
                        <li>Konsultacje z instruktorem</li>
                        <li>Jazdy dodatkowe: 90 PLN/h</li>
                    </ul>
                    <div class="course-actions">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="zapisz_kurs.php?kategoria=B" class="btn primary">Zapisz się na kurs</a>
                        <?php else: ?>
                            <a href="login.php" class="btn primary">Zaloguj się aby się zapisać</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="price-card" data-aos="fade-up" data-aos-delay="200">
                    <h3>Kategoria A</h3>
                    <p class="price">2200 PLN</p>
                    <ul>
                        <li>20 godzin teorii</li>
                        <li>20 godzin praktyki</li>
                        <li>Materiały szkoleniowe</li>
                        <li>E-learning</li>
                        <li>Sprzęt ochronny</li>
                        <li>Jazdy dodatkowe: 100 PLN/h</li>
                    </ul>
                    <div class="course-actions">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="zapisz_kurs.php?kategoria=A" class="btn primary">Zapisz się na kurs</a>
                        <?php else: ?>
                            <a href="login.php" class="btn primary">Zaloguj się aby się zapisać</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="price-card" data-aos="fade-up" data-aos-delay="300">
                    <h3>Kategoria C</h3>
                    <p class="price">3500 PLN</p>
                    <ul>
                        <li>20 godzin teorii</li>
                        <li>30 godzin praktyki</li>
                        <li>Materiały szkoleniowe</li>
                        <li>E-learning</li>
                        <li>Kwalifikacja wstępna</li>
                        <li>Jazdy dodatkowe: 150 PLN/h</li>
                    </ul>
                    <div class="course-actions">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="zapisz_kurs.php?kategoria=C" class="btn primary">Zapisz się na kurs</a>
                        <?php else: ?>
                            <a href="login.php" class="btn primary">Zaloguj się aby się zapisać</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="additional-info">
            <h2>Informacje Dodatkowe</h2>
            <div class="info-grid">
                <div class="info-item">
                    <h3>Płatności</h3>
                    <ul>
                        <li>Możliwość płatności w ratach (0% odsetek)</li>
                        <li>Płatność kartą lub gotówką</li>
                        <li>Faktury VAT</li>
                        <li>Rabaty dla grup</li>
                    </ul>
                </div>
                <div class="info-item">
                    <h3>Terminy</h3>
                    <ul>
                        <li>Kursy rozpoczynają się co tydzień</li>
                        <li>Zajęcia teoretyczne online lub stacjonarnie</li>
                        <li>Elastyczne godziny jazd</li>
                        <li>Możliwość przyspieszenia kursu</li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="contact-info" data-aos="fade-right">
                <h4>Kontakt</h4>
                <p>Telefon: +48 123 456 789</p>
                <p>Email: biuro@linianauka.pl</p>
            </div>
            <div class="social-links" data-aos="fade-up">
                <a href="#" class="social-icon">Facebook</a>
                <a href="#" class="social-icon">Instagram</a>
                <a href="#" class="social-icon">LinkedIn</a>
            </div>
            <div class="copyright" data-aos="fade-left">
                <p>&copy; 2025 Linia Nauka Jazdy. Wszystkie prawa zastrzeżone.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
