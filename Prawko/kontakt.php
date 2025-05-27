<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - Linia Nauka Jazdy</title>
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
                <li><a href="badania.php">Badania</a></li>
                <li><a href="oplaty.php">Opłaty</a></li>
                <li><a href="kontakt.php" class="active">Kontakt</a></li>
                <li><a href="login.php">Zaloguj</a></li>
            </ul>
        </nav>
    </header>

    <main class="contact-page">
        <section class="page-header" data-aos="fade-down">
            <h1>Skontaktuj się z Nami</h1>
            <p>Jesteśmy do Twojej dyspozycji. Wybierz najwygodniejszą formę kontaktu.</p>
        </section>

        <section class="contact-content">
            <div class="contact-info-cards">
                <div class="contact-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Adres</h3>
                    <p>ul. Przykładowa 123</p>
                    <p>00-000 Warszawa</p>
                </div>

                <div class="contact-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Telefon</h3>
                    <p>+48 123 456 789</p>
                    <p>+48 987 654 321</p>
                </div>

                <div class="contact-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email</h3>
                    <p>biuro@linianauka.pl</p>
                    <p>rekrutacja@linianauka.pl</p>
                </div>

                <div class="contact-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Godziny otwarcia</h3>
                    <p>Pon-Pt: 8:00 - 20:00</p>
                    <p>Sob: 9:00 - 15:00</p>
                </div>
            </div>

            <div class="contact-form-section" data-aos="fade-up">
                <h2>Formularz kontaktowy</h2>
                <form class="contact-form" data-aos="fade-up" data-aos-delay="100">
                    <div class="form-group">
                        <label for="name">Imię i Nazwisko</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="tel" id="phone" name="phone">
                    </div>

                    <div class="form-group">
                        <label for="subject">Temat</label>
                        <select id="subject" name="subject" required>
                            <option value="">Wybierz temat</option>
                            <option value="kurs-b">Kurs kategorii B</option>
                            <option value="kurs-a">Kurs kategorii A</option>
                            <option value="kurs-c">Kurs kategorii C</option>
                            <option value="kurs-d">Kurs kategorii D</option>
                            <option value="inne">Inne</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Wiadomość</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-container">
                            <input type="checkbox" required>
                            <span class="checkmark"></span>
                            Wyrażam zgodę na przetwarzanie moich danych osobowych
                        </label>
                    </div>

                    <button type="submit" class="btn primary">Wyślij wiadomość</button>
                </form>
            </div>
        </section>

        <section class="map-section">
            <h2 data-aos="fade-up">Nasza lokalizacja</h2>
            <div class="map-container" data-aos="zoom-in">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2443.3888787907547!2d21.017229776191437!3d52.23226947201275!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTLCsDEzJzU2LjIiTiAyMcKwMDEnMTQuMCJF!5e0!3m2!1spl!2spl!4v1620000000000!5m2!1spl!2spl" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
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