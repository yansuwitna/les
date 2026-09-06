@php
    $appPengaturan = \App\Models\Pengaturan::first();
    $appLogo = $appPengaturan?->logo_url ?: asset('favicon.ico');
    $appNama = $appPengaturan?->nama_les ?: config('app.name', 'Les Ceria');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ $appNama }}</title>

        <!-- Dynamic Favicon from Pengaturan Logo -->
        <link id="app-favicon" rel="icon" href="{{ $appLogo }}">
        <link rel="shortcut icon" href="{{ $appLogo }}">
        <link rel="apple-touch-icon" href="{{ $appLogo }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- html2canvas for exact element image download -->
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
        <script>
            (function () {
                function showSweetAlert(flash) {
                    if (!flash) return;
                    const successMsg = flash.success || flash.message || flash.status;
                    const errorMsg = flash.error;
                    if (successMsg) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: successMsg,
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    } else if (errorMsg) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMsg
                        });
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    try {
                        const appEl = document.getElementById('app');
                        if (appEl && appEl.dataset.page) {
                            const data = JSON.parse(appEl.dataset.page);
                            showSweetAlert(data.props?.flash);
                        }
                    } catch (e) {}
                });

                document.addEventListener('inertia:success', function (event) {
                    showSweetAlert(event.detail?.page?.props?.flash);
                });
            })();
        </script>
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
