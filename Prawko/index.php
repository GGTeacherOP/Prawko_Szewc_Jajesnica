<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linia Nauka Jazdy - Profesjonalna Szkoła Jazdy w Jaśienicy</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style>
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('szkolajazdytlo.png');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 150px 20px;
            margin-top: -80px;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-content h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .about-section {
            padding: 80px 20px;
            background-color: #f8f9fa;
        }

        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .about-image {
            border-radius: 10px;
            overflow: hidden;
        }

        .about-image img {
            width: 100%;
            height: auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .feature-card {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5em;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .testimonials-section {
            background-color: #fff;
            padding: 80px 20px;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
        }

        .testimonial-card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .testimonial-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #666;
        }

        .stats-section {
            background-color: var(--primary-color);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stat-item h3 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .courses-preview {
            padding: 80px 20px;
            background-color: #fff;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
        }

        .course-card {
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .course-card:hover {
            transform: translateY(-5px);
        }

        .course-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        .course-content {
            padding: 20px;
        }

        .cta-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('ins2.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
        }

        .cta-content {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <section class="hero-section">
            <div class="hero-content" data-aos="fade-up">
                <h1>Twoja droga do sukcesu zaczyna się tutaj</h1>
                <p>Profesjonalne szkolenia, doświadczeni instruktorzy i nowoczesna flota pojazdów</p>
                <a href="kurs_prawa_jazdy.php" class="btn primary">Rozpocznij kurs</a>
            </div>
        </section>

        <section class="about-section">
            <div class="about-content">
                <div class="about-text" data-aos="fade-right">
                    <h2>O nas</h2>
                    <p>Od ponad 15 lat kształcimy przyszłych kierowców, instruktorów i operatorów maszyn. Nasza szkoła wyróżnia się profesjonalnym podejściem, wysoką zdawalnością oraz indywidualnym podejściem do każdego kursanta.</p>
                    <p>Oferujemy:</p>
                    <ul>
                        <li>Kursy na wszystkie kategorie prawa jazdy</li>
                        <li>Szkolenia dla instruktorów</li>
                        <li>Kursy dla kierowców zawodowych</li>
                        <li>Szkolenia operatorów maszyn</li>
                        <li>Badania lekarskie i psychologiczne</li>
                    </ul>
                </div>
                <div class="about-image" data-aos="fade-left">
                    <img src="ins1.png" alt="Szkoła jazdy">
                </div>
            </div>
        </section>

        <section class="features-section">
            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up">
                    <div class="feature-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>Nowoczesna flota</h3>
                    <p>Szkolimy na najnowszych modelach pojazdów wyposażonych w systemy wspomagające naukę jazdy</p>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Doświadczeni instruktorzy</h3>
                    <p>Nasi instruktorzy posiadają wieloletnie doświadczenie i niezbędne kwalifikacje</p>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Wysoka zdawalność</h3>
                    <p>90% naszych kursantów zdaje egzamin państwowy za pierwszym razem</p>
                </div>
            </div>
        </section>

        <section class="testimonials-section">
            <h2 class="text-center">Co mówią o nas kursanci</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card" data-aos="fade-up">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">KW</div>
                        <div>
                            <h4>Krzysztof Wiśniewski</h4>
                            <p>Kategoria B</p>
                        </div>
                    </div>
                    <p>"Świetna atmosfera podczas kursu, profesjonalni instruktorzy i indywidualne podejście. Zdałem za pierwszym razem!"</p>
                </div>
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">AN</div>
                        <div>
                            <h4>Anna Nowak</h4>
                            <p>Kategoria A</p>
                        </div>
                    </div>
                    <p>"Bardzo profesjonalne podejście do nauki jazdy na motocyklu. Instruktorzy zwracają uwagę na każdy szczegół."</p>
                </div>
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">MK</div>
                        <div>
                            <h4>Marek Kowalski</h4>
                            <p>Kurs instruktorski</p>
                        </div>
                    </div>
                    <p>"Najlepszy kurs instruktorski w okolicy. Świetnie przygotowuje do zawodu instruktora nauki jazdy."</p>
                </div>
            </div>
        </section>

        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-item" data-aos="fade-up">
                    <h3>15+</h3>
                    <p>Lat doświadczenia</p>
                </div>
                <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                    <h3>50+</h3>
                    <p>Wykwalifikowanych instruktorów</p>
                </div>
                <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                    <h3>10000+</h3>
                    <p>Zadowolonych kursantów</p>
                </div>
                <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                    <h3>90%</h3>
                    <p>Zdawalność</p>
                </div>
            </div>
        </section>

        <section class="courses-preview">
            <h2 class="text-center">Popularne kursy</h2>
            <div class="courses-grid">
                <div class="course-card" data-aos="fade-up">
                    <div class="course-image" style="background-image: url('ins1.png');"></div>
                    <div class="course-content">
                        <h3>Kurs prawa jazdy kat. B</h3>
                        <p>Najpopularniejszy kurs dla przyszłych kierowców samochodów osobowych.</p>
                        <a href="kurs_prawa_jazdy.php" class="btn primary">Więcej informacji</a>
                    </div>
                </div>
                <div class="course-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="course-image" style="background-image: url('ins2.jpg');"></div>
                    <div class="course-content">
                        <h3>Kurs na instruktora</h3>
                        <p>Profesjonalne szkolenie dla przyszłych instruktorów nauki jazdy.</p>
                        <a href="kurs_instruktorow.php" class="btn primary">Więcej informacji</a>
                    </div>
                </div>
                <div class="course-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="course-image" style="background-image: url('ins3.jpg');"></div>
                    <div class="course-content">
                        <h3>Kwalifikacja zawodowa</h3>
                        <p>Kursy dla kierowców zawodowych i przewozu osób.</p>
                        <a href="kurs_kierowcow.php" class="btn primary">Więcej informacji</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content" data-aos="zoom-in">
                <h2>Rozpocznij swoją przygodę z kierownicą</h2>
                <p>Zapisz się na kurs już dziś i skorzystaj z promocyjnych cen</p>
                <a href="kurs_prawa_jazdy.php" class="btn primary">Zapisz się na kurs</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="contact-info">
                <h4>Kontakt</h4>
                <p>Telefon: +48 123 456 789</p>
                <p>Email: biuro@linianauka.pl</p>
                <p>Adres: ul. Przykładowa 123, Jaśienica</p>
            </div>
            <div class="social-links">
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="copyright">
                <p>&copy; 2024 Linia Nauka Jazdy. Wszystkie prawa zastrzeżone.</p>
            </div>
        </div>
    </footer>

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
</body>
</html> 