// Variables globales
let currentDate = new Date();
let currentView = 'month';
let isSidebarCollapsed = false;
let miniCalendarDate = new Date();

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar el calendario
    updateCalendar();
    
    // Event listeners para navegación
    document.getElementById('prev-btn').addEventListener('click', () => navigateCalendar('prev'));
    document.getElementById('next-btn').addEventListener('click', () => navigateCalendar('next'));
    document.getElementById('today-btn').addEventListener('click', () => {
        currentDate = new Date();
        
        // Actualizar mini-calendario cuando se presiona "Hoy" en la vista mensual
        if (currentView === 'month') {
            miniCalendarDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        }
        
        updateCalendar();
    });
    
    // Mejorar el manejo de cambio de vistas
    const viewButtons = document.querySelectorAll('.view-selector button');
    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Actualizar vista actual
            currentView = button.dataset.view;
            
            // Actualizar botones activos
            document.querySelectorAll('.view-selector button').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
            
            // Ocultar todas las vistas
            document.querySelectorAll('.calendar-view').forEach(view => {
                view.classList.remove('active');
                view.style.display = 'none';
            });
            
            // Mostrar solo la vista seleccionada
            const activeView = document.getElementById(`${currentView}-view`);
            activeView.classList.add('active');
            activeView.style.display = 'block';
            
            // Actualizar el calendario con la vista seleccionada
            updateCalendar();
        });
    });
    
    // Event listeners para navegación del mini calendario
    document.getElementById('mini-prev').addEventListener('click', () => {
        miniCalendarDate = new Date(miniCalendarDate.getFullYear(), miniCalendarDate.getMonth() - 1, 1);
        renderMiniCalendar();
    });
    
    document.getElementById('mini-next').addEventListener('click', () => {
        miniCalendarDate = new Date(miniCalendarDate.getFullYear(), miniCalendarDate.getMonth() + 1, 1);
        renderMiniCalendar();
    });
    
    // Event listener para botón de crear evento en la barra lateral
    document.getElementById('create-event-sidebar').addEventListener('click', () => {
        showEventModal();
    });
    
    // Añadir botón de toggle para sidebar en dispositivos móviles
    createMobileSidebarToggle();
    
    // Event listener para toggle del tema (claro/oscuro)
    document.getElementById('theme-toggle').addEventListener('change', toggleTheme);
    
    // Cargar preferencia de tema guardada
    const savedDarkMode = localStorage.getItem('darkMode');
    if (savedDarkMode === 'true') {
        document.body.classList.add('dark-mode');
        document.getElementById('theme-toggle').checked = true;
    }
    
    // Event listeners para categorías
    document.querySelectorAll('.category-item input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', filterEventsByCategory);
    });
    
    // Event listener para sección de categorías (expandir/colapsar)
    document.querySelector('.toggle-section').addEventListener('click', toggleCategoriesSection);
    
    // Inicializar mini calendario
    renderMiniCalendar();
});

// Función para alternar entre tema claro y oscuro
function toggleTheme(e) {
    const isDarkMode = e.target.checked;
    
    if (isDarkMode) {
        document.body.classList.add('dark-mode');
        // Guardar preferencia en localStorage
        localStorage.setItem('darkMode', 'true');
    } else {
        document.body.classList.remove('dark-mode');
        // Guardar preferencia en localStorage
        localStorage.setItem('darkMode', 'false');
    }
}

// Función para filtrar eventos por categoría
function filterEventsByCategory() {
    const checkedCategories = Array.from(document.querySelectorAll('.category-item input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.id.replace('cat-', ''));
    
    // Para eventos en la vista mensual
    document.querySelectorAll('.event').forEach(eventEl => {
        const category = eventEl.dataset.category;
        const matchCategory = (category === 'event' && checkedCategories.includes('events')) ||
                             (category === 'task' && checkedCategories.includes('tasks')) ||
                             (category === 'party' && checkedCategories.includes('parties')) ||
                             (category === 'personal' && checkedCategories.includes('personal'));
        
        eventEl.style.display = matchCategory ? 'block' : 'none';
    });
}

// Función para expandir/colapsar la sección de categorías
function toggleCategoriesSection() {
    const categoriesList = document.querySelector('.categories-list');
    const toggleBtn = document.querySelector('.toggle-section i');
    
    if (categoriesList.style.display === 'none') {
        categoriesList.style.display = 'flex';
        toggleBtn.className = 'fas fa-chevron-down';
    } else {
        categoriesList.style.display = 'none';
        toggleBtn.className = 'fas fa-chevron-right';
    }
}

// Renderizar mini calendario en el sidebar con datos del mes actual
function renderMiniCalendar() {
    const miniCalendar = document.getElementById('mini-calendar');
    const year = miniCalendarDate.getFullYear();
    const month = miniCalendarDate.getMonth();
    
    // Crear encabezado
    const calendarHeader = document.createElement('div');
    calendarHeader.classList.add('mini-calendar-header');
    calendarHeader.textContent = miniCalendarDate.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
    
    // Crear contenedor de días
    const daysContainer = document.createElement('div');
    daysContainer.classList.add('mini-days-container');
    
    // Crear cabecera de días de la semana
    const weekdays = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
    weekdays.forEach(day => {
        const dayEl = document.createElement('div');
        dayEl.classList.add('mini-day-header');
        dayEl.textContent = day;
        daysContainer.appendChild(dayEl);
    });
    
    // Primer día del mes
    const firstDay = new Date(year, month, 1);
    // Último día del mes
    const lastDay = new Date(year, month + 1, 0);
    
    // Ajustar el día de la semana (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
    let firstDayOfWeek = firstDay.getDay() - 1;
    if (firstDayOfWeek < 0) firstDayOfWeek = 6;
    
    // Días del mes anterior
    for (let i = 0; i < firstDayOfWeek; i++) {
        const day = document.createElement('div');
        day.classList.add('mini-day', 'other-month');
        daysContainer.appendChild(day);
    }
    
    // Días del mes actual
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const day = document.createElement('div');
        day.classList.add('mini-day');
        day.textContent = i;
        
        const date = new Date(year, month, i);
        if (isToday(date)) {
            day.classList.add('today');
        }
        
        // Si es el día seleccionado actualmente
        if (currentDate.getDate() === i && currentDate.getMonth() === month && currentDate.getFullYear() === year) {
            day.classList.add('selected');
        }
        
        // Al hacer clic, cambia a la fecha seleccionada y cambia a la vista diaria
        day.addEventListener('click', () => {
            currentDate = new Date(year, month, i);
            
            // Cambiar a la vista de día
            currentView = 'day';
            
            // Actualizar botones de la vista
            document.querySelectorAll('.view-selector button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.view-selector button[data-view="day"]`).classList.add('active');
            
            // Ocultar todas las vistas y mostrar solo la de día
            document.querySelectorAll('.calendar-view').forEach(view => {
                view.classList.remove('active');
                view.style.display = 'none';
            });
            const dayView = document.getElementById('day-view');
            dayView.classList.add('active');
            dayView.style.display = 'block';
            
            updateCalendar();
        });
        
        daysContainer.appendChild(day);
    }
    
    miniCalendar.innerHTML = '';
    miniCalendar.appendChild(calendarHeader);
    miniCalendar.appendChild(daysContainer);
}

// Actualizar el calendario según la vista actual
function updateCalendar() {
    switch (currentView) {
        case 'year':
            renderYearView();
            break;
        case 'month':
            renderMonthView();
            break;
        case 'week':
            renderWeekView();
            break;
        case 'day':
            renderDayView();
            break;
    }
    // Renderizar eventos en vista activa
    renderEvents();
    if (currentView === 'week') {
        renderEventsInWeekView();
    } else if (currentView === 'day') {
        renderEventsInDayView();
    }
}

// Actualizar el encabezado del calendario
function updateHeader() {
    let displayText;
    
    if (currentView === 'year') {
        // Modificar para que muestre "Año" seguido del año correspondiente
        displayText = `Año ${currentDate.getFullYear()}`;
    } else {
        // Mantener el formato actual para otras vistas
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        displayText = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    }
    
    document.getElementById('current-month-year').textContent = displayText;
}

// Navegar entre periodos (año, mes, semana, día)
function navigateCalendar(direction) {
    switch(currentView) {
        case 'year':
            if (direction === 'prev') {
                currentDate = new Date(currentDate.getFullYear() - 1, 0, 1);
            } else {
                currentDate = new Date(currentDate.getFullYear() + 1, 0, 1);
            }
            break;
        case 'month':
            if (direction === 'prev') {
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
                // Actualizar mini-calendario cuando se cambia en la vista mensual
                miniCalendarDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            } else {
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
                // Actualizar mini-calendario cuando se cambia en la vista mensual
                miniCalendarDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            }
            break;
        case 'week':
            const days = direction === 'prev' ? -7 : 7;
            currentDate.setDate(currentDate.getDate() + days);
            break;
        case 'day':
            const day = direction === 'prev' ? -1 : 1;
            currentDate.setDate(currentDate.getDate() + day);
            break;
    }
    
    updateCalendar();
}

// Renderizar vista anual
function renderYearView() {
    const yearGrid = document.getElementById('year-grid');
    yearGrid.innerHTML = '';
    
    const year = currentDate.getFullYear();
    
    // Crear un contenedor para cada mes
    for (let month = 0; month < 12; month++) {
        const monthContainer = document.createElement('div');
        monthContainer.classList.add('year-month');
        
        // Crear el encabezado del mes
        const monthHeader = document.createElement('div');
        monthHeader.classList.add('year-month-header');
        
        const monthDate = new Date(year, month, 1);
        monthHeader.textContent = monthDate.toLocaleDateString('es-ES', { month: 'long' });
        
        // Crear el contenedor para los días
        const monthDays = document.createElement('div');
        monthDays.classList.add('year-month-days');
        
        // Crear los días de la semana
        const weekdaysContainer = document.createElement('div');
        weekdaysContainer.classList.add('year-month-weekdays');
        
        const weekdays = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        weekdays.forEach(day => {
            const dayEl = document.createElement('div');
            dayEl.textContent = day;
            weekdaysContainer.appendChild(dayEl);
        });
        
        // Crear la cuadrícula de días
        const daysGrid = document.createElement('div');
        daysGrid.classList.add('year-month-grid');
        
        // Primer día del mes
        const firstDay = new Date(year, month, 1);
        // Último día del mes
        const lastDay = new Date(year, month + 1, 0);
        
        // Ajustar el día de la semana (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
        let firstDayOfWeek = firstDay.getDay() - 1;
        if (firstDayOfWeek < 0) firstDayOfWeek = 6;
        
        // Días del mes anterior
        for (let i = 0; i < firstDayOfWeek; i++) {
            const day = document.createElement('div');
            day.classList.add('year-day', 'other-month');
            daysGrid.appendChild(day);
        }
        
        // Días del mes actual
        for (let i = 1; i <= lastDay.getDate(); i++) {
            const day = document.createElement('div');
            day.classList.add('year-day');
            day.textContent = i;
            
            const date = new Date(year, month, i);
            
            // Verificar si es hoy
            if (isToday(date)) {
                day.classList.add('today');
            }
            
            // Verificar si hay eventos para este día
            const hasEvents = checkEventsForDate(date);
            if (hasEvents) {
                const indicator = document.createElement('div');
                indicator.classList.add('year-day-event-indicator');
                day.appendChild(indicator);
            }
            
            // Añadir evento de clic para navegar a la vista diaria
            day.addEventListener('click', () => {
                currentDate = date;
                currentView = 'day';
                
                // Actualizar botones de la vista
                document.querySelectorAll('.view-selector button').forEach(btn => {
                    btn.classList.remove('active');
                });
                document.querySelector(`.view-selector button[data-view="day"]`).classList.add('active');
                
                updateCalendar();
            });
            
            daysGrid.appendChild(day);
        }
        
        // Días del mes siguiente para completar la cuadrícula
        const totalDays = firstDayOfWeek + lastDay.getDate();
        const remainingDays = Math.ceil(totalDays / 7) * 7 - totalDays;
        
        for (let i = 0; i < remainingDays; i++) {
            const day = document.createElement('div');
            day.classList.add('year-day', 'other-month');
            daysGrid.appendChild(day);
        }
        
        // Añadir elementos al contenedor del mes
        monthDays.appendChild(weekdaysContainer);
        monthDays.appendChild(daysGrid);
        
        monthContainer.appendChild(monthHeader);
        monthContainer.appendChild(monthDays);
        
        // Añadir evento de clic para el encabezado del mes (navegar a vista mensual)
        monthHeader.addEventListener('click', () => {
            currentDate = new Date(year, month, 1);
            currentView = 'month';
            
            // Actualizar botones de la vista
            document.querySelectorAll('.view-selector button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.view-selector button[data-view="month"]`).classList.add('active');
            
            updateCalendar();
        });
        
        yearGrid.appendChild(monthContainer);
    }
}

// Función para verificar si hay eventos en una fecha específica
function checkEventsForDate(date) {
    if (!events || !events.length) return false;
    
    const dateStr = formatDate(date);
    return events.some(event => formatDate(event.date) === dateStr);
}

// Renderizar vista mensual
function renderMonthView() {
    const calendarDays = document.getElementById('calendar-days');
    calendarDays.innerHTML = '';
    
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Primer día del mes
    const firstDay = new Date(year, month, 1);
    // Último día del mes
    const lastDay = new Date(year, month + 1, 0);
    
    // Ajustar el día de la semana (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
    let firstDayOfWeek = firstDay.getDay() - 1;
    if (firstDayOfWeek < 0) firstDayOfWeek = 6; // Si es domingo (0), ajustamos a 6
    
    // Días del mes anterior para completar la primera semana
    const prevMonthDays = firstDayOfWeek;
    const prevMonth = new Date(year, month, 0);
    
    for (let i = prevMonthDays - 1; i >= 0; i--) {
        const day = document.createElement('div');
        day.classList.add('day', 'other-month');
        
        const dayNumber = prevMonth.getDate() - i;
        day.innerHTML = `<div class="day-number">${dayNumber}</div><div class="events"></div>`;
        
        const date = new Date(year, month - 1, dayNumber);
        day.dataset.date = formatDate(date);
        
        day.addEventListener('click', () => handleDayClick(date));
        calendarDays.appendChild(day);
    }
    
    // Días del mes actual
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const day = document.createElement('div');
        day.classList.add('day');
        
        const date = new Date(year, month, i);
        if (isToday(date)) {
            day.classList.add('today');
        }
        
        day.innerHTML = `<div class="day-number">${i}</div><div class="events"></div>`;
        day.dataset.date = formatDate(date);
        
        day.addEventListener('click', () => handleDayClick(date));
        calendarDays.appendChild(day);
    }
    
    // Días del mes siguiente para completar la última semana
    const totalDaysDisplayed = prevMonthDays + lastDay.getDate();
    const nextMonthDays = 42 - totalDaysDisplayed; // 42 = 6 filas de 7 días
    
    for (let i = 1; i <= nextMonthDays; i++) {
        const day = document.createElement('div');
        day.classList.add('day', 'other-month');
        
        day.innerHTML = `<div class="day-number">${i}</div><div class="events"></div>`;
        
        const date = new Date(year, month + 1, i);
        day.dataset.date = formatDate(date);
        
        day.addEventListener('click', () => handleDayClick(date));
        calendarDays.appendChild(day);
    }
    
    // Mostrar eventos en el calendario
    renderEvents();
}

// Renderizar vista semanal mejorada
function renderWeekView() {
    const weekView = document.getElementById('week-view');
    weekView.innerHTML = '';
    
    // Obtener el lunes de la semana actual
    const currentDay = currentDate.getDay(); // 0 = Dom, 1 = Lun, ...
    const mondayOffset = currentDay === 0 ? -6 : 1 - currentDay;
    const monday = new Date(currentDate);
    monday.setDate(currentDate.getDate() + mondayOffset);
    
    // Crear encabezados de días
    const weekHeader = document.createElement('div');
    weekHeader.classList.add('week-header');
    
    // Columna de hora en el encabezado
    const hourHeaderCol = document.createElement('div');
    hourHeaderCol.classList.add('hour-header');
    hourHeaderCol.textContent = 'Hora';
    weekHeader.appendChild(hourHeaderCol);
    
    // Crear contenedor de horarios
    const weekContainer = document.createElement('div');
    weekContainer.classList.add('week-container');
    weekContainer.style.height = 'calc(100% - 70px)'; // Asegurar altura suficiente
    
    // Columna de horas
    const hourColumn = document.createElement('div');
    hourColumn.classList.add('hour-column');
    
    // Asegurar que se muestren todas las horas del día
    for (let hour = 0; hour < 24; hour++) {
        const hourDiv = document.createElement('div');
        hourDiv.classList.add('hour');
        hourDiv.textContent = `${hour}:00`;
        hourDiv.style.height = '60px'; // Forzar altura fija
        hourDiv.style.minHeight = '60px';
        hourColumn.appendChild(hourDiv);
    }
    weekContainer.appendChild(hourColumn);
    
    // Crear columnas para cada día de la semana
    for (let i = 0; i < 7; i++) {
        const date = new Date(monday);
        date.setDate(monday.getDate() + i);
        
        // Encabezado del día
        const dayHeader = document.createElement('div');
        dayHeader.classList.add('day-header');
        if (isToday(date)) {
            dayHeader.classList.add('today');
        }
        
        const dayName = document.createElement('div');
        dayName.classList.add('day-name');
        dayName.textContent = date.toLocaleDateString('es-ES', { weekday: 'short' });
        
        const dayNum = document.createElement('div');
        dayNum.classList.add('day-number');
        dayNum.textContent = date.getDate();
        
        dayHeader.appendChild(dayName);
        dayHeader.appendChild(dayNum);
        
        // Hacer el encabezado del día clicable para ir a la vista diaria
        dayHeader.addEventListener('click', () => {
            currentDate = new Date(date);
            currentView = 'day';
            
            // Actualizar botones de la vista
            document.querySelectorAll('.view-selector button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.view-selector button[data-view="day"]`).classList.add('active');
            
            // Ocultar todas las vistas y mostrar solo la de día
            document.querySelectorAll('.calendar-view').forEach(view => {
                view.classList.remove('active');
                view.style.display = 'none';
            });
            const dayView = document.getElementById('day-view');
            dayView.classList.add('active');
            dayView.style.display = 'block';
            
            updateCalendar();
        });
        
        weekHeader.appendChild(dayHeader);
        
        // Columna del día
        const dayColumn = document.createElement('div');
        dayColumn.classList.add('day-column');
        dayColumn.dataset.date = formatDate(date);
        
        // Horas del día
        for (let hour = 0; hour < 24; hour++) {
            const hourDiv = document.createElement('div');
            hourDiv.classList.add('hour-cell');
            hourDiv.dataset.hour = hour;
            hourDiv.dataset.date = formatDate(date);
            hourDiv.style.height = '60px'; // Forzar altura fija
            hourDiv.style.minHeight = '60px';
            hourDiv.addEventListener('click', () => {
                const eventDate = new Date(date);
                eventDate.setHours(hour);
                showEventModal(null, eventDate);
            });
            dayColumn.appendChild(hourDiv);
        }
        
        weekContainer.appendChild(dayColumn);
    }
    
    weekView.appendChild(weekHeader);
    weekView.appendChild(weekContainer);
    
    // Añadir indicador de hora actual si estamos viendo la semana actual
    const today = new Date();
    const todayFormatted = formatDate(today);
    const todayColumn = weekContainer.querySelector(`.day-column[data-date="${todayFormatted}"]`);
    
    if (todayColumn) {
        const currentHour = today.getHours();
        const currentMinute = today.getMinutes();
        const hourCell = todayColumn.querySelector(`.hour-cell[data-hour="${currentHour}"]`);
        
        if (hourCell) {
            const indicator = document.createElement('div');
            indicator.classList.add('current-time-indicator');
            const timeString = `${String(today.getHours()).padStart(2, '0')}:${String(today.getMinutes()).padStart(2, '0')}`;
            indicator.setAttribute('data-time', timeString);
            
            const offsetPercentage = (currentMinute / 60) * 100;
            indicator.style.top = `${offsetPercentage}%`;
            hourCell.appendChild(indicator);
            
            // Desplazar automáticamente a la hora actual
            setTimeout(() => {
                hourCell.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 200);
        }
    }
    
    // Mostrar eventos en la vista semanal
    renderEventsInWeekView();
}

// Renderizar vista diaria mejorada
function renderDayView() {
    const dayView = document.getElementById('day-view');
    dayView.innerHTML = '';
    
    // Encabezado con la fecha actual
    const dayHeader = document.createElement('div');
    dayHeader.classList.add('day-view-header');
    
    // Si es hoy, mostrar indicador
    if (isToday(currentDate)) {
        const todayIndicator = document.createElement('div');
        todayIndicator.classList.add('today-indicator');
        todayIndicator.textContent = 'Hoy';
        dayHeader.appendChild(todayIndicator);
    }
    
    const dayDate = document.createElement('div');
    dayDate.classList.add('day-date');
    const formattedDate = currentDate.toLocaleDateString('es-ES', { 
        weekday: 'long', 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
    // Capitalizar la primera letra
    dayDate.textContent = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
    dayHeader.appendChild(dayDate);
    
    // Contenedor principal de la vista de día
    const dayContainer = document.createElement('div');
    dayContainer.classList.add('day-view-container');
    dayContainer.style.height = 'calc(100% - 60px)'; // Asegurar altura suficiente
    
    // Columna de horas
    const hoursColumn = document.createElement('div');
    hoursColumn.classList.add('day-hours-column');
    
    // Columna de contenido
    const contentColumn = document.createElement('div');
    contentColumn.classList.add('day-content-column');
    
    // Horas del día - asegurar que se muestren las 24 horas
    for (let hour = 0; hour < 24; hour++) {
        // Hora en la columna izquierda
        const hourDiv = document.createElement('div');
        hourDiv.classList.add('day-hour');
        hourDiv.textContent = `${hour}:00`;
        hourDiv.style.height = '60px'; // Forzar altura fija
        hourDiv.style.minHeight = '60px';
        hoursColumn.appendChild(hourDiv);
        
        // Contenido en la columna derecha
        const hourContent = document.createElement('div');
        hourContent.classList.add('day-hour-content');
        hourContent.dataset.hour = hour;
        hourContent.dataset.date = formatDate(currentDate);
        hourContent.style.height = '60px'; // Forzar altura fija
        hourContent.style.minHeight = '60px';
        hourContent.addEventListener('click', () => {
            const eventDate = new Date(currentDate);
            eventDate.setHours(hour);
            showEventModal(null, eventDate);
        });
        contentColumn.appendChild(hourContent);
    }
    
    dayContainer.appendChild(hoursColumn);
    dayContainer.appendChild(contentColumn);
    
    dayView.appendChild(dayHeader);
    dayView.appendChild(dayContainer);
    
    // Añadir indicador de hora actual si estamos viendo el día actual
    if (isToday(currentDate)) {
        const today = new Date();
        const currentHour = today.getHours();
        const currentMinute = today.getMinutes();
        const hourContent = contentColumn.querySelector(`.day-hour-content[data-hour="${currentHour}"]`);
        
        if (hourContent) {
            const indicator = document.createElement('div');
            indicator.classList.add('current-time-indicator');
            const timeString = `${String(today.getHours()).padStart(2, '0')}:${String(today.getMinutes()).padStart(2, '0')}`;
            indicator.setAttribute('data-time', timeString);
            
            const offsetPercentage = (currentMinute / 60) * 100;
            indicator.style.top = `${offsetPercentage}%`;
            hourContent.appendChild(indicator);
            
            // Desplazar automáticamente a la hora actual
            setTimeout(() => {
                hourContent.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 200);
        }
    }
    
    // Mostrar eventos en la vista diaria
    renderEventsInDayView();
}

// Comprobar si una fecha es hoy
function isToday(date) {
    const today = new Date();
    return date.getDate() === today.getDate() &&
           date.getMonth() === today.getMonth() &&
           date.getFullYear() === today.getFullYear();
}

// Formatear fecha como YYYY-MM-DD
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Manejar click en un día
function handleDayClick(date) {
    currentDate = date;
    showEventModal(null, date);
}

// Función para crear el botón de toggle del sidebar en móviles
function createMobileSidebarToggle() {
    // Crear botón de toggle
    const toggleBtn = document.createElement('button');
    toggleBtn.classList.add('toggle-sidebar-btn');
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    
    // Crear overlay para cerrar sidebar al tocar fuera
    const overlay = document.createElement('div');
    overlay.classList.add('sidebar-overlay');
    
    // Añadir elementos al DOM
    document.body.appendChild(toggleBtn);
    document.body.appendChild(overlay);
    
    // Event listener para botón de toggle
    toggleBtn.addEventListener('click', toggleSidebar);
    
    // Event listener para overlay
    overlay.addEventListener('click', closeSidebar);
}

// Función para mostrar/ocultar el sidebar
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (sidebar.classList.contains('show')) {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    } else {
        sidebar.classList.add('show');
        overlay.classList.add('show');
    }
}

// Función para cerrar el sidebar
function closeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
}
