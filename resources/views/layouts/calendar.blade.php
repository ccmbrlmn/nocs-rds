<script>
const userRole = @json(auth()->user()->role ?? null);
</script>

<section class="relative">
  <div class="w-full relative z-10">
    <div class="w-full max-w-full mx-0 px-0">
      <div class="grid grid-cols-full gap-4 max-w-full mx-0 h-full">

        <script src="//unpkg.com/alpinejs" defer></script>

        <div class="col-span-full xl:col-span-7 px-3 py-3 sm:p-4 bg-white dark:bg-gray-800 rounded-2xl max-xl:row-start-1 w-full h-full">
          <div class="flex flex-col md:flex-row gap-4 items-start justify-between mb-4">

            <div class="flex items-center gap-3">
              <h5 id="calendar-title" class="text-2xl leading-8 font-semibold text-gray-900 dark:text-gray-200"></h5>
              <div class="flex items-center">
                <button id="prev-month" class="text-indigo-600 p-2 rounded transition-all duration-300 hover:text-white hover:bg-indigo-600">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="none">
                    <path d="M10.0002 11.9999L6 7.99971L10.0025 3.99719" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </button>
                <button id="next-month" class="text-indigo-600 p-2 rounded transition-all duration-300 hover:text-white hover:bg-indigo-600">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="none">
                    <path d="M6.00236 3.99707L10.0025 7.99723L6 11.9998" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </button>
              </div>
            </div>

            <div>
              @auth
                  <button
                      @click="openRequestForm = true"
                      class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow">
                      Create Request
                  </button>
              @endauth
            </div>

          </div>

<div class="border border-indigo-200 dark:border-gray-400 rounded-xl w-full flex flex-col">
          
            <div class="grid grid-cols-7 rounded-t-xl border-b border-indigo-200 bg-indigo-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-base font-semibold">

              <div class="py-3 border-r border-indigo-200 flex items-center justify-center cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600"
                   data-weekday="0">Sun</div>
              <div class="py-3 border-r border-indigo-200 flex items-center justify-center cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600"
                   data-weekday="1">Mon</div>
              <div class="py-3 border-r border-indigo-200 flex items-center justify-center cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600"
                   data-weekday="2">Tue</div>
              <div class="py-3 border-r border-indigo-200 flex items-center justify-center cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600"
                   data-weekday="3">Wed</div>
              <div class="py-3 border-r border-indigo-200 flex items-center justify-center cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600"
                   data-weekday="4">Thu</div>
              <div class="py-3 border-r border-indigo-200 flex items-center justify-center cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600"
                   data-weekday="5">Fri</div>
              <div class="py-3 flex items-center justify-center cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600"
                   data-weekday="6">Sat</div>
             
            </div>

            <div id="calendar-days" class="grid grid-cols-7 rounded-b-xl text-gray-900 dark:text-gray-200"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
const calendarEvents = @json($calendarEvents);
const statusColors = @json(config('status'));
let dateNotes = {};

document.addEventListener('DOMContentLoaded', async () => {
  const calendarTitle = document.getElementById('calendar-title');
  const calendarDays = document.getElementById('calendar-days');
  const prevMonthBtn = document.getElementById('prev-month');
  const nextMonthBtn = document.getElementById('next-month');
    const response = await fetch('/date-notes');
    dateNotes = await response.json();

  let currentDate = new Date();
  let currentView = 'month';
let selectedWeekdayHeader = null;

  function renderCalendar(date) {
    calendarDays.innerHTML = '';

    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const year = date.getFullYear();
    const month = date.getMonth();
    const today = new Date();
    const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;

    calendarTitle.textContent = `${monthNames[month]} ${year}`;

    const firstDayOfMonth = new Date(year, month, 1).getDay();
    const lastDateOfMonth = new Date(year, month + 1, 0).getDate();
    const lastDayOfPrevMonth = new Date(year, month, 0).getDate();

    for (let i = firstDayOfMonth; i > 0; i--) {
      calendarDays.innerHTML += `
  <div class="flex p-3 
    bg-gray-50 dark:bg-gray-700 
    border-r border-b border-indigo-200 dark:border-gray-300 
    text-xs font-semibold 
    text-gray-400 dark:text-gray-500">
    ${lastDayOfPrevMonth - i + 1}
  </div>`;
    }

    for (let day = 1; day <= lastDateOfMonth; day++) {
      const isToday = isCurrentMonth && day === today.getDate();
      const displayDate = `${year}-${String(month + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
      const eventsToday = calendarEvents.filter(ev => ev.setup_date === displayDate);

      const MAX_VISIBLE = 2;
      const visibleEvents = eventsToday.slice(0, MAX_VISIBLE);
      const hiddenCount = eventsToday.length - MAX_VISIBLE;

      let eventsHtml = '';

      visibleEvents.forEach(ev => {
        const statusLabel = ev.computed_status;
        const colorClass = statusColors[statusLabel]?.text || 'text-indigo-600';

        eventsHtml += `
          <div class="text-xs font-medium truncate ${colorClass}">
            ${ev.event_name} (${statusLabel})
          </div>`;
      });

      const dayCell = document.createElement('div');
      dayCell.className = `
  relative 
  border-r border-b border-indigo-200 dark:border-gray-300 
  text-xs font-semibold 
  flex flex-col items-start justify-start 
  p-2 pt-7 
  cursor-pointer 
  bg-white dark:bg-gray-800 
  hover:bg-indigo-50 dark:hover:bg-gray-700
`;

      dayCell.innerHTML = `
        <span class="absolute top-1 left-2 ${isToday ? 'bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center' : ''}">
          ${day}
        </span>
        <div class="calendar-events">${eventsHtml}</div>
        ${hiddenCount > 0 ? `<div class="mt-1 text-xs text-indigo-600 font-semibold">+${hiddenCount} more</div>` : ''}
      `;

      dayCell.addEventListener('click', () => {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/50 z-50 flex items-center justify-center';

        modal.innerHTML = `
          <div class="bg-white rounded-xl w-[400px] p-5">
            <div class="flex justify-between mb-3">
              <h3 class="font-semibold text-lg">${monthNames[month]} ${day}, ${year}</h3>
              <button id="close-modal">✕</button>
            </div>

            ${
              eventsToday.map(ev => {
                const statusLabel = ev.computed_status;
                const colorClass = statusColors[statusLabel]?.text || '';

                return `
                  <a href="${userRole === 'admin' ? '/admin/requests/' + ev.id : '/request-details/' + ev.id}"
                     class="block py-2 border-b text-sm ${colorClass}">
                    <strong>${ev.event_name}</strong>
                    <div class="text-xs">${statusLabel}</div>
                  </a>`;
              }).join('')
            }
          </div>
        `;

        document.body.appendChild(modal);
        modal.querySelector('#close-modal').onclick = () => modal.remove();
      });

      calendarDays.appendChild(dayCell);
    }

    const totalCells = firstDayOfMonth + lastDateOfMonth;
    const nextDays = totalCells <= 35 ? 35 - totalCells : 42 - totalCells;

    for (let i = 1; i <= nextDays; i++) {
      const emptyCell = document.createElement('div');
      emptyCell.className = `
  border-r border-b 
  border-indigo-200 dark:border-gray-300 
  p-2 
  bg-gray-50 dark:bg-gray-700
`;
      calendarDays.appendChild(emptyCell);
    }
  }

prevMonthBtn.onclick = () => {
    if (currentView === 'weekday') {
        currentView = 'month';
        calendarDays.style.display = 'grid';
        calendarDays.style.gridTemplateColumns = 'repeat(7, 1fr)';
        calendarDays.style.gap = '';
        if (selectedWeekdayHeader) {
            selectedWeekdayHeader.classList.remove('bg-indigo-200', 'dark:bg-gray-600');
            selectedWeekdayHeader = null;
        }
        renderCalendar(currentDate);
    } else {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    }
};

nextMonthBtn.onclick = () => {
    if (currentView === 'weekday') {
        currentView = 'month';
        calendarDays.style.display = 'grid';
        calendarDays.style.gridTemplateColumns = 'repeat(7, 1fr)';
        calendarDays.style.gap = '';
        if (selectedWeekdayHeader) {
            selectedWeekdayHeader.classList.remove('bg-indigo-200', 'dark:bg-gray-600');
            selectedWeekdayHeader = null;
        }
        renderCalendar(currentDate);
    } else {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    }
};

document.querySelectorAll('[data-weekday]').forEach(header => {
  header.addEventListener('click', () => {
    const weekday = parseInt(header.dataset.weekday);
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    if (selectedWeekdayHeader) {
      selectedWeekdayHeader.classList.remove('bg-indigo-200', 'dark:bg-gray-600');
    }
    
    header.classList.add('bg-indigo-200', 'dark:bg-gray-600');
    selectedWeekdayHeader = header;

    let matchingDates = [];
    const lastDate = new Date(year, month + 1, 0).getDate();

    for (let d = 1; d <= lastDate; d++) {
      const dateObj = new Date(year, month, d);
      if (dateObj.getDay() === weekday) {
        const formatted = `${year}-${String(month + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const events = calendarEvents.filter(ev => ev.setup_date === formatted);
        matchingDates.push({ date: formatted, events });
      }
    }

    currentView = 'weekday';
    renderWeekdayView(weekday, matchingDates, year, month);
  });
});

  renderCalendar(currentDate);
});

function renderWeekdayView(weekday, data, year, month) {
    const calendarDays = document.getElementById('calendar-days');
    const calendarTitle = document.getElementById('calendar-title');
    calendarDays.innerHTML = '';

    const weekdayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
calendarTitle.textContent = `${weekdayNames[weekday]}s of ${monthNames[month]} ${year}`;

    // Single-column layout for this weekday
    calendarDays.style.display = 'grid';
    calendarDays.style.gridTemplateColumns = '1fr';
    calendarDays.style.gap = '10px';

    data.forEach(item => {
        const displayDate = item.date;
        const events = item.events;

        const MAX_VISIBLE = 2;
        const visibleEvents = events.slice(0, MAX_VISIBLE);
        const hiddenCount = events.length - MAX_VISIBLE;

let eventsHtml = '';

        const cell = document.createElement('div');
        cell.className = 'relative rounded-xl p-4 flex flex-col items-start justify-start';

        cell.innerHTML = `
           <span class="absolute top-2 left-2 text-sm font-semibold">${new Date(displayDate).getDate()}</span>
<div class="calendar-events w-full mb-2">${eventsHtml}</div>
${hiddenCount > 0 ? `<div class="mt-1 text-xs text-indigo-600 font-semibold">+${hiddenCount} more</div>` : ''}
<textarea class="
  w-full 
  border border-indigo-100 dark:border-gray-600 
  bg-indigo-50 dark:bg-gray-700 
  text-gray-900 dark:text-gray-100 
  placeholder-indigo-400 dark:placeholder-gray-400 
  rounded-lg p-2 text-sm 
  auto-save-note
"
maxlength="150"
placeholder="Add note (max of 150 characters only)..."
data-date="${displayDate}">${dateNotes[displayDate]?.latest || ''}</textarea>

        `;

        calendarDays.appendChild(cell);
    });

document.querySelectorAll('.auto-save-note').forEach(textarea => {
    textarea.addEventListener('input', () => {
        const date = textarea.dataset.date;
        const note = textarea.value;

        fetch('/date-notes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ date, note })
        }).then(() => {
            dateNotes[date] = {
                latest: note,
                history: dateNotes[date]?.history || []
            };
        });
    });
});
}
</script>

<style>
#calendar-days {
    display: grid;
    grid-auto-rows: minmax(120px, auto);
}

#calendar-days > div:nth-child(7n) {
    border-right: none;
}

#calendar-days > div:nth-last-child(-n+7) {
    border-bottom: none;
}

#calendar-days > div {
    min-height: 120px;
    display: flex;
    flex-direction: column;
    position: relative;
}

#calendar-days span {
    position: absolute;
    top: 4px;
    left: 6px;
    font-size: 14px;
}

.calendar-events {
    margin-top: 4px;
    max-height: 65px;
    overflow-y: auto;
    width: 100%;
    overflow-x: hidden;
    word-break: break-word;
}

.calendar-events > a,
.calendar-events > div {
    display: block;
    white-space: nowrap; 
    overflow: hidden;  
    text-overflow: ellipsis;
}
</style>
