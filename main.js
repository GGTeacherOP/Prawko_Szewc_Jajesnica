function showAvailableSlots() {
    const instructor = document.getElementById('instructor').value;
    const duration = parseInt(document.getElementById('duration').value);
    const date = document.getElementById('date').value;
    const slotsListDiv = document.getElementById('slots-list');
    const availableSlotsContainer = document.getElementById('available-slots');

    if (!instructor || !date) {
      alert('Proszę wybrać instruktora i datę.');
      return;
    }

    slotsListDiv.innerHTML = ''; // Czyszczenie poprzednich wyników

    // --- Definicja WSZYSTKICH możliwych godzin rozpoczęcia --- 
    const allPossibleStartTimes1h = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
    const allPossibleStartTimes2h = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00']; // Ostatnia jazda 2h zaczyna się o 16:00

    const allPossibleStartTimes = duration === 1 ? allPossibleStartTimes1h : allPossibleStartTimes2h;

    // --- Symulacja NIEDOSTĘPNYCH godzin (dla przykładu) --- 
    // W realnej aplikacji te dane pochodziłyby z serwera
    // Przykładowe niedostępne sloty dla instruktora "Robert Lewandowski" w dniu "2025-05-20"
    let unavailableTimes = [];
    if (instructor === 'Robert Lewandowski' && date === '2025-05-20') {
        if (duration === 1) unavailableTimes = ['10:00', '14:00'];
        if (duration === 2) unavailableTimes = ['09:00']; 
    } else if (instructor === 'Muhamed Kebsik' && date === '2025-05-17') {
        if (duration === 1) unavailableTimes = ['08:00', '09:00', '13:00'];
        if (duration === 2) unavailableTimes = ['12:00', '16:00'];
    }
    // Można dodać więcej logiki symulacji...

    // --- Wyświetlanie WSZYSTKICH terminów (dostępnych i niedostępnych) --- 
    if (allPossibleStartTimes.length > 0) {
      allPossibleStartTimes.forEach(startTime => {
        const isUnavailable = unavailableTimes.includes(startTime);

        const startHour = parseInt(startTime.split(':')[0]);
        const startMinute = parseInt(startTime.split(':')[1]);
        let endHour = startHour + duration;
        let endMinute = startMinute;
        
        const formattedStartTime = `${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}`;
        const formattedEndTime = `${String(endHour).padStart(2, '0')}:${String(endMinute).padStart(2, '0')}`;
        const timeSlotString = `${formattedStartTime} - ${formattedEndTime}`;

        let slotElement;
        if (isUnavailable) {
          // Tworzenie nieklikalnego elementu dla niedostępnego terminu
          slotElement = document.createElement('span');
          slotElement.textContent = timeSlotString;
          slotElement.classList.add('slot-time', 'unavailable-slot'); // Dodaj klasy dla stylizacji
        } else {
          // Tworzenie przycisku dla dostępnego terminu
          slotElement = document.createElement('button');
          slotElement.textContent = timeSlotString;
          slotElement.classList.add('slot-button'); // Użyj tej samej klasy co wcześniej lub nowej
          slotElement.onclick = function() { bookSlot(instructor, date, timeSlotString); };
        }
        slotsListDiv.appendChild(slotElement);
      });
      availableSlotsContainer.style.display = 'block';
    } else {
      // Ten warunek raczej nie zajdzie przy obecnej logice, ale zostawiam
      slotsListDiv.innerHTML = '<p>Brak zdefiniowanych godzin pracy.</p>'; 
      availableSlotsContainer.style.display = 'block'; 
    }
  }

  function bookSlot(instructor, date, timeSlot) {
    // Tutaj docelowo logika rezerwacji terminu (np. wysłanie zapytania do serwera)
    alert(`Zarezerwowano jazdę:
Instruktor: ${instructor}
Data: ${date}
Godzina: ${timeSlot}

(To jest tylko symulacja, termin nie został zapisany.)`);
    
    // Opcjonalnie: wyczyść formularz lub ukryj terminy po rezerwacji
    // document.getElementById('planning-form').reset();
    // document.getElementById('available-slots').style.display = 'none';
  }

  // Dodaj walidację daty - nie można wybrać daty z przeszłości
  const dateInput = document.getElementById('date');
  const today = new Date().toISOString().split('T')[0];
  dateInput.setAttribute('min', today);

// --- NOWA FUNKCJA LOGOWANIA ---
async function loginUser() {
    console.log("loginUser function called"); // <-- DEBUG: Sprawdzenie wywołania
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');
    const notificationDiv = document.getElementById('notification');

    // Upewnij się, że elementy istnieją, zanim odczytasz wartość
    if (!loginInput || !passwordInput || !notificationDiv) {
        console.error("Nie znaleziono elementów formularza logowania lub powiadomienia.");
        // Możesz też wyświetlić błąd użytkownikowi w inny sposób
        alert("Wystąpił błąd interfejsu logowania.");
        return;
    }


    const login = loginInput.value;
    const password = passwordInput.value;

    notificationDiv.style.display = 'none'; // Ukryj poprzednie powiadomienia
    notificationDiv.classList.remove('success', 'error'); // Usuń klasy statusu

    if (!login || !password) {
        showNotification('Wprowadź login i hasło', 'error');
        return;
    }
    console.log(`Attempting login for user: ${login}`); // <-- DEBUG: Logowanie loginu

    try {
        console.log("Sending fetch request to login.php..."); // <-- DEBUG: Przed fetch
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ login: login, haslo: password })
        });
        console.log("Fetch response received, status:", response.status); // <-- DEBUG: Status odpowiedzi

        // Sprawdź, czy odpowiedź jest OK (status 200-299)
        if (!response.ok) {
             // Spróbuj odczytać treść błędu, jeśli serwer ją zwrócił
             let errorText = `Błąd serwera: ${response.status}`;
             try {
                 const errorData = await response.json();
                 errorText = errorData.message || errorText;
             } catch(e) {
                 // Ignoruj błąd parsowania JSON, jeśli odpowiedź nie była JSONem
             }
             throw new Error(errorText);
        }

        const result = await response.json();
        console.log("Parsed JSON result:", result); // <-- DEBUG: Odpowiedź z PHP

        if (result.status === 'success') {
            // Zapisz dane użytkownika w sessionStorage
            sessionStorage.setItem('userId', result.id);
            sessionStorage.setItem('userRole', result.rola);
            sessionStorage.setItem('userName', `${result.imie} ${result.nazwisko}`);

            const message = `Witaj ${result.imie} ${result.nazwisko}! Jesteś ${result.rola}.`;
            showNotification(message, 'success');

            // Przekierowanie po 3 sekundach
             setTimeout(() => {
                 // Użyj odpowiednich ścieżek do plików HTML
                 if (result.rola === 'Uczniem') {
                     window.location.href = 'zajecia_praktyczne.html';
                 } else if (result.rola === 'Instruktorem') {
                     window.location.href = 'planowanie.html'; // Upewnij się, że ta strona istnieje i jest odpowiednia
                 } else {
                     // Domyślne przekierowanie, jeśli rola nie jest rozpoznana lub na wszelki wypadek
                     window.location.href = 'info.html'; // Upewnij się, że ta strona istnieje
                 }
             }, 3000); // 3000 ms = 3 sekundy

        } else {
            showNotification(result.message || 'Nieprawidłowy login lub hasło', 'error');
        }

    } catch (error) {
        console.error('Login Error Caught:', error); // <-- DEBUG: Złapany błąd
        showNotification(`Wystąpił błąd: ${error.message || 'Spróbuj ponownie.'}`, 'error');
    }
}

// Funkcja pomocnicza do wyświetlania powiadomień
function showNotification(message, type) {
    const notificationDiv = document.getElementById('notification');
    if (!notificationDiv) {
        console.error("Nie znaleziono elementu powiadomienia #notification.");
        alert(message); // Wyświetl jako alert, jeśli div nie istnieje
        return;
    }
    notificationDiv.textContent = message;
    notificationDiv.className = 'notification ' + type; // Ustaw klasę bazową i klasę typu (success/error)
    notificationDiv.style.display = 'block';
    notificationDiv.style.position = 'fixed'; // Aby wycentrować na ekranie
    notificationDiv.style.left = '50%';
    notificationDiv.style.top = '50%';
    notificationDiv.style.transform = 'translate(-50%, -50%)'; // Centrowanie
    notificationDiv.style.zIndex = '1000'; // Upewnij się, że jest na wierzchu

    // Ukrywaj tylko powiadomienia o błędach po pewnym czasie,
    // sukces zniknie przy przekierowaniu
    if (type !== 'success') {
       setTimeout(() => {
           if (notificationDiv.style.display !== 'none') { // Sprawdź czy nadal jest widoczne
                notificationDiv.style.display = 'none';
           }
       }, 5000); // Ukryj błąd po 5 sekundach
    }
    // Powiadomienie o sukcesie zniknie samo po przekierowaniu strony po 3s
}