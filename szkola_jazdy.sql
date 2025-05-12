
CREATE DATABASE szkola_jazdy;
USE szkola_jazdy;

CREATE TABLE instruktorzy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    haslo VARCHAR(255) NOT NULL,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    aktywny BOOLEAN DEFAULT TRUE
);

CREATE TABLE uczniowie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    haslo VARCHAR(255) NOT NULL,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    wyjezdzone_godziny INT DEFAULT 0 CHECK (wyjezdzone_godziny BETWEEN 0 AND 30),
    id_instruktora INT,
    data_rejestracji DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (id_instruktora) REFERENCES instruktorzy(id) ON DELETE SET NULL
);

CREATE TABLE pojazdy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rejestracja VARCHAR(20) NOT NULL UNIQUE,
    model VARCHAR(50) NOT NULL,
    dostepny BOOLEAN DEFAULT TRUE
);

CREATE TABLE jazdy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ucznia INT NOT NULL,
    id_instruktora INT NOT NULL,
    id_pojazdu INT,
    data_jazdy DATE NOT NULL,
    godzina_od TIME NOT NULL,
    godzina_do TIME NOT NULL,
    odbyta BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_ucznia) REFERENCES uczniowie(id) ON DELETE CASCADE,
    FOREIGN KEY (id_instruktora) REFERENCES instruktorzy(id) ON DELETE CASCADE,
    FOREIGN KEY (id_pojazdu) REFERENCES pojazdy(id) ON DELETE SET NULL
);


INSERT INTO instruktorzy (login, haslo, imie, nazwisko) VALUES
('muhamedk', 'haslo123', 'Muhamed', 'Kebsik'),
('robertl', 'haslo123', 'Robert', 'Lewandowski'),
('konradm', 'haslo123', 'Konrad', 'McGregor');

INSERT INTO uczniowie (login, haslo, imie, nazwisko, wyjezdzone_godziny, id_instruktora) VALUES
('ernestpacura0', 'haslo123', 'Ernest', 'Pacura', 27, 1),
('sandraszyc1', 'haslo123', 'Sandra', 'Szyc', 12, 2),
('robertknapek2', 'haslo123', 'Robert', 'Knapek', 24, 3),
('adapostawa3', 'haslo123', 'Ada', 'Postawa', 28, 1),
('emilsutor4', 'haslo123', 'Emil', 'Sutor', 13, 2),
('maurycykazubek5', 'haslo123', 'Maurycy', 'Kazubek', 15, 3),
('przemysławandrejczuk6', 'haslo123', 'Przemysław', 'Andrejczuk', 18, 1),
('sebastianziętal7', 'haslo123', 'Sebastian', 'Ziętal', 30, 2),
('sebastiannieścioruk8', 'haslo123', 'Sebastian', 'Nieścioruk', 16, 3),
('patrykpawela9', 'haslo123', 'Patryk', 'Pawela', 15, 1),
('iwodobroń10', 'haslo123', 'Iwo', 'Dobroń', 12, 2),
('tymonkorzekwa11', 'haslo123', 'Tymon', 'Korzekwa', 29, 3),
('oskarkorbut12', 'haslo123', 'Oskar', 'Korbut', 25, 1),
('wojciechostręga13', 'haslo123', 'Wojciech', 'Ostręga', 26, 2),
('kornelmacha14', 'haslo123', 'Kornel', 'Macha', 9, 3),
('melaniamartyka15', 'haslo123', 'Melania', 'Martyka', 30, 1),
('maksślimak16', 'haslo123', 'Maks', 'Ślimak', 15, 2),
('jędrzejfac17', 'haslo123', 'Jędrzej', 'Fac', 11, 3),
('gabrielpleban18', 'haslo123', 'Gabriel', 'Pleban', 18, 1),
('malwinasamulak19', 'haslo123', 'Malwina', 'Samulak', 28, 2),
('angelikaskowyra20', 'haslo123', 'Angelika', 'Skowyra', 29, 3),
('stanisławpierz21', 'haslo123', 'Stanisław', 'Pierz', 16, 1),
('krystynaromańczyk22', 'haslo123', 'Krystyna', 'Romańczyk', 16, 2),
('tomaszszpyra23', 'haslo123', 'Tomasz', 'Szpyra', 14, 3),
('blankaplak24', 'haslo123', 'Blanka', 'Plak', 19, 1),
('jakubchaberek25', 'haslo123', 'Jakub', 'Chaberek', 12, 2),
('anastazjatowarek26', 'haslo123', 'Anastazja', 'Towarek', 24, 3),
('józefćwierz27', 'haslo123', 'Józef', 'Ćwierz', 11, 1),
('arkadiuszimiołczyk28', 'haslo123', 'Arkadiusz', 'Imiołczyk', 19, 2),
('wiktorrzeźniczak29', 'haslo123', 'Wiktor', 'Rzeźniczak', 25, 3),
('nicoleświech30', 'haslo123', 'Nicole', 'Świech', 20, 1),
('tadeuszidzi31', 'haslo123', 'Tadeusz', 'Idzi', 29, 2),
('julianmłocek32', 'haslo123', 'Julian', 'Młocek', 17, 3),
('ingafrelek33', 'haslo123', 'Inga', 'Frelek', 22, 1),
('tomaszhyży34', 'haslo123', 'Tomasz', 'Hyży', 25, 2),
('alexmaciuszek35', 'haslo123', 'Alex', 'Maciuszek', 19, 3),
('apoloniapyrkosz36', 'haslo123', 'Apolonia', 'Pyrkosz', 28, 1),
('dominikbok37', 'haslo123', 'Dominik', 'Bok', 13, 2),
('biankajeske38', 'haslo123', 'Bianka', 'Jeske', 21, 3),
('marcelłasica39', 'haslo123', 'Marcel', 'Łasica', 10, 1),
('józefmarko40', 'haslo123', 'Józef', 'Marko', 23, 2),
('rozaliałukowiak41', 'haslo123', 'Rozalia', 'Łukowiak', 22, 3),
('krystynakulon42', 'haslo123', 'Krystyna', 'Kulon', 28, 1),
('stefanbarczuk43', 'haslo123', 'Stefan', 'Barczuk', 27, 2),
('tomaszwlazły44', 'haslo123', 'Tomasz', 'Wlazły', 21, 3),
('ernestzaucha45', 'haslo123', 'Ernest', 'Zaucha', 10, 1),
('aureliadrobny46', 'haslo123', 'Aurelia', 'Drobny', 15, 2),
('alekszacharek47', 'haslo123', 'Aleks', 'Zacharek', 17, 3),
('rafałurynowicz48', 'haslo123', 'Rafał', 'Urynowicz', 23, 1);
('danielmuc49', 'haslo123', 'Daniel', 'Muc', 11, 2);

INSERT INTO pojazdy (rejestracja, model, dostepny) VALUES
('DW0001H20', 'Hyundai i20', TRUE),
('DW0002H20', 'Hyundai i20', TRUE),
('DW0003H20', 'Hyundai i20', TRUE),
('DW0004H20', 'Hyundai i20', TRUE),
('DW0005H20', 'Hyundai i20', FALSE);