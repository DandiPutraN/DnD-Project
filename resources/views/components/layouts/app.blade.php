<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>Dandi Putra Nugraha Portfolio</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('front/images/Logo.png') }}" />

        <!-- Filament CSS -->
        @filamentStyles

        <!-- Vite CSS -->
        @vite('resources/css/app.css')

        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('front/style.css') }}" />

        <!-- Box Icons -->
        <link
          href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
          rel="stylesheet"
        />
    </head>

    <body>
        @livewire('partials.navbar')

        <main>
            {{ $slot }}
        </main>

        @livewire('partials.footer')

        <!-- Filament JS -->
        @filamentScripts

        @livewireScripts

        <!-- Typed.js -->
        <script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>

        <!-- Email.js -->
        <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
        <script>
          (function () {
            emailjs.init("tkKxnFTTTampSyr3G");
          })();
        </script>

        <!-- Custom JS -->
        <script src="{{ asset('front/script.js') }}" defer></script>
    </body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> 77a3d166bdd506020323b922cd30eed538b6bac3
