{{-- App instalable (PWA): manifest, color de la barra, icono de iOS y service worker --}}
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#0B4F58">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
<meta name="apple-mobile-web-app-title" content="PoolBook">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
</script>
