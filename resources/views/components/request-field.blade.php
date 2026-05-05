<div class="flex gap-4 p-3 rounded-lg transition hover:bg-gray-100 dark:hover:bg-gray-700">

    <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">
        {{ $icon }}
    </span>

    <div>
        <p class="header-text font-semibold dark:text-gray-300">
            {{ $label }}
        </p>

        <p class="detail-text dark:text-gray-200">
            {{ $value }}
        </p>
    </div>

</div>
