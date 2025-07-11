<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kursy Operatorów Maszyn - Linia Nauka Jazdy</title>
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
            <h1>Kursy Operatorów Maszyn</h1>
            <p>Profesjonalne szkolenia na operatorów różnych typów maszyn i urządzeń</p>
        </section>

        <section class="course-content">
            <div class="course-intro" data-aos="fade-up">
                <h2>Zostań Wykwalifikowanym Operatorem</h2>
                <p>Oferujemy szeroki wybór kursów dla operatorów maszyn budowlanych, drogowych i magazynowych. Nasze szkolenia są zgodne z wymogami UDT i IMBiGS.</p>
                
                <div class="operator-market" data-aos="fade-up" data-aos-delay="100">
                    <h3>Perspektywy Zawodowe</h3>
                    <ul>
                        <li>Wynagrodzenie od 5000 do 9000 PLN</li>
                        <li>Duże zapotrzebowanie na rynku</li>
                        <li>Możliwość pracy w kraju i za granicą</li>
                        <li>Rozwój w branży budowlanej</li>
                        <li>Stabilne zatrudnienie</li>
                        <li>Możliwość prowadzenia własnej działalności</li>
                    </ul>
                </div>
            </div>

            <div class="machine-categories">
                <h2 data-aos="fade-up">Kategorie Maszyn</h2>
                <div class="categories-grid">
                    <div class="category-item" data-aos="fade-right" data-aos-delay="100">
                        <h3>Maszyny Budowlane</h3>
                        <div class="machine-types">
                            <div class="machine-type" data-aos="fade-up" data-aos-delay="150">
                                <h4>Koparki</h4>
                                <ul>
                                    <li>Koparki jednonaczyniowe</li>
                                    <li>Koparko-ładowarki</li>
                                    <li>Koparki gąsienicowe</li>
                                    <li>Mini koparki</li>
                                </ul>
                            </div>
                            <div class="machine-type" data-aos="fade-up" data-aos-delay="200">
                                <h4>Ładowarki</h4>
                                <ul>
                                    <li>Ładowarki teleskopowe</li>
                                    <li>Ładowarki kołowe</li>
                                    <li>Ładowarki przegubowe</li>
                                    <li>Mini ładowarki</li>
                                </ul>
                            </div>
                            <div class="machine-type" data-aos="fade-up" data-aos-delay="250">
                                <h4>Sprzęt Ciężki</h4>
                                <ul>
                                    <li>Spycharki</li>
                                    <li>Zgarniarki</li>
                                    <li>Równiarki</li>
                                    <li>Walce drogowe</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="category-item" data-aos="fade-up" data-aos-delay="200">
                        <h3>Maszyny Magazynowe</h3>
                        <div class="machine-types">
                            <div class="machine-type" data-aos="fade-up" data-aos-delay="150">
                                <h4>Wózki Widłowe</h4>
                                <ul>
                                    <li>Wózki czołowe</li>
                                    <li>Wózki boczne</li>
                                    <li>Wózki wysokiego składowania</li>
                                    <li>Wózki specjalistyczne</li>
                                </ul>
                            </div>
                            <div class="machine-type" data-aos="fade-up" data-aos-delay="200">
                                <h4>Suwnice i Żurawie</h4>
                                <ul>
                                    <li>Suwnice pomostowe</li>
                                    <li>Żurawie wieżowe</li>
                                    <li>Żurawie samojezdne</li>
                                    <li>HDS</li>
                                </ul>
                            </div>
                            <div class="machine-type" data-aos="fade-up" data-aos-delay="250">
                                <h4>Platformy</h4>
                                <ul>
                                    <li>Podnośniki nożycowe</li>
                                    <li>Podnośniki teleskopowe</li>
                                    <li>Podesty ruchome</li>
                                    <li>Platformy załadowcze</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="category-item" data-aos="fade-left" data-aos-delay="300">
                        <h3>Maszyny Drogowe</h3>
                        <div class="machine-types">
                            <div class="machine-type" data-aos="fade-up" data-aos-delay="150">
                                <h4>Maszyny do Nawierzchni</h4>
                                <ul>
                                    <li>Rozściełacze</li>
                                    <li>Frezarki</li>
                                    <li>Skrapiarki</li>
                                    <li>Recyklery</li>
                                </ul>
                            </div>
                            <div class="machine-type" data-aos="fade-up" data-aos-delay="200">
                                <h4>Zagęszczarki</h4>
                                <ul>
                                    <li>Walce wibracyjne</li>
                                    <li>Ubijaki</li>
                                    <li>Płyty wibracyjne</li>
                                    <li>Zagęszczarki gruntu</li>
                                </ul>
                            </div>
                            <div class="machine-type" data-aos="fade-up" data-aos-delay="250">
                                <h4>Sprzęt Pomocniczy</h4>
                                <ul>
                                    <li>Przecinarki</li>
                                    <li>Piły do asfaltu</li>
                                    <li>Szczotki mechaniczne</li>
                                    <li>Zamiatarki</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="training-process">
                <h2 data-aos="fade-up">Proces Szkolenia</h2>
                <div class="process-content">
                    <div class="process-item" data-aos="fade-right" data-aos-delay="100">
                        <h3>Teoria</h3>
                        <ul>
                            <li>Budowa i zasady działania maszyn</li>
                            <li>Przepisy BHP</li>
                            <li>Dokumentacja techniczna</li>
                            <li>Zasady bezpiecznej pracy</li>
                            <li>Odpowiedzialność operatora</li>
                        </ul>
                    </div>
                    <div class="process-item" data-aos="fade-up" data-aos-delay="200">
                        <h3>Praktyka</h3>
                        <ul>
                            <li>Codzienna obsługa maszyn</li>
                            <li>Techniki manewrowania</li>
                            <li>Symulacje awarii</li>
                            <li>Praca w różnych warunkach</li>
                            <li>Załadunek i rozładunek</li>
                        </ul>
                    </div>
                    <div class="process-item" data-aos="fade-left" data-aos-delay="300">
                        <h3>Egzamin UDT</h3>
                        <ul>
                            <li>Test teoretyczny</li>
                            <li>Egzamin praktyczny</li>
                            <li>Wydanie uprawnień</li>
                            <li>Certyfikacja</li>
                            <li>Wpis do książki operatora</li>
                        </ul>
                    </div>
                </div>
            </section>

            <div class="pricing-tables">
                <div class="pricing-table" data-aos="fade-up" data-aos-delay="100">
                    <div class="pricing-header">
                        <h3>Kurs Koparki</h3>
                    </div>
                    <?php
                    require_once 'config.php';
                    require_once 'course_enrollment_button.php';
                    displayEnrollmentButton('Operatorzy Maszyn', $conn, 'Kurs Operatora Koparki');
                    ?>
                </div>

                <div class="pricing-table" data-aos="fade-up" data-aos-delay="200">
                    <div class="pricing-header">
                        <h3>Kurs Ładowarki</h3>
                    </div>
                    <?php
                    displayEnrollmentButton('Operatorzy Maszyn', $conn, 'Kurs Operatora Ładowarki');
                    ?>
                </div>

                <div class="pricing-table" data-aos="fade-up" data-aos-delay="300">
                    <div class="pricing-header">
                        <h3>Kurs Wózka Widłowego</h3>
                    </div>
                    <?php
                    displayEnrollmentButton('Operatorzy Maszyn', $conn, 'Kurs Operatora Wózka Widłowego');
                    ?>
                </div>

                <div class="pricing-table" data-aos="fade-up" data-aos-delay="400">
                    <div class="pricing-header">
                        <h3>Kurs Suwnicy</h3>
                    </div>
                    <?php
                    displayEnrollmentButton('Operatorzy Maszyn', $conn, 'Kurs Operatora Suwnicy');
                    ?>
                </div>

                <div class="pricing-table" data-aos="fade-up" data-aos-delay="500">
                    <div class="pricing-header">
                        <h3>Kurs Podnośnika</h3>
                    </div>
                    <?php
                    displayEnrollmentButton('Operatorzy Maszyn', $conn, 'Kurs Operatora Podnośnika');
                    ?>
                </div>

                <div class="pricing-table" data-aos="fade-up" data-aos-delay="600">
                    <div class="pricing-header">
                        <h3>Kurs Maszyn Drogowych</h3>
                    </div>
                    <?php
                    displayEnrollmentButton('Operatorzy Maszyn', $conn, 'Kurs Operatora Maszyn Drogowych');
                    ?>
                </div>
            </div>

            <div class="course-requirements">
                <h2>Informacje o Szkoleniach</h2>
                <div class="requirements-grid">
                    <div class="requirement-card">
                        <h3><i class="fas fa-user-check"></i> Wymagania</h3>
                        <ul>
                            <li>Ukończone 18 lat</li>
                            <li>Wykształcenie minimum podstawowe</li>
                            <li>Dobry stan zdrowia</li>
                            <li>Badania lekarskie</li>
                        </ul>
                    </div>
                    <div class="requirement-card">
                        <h3><i class="fas fa-clock"></i> Organizacja</h3>
                        <ul>
                            <li>Zajęcia w weekendy</li>
                            <li>Małe grupy szkoleniowe</li>
                            <li>Doświadczeni instruktorzy</li>
                            <li>Nowoczesny sprzęt</li>
                        </ul>
                    </div>
                    <div class="requirement-card">
                        <h3><i class="fas fa-certificate"></i> Uprawnienia</h3>
                        <ul>
                            <li>Certyfikat UDT</li>
                            <li>Książeczka operatora</li>
                            <li>Uznawane w całej UE</li>
                            <li>Bezterminowe</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="timeline">
                <h2>Przebieg Szkolenia</h2>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3>Etap 1: Teoria</h3>
                        <p>Zajęcia teoretyczne z zakresu BHP, budowy i eksploatacji maszyn oraz przepisów.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3>Etap 2: Praktyka</h3>
                        <p>Zajęcia praktyczne na maszynach pod okiem doświadczonych instruktorów.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3>Etap 3: Egzamin</h3>
                        <p>Egzamin państwowy przed komisją UDT - teoria i praktyka.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content" data-aos="zoom-in">
                <h2>Rozpocznij Karierę Operatora</h2>
                <p>Zdobądź uprawnienia i rozpocznij pracę w branży!</p>
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