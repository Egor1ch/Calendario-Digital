function navigateToView(targetView, date) {
    console.log(`NavigateToView: Cambiando de vista ${currentView} a ${targetView}`, date);
    
    const targetDate = date ? new Date(date) : new Date(currentDate);

    currentDate = targetDate;
    
    const previousView = currentView;
    
    currentView = targetView;
    
    document.querySelectorAll('.view-selector button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    const viewButton = document.querySelector(`.view-selector button[data-view="${targetView}"]`);
    if (viewButton) {
        viewButton.classList.add('active');
    } else {
        console.error(`Botón para vista ${targetView} no encontrado`);
    }
    
    document.querySelectorAll('.calendar-view').forEach(view => {
        view.classList.remove('active');
        view.style.display = 'none';
    });
    
    const viewElement = document.getElementById(`${targetView}-view`);
    if (viewElement) {
        viewElement.classList.add('active');
        viewElement.style.display = 'block';
    } else {
        console.error(`Elemento para vista ${targetView} no encontrado`);
        return false;
    }
    
    console.log("Llamando a updateCalendar desde navigateToView");
    updateCalendar();
    updateHeader();
    
    const success = currentView === targetView && 
                    document.getElementById(`${targetView}-view`).style.display === 'block';
    
    if (!success) {
        console.error(`Fallo al cambiar de ${previousView} a ${targetView}`);
    } else {
        console.log(`Navegación exitosa de ${previousView} a ${targetView}`);
    }
    
    return success;
}

function fixYearViewDayClicks() {
    console.log("Aplicando corrección para los clics en días de la vista anual");
    
    setTimeout(() => {
        const yearDays = document.querySelectorAll('.year-day:not(.other-month)');
        
        if (yearDays.length === 0) {
            return;
        }
        
        console.log(`Encontrados ${yearDays.length} días en la vista anual para mejorar`);

        yearDays.forEach(day => {
            const dayText = day.textContent.trim();
            if (!dayText || isNaN(parseInt(dayText))) {
                return;
            }
            
            const dayNum = parseInt(dayText);
            
            const monthContainer = day.closest('.year-month');
            if (!monthContainer) {
                return;
            }
            
            const monthHeader = monthContainer.querySelector('.year-month-header');
            if (!monthHeader) {
                return;
            }
            
            const monthName = monthHeader.textContent.trim().toLowerCase();
            const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                           'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            const monthIndex = months.indexOf(monthName);
            
            if (monthIndex === -1) {
                console.error(`No se pudo identificar el mes: ${monthName}`);
                return;
            }
            
            const year = currentDate.getFullYear();
            const date = new Date(year, monthIndex, dayNum);
            
            day.dataset.fixedNavigation = 'true';
            
            day.style.cursor = 'pointer';
            
            day.addEventListener('click', (e) => {
                if (e.target.dataset.handledByImprovedNavigation === 'true') {
                    return;
                }
                
                e.target.dataset.handledByImprovedNavigation = 'true';
                if (e.ctrlKey || e.metaKey || e.shiftKey) {
                    return;
                }
                
                e.stopPropagation();
                
                console.log(`Clic mejorado en día ${dayNum} de ${monthName}`);
                navigateToView('day', date);
            });
        });
        
        console.log("Corrección para los clics en días de la vista anual aplicada");
    }, 200);
}

function setupViewChangeObserver() {
    let lastView = currentView;
    
    setInterval(() => {
        if (currentView !== lastView) {
            console.log(`Vista cambiada de ${lastView} a ${currentView}`);
            lastView = currentView;
            
            if (currentView === 'year') {
                console.log("Vista de año detectada, aplicando mejoras");
                fixYearViewDayClicks();
            }
        }
    }, 500);
}

document.addEventListener('DOMContentLoaded', () => {
    console.log("Inicializando ayudantes de navegación");
    
    if (currentView === 'year') {
        fixYearViewDayClicks();
    }
    
    setupViewChangeObserver();
    
    const originalRenderYearView = window.renderYearView;
    if (typeof originalRenderYearView === 'function') {
        window.renderYearView = function() {
            originalRenderYearView.apply(this, arguments);
            fixYearViewDayClicks();
        };
        console.log("Función renderYearView mejorada");
    }
    
    console.log("Ayudantes de navegación inicializados");
});
