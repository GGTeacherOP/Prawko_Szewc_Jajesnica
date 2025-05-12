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

    let unavailableTimes = [];
    if (instructor === 'Robert Lewandowski' && date === '2025-05-20') {
        if (duration === 1) unavailableTimes = ['10:00', '14:00'];
        if (duration === 2) unavailableTimes = ['09:00']; 
    } else if (instructor === 'Muhamed Kebsik' && date === '2025-05-17') {
        if (duration === 1) unavailableTimes = ['08:00', '09:00', '13:00'];
        if (duration === 2) unavailableTimes = ['12:00', '16:00'];
    }

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
          slotElement = document.createElement('span');
          slotElement.textContent = timeSlotString;
          slotElement.classList.add('slot-time', 'unavailable-slot');
        } else {
          slotElement = document.createElement('button');
          slotElement.textContent = timeSlotString;
          slotElement.classList.add('slot-button');
          slotElement.onclick = function() { bookSlot(instructor, date, timeSlotString); };
        }
        slotsListDiv.appendChild(slotElement);
      });
      availableSlotsContainer.style.display = 'block';
    } else {
      slotsListDiv.innerHTML = '<p>Brak zdefiniowanych godzin pracy.</p>'; 
      availableSlotsContainer.style.display = 'block'; 
    }
  }

  function bookSlot(instructor, date, timeSlot) {
    alert(`Zarezerwowano jazdę:
Instruktor: ${instructor}
Data: ${date}
Godzina: ${timeSlot}

(To jest tylko symulacja, termin nie został zapisany.)`);
    
  }

  const dateInput = document.getElementById('date');
  const today = new Date().toISOString().split('T')[0];
  dateInput.setAttribute('min', today);

async function loginUser() {
    console.log("loginUser function called");
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');
    const notificationDiv = document.getElementById('notification');

    if (!loginInput || !passwordInput || !notificationDiv) {
        console.error("Nie znaleziono elementów formularza logowania lub powiadomienia.");
        alert("Wystąpił błąd interfejsu logowania.");
        return;
    }


    const login = loginInput.value;
    const password = passwordInput.value;

    notificationDiv.style.display = 'none'; 
    notificationDiv.classList.remove('success', 'error'); 

    if (!login || !password) {
        showNotification('Wprowadź login i hasło', 'error');
        return;
    }
    console.log(`Attempting login for user: ${login}`);     

    try {
        console.log("Sending fetch request to login.php..."); 
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ login: login, haslo: password })
        });
        console.log("Fetch response received, status:", response.status); 

        if (!response.ok) {
             let errorText = `Błąd serwera: ${response.status}`;
             try {
                 const errorData = await response.json();
                 errorText = errorData.message || errorText;
             } catch(e) {
                 
             }
             throw new Error(errorText);
        }

        const result = await response.json();
        console.log("Parsed JSON result:", result); 

        if (result.status === 'success') {
            sessionStorage.setItem('userId', result.id);
            sessionStorage.setItem('userRole', result.rola);
            sessionStorage.setItem('userName', `${result.imie} ${result.nazwisko}`);

            const message = `Witaj ${result.imie} ${result.nazwisko}! Jesteś ${result.rola}.`;
            showNotification(message, 'success');

             setTimeout(() => {
                 if (result.rola === 'Uczniem') {
                     window.location.href = 'zajecia_praktyczne.html';
                 } else if (result.rola === 'Instruktorem') {
                     window.location.href = 'planowanie.html'; 
                 } else {
                     window.location.href = 'info.html'; 
                 }
             }, 3000); 

        } else {
            showNotification(result.message || 'Nieprawidłowy login lub hasło', 'error');
        }

    } catch (error) {
        console.error('Login Error Caught:', error); 
        showNotification(`Wystąpił błąd: ${error.message || 'Spróbuj ponownie.'}`, 'error');
    }
}

function showNotification(message, type) {
    const notificationDiv = document.getElementById('notification');
    if (!notificationDiv) {
        console.error("Nie znaleziono elementu powiadomienia #notification.");
        alert(message); 
        return;
    }
    notificationDiv.textContent = message;
    notificationDiv.className = 'notification ' + type; 
    notificationDiv.style.display = 'block';
    notificationDiv.style.position = 'fixed'; 
    notificationDiv.style.left = '50%';
    notificationDiv.style.top = '50%';
    notificationDiv.style.transform = 'translate(-50%, -50%)'; 
    notificationDiv.style.zIndex = '1000'; 

    if (type !== 'success') {
       setTimeout(() => {
           if (notificationDiv.style.display !== 'none') { 
                notificationDiv.style.display = 'none';
           }
       }, 5000); 
    }
}