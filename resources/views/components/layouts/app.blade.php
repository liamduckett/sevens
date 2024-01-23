<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite('resources/css/app.css')
        @vite('resources/js/app.js')

        <title>{{ $title ?? 'Page Title' }}</title>
    </head>
    <body>
        {{ $slot }}

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.Echo.channel('lobby')
                    .listen('PlayerJoined', () => {
                        // console.log('PlayerJoined');
                    })
                    .listen('PlayerLeft', () => {
                        // console.log('PlayerLeft');
                    })
                    .listen('GameStarted', () => {
                        // console.log('GameStarted');
                    })
                    .listen('TurnTaken', () => {
                        // console.log('TurnTaken');
                    })
                ;
            });
        </script>
    </body>
</html>
