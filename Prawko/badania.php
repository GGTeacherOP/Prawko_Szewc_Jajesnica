<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badania Lekarskie i Psychologiczne - Linia Nauka Jazdy</title>
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
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="kurs_prawa_jazdy.php">Kurs Prawa Jazdy</a></li>
                <li><a href="kurs_instruktorow.php">Kursy dla Instruktorów</a></li>
                <li><a href="kurs_kierowcow.php">Kursy Kierowców Zawodowych</a></li>
                <li><a href="kurs_operatorow.php">Kursy Operatorów Maszyn</a></li>
                <li><a href="badania.php" class="active">Badania</a></li>
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

    <main>
        <section class="page-header" data-aos="fade-down">
            <h1>Badania Lekarskie i Psychologiczne</h1>
            <p>Kompleksowe badania dla kandydatów na kierowców i kierowców zawodowych</p>
        </section>

        <section class="course-content">
            <div class="course-intro" data-aos="fade-up">
                <h2>Centrum Badań Transportowych</h2>
                <p>Oferujemy pełen zakres badań wymaganych do uzyskania i przedłużenia uprawnień do kierowania pojazdami. Nasze centrum medyczne współpracuje z doświadczonymi specjalistami i posiada nowoczesny sprzęt diagnostyczny.</p>
            </div>

            <div class="requirements-grid">
                <div class="requirement-card" data-aos="fade-up" data-aos-delay="100">
                    <h3><i class="fas fa-stethoscope"></i> Badania Lekarskie</h3>
                    <ul>
                        <li>Ogólne badanie stanu zdrowia</li>
                        <li>Badanie wzroku i widzenia barw</li>
                        <li>Badanie słuchu i równowagi</li>
                        <li>Badanie układu ruchu</li>
                        <li>EKG (dla kierowców zawodowych)</li>
                        <li>Konsultacje specjalistyczne</li>
                    </ul>
                </div>
                <div class="requirement-card" data-aos="fade-up" data-aos-delay="200">
                    <h3><i class="fas fa-brain"></i> Badania Psychologiczne</h3>
                    <ul>
                        <li>Ocena sprawności psychomotorycznej</li>
                        <li>Badanie czasu reakcji</li>
                        <li>Test koordynacji wzrokowo-ruchowej</li>
                        <li>Ocena koncentracji uwagi</li>
                        <li>Badanie osobowości</li>
                        <li>Testy psychologiczne</li>
                    </ul>
                </div>
                <div class="requirement-card" data-aos="fade-up" data-aos-delay="300">
                    <h3><i class="fas fa-user-check"></i> Dla Kogo</h3>
                    <ul>
                        <li>Kandydaci na kierowców</li>
                        <li>Kierowcy zawodowi</li>
                        <li>Instruktorzy nauki jazdy</li>
                        <li>Operatorzy maszyn</li>
                        <li>Kierowcy pojazdów uprzywilejowanych</li>
                        <li>Kierowcy po wypadkach</li>
                    </ul>
                </div>
            </div>

            <section class="exam-info">
                <h2 data-aos="fade-up">Informacje o Badaniach</h2>
                <div class="exam-grid">
                    <div class="exam-item" data-aos="fade-right">
                        <h3>Okres Ważności</h3>
                        <ul>
                            <li>5 lat dla kierowców zawodowych</li>
                            <li>15 lat dla kierowców osobowych (do 55 roku życia)</li>
                            <li>5 lat dla kierowców powyżej 55 roku życia</li>
                            <li>2 lata dla kierowców z ograniczeniami zdrowotnymi</li>
                        </ul>
                    </div>
                    <div class="exam-item" data-aos="fade-left">
                        <h3>Wymagane Dokumenty</h3>
                        <ul>
                            <li>Dokument tożsamości</li>
                            <li>Aktualne zdjęcie</li>
                            <li>Poprzednie orzeczenie (jeśli dotyczy)</li>
                            <li>Dokumentacja medyczna (jeśli jest)</li>
                        </ul>
                    </div>
                </div>
            </section>

            <div class="pricing-tables">
                <div class="pricing-table" data-aos="fade-up" data-aos-delay="100">
                    <div class="pricing-header">
                        <h3>Badania Podstawowe</h3>
                        <div class="pricing-price">200 PLN</div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li>Badanie lekarskie</li>
                            <li>Badanie wzroku</li>
                            <li>Orzeczenie lekarskie</li>
                            <li>Wpis do systemu</li>
                            <li>Konsultacja wyników</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="umow_badanie.php?typ=podstawowe" class="btn primary">Umów badanie</a>
                        <?php else: ?>
                            <a href="login.php" class="btn primary">Zaloguj się aby umówić</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="pricing-table" data-aos="fade-up" data-aos-delay="200">
                    <div class="pricing-header">
                        <h3>Badania Zawodowe</h3>
                        <div class="pricing-price">350 PLN</div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li>Wszystkie badania podstawowe</li>
                            <li>Badania psychologiczne</li>
                            <li>EKG</li>
                            <li>Badanie psychotechniczne</li>
                            <li>Konsultacje specjalistyczne</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="umow_badanie.php?typ=zawodowe" class="btn primary">Umów badanie</a>
                        <?php else: ?>
                            <a href="login.php" class="btn primary">Zaloguj się aby umówić</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="pricing-table" data-aos="fade-up" data-aos-delay="300">
                    <div class="pricing-header">
                        <h3>Pakiet Instruktorski</h3>
                        <div class="pricing-price">450 PLN</div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li>Wszystkie badania zawodowe</li>
                            <li>Rozszerzone testy psychologiczne</li>
                            <li>Badanie widzenia zmierzchowego</li>
                            <li>Ocena predyspozycji zawodowych</li>
                            <li>Konsultacja z psychologiem</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="#" class="btn primary" onclick="checkLoginAndRedirect(event)">Umów badanie</a>
                    </div>
                </div>
            </div>

            <section class="additional-info">
                <h2 data-aos="fade-up">Dodatkowe Informacje</h2>
                <div class="info-grid">
                    <div class="info-item" data-aos="fade-right">
                        <h3><i class="fas fa-clock"></i> Godziny Przyjęć</h3>
                        <ul>
                            <li>Poniedziałek - Piątek: 8:00 - 18:00</li>
                            <li>Sobota: 9:00 - 14:00</li>
                            <li>Możliwość umówienia na konkretną godzinę</li>
                            <li>Badania również w języku angielskim</li>
                        </ul>
                    </div>
                    <div class="info-item" data-aos="fade-left">
                        <h3><i class="fas fa-info-circle"></i> Ważne Informacje</h3>
                        <ul>
                            <li>Wyniki badań w tym samym dniu</li>
                            <li>Bezpłatne konsultacje wyników</li>
                            <li>Możliwość płatności kartą</li>
                            <li>Parking dla pacjentów</li>
                        </ul>
                    </div>
                </div>
            </section>
        </section>

        <section class="cta-section">
            <div class="cta-content" data-aos="zoom-in">
                <h2>Umów się na Badania</h2>
                <p>Nie czekaj - wykonaj niezbędne badania już dziś! Szybko, profesjonalnie i w przystępnej cenie.</p>
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