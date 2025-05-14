// Globalne zmienne do przechowywania danych zalogowanego użytkownika
let currentUserId = sessionStorage.getItem('userId');
let currentUserRole = sessionStorage.getItem('userRole');
let currentUserName = sessionStorage.getItem('userName');

// Funkcja do przełączania między formularzami
function switchTab(tabName) {
    // Ukryj wszystkie formularze
    document.querySelectorAll('.form-container').forEach(form => {
        form.classList.remove('active');
    });
    
    // Usuń klasę active ze wszystkich przycisków
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Pokaż wybrany formularz
    document.getElementById(tabName + '-form').classList.add('active');
    
    // Dodaj klasę active do wybranego przycisku
    document.querySelector(`button[onclick="switchTab('${tabName}')"]`).classList.add('active');
}

// Funkcja do rejestracji użytkownika
async function registerUser() {
    const login = document.getElementById('reg-login').value;
    const password = document.getElementById('reg-password').value;
    const confirmPassword = document.getElementById('reg-confirm-password').value;
    const name = document.getElementById('reg-name').value;
    const surname = document.getElementById('reg-surname').value;
    const email = document.getElementById('reg-email').value;
    const role = document.getElementById('reg-role').value;
    
    // Sprawdź czy hasła są takie same
    if (password !== confirmPassword) {
        showNotification('Hasła nie są identyczne', 'error');
        return;
    }
    
    try {
        const response = await fetch('register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login,
                password,
                name,
                surname,
                email,
                role
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification('Rejestracja udana! Możesz się teraz zalogować.', 'success');
            // Przełącz na formularz logowania
            switchTab('login');
            // Wyczyść formularz rejestracji
            document.querySelector('#register-form form').reset();
        } else {
            showNotification(data.message || 'Błąd rejestracji', 'error');
        }
    } catch (error) {
        console.error('Błąd podczas rejestracji:', error);
        showNotification('Błąd podczas rejestracji', 'error');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Aktualizuj globalne zmienne po załadowaniu DOM
    currentUserId = sessionStorage.getItem('userId');
    currentUserRole = sessionStorage.getItem('userRole');
    currentUserName = sessionStorage.getItem('userName');

    // Dostosuj nawigację od razu po załadowaniu strony
    if (currentUserRole) {
        updateNavigation(currentUserRole);
    }

    // Wywołaj funkcję pobierania jazd, jeśli jesteśmy na odpowiedniej stronie
    if (document.getElementById('student-lessons-table') || document.getElementById('instructor-lessons-table')) {
        fetchAndDisplayLessons(); 
    }

    // Załaduj instruktorów do dropdownu, jeśli istnieje
    const instructorSelect = document.getElementById('instructor');
    if (instructorSelect) {
        loadInstructors(); 
    }

    // Ustawienie min daty dla inputa
    const dateInput = document.getElementById('date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }

    // Aktualizuj powitanie, jeśli na stronie logowania jest już zalogowany użytkownik
    // (może się zdarzyć po odświeżeniu strony logowania)
    if (window.location.pathname.includes('index.html') && currentUserId) {
       // Można tu dodać logikę przekierowania lub ukrycia formularza logowania
    } else if (!window.location.pathname.includes('index.html') && !currentUserId) {
       // Jeśli nie jesteśmy na stronie logowania i nie ma userId, przekieruj
       // window.location.href = 'index.html'; // Opcjonalne: automatyczne przekierowanie
    }

    // Ustawienie nazw użytkownika w odpowiednich miejscach (jeśli istnieją)
    const studentNameSpan = document.getElementById('student-name');
    const instructorNameSpan = document.getElementById('instructor-name');
    if (studentNameSpan && currentUserName && currentUserRole === 'Uczniem') {
        studentNameSpan.textContent = currentUserName;
        document.getElementById('student-schedule-view').style.display = 'block'; // Pokaż widok ucznia
        document.getElementById('booking-form-container').style.display = 'block'; // Pokaż formularz rezerwacji
    } else if (instructorNameSpan && currentUserName && currentUserRole === 'Instruktorem') {
        instructorNameSpan.textContent = currentUserName;
        document.getElementById('instructor-view').style.display = 'block'; // Pokaż widok instruktora
    } 
    // Jeśli jesteśmy na stronie planowanie.html i zalogowany jest uczeń, pokaż odpowiedni komunikat
    else if (document.getElementById('student-view') && currentUserRole === 'Uczniem') {
         document.getElementById('student-view').style.display = 'block';
    }
    // Jeśli nie zalogowany na chronionych stronach
    else if ((studentNameSpan || instructorNameSpan) && !currentUserId) {
        if (document.getElementById('unauthorized-view')){
            document.getElementById('unauthorized-view').style.display = 'block';
        }
    }

});

// Funkcja do pobierania i wyświetlania lekcji
async function fetchAndDisplayLessons() {
    console.log("fetchAndDisplayLessons: Rozpoczęto.");
    const userId = sessionStorage.getItem('userId');
    const userRole = sessionStorage.getItem('userRole');
    console.log(`fetchAndDisplayLessons: userId=${userId}, userRole=${userRole}`);
    if (!userId || !userRole) {
        console.log("fetchAndDisplayLessons: Brak userId lub userRole w sessionStorage.");
        return;
    }

    try {
        console.log("fetchAndDisplayLessons: Wysyłanie żądania do get_lessons.php (POST)");
        const response = await fetch('get_lessons.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ userId, userRole })
        });
        if (!response.ok) {
            console.error("fetchAndDisplayLessons: Błąd odpowiedzi serwera", response.status);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log("fetchAndDisplayLessons: Otrzymano dane:", data);

        if (data.status === 'success') {
            if (userRole === 'Uczniem') {
                console.log("fetchAndDisplayLessons: Wywołuję displayStudentLessons");
                displayStudentLessons(data.lessons);
            } else if (userRole === 'Instruktorem') {
                console.log("fetchAndDisplayLessons: Wywołuję displayInstructorLessons");
                displayInstructorLessons(data.lessons);
            }
        } else {
            console.error("fetchAndDisplayLessons: Błąd w danych odpowiedzi:", data.message);
            // Można wyświetlić błąd użytkownikowi w odpowiedniej tabeli
            if (userRole === 'Uczniem' && document.getElementById('student-lessons-table')) {
                document.getElementById('student-lessons-table').innerHTML = `<tr><td colspan="5">Błąd ładowania: ${data.message}</td></tr>`;
            } else if (userRole === 'Instruktorem' && document.getElementById('instructor-lessons-table')) {
                document.getElementById('instructor-lessons-table').innerHTML = `<tr><td colspan="4">Błąd ładowania: ${data.message}</td></tr>`;
            }
        }
    } catch (error) {
        console.error('fetchAndDisplayLessons: Błąd podczas pobierania lekcji:', error);
        // Wyświetl błąd w odpowiedniej tabeli
        if (userRole === 'Uczniem' && document.getElementById('student-lessons-table')) {
            document.getElementById('student-lessons-table').innerHTML = `<tr><td colspan="5">Błąd połączenia lub przetwarzania.</td></tr>`;
        } else if (userRole === 'Instruktorem' && document.getElementById('instructor-lessons-table')) {
            document.getElementById('instructor-lessons-table').innerHTML = `<tr><td colspan="4">Błąd połączenia lub przetwarzania.</td></tr>`;
        }
    }
}

// Funkcja do wyświetlania lekcji dla ucznia
function displayStudentLessons(lessons) {
    console.log("displayStudentLessons: Rozpoczęto z lekcjami:", lessons);
    const tableBody = document.getElementById('student-lessons-table'); 
    if (!tableBody) {
        console.error("displayStudentLessons: Nie znaleziono tbody o ID student-lessons-table");
        return;
    }

    tableBody.innerHTML = ''; // Wyczyść istniejące wiersze

    if (lessons.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6">Nie masz jeszcze zaplanowanych żadnych jazd.</td></tr>'; // Zwiększono colspan
        return;
    }

    lessons.forEach(lesson => {
        const row = tableBody.insertRow();
        // Dodanie klasy CSS do wiersza na podstawie statusu
        row.className = `status-${lesson.status.toLowerCase()}`; // np. status-zaplanowana

        row.insertCell(0).textContent = lesson.data_jazdy;
        row.insertCell(1).textContent = `${lesson.godzina_od} - ${lesson.godzina_do}`;
        row.insertCell(2).textContent = `${lesson.imie_instruktora} ${lesson.nazwisko_instruktora}`;
        // Usunięto kolumnę z imieniem ucznia dla widoku ucznia
        const statusCell = row.insertCell(3); // Kolumna ze statusem
        statusCell.textContent = lesson.status;
        
        const actionCell = row.insertCell(4); // Kolumna na akcje (np. przycisk Odwołaj)
        if (lesson.status === 'Zaplanowana') {
            const cancelButton = document.createElement('button');
            cancelButton.textContent = 'Odwołaj';
            cancelButton.classList.add('cancel-button');
            cancelButton.onclick = () => cancelLesson(lesson.id);
            actionCell.appendChild(cancelButton);
        }
    });
}

// Funkcja do wyświetlania lekcji dla instruktora
function displayInstructorLessons(lessons) {
    console.log("displayInstructorLessons: Rozpoczęto z lekcjami:", lessons);
    const tableBody = document.getElementById('instructor-lessons-table'); 
    if (!tableBody) {
        console.error("displayInstructorLessons: Nie znaleziono tbody o ID instructor-lessons-table");
        return;
    }

    tableBody.innerHTML = ''; // Wyczyść istniejące wiersze

    if (lessons.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="4">Nie masz jeszcze zaplanowanych żadnych jazd.</td></tr>';
        return;
    }

    lessons.forEach(lesson => {
        const row = tableBody.insertRow();
        // Dodanie klasy CSS do wiersza na podstawie statusu
        row.className = `status-${lesson.status.toLowerCase()}`; // np. status-zaplanowana

        row.insertCell(0).textContent = lesson.data_jazdy;
        row.insertCell(1).textContent = `${lesson.godzina_od} - ${lesson.godzina_do}`;
        row.insertCell(2).textContent = `${lesson.imie_ucznia} ${lesson.nazwisko_ucznia}`;
        const statusCell = row.insertCell(3);
        statusCell.textContent = lesson.status;
        // Instruktor nie ma przycisku "Odwołaj"
    });
}

// Funkcja do odwoływania lekcji przez ucznia
async function cancelLesson(lessonId) {
    if (!currentUserId) {
        alert('Błąd: Nie można zidentyfikować użytkownika.');
        return;
    }

    if (confirm('Czy na pewno chcesz odwołać tę jazdę?')) {
        try {
            const response = await fetch('cancel_lesson.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ lessonId: lessonId, userId: currentUserId })
            });

            const data = await response.json();

            if (data.status === 'success') {
                alert('Jazda została odwołana.');
                fetchAndDisplayLessons(); // Odśwież listę jazd
            } else {
                alert(`Błąd odwoływania jazdy: ${data.message || 'Nieznany błąd'}`);
                console.error("Błąd odpowiedzi z cancel_lesson.php:", data);
            }
        } catch (error) {
            console.error('Błąd sieciowy podczas odwoływania jazdy:', error);
            alert('Wystąpił błąd sieciowy. Spróbuj ponownie.');
        }
    }
}

// Funkcja do logowania użytkownika
async function loginUser() {
    const login = document.getElementById('login').value;
    const password = document.getElementById('login-password').value;

    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login: login,
                password: password
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            // Zapisz dane użytkownika
            sessionStorage.setItem('userId', data.userId);
            sessionStorage.setItem('userRole', data.userRole);
            sessionStorage.setItem('userName', data.userName);

            // Aktualizuj globalne zmienne
            currentUserId = data.userId;
            currentUserRole = data.userRole;
            currentUserName = data.userName;

            // Pokaż popup powitalny
            showWelcomePopup(data.userName);

            // Przekieruj użytkownika w zależności od roli
            if (data.userRole === 'Uczniem') {
                window.location.href = 'planowanie.html';
            } else if (data.userRole === 'Instruktorem') {
                window.location.href = 'planowanie.html';
            }
        } else {
            showNotification(data.message || 'Błąd logowania', 'error');
        }
    } catch (error) {
        console.error('Błąd podczas logowania:', error);
        showNotification('Błąd podczas logowania', 'error');
    }
}


// Funkcja do pokazywania dostępnych terminów (placeholder)
async function showAvailableSlots() {
    const instructorSelect = document.getElementById('instructor');
    const durationSelect = document.getElementById('duration');
    const dateInput = document.getElementById('date');
    const availableSlotsDiv = document.getElementById('available-slots');
    const slotsListDiv = document.getElementById('slots-list');

    const instructorName = instructorSelect.value; // Pobieramy nazwę instruktora
    const duration = durationSelect.value;
    const date = dateInput.value;

    console.log('Wybrano:', { instructorName, duration, date });

    if (!instructorName || !date) {
        slotsListDiv.innerHTML = '<p style="color: red;">Proszę wybrać instruktora i datę.</p>';
        availableSlotsDiv.style.display = 'block';
        return;
    }

    slotsListDiv.innerHTML = '<p>Ładowanie dostępnych terminów...</p>';
    availableSlotsDiv.style.display = 'block';

    try { // Dodano try-catch dla bezpieczeństwa
        await new Promise(resolve => setTimeout(resolve, 500)); // Symulacja opóźnienia sieciowego

        // Przykładowe sloty (do zastąpienia prawdziwymi danymi z serwera)
        const exampleSlots = [
            '08:00 - 09:00',
            '09:00 - 10:00', // Kolejny slot
            '11:00 - 12:00',
            '14:00 - 15:00'
        ];

        let filteredSlots = [];
        const startHour = 8; // Godzina rozpoczęcia pracy
        const endHour = 18;   // Godzina zakończenia pracy

        if (duration === '2') {
            // Generuj wszystkie możliwe sloty 2-godzinne od startHour do endHour
            for (let hour = startHour; hour < endHour; hour += 2) {
                if (hour + 2 <= endHour) { // Upewnij się, że nie wykraczamy poza endHour
                    const startTime = `${hour.toString().padStart(2, '0')}:00`;
                    const endTime = `${(hour + 2).toString().padStart(2, '0')}:00`;
                    const twoHourSlot = `${startTime} - ${endTime}`;
                    filteredSlots.push(twoHourSlot);
                }
            }
        } else {
            // Dla jednogodzinnych używamy przykładowych (bez zmian)
            filteredSlots = exampleSlots; 
        }


        if (filteredSlots.length > 0) {
             slotsListDiv.innerHTML = filteredSlots.map(slot =>
                `<button class="slot-button" onclick="selectSlot('${slot}')">${slot}</button>`
            ).join('');
        } else {
            slotsListDiv.innerHTML = '<p>Brak dostępnych terminów dla wybranych kryteriów.</p>';
        }

    } catch (error) {
        console.error("Błąd podczas generowania slotów:", error);
        slotsListDiv.innerHTML = '<p style="color: red;">Wystąpił błąd podczas ładowania terminów. Sprawdź konsolę (F12).</p>';
    }
    // --- Koniec Placeholder ---
}

// Funkcja do obsługi wyboru slotu (teraz z użyciem ID)
async function selectSlot(slot) {
    console.log('Wybrano slot:', slot);
    
    const instructorSelect = document.getElementById('instructor');
    const dateInput = document.getElementById('date');
    const instructorId = instructorSelect.value; // Pobieramy ID instruktora
    const instructorName = instructorSelect.options[instructorSelect.selectedIndex].text; // Nazwa dla komunikatu
    const date = dateInput.value;
    const [godzina_od, godzina_do] = slot.split(' - ');
    
    if (!instructorId) {
        alert('Proszę najpierw wybrać instruktora.');
        return;
    }

    // Potwierdzenie rezerwacji
    if (confirm(`Czy na pewno chcesz zarezerwować jazdę:\nInstruktor: ${instructorName}\nData: ${date}\nGodzina: ${slot}?`)) {
        // Wywołanie funkcji rezerwującej
        await bookLesson(currentUserId, instructorId, date, godzina_od, godzina_do);
    }
}


// Funkcja do rezerwacji lekcji (komunikacja z book_lesson.php)
async function bookLesson(studentId, instructorId, date, startTime, endTime) {
    if (!studentId || !instructorId || !date || !startTime || !endTime) {
        alert('Błąd: Brak wszystkich wymaganych danych do rezerwacji.');
        console.error('Brakujące dane:', { studentId, instructorId, date, startTime, endTime });
        return;
    }
    console.log('Dane do rezerwacji:', { studentId, instructorId, date, startTime, endTime });

    try {
        const response = await fetch('book_lesson.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                userId: studentId, 
                instructorId: instructorId, 
                date: date, 
                startTime: startTime, 
                endTime: endTime 
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            alert('Jazda została pomyślnie zarezerwowana!');
            fetchAndDisplayLessons(); // Odśwież listę jazd
            document.getElementById('available-slots').style.display = 'none'; // Ukryj sloty
        } else {
            alert(`Błąd rezerwacji: ${data.message || 'Nieznany błąd serwera'}`);
            console.error('Błąd odpowiedzi z book_lesson.php:', data);
        }
    } catch (error) {
        console.error('Błąd sieciowy podczas rezerwacji lekcji:', error);
        alert('Wystąpił błąd sieciowy podczas próby rezerwacji. Spróbuj ponownie.');
    }
}