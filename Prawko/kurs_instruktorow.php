<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kursy dla Instruktorów - Linia Nauka Jazdy</title>
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
            <h1>Kursy dla Instruktorów</h1>
            <p>Zostań profesjonalnym instruktorem nauki jazdy</p>
        </section>

        <section class="course-content">
            <div class="course-intro" data-aos="fade-up">
                <h2>Dlaczego warto zostać instruktorem?</h2>
                <p>Zawód instruktora nauki jazdy to nie tylko praca, to pasja i możliwość rozwoju. Oferujemy kompleksowe szkolenie, które przygotuje Cię do tej odpowiedzialnej roli.</p>
                <div class="career-prospects" data-aos="fade-up" data-aos-delay="100">
                    <h3>Perspektywy Zawodowe</h3>
                    <ul>
                        <li>Stabilne zatrudnienie w szkołach jazdy</li>
                        <li>Możliwość prowadzenia własnej działalności</li>
                        <li>Elastyczne godziny pracy</li>
                        <li>Satysfakcjonujące wynagrodzenie</li>
                        <li>Ciągły rozwój zawodowy</li>
                        <li>Uznawane kwalifikacje w całej UE</li>
                    </ul>
                </div>
            </div>

            <div class="course-requirements">
                <h2 data-aos="fade-up">Wymagania dla kandydatów</h2>
                <div class="requirements-grid">
                    <div class="requirement-card" data-aos="fade-up" data-aos-delay="100">
                        <h3><i class="fas fa-id-card"></i> Podstawowe wymagania</h3>
                        <ul>
                            <li>Ukończone 23 lata</li>
                            <li>Wykształcenie średnie</li>
                            <li>Prawo jazdy kategorii B od min. 3 lat</li>
                            <li>Niekaralność</li>
                            <li>Znajomość języka polskiego</li>
                            <li>Zdolność pedagogiczna</li>
                        </ul>
                    </div>
                    <div class="requirement-card" data-aos="fade-up" data-aos-delay="200">
                        <h3><i class="fas fa-heart"></i> Stan zdrowia</h3>
                        <ul>
                            <li>Dobry stan zdrowia</li>
                            <li>Badania psychologiczne</li>
                            <li>Brak przeciwwskazań do wykonywania zawodu</li>
                            <li>Dobry wzrok i słuch</li>
                            <li>Sprawność fizyczna</li>
                            <li>Odporność na stres</li>
                        </ul>
                    </div>
                    <div class="requirement-card" data-aos="fade-up" data-aos-delay="300">
                        <h3><i class="fas fa-book"></i> Dokumenty</h3>
                        <ul>
                            <li>Wniosek o rozpoczęcie kursu</li>
                            <li>Zaświadczenie o niekaralności</li>
                            <li>Orzeczenie lekarskie i psychologiczne</li>
                            <li>Kopia prawa jazdy</li>
                            <li>Zdjęcia do dokumentów</li>
                            <li>Dowód osobisty</li>
                        </ul>
                    </div>
                </div>
            </div>

            <section class="course-modules">
                <h2 data-aos="fade-up">Szczegółowy Program Kursu</h2>
                <div class="modules-grid">
                    <div class="module-item" data-aos="fade-right" data-aos-delay="100">
                        <h3>Moduł 1: Psychologia Nauczania</h3>
                        <ul>
                            <li>Podstawy psychologii</li>
                            <li>Techniki nauczania dorosłych</li>
                            <li>Radzenie sobie ze stresem</li>
                            <li>Komunikacja interpersonalna</li>
                            <li>Motywowanie kursantów</li>
                        </ul>
                    </div>
                    <div class="module-item" data-aos="fade-up" data-aos-delay="200">
                        <h3>Moduł 2: Metodyka Nauczania</h3>
                        <ul>
                            <li>Planowanie zajęć</li>
                            <li>Prowadzenie wykładów</li>
                            <li>Zajęcia praktyczne</li>
                            <li>Ocena postępów</li>
                            <li>Dokumentacja szkoleniowa</li>
                        </ul>
                    </div>
                    <div class="module-item" data-aos="fade-left" data-aos-delay="300">
                        <h3>Moduł 3: Przepisy i Technika</h3>
                        <ul>
                            <li>Prawo o ruchu drogowym</li>
                            <li>Technika kierowania pojazdem</li>
                            <li>Zasady bezpieczeństwa</li>
                            <li>Pierwsza pomoc</li>
                            <li>Budowa i eksploatacja pojazdów</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="practical-training">
                <h2 data-aos="fade-up">Praktyka Zawodowa</h2>
                <div class="training-content">
                    <div class="training-item" data-aos="fade-right">
                        <h3>Zajęcia z Kursantami</h3>
                        <ul>
                            <li>Obserwacja doświadczonych instruktorów</li>
                            <li>Prowadzenie zajęć pod nadzorem</li>
                            <li>Samodzielne prowadzenie zajęć</li>
                            <li>Feedback i ocena postępów</li>
                        </ul>
                    </div>
                    <div class="training-item" data-aos="fade-left">
                        <h3>Dokumentacja i Administracja</h3>
                        <ul>
                            <li>Prowadzenie dokumentacji kursanta</li>
                            <li>Planowanie harmonogramu zajęć</li>
                            <li>Współpraca z WORD</li>
                            <li>Obsługa systemów OSK</li>
                        </ul>
                    </div>
                </div>
            </section>

            <div class="timeline">
                <h2 data-aos="fade-up">Przebieg kursu</h2>
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="timeline-content">
                        <h3>Etap 1: Zajęcia teoretyczne</h3>
                        <p>140 godzin zajęć z zakresu psychologii, metodyki nauczania, przepisów ruchu drogowego i techniki jazdy.</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="timeline-content">
                        <h3>Etap 2: Zajęcia praktyczne</h3>
                        <p>60 godzin praktycznej nauki prowadzenia szkoleń, w tym techniki jazdy i metodyki nauczania.</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="timeline-content">
                        <h3>Etap 3: Praktyki pedagogiczne</h3>
                        <p>50 godzin praktyk z doświadczonym instruktorem, obserwacja i prowadzenie zajęć.</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="timeline-content">
                        <h3>Etap 4: Egzamin wewnętrzny</h3>
                        <p>Sprawdzenie wiedzy i umiejętności przed przystąpieniem do egzaminu państwowego.</p>
                    </div>
                </div>
            </div>

            <div class="pricing-tables">
                <div class="pricing-table" data-aos="fade-up" data-aos-delay="100">
                    <?php
                    require_once 'config.php';
                    require_once 'course_enrollment_button.php';
                    displayEnrollmentButton('Instruktorzy', $conn);
                    ?>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content" data-aos="zoom-in">
                <h2>Rozpocznij Karierę Instruktora</h2>
                <p>Dołącz do grona profesjonalnych instruktorów nauki jazdy. Zapisz się na kurs już dziś!</p>
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