<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kursy - Linia Nauka Jazdy</title>
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
            <h1>Nasze Kursy</h1>
            <p>Profesjonalne szkolenia prowadzone przez doświadczonych instruktorów</p>
        </section>

        <section class="courses-grid">
            <div class="course-card" data-aos="fade-up" data-aos-delay="100">
                <h2><i class="fas fa-car"></i> Kurs Prawa Jazdy</h2>
                <p>Kompleksowe szkolenie przygotowujące do egzaminu na prawo jazdy kategorii A, B, C, D i innych.</p>
                <ul>
                    <li>Teoria i praktyka</li>
                    <li>Doświadczeni instruktorzy</li>
                    <li>Nowoczesne pojazdy</li>
                    <li>Elastyczny harmonogram</li>
                </ul>
                <a href="kurs_prawa_jazdy.php" class="btn">Dowiedz się więcej</a>
            </div>

            <div class="course-card" data-aos="fade-up" data-aos-delay="200">
                <h2><i class="fas fa-chalkboard-teacher"></i> Kurs Instruktorów</h2>
                <p>Szkolenie dla przyszłych instruktorów nauki jazdy - zdobądź uprawnienia do nauczania innych.</p>
                <ul>
                    <li>Teoria i metodyka</li>
                    <li>Praktyczne zajęcia</li>
                    <li>Egzamin wewnętrzny</li>
                    <li>Certyfikat ukończenia</li>
                </ul>
                <a href="kurs_instruktorow.php" class="btn">Dowiedz się więcej</a>
            </div>

            <div class="course-card" data-aos="fade-up" data-aos-delay="300">
                <h2><i class="fas fa-truck"></i> Kurs Operatorów</h2>
                <p>Szkolenie na operatorów wózków widłowych i innych urządzeń transportu bliskiego.</p>
                <ul>
                    <li>Teoria UDT</li>
                    <li>Praktyczne zajęcia</li>
                    <li>Egzamin państwowy</li>
                    <li>Certyfikat UDT</li>
                </ul>
                <a href="kurs_operatorow.php" class="btn">Dowiedz się więcej</a>
            </div>

            <div class="course-card" data-aos="fade-up" data-aos-delay="400">
                <h2><i class="fas fa-user-tie"></i> Kurs Kierowców</h2>
                <p>Szkolenia dla kierowców zawodowych - kwalifikacja wstępna i szkolenia okresowe.</p>
                <ul>
                    <li>Kwalifikacja wstępna</li>
                    <li>Szkolenia okresowe</li>
                    <li>ADR - transport materiałów niebezpiecznych</li>
                    <li>Certyfikaty UE</li>
                </ul>
                <a href="kurs_kierowcow.php" class="btn">Dowiedz się więcej</a>
            </div>
        </section>

        <section class="benefits" data-aos="fade-up">
            <h2>Dlaczego warto wybrać nasze kursy?</h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Doświadczeni Instruktorzy</h3>
                    <p>Nasi instruktorzy posiadają wieloletnie doświadczenie w nauczaniu.</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-car-side"></i>
                    <h3>Nowoczesny Sprzęt</h3>
                    <p>Dysponujemy nowoczesnymi pojazdami szkoleniowymi i symulatorami.</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-clock"></i>
                    <h3>Elastyczny Grafik</h3>
                    <p>Dostosowujemy terminy zajęć do potrzeb kursantów.</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h3>Atrakcyjne Ceny</h3>
                    <p>Oferujemy konkurencyjne ceny i możliwość płatności w ratach.</p>
                </div>
            </div>
        </section>

        <section class="cta-section" data-aos="fade-up">
            <h2>Zapisz się na kurs już dziś!</h2>
            <p>Skontaktuj się z nami, aby dowiedzieć się więcej o naszych kursach i rozpocząć swoją przygodę z nauką jazdy.</p>
            <a href="kontakt.php" class="btn btn-primary">Kontakt</a>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html> 