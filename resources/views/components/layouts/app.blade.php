<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite('resources/css/app.css')
        @vite('resources/js/app.js')

        <title>Sevens | Liam Duckett</title>
    </head>
    <body>
        {{ $slot }}

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.Echo.channel('lobby')
                    .listen('PlayerJoined', () => {})
                    .listen('PlayerLeft', () => {})
                    .listen('GameStarted', () => {})
                    .listen('TurnTaken', () => {})
                    .listen('GameWon', () => {})
                ;
            });
        </script>
    </body>
</html>
