let currentDate = new Date();
let currentView = 'month';
let isSidebarCollapsed = false;
let miniCalendarDate = new Date();

document.addEventListener('DOMContentLoaded', () => {
    updateCalendar();
    
    document.getElementById('prev-btn').addEventListener('click', () => navigateCalendar('prev'));
    document.getElementById('next-btn').addEventListener('click', () => navigateCalendar('next'));
    document.getElementById('today-btn').addEventListener('click', () => {
        currentDate = new Date();
        
        if (currentView === 'month') {
            miniCalendarDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        }
        
        updateCalendar();
    });
    
    const viewButtons = document.querySelectorAll('.view-selector button');
    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentView = button.dataset.view;
            
            document.querySelectorAll('.view-selector button').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
            
            document.querySelectorAll('.calendar-view').forEach(view => {
                view.classList.remove('active');
                view.style.display = 'none';
            });
            
            const activeView = document.getElementById(`${currentView}-view`);
            activeView.classList.add('active');
            activeView.style.display = 'block';
            
            updateCalendar();
        });
    });
    
    if (window.innerWidth > 900) {
        initializeMiniCalendar();
    }
    
    window.addEventListener('resize', handleWindowResize);
    
    document.getElementById('create-event-sidebar').addEventListener('click', () => {
        showEventModal();
    });
    
    createMobileSidebarToggle();
    
    document.getElementById('theme-toggle').addEventListener('change', toggleTheme);
    
    const savedDarkMode = localStorage.getItem('darkMode');
    if (savedDarkMode === 'true') {
        document.body.classList.add('dark-mode');
        document.getElementById('theme-toggle').checked = true;
    }
    
    document.querySelectorAll('.category-item input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', filterEventsByCategory);
    });
    
    document.querySelector('.toggle-section').addEventListener('click', toggleCategoriesSection);
});

function handleWindowResize() {
    const miniCalendarWrapper = document.querySelector('.mini-calendar-wrapper');
    
    if (window.innerWidth <= 900) {
        if (miniCalendarWrapper) {
            miniCalendarWrapper.style.display = 'none';
        }
    } else {
        if (miniCalendarWrapper) {
            miniCalendarWrapper.style.display = 'block';
            
            if (!document.querySelector('.mini-days-container')) {
                renderMiniCalendar();
            }
        }
    }
}

function initializeMiniCalendar() {
    document.getElementById('mini-prev').addEventListener('click', () => {
        miniCalendarDate = new Date(miniCalendarDate.getFullYear(), miniCalendarDate.getMonth() - 1, 1);
        renderMiniCalendar();
    });
    
    document.getElementById('mini-next').addEventListener('click', () => {
        miniCalendarDate = new Date(miniCalendarDate.getFullYear(), miniCalendarDate.getMonth() + 1, 1);
        renderMiniCalendar();
    });
    
    renderMiniCalendar();
}

function toggleTheme(e) {
    const isDarkMode = e.target.checked;
    
    if (isDarkMode) {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'false');
    }
}

function filterEventsByCategory() {
    const checkedCategories = Array.from(document.querySelectorAll('.category-item input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.id.replace('cat-', ''));
    
    document.querySelectorAll('.event').forEach(eventEl => {
        const category = eventEl.dataset.category;
        const matchCategory = (category === 'event' && checkedCategories.includes('events')) ||
                             (category === 'task' && checkedCategories.includes('tasks')) ||
                             (category === 'party' && checkedCategories.includes('parties')) ||
                             (category === 'personal' && checkedCategories.includes('personal'));
        
        eventEl.style.display = matchCategory ? 'block' : 'none';
    });
}

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

function renderMiniCalendar() {
    const miniCalendar = document.getElementById('mini-calendar');
    const year = miniCalendarDate.getFullYear();
    const month = miniCalendarDate.getMonth();
    
    const calendarHeader = document.createElement('div');
    calendarHeader.classList.add('mini-calendar-header');
    calendarHeader.textContent = miniCalendarDate.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
    
    const daysContainer = document.createElement('div');
    daysContainer.classList.add('mini-days-container');
    
    const weekdays = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
    weekdays.forEach(day => {
        const dayEl = document.createElement('div');
        dayEl.classList.add('mini-day-header');
        dayEl.textContent = day;
        daysContainer.appendChild(dayEl);
    });
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    
    let firstDayOfWeek = firstDay.getDay() - 1;
    if (firstDayOfWeek < 0) firstDayOfWeek = 6;
    
    for (let i = 0; i < firstDayOfWeek; i++) {
        const day = document.createElement('div');
        day.classList.add('mini-day', 'other-month');
        daysContainer.appendChild(day);
    }
    
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const day = document.createElement('div');
        day.classList.add('mini-day');
        day.textContent = i;
        
        const date = new Date(year, month, i);
        if (isToday(date)) {
            day.classList.add('today');
        }
        
        if (currentDate.getDate() === i && currentDate.getMonth() === month && currentDate.getFullYear() === year) {
            day.classList.add('selected');
        }
        
        day.addEventListener('click', () => {
            currentDate = new Date(year, month, i);
            
            currentView = 'day';
            
            document.querySelectorAll('.view-selector button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.view-selector button[data-view="day"]`).classList.add('active');
            
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

function updateCalendar() {
    updateHeader();
    
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
    
    renderEvents();
    if (currentView === 'week') {
        renderEventsInWeekView();
    } else if (currentView === 'day') {
        renderEventsInDayView();
    }
    
    if (window.innerWidth > 900 && document.querySelector('.mini-calendar-wrapper').style.display !== 'none') {
        renderMiniCalendar();
    }
}

function updateHeader() {
    let displayText;
    
    if (currentView === 'year') {
        displayText = `Año ${currentDate.getFullYear()}`;
    } else if (currentView === 'month') {
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        displayText = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    } else if (currentView === 'week') {
        const currentDay = currentDate.getDay();
        const mondayOffset = currentDay === 0 ? -6 : 1 - currentDay;
        const monday = new Date(currentDate);
        monday.setDate(currentDate.getDate() + mondayOffset);
        
        const sunday = new Date(monday);
        sunday.setDate(monday.getDate() + 6);
        
        const mondayStr = monday.getDate();
        const sundayStr = sunday.getDate();
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        
        if (monday.getMonth() !== sunday.getMonth()) {
            displayText = `${mondayStr} ${monthNames[monday.getMonth()]} - ${sundayStr} ${monthNames[sunday.getMonth()]} ${currentDate.getFullYear()}`;
        } else {
            displayText = `${mondayStr} - ${sundayStr} ${monthNames[monday.getMonth()]} ${currentDate.getFullYear()}`;
        }
    } else {
        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        const formattedDate = currentDate.toLocaleDateString('es-ES', options);
        displayText = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
    }
    
    document.getElementById('current-month-year').textContent = displayText;
}

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
                if (window.innerWidth > 900) {
                    miniCalendarDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                }
            } else {
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
                if (window.innerWidth > 900) {
                    miniCalendarDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                }
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

function renderYearView() {
    const yearGrid = document.getElementById('year-grid');
    yearGrid.innerHTML = '';
    
    const year = currentDate.getFullYear();
    
    for (let month = 0; month < 12; month++) {
        const monthContainer = document.createElement('div');
        monthContainer.classList.add('year-month');
        
        const monthHeader = document.createElement('div');
        monthHeader.classList.add('year-month-header');
        
        const monthDate = new Date(year, month, 1);
        monthHeader.textContent = monthDate.toLocaleDateString('es-ES', { month: 'long' });
        
        const monthDays = document.createElement('div');
        monthDays.classList.add('year-month-days');
        
        const weekdaysContainer = document.createElement('div');
        weekdaysContainer.classList.add('year-month-weekdays');
        
        const weekdays = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        weekdays.forEach(day => {
            const dayEl = document.createElement('div');
            dayEl.textContent = day;
            weekdaysContainer.appendChild(dayEl);
        });
        
        const daysGrid = document.createElement('div');
        daysGrid.classList.add('year-month-grid');
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        
        let firstDayOfWeek = firstDay.getDay() - 1;
        if (firstDayOfWeek < 0) firstDayOfWeek = 6;
        
        for (let i = 0; i < firstDayOfWeek; i++) {
            const day = document.createElement('div');
            day.classList.add('year-day', 'other-month');
            daysGrid.appendChild(day);
        }
        
        for (let i = 1; i <= lastDay.getDate(); i++) {
            const day = document.createElement('div');
            day.classList.add('year-day');
            day.textContent = i;
            
            const date = new Date(year, month, i);
            
            if (isToday(date)) {
                day.classList.add('today');
            }
            
            const hasEvents = checkEventsForDate(date);
            if (hasEvents) {
                const indicator = document.createElement('div');
                indicator.classList.add('year-day-event-indicator');
                day.appendChild(indicator);
            }
            
            day.addEventListener('click', () => {
                currentDate = date;
                currentView = 'day';
                
                document.querySelectorAll('.view-selector button').forEach(btn => {
                    btn.classList.remove('active');
                });
                document.querySelector(`.view-selector button[data-view="day"]`).classList.add('active');
                
                updateCalendar();
            });
            
            daysGrid.appendChild(day);
        }
        
        const totalDays = firstDayOfWeek + lastDay.getDate();
        const remainingDays = Math.ceil(totalDays / 7) * 7 - totalDays;
        
        for (let i = 0; i < remainingDays; i++) {
            const day = document.createElement('div');
            day.classList.add('year-day', 'other-month');
            daysGrid.appendChild(day);
        }
        
        monthDays.appendChild(weekdaysContainer);
        monthDays.appendChild(daysGrid);
        
        monthContainer.appendChild(monthHeader);
        monthContainer.appendChild(monthDays);
        
        monthHeader.addEventListener('click', () => {
            currentDate = new Date(year, month, 1);
            currentView = 'month';
            
            document.querySelectorAll('.view-selector button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.view-selector button[data-view="month"]`).classList.add('active');
            
            updateCalendar();
        });
        
        yearGrid.appendChild(monthContainer);
    }
}

function checkEventsForDate(date) {
    if (!events || !events.length) return false;
    
    const dateStr = formatDate(date);
    return events.some(event => formatDate(event.date) === dateStr);
}

function renderMonthView() {
    const calendarDays = document.getElementById('calendar-days');
    calendarDays.innerHTML = '';
    
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    
    let firstDayOfWeek = firstDay.getDay() - 1;
    if (firstDayOfWeek < 0) firstDayOfWeek = 6;
    
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
    
    const totalDaysDisplayed = prevMonthDays + lastDay.getDate();
    const nextMonthDays = 42 - totalDaysDisplayed;
    
    for (let i = 1; i <= nextMonthDays; i++) {
        const day = document.createElement('div');
        day.classList.add('day', 'other-month');
        
        day.innerHTML = `<div class="day-number">${i}</div><div class="events"></div>`;
        
        const date = new Date(year, month + 1, i);
        day.dataset.date = formatDate(date);
        
        day.addEventListener('click', () => handleDayClick(date));
        calendarDays.appendChild(day);
    }
    
    renderEvents();
}

function renderWeekView() {
    const weekView = document.getElementById('week-view');
    weekView.innerHTML = '';
    
    const currentDay = currentDate.getDay();
    const mondayOffset = currentDay === 0 ? -6 : 1 - currentDay;
    const monday = new Date(currentDate);
    monday.setDate(currentDate.getDate() + mondayOffset);
    
    const weekHeader = document.createElement('div');
    weekHeader.classList.add('week-header');
    
    const hourHeaderCol = document.createElement('div');
    hourHeaderCol.classList.add('hour-header');
    hourHeaderCol.textContent = 'Hora';
    weekHeader.appendChild(hourHeaderCol);
    
    const weekContainer = document.createElement('div');
    weekContainer.classList.add('week-container');
    weekContainer.style.height = 'calc(100% - 70px)';
    
    const hourColumn = document.createElement('div');
    hourColumn.classList.add('hour-column');
    
    for (let hour = 0; hour < 24; hour++) {
        const hourDiv = document.createElement('div');
        hourDiv.classList.add('hour');
        hourDiv.textContent = `${hour}:00`;
        hourDiv.style.height = '60px';
        hourDiv.style.minHeight = '60px';
        hourColumn.appendChild(hourDiv);
    }
    weekContainer.appendChild(hourColumn);
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(monday);
        date.setDate(monday.getDate() + i);
        
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
        
        dayHeader.addEventListener('click', () => {
            currentDate = new Date(date);
            currentView = 'day';
            
            document.querySelectorAll('.view-selector button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.view-selector button[data-view="day"]`).classList.add('active');
            
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
        
        const dayColumn = document.createElement('div');
        dayColumn.classList.add('day-column');
        dayColumn.dataset.date = formatDate(date);
        
        for (let hour = 0; hour < 24; hour++) {
            const hourDiv = document.createElement('div');
            hourDiv.classList.add('hour-cell');
            hourDiv.dataset.hour = hour;
            hourDiv.dataset.date = formatDate(date);
            hourDiv.style.height = '60px';
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
            
            setTimeout(() => {
                hourCell.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 200);
        }
    }
    
    renderEventsInWeekView();
}

function renderDayView() {
    const dayView = document.getElementById('day-view');
    dayView.innerHTML = '';
    
    const dayHeader = document.createElement('div');
    dayHeader.classList.add('day-view-header');
    
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
    dayDate.textContent = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
    dayHeader.appendChild(dayDate);
    
    const dayContainer = document.createElement('div');
    dayContainer.classList.add('day-view-container');
    dayContainer.style.height = 'calc(100% - 60px)';
    
    const hoursColumn = document.createElement('div');
    hoursColumn.classList.add('day-hours-column');
    
    const contentColumn = document.createElement('div');
    contentColumn.classList.add('day-content-column');
    
    for (let hour = 0; hour < 24; hour++) {
        const hourDiv = document.createElement('div');
        hourDiv.classList.add('day-hour');
        hourDiv.textContent = `${hour}:00`;
        hourDiv.style.height = '60px';
        hourDiv.style.minHeight = '60px';
        hoursColumn.appendChild(hourDiv);
        
        const hourContent = document.createElement('div');
        hourContent.classList.add('day-hour-content');
        hourContent.dataset.hour = hour;
        hourContent.dataset.date = formatDate(currentDate);
        hourContent.style.height = '60px';
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
            
            setTimeout(() => {
                hourContent.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 200);
        }
    }
    
    renderEventsInDayView();
}

function isToday(date) {
    const today = new Date();
    return date.getDate() === today.getDate() &&
           date.getMonth() === today.getMonth() &&
           date.getFullYear() === today.getFullYear();
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function handleDayClick(date) {
    currentDate = date;
    showEventModal(null, date);
}

function createMobileSidebarToggle() {
    const toggleBtn = document.createElement('button');
    toggleBtn.classList.add('toggle-sidebar-btn');
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    
    const overlay = document.createElement('div');
    overlay.classList.add('sidebar-overlay');
    
    document.body.appendChild(toggleBtn);
    document.body.appendChild(overlay);
    
    toggleBtn.addEventListener('click', toggleSidebar);
    
    overlay.addEventListener('click', closeSidebar);
}

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

function closeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
}