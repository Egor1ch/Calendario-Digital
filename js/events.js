let events = [];
let currentEditingEvent = null;

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM cargado - Iniciando events.js');
    
    window.debugModal = function() {
        const button = document.getElementById('create-event-sidebar');
        const modal = document.getElementById('event-modal');
        console.log('=== DEBUG MODAL ===');
        console.log('Botón:', button);
        console.log('Modal:', modal);
        console.log('Modal display:', modal ? modal.style.display : 'Modal no encontrado');
        console.log('Modal computed style:', modal ? window.getComputedStyle(modal).display : 'N/A');
        console.log('==================');    };
    
    loadEvents();
    
    const createButton = document.getElementById('create-event-sidebar');
    if (createButton) {
        createButton.addEventListener('click', () => {
            console.log('Botón create-event-sidebar clickeado');
            showEventModal();
        });
    } else {
        console.error('ERROR: Botón create-event-sidebar no encontrado');
    }
    
    const closeButton = document.querySelector('#event-modal .close-modal');
    if (closeButton) {
        closeButton.addEventListener('click', () => {
            hideEventModal();
        });
    } else {
        console.error('ERROR: Botón close-modal no encontrado');
    }
    
    document.getElementById('event-form').addEventListener('submit', handleEventSubmit);
    
    document.getElementById('delete-btn').addEventListener('click', deleteEvent);
    
    setupCategoryFilters();
});

function setupCategoryFilters() {
    document.querySelectorAll('.category-item input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            filterEventsByCategory();
        });
    });
}

function filterEventsByCategory() {
    const checkedCategories = Array.from(document.querySelectorAll('.category-item input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.id.replace('cat-', ''));
    
    document.querySelectorAll('.event').forEach(eventEl => {
        const category = eventEl.dataset.category;
        let matchCategory = false;
        
        if ((category === 'event' && checkedCategories.includes('events')) ||
            (category === 'party' && checkedCategories.includes('parties'))) {
            matchCategory = true;
        }
        
        if (category.startsWith('custom_')) {
            const catId = category.replace('custom_', '');
            if (checkedCategories.includes(`custom-${catId}`)) {
                matchCategory = true;
            }
        }
        
        eventEl.style.display = matchCategory ? 'block' : 'none';
    });
    
    if (currentView === 'year') {
        renderYearView();
    } else if (currentView === 'month') {
        updateEventCounters();
    }
}

function updateEventCounters() {
    const checkedCategories = Array.from(document.querySelectorAll('.category-item input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.id.replace('cat-', ''));
    
    document.querySelectorAll('.day').forEach(dayEl => {
        const dateStr = dayEl.dataset.date;
        const eventsContainer = dayEl.querySelector('.events');
        
        if (eventsContainer) {
            let hasVisibleEvents = false;
            eventsContainer.querySelectorAll('.event').forEach(eventEl => {
                if (eventEl.style.display !== 'none') {
                    hasVisibleEvents = true;
                }
            });
            
            if (!hasVisibleEvents) {
                dayEl.classList.add('no-visible-events');
            } else {
                dayEl.classList.remove('no-visible-events');
            }
        }
    });
}

function loadEvents() {
    fetch('api/event_actions.php?action=getAll')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                events = data.events.map(event => {
                    const dateStr = event.fecha;
                    const timeStr = event.hora || '00:00:00';
                    const [year, month, day] = dateStr.split('-').map(num => parseInt(num));
                    const [hours, minutes, seconds] = timeStr.split(':').map(num => parseInt(num));
                    
                    return {
                        id: event.id.toString(),
                        title: event.titulo,
                        date: new Date(year, month - 1, day, hours, minutes, seconds),
                        category: event.categoria,
                        description: event.descripcion || ''
                    };
                });
                
                renderEvents();
            } else {
                console.error('Error al cargar eventos:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al realizar la petición:', error);
            
            const storedEvents = localStorage.getItem('calendarEvents');
            if (storedEvents) {
                events = JSON.parse(storedEvents);
                
                events.forEach(event => {
                    if (typeof event.date === 'string') {
                        event.date = new Date(event.date);
                    }
                });
                
                renderEvents();
            }
        });
}

function saveEventsLocally() {
    localStorage.setItem('calendarEvents', JSON.stringify(events));
}

function renderEvents() {
    document.querySelectorAll('.events').forEach(container => {
        container.innerHTML = '';
    });
    
    events.forEach(event => {
        const dateStr = formatDate(event.date);
        const dayElement = document.querySelector(`.day[data-date="${dateStr}"]`);
        
        if (dayElement) {
            const eventsContainer = dayElement.querySelector('.events');
            const eventElement = document.createElement('div');
            eventElement.classList.add('event');
            eventElement.dataset.id = event.id;
            eventElement.dataset.category = event.category;
            eventElement.textContent = event.title;
            
            if (event.category.startsWith('custom_') && typeof getCategoryColor === 'function') {
                const color = getCategoryColor(event.category);
                eventElement.style.backgroundColor = color;
                eventElement.style.color = isLightColor(color) ? 'black' : 'white';
            }
            
            eventElement.addEventListener('click', (e) => {
                e.stopPropagation();
                showEventModal(event);
            });
            
            eventsContainer.appendChild(eventElement);
        }
    });
}

function renderEventsInWeekView() {
    document.querySelectorAll('.week-event').forEach(el => el.remove());
    
    events.forEach(event => {
        const dateStr = formatDate(event.date);
        const hour = event.date.getHours();
        const minutes = event.date.getMinutes();
        const dayColumn = document.querySelector(`.day-column[data-date="${dateStr}"]`);
        
        if (dayColumn) {
            const hourCell = dayColumn.querySelector(`.hour-cell[data-hour="${hour}"]`);
            
            if (hourCell) {
                const eventElement = document.createElement('div');
                eventElement.classList.add('event', 'week-event');
                eventElement.dataset.id = event.id;
                eventElement.dataset.category = event.category;
                
                if (event.category.startsWith('custom_') && typeof getCategoryColor === 'function') {
                    const color = getCategoryColor(event.category);
                    eventElement.style.backgroundColor = color;
                    eventElement.style.color = isLightColor(color) ? 'black' : 'white';
                }
                
                let durationMinutes = 60; // Por defecto, 1 hora
                if (event.endTime) {
                    const endTime = new Date(event.endTime);
                    durationMinutes = (endTime - event.date) / (1000 * 60);
                }
                
                const topOffset = (minutes / 60) * 100;
                eventElement.style.top = `${topOffset}%`;
                
                const heightPercentage = Math.min((durationMinutes / 60) * 100, 100 - topOffset);
                eventElement.style.height = `${heightPercentage}%`;
                
                const timeStr = event.date.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
                
                eventElement.innerHTML = `
                    <div class="event-time">${timeStr}</div>
                    <div class="event-title">${event.title}</div>
                `;
                
                eventElement.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showEventModal(event);
                });
                
                hourCell.appendChild(eventElement);
            }
        }
    });
}

function renderEventsInDayView() {
    document.querySelectorAll('.day-event').forEach(el => el.remove());
    
    const dateStr = formatDate(currentDate);
    const contentColumn = document.querySelector('.day-content-column');
    
    if (!contentColumn) return;
    
    const eventsByHour = {};
    
    events.forEach(event => {
        if (formatDate(event.date) === dateStr) {
            const hour = event.date.getHours();
            if (!eventsByHour[hour]) {
                eventsByHour[hour] = [];
            }
            eventsByHour[hour].push(event);
        }
    });
    
    for (const hour in eventsByHour) {
        const hourContent = contentColumn.querySelector(`.day-hour-content[data-hour="${hour}"]`);
        
        if (hourContent) {
            eventsByHour[hour].forEach(event => {
                const eventElement = document.createElement('div');
                eventElement.classList.add('event', 'day-event');
                eventElement.dataset.id = event.id;
                eventElement.dataset.category = event.category;
                
                if (event.category.startsWith('custom_') && typeof getCategoryColor === 'function') {
                    const color = getCategoryColor(event.category);
                    eventElement.style.backgroundColor = color;
                    eventElement.style.color = isLightColor(color) ? 'black' : 'white';
                }
                
                const timeStr = event.date.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
                
                eventElement.innerHTML = `
                    <div class="event-time">${timeStr}</div>
                    <div class="event-title">${event.title}</div>
                `;
                
                eventElement.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showEventModal(event);
                });
                
                hourContent.appendChild(eventElement);
            });
        }
    }
}

function showEventModal(event = null, date = new Date()) {
    console.log('showEventModal llamada', { event, date });
    const modal = document.getElementById('event-modal');
    const form = document.getElementById('event-form');
    const deleteBtn = document.getElementById('delete-btn');
    const modalTitle = document.getElementById('modal-title');
    
    console.log('Elementos del modal:', { modal, form, deleteBtn, modalTitle });
    
    if (!modal) {
        console.error('ERROR: Modal event-modal no encontrado');
        return;
    }
    
    if (!form) {
        console.error('ERROR: Formulario event-form no encontrado');
        return;
    }
    
    form.reset();
    
    if (event) {
        currentEditingEvent = event;
        modalTitle.textContent = 'Editar evento';
        
        document.getElementById('event-id').value = event.id;
        document.getElementById('event-title').value = event.title;
        
        const dateStr = formatDate(event.date);
        document.getElementById('event-date').value = dateStr;
        
        const hours = String(event.date.getHours()).padStart(2, '0');
        const minutes = String(event.date.getMinutes()).padStart(2, '0');
        document.getElementById('event-time').value = `${hours}:${minutes}`;
        
        document.getElementById('event-category').value = event.category;
        document.getElementById('event-description').value = event.description || '';
        
        deleteBtn.style.display = 'block';
    } else {
        currentEditingEvent = null;
        modalTitle.textContent = 'Nuevo evento';
        
        document.getElementById('event-id').value = '';
        
        const dateStr = formatDate(date);
        document.getElementById('event-date').value = dateStr;
        
        if (date.getHours() || date.getMinutes()) {
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            document.getElementById('event-time').value = `${hours}:${minutes}`;
        }
          deleteBtn.style.display = 'none';
    }
    
    console.log('Mostrando modal...');
    modal.style.display = 'flex';
    console.log('Modal mostrado, display:', modal.style.display);
}

function hideEventModal() {
    const modal = document.getElementById('event-modal');
    modal.style.display = 'none';
    currentEditingEvent = null;
}

function handleEventSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const title = document.getElementById('event-title').value;
    const dateStr = document.getElementById('event-date').value;
    const timeStr = document.getElementById('event-time').value || '00:00';
    const category = document.getElementById('event-category').value;
    const description = document.getElementById('event-description').value;
    
    const [year, month, day] = dateStr.split('-').map(num => parseInt(num));
    const [hours, minutes] = timeStr.split(':').map(num => parseInt(num));
    const date = new Date(year, month - 1, day, hours, minutes);
    
    const formData = new FormData();
    
    if (currentEditingEvent) {
        formData.append('action', 'update');
        formData.append('id', currentEditingEvent.id);
    } else {
        formData.append('action', 'create');
    }
    
    formData.append('title', title);
    formData.append('date', dateStr);
    formData.append('time', timeStr);
    formData.append('category', category);
    formData.append('description', description);
    
    fetch('api/event_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (currentEditingEvent) {
                const index = events.findIndex(ev => ev.id === currentEditingEvent.id);
                if (index !== -1) {
                    events[index] = {
                        id: currentEditingEvent.id,
                        title,
                        date,
                        category,
                        description
                    };
                }
            } else {
                const newEvent = {
                    id: data.event.id.toString(),
                    title,
                    date,
                    category,
                    description
                };
                events.push(newEvent);
            }
            
            saveEventsLocally();
            
            updateCalendar();
            hideEventModal();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar el evento. Inténtalo de nuevo.');
    });
}

function deleteEvent() {
    if (currentEditingEvent) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', currentEditingEvent.id);
        
        fetch('api/event_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                events = events.filter(event => event.id !== currentEditingEvent.id);

                saveEventsLocally();
                updateCalendar();
                hideEventModal();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el evento. Inténtalo de nuevo.');
        });
    }
}
