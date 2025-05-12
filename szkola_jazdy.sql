
CREATE DATABASE szkoła_jazdy;
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
('ernestpacura0', '&iqPcsuS)4', 'Ernest', 'Pacura', 27, 1),
('sandraszyc1', '&9dIoC(VC2', 'Sandra', 'Szyc', 12, 2),
('robertknapek2', 'mK_2$UnRc^', 'Robert', 'Knapek', 24, 3),
('adapostawa3', 'W@x5Db%cSX', 'Ada', 'Postawa', 28, 1),
('emilsutor4', '&W2B7qMsUd', 'Emil', 'Sutor', 13, 2),
('maurycykazubek5', '4bDcsqtt(a', 'Maurycy', 'Kazubek', 1, 3),
('przemysławandrejczuk6', '+15wK9xwYX', 'Przemysław', 'Andrejczuk', 8, 1),
('sebastianziętal7', ')3Webq#kTj', 'Sebastian', 'Ziętal', 30, 2),
('sebastiannieścioruk8', 'r2WO#Cq$%y', 'Sebastian', 'Nieścioruk', 16, 3),
('patrykpawela9', 'w+2AmycX2X', 'Patryk', 'Pawela', 15, 1),
('iwodobroń10', '*tlUcamZ6+', 'Iwo', 'Dobroń', 12, 2),
('tymonkorzekwa11', '^x5OSbKq1F', 'Tymon', 'Korzekwa', 29, 3),
('oskarkorbut12', '^X5Wc9HS9N', 'Oskar', 'Korbut', 25, 1),
('wojciechostręga13', '(A(T1CJm)(', 'Wojciech', 'Ostręga', 26, 2),
('kornelmacha14', 'Tlz2F24u*^', 'Kornel', 'Macha', 9, 3),
('melaniamartyka15', 'z&j1E%LhsL', 'Melania', 'Martyka', 30, 1),
('maksślimak16', 'zlY*LEUb%3', 'Maks', 'Ślimak', 15, 2),
('jędrzejfac17', 'Yq5uPepBB_', 'Jędrzej', 'Fac', 11, 3),
('gabrielpleban18', 'l%4q#W$w)C', 'Gabriel', 'Pleban', 18, 1),
('malwinasamulak19', 'Ucl8zhQbR&', 'Malwina', 'Samulak', 28, 2),
('angelikaskowyra20', 'AU!0XfjGG0', 'Angelika', 'Skowyra', 29, 3),
('stanisławpierz21', '!0P&dlEnML', 'Stanisław', 'Pierz', 6, 1),
('krystynaromańczyk22', 'Y!SD9TOrXe', 'Krystyna', 'Romańczyk', 16, 2),
('tomaszszpyra23', '#6clQZ1jKh', 'Tomasz', 'Szpyra', 4, 3),
('blankaplak24', 'v!73ImajdJ', 'Blanka', 'Plak', 9, 1),
('jakubchaberek25', ')6jgCdc#ux', 'Jakub', 'Chaberek', 4, 2),
('anastazjatowarek26', '^5FWFaags5', 'Anastazja', 'Towarek', 24, 3),
('józefćwierz27', '(o2)xFN9v$', 'Józef', 'Ćwierz', 3, 1),
('arkadiuszimiołczyk28', '@sSlB5j9J8', 'Arkadiusz', 'Imiołczyk', 19, 2),
('wiktorrzeźniczak29', '@IuVA1EnZR', 'Wiktor', 'Rzeźniczak', 25, 3),
('nicoleświech30', '&1$5WJdp1J', 'Nicole', 'Świech', 8, 1),
('tadeuszidzi31', 'Y&L9oLM4(2', 'Tadeusz', 'Idzi', 29, 2),
('julianmłocek32', 'O7SHY@Cv!&', 'Julian', 'Młocek', 17, 3),
('ingafrelek33', 'q+4&L7j(pr', 'Inga', 'Frelek', 22, 1),
('tomaszhyży34', 'Q%8wSgE8Ks', 'Tomasz', 'Hyży', 25, 2),
('alexmaciuszek35', '*YgROPOvK2', 'Alex', 'Maciuszek', 19, 3),
('apoloniapyrkosz36', '!!QPmcgb!5', 'Apolonia', 'Pyrkosz', 28, 1),
('dominikbok37', '*M#9JJKgeQ', 'Dominik', 'Bok', 4, 2),
('biankajeske38', '&4ZpV$G5&6', 'Bianka', 'Jeske', 9, 3),
('marcelłasica39', 'qhjr9$CwZ%', 'Marcel', 'Łasica', 3, 1),
('józefmarko40', '7$07aGkkx+', 'Józef', 'Marko', 23, 2),
('rozaliałukowiak41', 'BRs692Cz@n', 'Rozalia', 'Łukowiak', 2, 3),
('krystynakulon42', 'v^3B$ljM(&', 'Krystyna', 'Kulon', 28, 1),
('stefanbarczuk43', 'T9HwC4o8*n', 'Stefan', 'Barczuk', 27, 2),
('tomaszwlazły44', '(QMWj@Xe3I', 'Tomasz', 'Wlazły', 21, 3),
('ernestzaucha45', 'G6xTBdS8(s', 'Ernest', 'Zaucha', 10, 1),
('aureliadrobny46', '95UpCV@)$y', 'Aurelia', 'Drobny', 15, 2),
('alekszacharek47', '*5)5GY)O9w', 'Aleks', 'Zacharek', 17, 3),
('rafałurynowicz48', 'wT0*7Jae^_', 'Rafał', 'Urynowicz', 3, 1),
('danielmuc49', 'bhGpba4D&0', 'Daniel', 'Muc', 11, 2);

INSERT INTO pojazdy (rejestracja, model, dostepny) VALUES
('DW0001H20', 'Hyundai i20', TRUE),
('DW0002H20', 'Hyundai i20', TRUE),
('DW0003H20', 'Hyundai i20', TRUE),
('DW0004H20', 'Hyundai i20', TRUE),
('DW0005H20', 'Hyundai i20', FALSE);