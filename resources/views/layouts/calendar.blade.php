<section class="relative">
  <div class="w-full py-10 relative z-10">
    <div class="w-full max-w-full mx-4 px-2 lg:px-8">
      <div class="grid grid-cols-full gap-8 max-w-6xl mx-auto xl:max-w-full vh-100">

        <script src="//unpkg.com/alpinejs" defer></script>

        <div class="col-span-full xl:col-span-7 px-4 py-6 sm:p-10 bg-gradient-to-b from-white/25 to-white xl:bg-white rounded-2xl max-xl:row-start-1 w-full h-[800px]">
          <div class="flex flex-col md:flex-row gap-4 items-center justify-between mb-6">
            <div class="flex items-center gap-4">
              <h5 id="calendar-title" class="text-2xl leading-8 font-semibold text-gray-900"></h5>
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
            
            <div x-data="{ openRequestForm: false }">
              <button @click="openRequestForm = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                  Add Request
              </button>
              @include('form.request-form')
            </div>
          </div>

          <div class="border border-indigo-200 rounded-xl w-full flex-col">
            <div class="grid grid-cols-7 rounded-t-3xl border-b border-indigo-200 bg-indigo-50 text-indigo-600 text-base font-semibold">
              <div class="py-4 border-r border-indigo-200 flex items-center justify-center">Sun</div>
              <div class="py-4 border-r border-indigo-200 flex items-center justify-center">Mon</div>
              <div class="py-4 border-r border-indigo-200 flex items-center justify-center">Tue</div>
              <div class="py-4 border-r border-indigo-200 flex items-center justify-center">Wed</div>
              <div class="py-4 border-r border-indigo-200 flex items-center justify-center">Thu</div>
              <div class="py-4 border-r border-indigo-200 flex items-center justify-center">Fri</div>
              <div class="py-4 flex items-center justify-center">Sat</div>
            </div>

            <div id="calendar-days" class="grid grid-cols-7 rounded-b-xl flex-grow"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
const calendarEvents = @json($calendarEvents);
const statusColors = @json(config('status'));

document.addEventListener('DOMContentLoaded', () => {
  const calendarTitle = document.getElementById('calendar-title');
  const calendarDays = document.getElementById('calendar-days');
  const prevMonthBtn = document.getElementById('prev-month');
  const nextMonthBtn = document.getElementById('next-month');

  let currentDate = new Date();

  function renderCalendar(date) {
    calendarDays.innerHTML = '';

    const monthNames = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const year = date.getFullYear();
    const month = date.getMonth();
    const today = new Date();
    const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;

    calendarTitle.textContent = `${monthNames[month]} ${year}`;

    const firstDayOfMonth = new Date(year, month, 1).getDay();
    const lastDateOfMonth = new Date(year, month + 1, 0).getDate();
    const lastDayOfPrevMonth = new Date(year, month, 0).getDate();

    for (let i = firstDayOfMonth; i > 0; i--) {
      calendarDays.innerHTML += `<div class="flex p-3.5 bg-gray-50 border-r border-b border-indigo-200 text-xs font-semibold text-gray-400">${lastDayOfPrevMonth - i + 1}</div>`;
    }

    for (let day = 1; day <= lastDateOfMonth; day++) {
      const isToday = isCurrentMonth && day === today.getDate();
      const displayDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const eventsToday = calendarEvents.filter(ev => ev.setup_date === displayDate);
      const MAX_VISIBLE = 2;
      const visibleEvents = eventsToday.slice(0, MAX_VISIBLE);
      const hiddenCount = eventsToday.length - MAX_VISIBLE;

      let eventsHtml = '';
      visibleEvents.forEach(ev => {
        let statusLabel = ev.computed_status;
        let colorClass = statusColors[statusLabel] ? statusColors[statusLabel].text : 'text-indigo-600';
        eventsHtml += `<div class="text-xs font-medium truncate ${colorClass}">${ev.event_name} (${statusLabel})</div>`;
      });

      calendarDays.innerHTML += `
        <div x-data="{ open: false }" @click="open = true" class="relative border-r border-b border-blue-200 text-xs font-semibold flex flex-col items-start justify-start p-2 pt-8 cursor-pointer hover:bg-indigo-50">
          <span class="absolute top-1 left-2 ${isToday ? 'bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center' : ''}">${day}</span>
          <div class="calendar-events">${eventsHtml}</div>
          ${hiddenCount > 0 ? `<div class="mt-1 text-xs text-indigo-600 font-semibold">+${hiddenCount} more</div>` : ''}
          <div x-show="open" @click.stop class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-xl w-[420px] p-5">
              <div class="flex justify-between mb-3">
                <h3 class="font-semibold text-lg">${monthNames[month]} ${day}, ${year}</h3>
                <button @click="open = false" class="text-gray-500">✕</button>
              </div>
              ${eventsToday.map(ev => {
                let statusLabel = ev.computed_status;
                let colorClass = statusColors[statusLabel] ? statusColors[statusLabel].text : 'text-indigo-600';
                return `<div class="py-2 border-b text-sm">
                          <strong>${ev.event_name}</strong>
                          <div class="${colorClass} text-xs">${statusLabel}</div>
                        </div>`;
              }).join('')}
            </div>
          </div>
        </div>
      `;
    }

    const totalCells = firstDayOfMonth + lastDateOfMonth;
    const nextDays = totalCells <= 35 ? 35 - totalCells : 42 - totalCells;

    for (let i = 1; i <= nextDays; i++) {
      calendarDays.innerHTML += `
        <div class="relative border-r border-b border-blue-200 text-xs font-semibold flex flex-col items-start justify-start p-2 pt-8 cursor-pointer hover:bg-indigo-50">
          <span class="absolute top-1 left-2"></span>
          <div class="calendar-events"></div>
        </div>
      `;
    }
  }

  prevMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
  });

  nextMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
  });

  renderCalendar(currentDate);
});
</script>

<style>
.border {
    display: flex;
    flex-direction: column;
}

#calendar-days {
    flex-grow: 1;
    display: grid;
    grid-auto-rows: minmax(120px, auto);
}

#calendar-days > div {
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
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
    max-height: 60px;
    overflow-y: auto;
    width: 100%;
}

.calendar-events > div {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}
</style>
