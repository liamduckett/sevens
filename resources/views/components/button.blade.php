@props(['color' => 'indigo'])

@php
    $colorClasses = match($color) {
        'indigo' => 'bg-indigo-900 focus:bg-indigo-700 focus:ring-indigo-700 hover:bg-indigo-700 hover:ring-indigo-700 active:bg-indigo-500',
        'red' => 'bg-red-800 focus:bg-red-700 focus:ring-red-700 hover:bg-red-700 hover:ring-red-700 active:bg-red-500',
        'green' => 'bg-green-700 focus:bg-green-700 focus:ring-green-600 hover:bg-green-600 hover:ring-green-600 active:bg-green-500',
    }
@endphp

<button {{ $attributes->merge([
    'class' => "inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-sm
                text-white tracking-wider shadow-2xl
                focus:ring-2 focus:ring-offset-2 focus:outline-none
                hover:ring-2 hover:ring-offset-2
                disabled:opacity-50 disabled:cursor-not-allowed
                transition ease-in-out duration-150 justify-center w-max $colorClasses",
    ]) }}>

    <div class="flex justify-center items-center gap-2">
        {{ $slot }}
    </div>
</button>
