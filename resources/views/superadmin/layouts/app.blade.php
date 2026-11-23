<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'E-LEARNING Admin' }}</title>

    {{-- Terapkan state sidebar sebelum render supaya tidak berkedip --}}
    <script>
        try {
            if (localStorage.getItem('sidebarCollapsed') === '1') {
                document.documentElement.classList.add('sb-collapsed');
            }
        } catch (e) {}
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#f2faf4',
                            100: '#e1f4e6',
                            200: '#c3e8cd',
                            300: '#95d5a8',
                            400: '#5fba7d',
                            500: '#3a9d5d',
                            600: '#2a7e49',
                            700: '#23653c',
                            800: '#1f5132',
                            900: '#1a432b',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Sidebar collapsed: hanya desktop (>= lg). Di mobile sidebar tetap drawer. */
        .sb-divider {
            display: none;
        }

        @media (min-width: 1024px) {
            html.sb-collapsed #sidebar {
                width: 5rem;
            }

            html.sb-collapsed #mainContent {
                margin-left: 5rem;
            }

            html.sb-collapsed .sb-label {
                display: none;
            }

            html.sb-collapsed .sb-divider {
                display: block;
            }

            html.sb-collapsed .sb-link {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }

            html.sb-collapsed .sb-link i {
                margin: 0;
            }
        }
    </style>
    @livewireStyles
</head>

<body class="min-h-screen font-sans bg-brand-50/40 text-slate-700 antialiased">
    @include('superadmin.layouts.header')
    @include('superadmin.layouts.sidebar')
    @include('superadmin.layouts.content')

    <script>
        (function() {
            const html = document.documentElement;
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('sidebarToggle');
            const isDesktop = () => window.matchMedia('(min-width: 1024px)').matches;

            function closeMobile() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            toggle?.addEventListener('click', function() {
                if (isDesktop()) {
                    // Desktop: lebar penuh <-> hanya icon
                    const collapsed = html.classList.toggle('sb-collapsed');
                    try {
                        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
                    } catch (e) {}
                } else {
                    // Mobile: buka/tutup drawer
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                }
            });

            overlay?.addEventListener('click', closeMobile);
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeMobile();
            });
            window.addEventListener('resize', () => {
                if (isDesktop()) closeMobile();
            });
        })();
    </script>

    @livewireScripts
</body>

</html>