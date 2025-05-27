<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opłaty - Linia Nauka Jazdy</title>
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
            <h1>Opłaty i Formy Płatności</h1>
            <p>Przejrzyste zasady płatności i elastyczne formy finansowania</p>
        </section>

        <section class="course-content">
            <div class="course-intro" data-aos="fade-up">
                <h2>Cennik Kursów</h2>
                <p>Oferujemy konkurencyjne ceny i różne formy płatności. Sprawdź naszą ofertę i wybierz najlepszą opcję dla siebie.</p>
            </div>

            <div class="pricing-tables">
                <div class="pricing-table" data-aos="fade-up" data-aos-delay="100">
                    <div class="pricing-header">
                        <h3>Kurs Podstawowy</h3>
                        <div class="pricing-price">2500 PLN</div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li>30 godzin teorii</li>
                            <li>30 godzin praktyki</li>
                            <li>Materiały szkoleniowe</li>
                            <li>Egzamin wewnętrzny</li>
                            <li>Wsparcie instruktora</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="zapisz_kurs.php?kategoria=B" class="btn primary">Zapisz się</a>
                        <?php else: ?>
                            <a href="login.php" class="btn primary">Zaloguj się aby się zapisać</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pricing-table" data-aos="fade-up" data-aos-delay="200">
                    <div class="pricing-header">
                        <h3>Kurs Rozszerzony</h3>
                        <div class="pricing-price">3000 PLN</div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li>40 godzin teorii</li>
                            <li>40 godzin praktyki</li>
                            <li>Materiały szkoleniowe</li>
                            <li>Egzamin wewnętrzny</li>
                            <li>Dodatkowe konsultacje</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="zapisz_kurs.php?kategoria=C" class="btn primary">Zapisz się</a>
                        <?php else: ?>
                            <a href="login.php" class="btn primary">Zaloguj się aby się zapisać</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pricing-table" data-aos="fade-up" data-aos-delay="300">
                    <div class="pricing-header">
                        <h3>Kwalifikacja Zawodowa</h3>
                        <div class="pricing-price">3500 PLN</div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li>280 godzin szkolenia</li>
                            <li>Materiały szkoleniowe</li>
                            <li>Zajęcia praktyczne</li>
                            <li>Egzamin państwowy</li>
                            <li>Certyfikat kwalifikacji</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="zapisz_kurs.php?kategoria=D" class="btn primary">Zapisz się</a>
                        <?php else: ?>
                            <a href="login.php" class="btn primary">Zaloguj się aby się zapisać</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="course-requirements">
                <h2 data-aos="fade-up">Formy Płatności</h2>
                <div class="requirements-grid">
                    <div class="requirement-card" data-aos="fade-up" data-aos-delay="100">
                        <h3><i class="fas fa-money-bill-wave"></i> Płatność Gotówką</h3>
                        <ul>
                            <li>Wpłata w biurze szkoły</li>
                            <li>Możliwość płatności w ratach</li>
                            <li>Bez dodatkowych opłat</li>
                            <li>Faktura VAT</li>
                        </ul>
                    </div>
                    <div class="requirement-card" data-aos="fade-up" data-aos-delay="200">
                        <h3><i class="fas fa-credit-card"></i> Płatność Kartą</h3>
                        <ul>
                            <li>Wszystkie karty płatnicze</li>
                            <li>Płatność zbliżeniowa</li>
                            <li>Terminal w biurze</li>
                            <li>Bezpieczne transakcje</li>
                        </ul>
                    </div>
                    <div class="requirement-card" data-aos="fade-up" data-aos-delay="300">
                        <h3><i class="fas fa-university"></i> Przelew Bankowy</h3>
                        <ul>
                            <li>Szybkie przelewy online</li>
                            <li>BLIK</li>
                            <li>Przelew tradycyjny</li>
                            <li>Potwierdzenie przelewu</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="course-requirements">
                <h2 data-aos="fade-up">Dofinansowania i Raty</h2>
                <div class="requirements-grid">
                    <div class="requirement-card" data-aos="fade-right">
                        <h3><i class="fas fa-hand-holding-usd"></i> Dofinansowanie z UP</h3>
                        <p>Możliwość uzyskania dofinansowania z Urzędu Pracy do 80% wartości kursu. Pomagamy w wypełnieniu wniosków.</p>
                    </div>
                    <div class="requirement-card" data-aos="fade-up">
                        <h3><i class="fas fa-percentage"></i> Raty 0%</h3>
                        <p>Możliwość rozłożenia płatności na raty 0% bez dodatkowych kosztów. Okres spłaty do 10 miesięcy.</p>
                    </div>
                    <div class="requirement-card" data-aos="fade-left">
                        <h3><i class="fas fa-users"></i> Rabaty Grupowe</h3>
                        <p>Specjalne zniżki dla grup zorganizowanych. Im większa grupa, tym większy rabat.</p>
                    </div>
                </div>
            </div>

            <div class="additional-info">
                <h2 data-aos="fade-up">Dodatkowe Opłaty</h2>
                <div class="info-grid">
                    <div class="info-item" data-aos="fade-right">
                        <h3>Jazdy Dodatkowe</h3>
                        <ul>
                            <li>Kat. B - 90 PLN/h</li>
                            <li>Kat. A - 100 PLN/h</li>
                            <li>Kat. C - 150 PLN/h</li>
                            <li>Kat. D - 180 PLN/h</li>
                        </ul>
                    </div>
                    <div class="info-item" data-aos="fade-up">
                        <h3>Egzaminy Wewnętrzne</h3>
                        <ul>
                            <li>Teoria - 50 PLN</li>
                            <li>Praktyka kat. B - 100 PLN</li>
                            <li>Praktyka kat. C,D - 150 PLN</li>
                            <li>Praktyka kat. A - 100 PLN</li>
                        </ul>
                    </div>
                    <div class="info-item" data-aos="fade-left">
                        <h3>Materiały Dodatkowe</h3>
                        <ul>
                            <li>Podręcznik - 50 PLN</li>
                            <li>Płyta CD - 30 PLN</li>
                            <li>Testy online - 40 PLN</li>
                            <li>Aplikacja - 25 PLN</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content" data-aos="zoom-in">
                <h2>Rozpocznij Szkolenie Już Dziś</h2>
                <p>Skontaktuj się z nami, aby omówić szczegóły finansowania i wybrać najlepszą opcję płatności.</p>
                <a href="kontakt.php" class="hero-btn primary">Skontaktuj się z Nami</a>
            </div>
        </section>

        <div class="contact-section">
            <h2>Masz pytania dotyczące opłat?</h2>
            <p>Skontaktuj się z nami telefonicznie lub mailowo</p>
            <div class="contact-info">
                <div>
                    <i class="fas fa-phone"></i>
                    <p>+48 123 456 789</p>
                </div>
                <div>
                    <i class="fas fa-envelope"></i>
                    <p>info@linianauki.pl</p>
                </div>
            </div>
        </div>
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