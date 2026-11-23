<main id="mainContent"
    class="lg:ml-64 min-h-[calc(100vh-4rem)] flex flex-col bg-brand-50/40 transition-[margin] duration-300 ease-in-out">

    <div class="flex-1 p-4 sm:p-6">
        <nav class="text-sm mb-4 text-slate-500">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-brand-600 transition">Dashboard</a>
        </nav>

        <h1 class="text-2xl font-bold text-brand-900 mb-6">{{ $title ?? 'Dashboard' }}</h1>

        <div class="bg-white rounded-2xl border border-brand-100 shadow-sm p-4 sm:p-6">
            {{ $slot }}
        </div>
    </div>

    @include('superadmin.layouts.footer')
</main>