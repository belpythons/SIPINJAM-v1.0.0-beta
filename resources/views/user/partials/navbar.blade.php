{{-- User Navbar Partial --}}
{{-- Sticky top navbar with user profile dropdown --}}

<header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-gray-200 bg-white/90 backdrop-blur-sm px-4 lg:px-6">
    {{-- Left: Page title or mobile menu --}}
    <div class="flex items-center gap-3">
        <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <i data-lucide="menu" class="h-5 w-5 text-gray-600"></i>
        </button>
        <h2 class="text-sm font-semibold text-gray-700">User Portal</h2>
    </div>

    {{-- Right: User Profile Dropdown --}}
    <div class="relative">
        <button id="user-dropdown-btn" class="flex items-center gap-2.5 rounded-lg px-2.5 py-1.5 hover:bg-gray-100 transition-colors">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200">
            @else
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-semibold">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
            @endif
            <div class="hidden sm:block text-left">
                <p class="text-sm font-medium text-gray-900 leading-tight">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-gray-500 leading-tight">{{ Auth::user()->email ?? '' }}</p>
            </div>
            <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400 hidden sm:block"></i>
        </button>

        {{-- Dropdown Menu --}}
        <div id="user-dropdown-menu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl border border-gray-200 shadow-lg py-1.5 z-50">
            <div class="px-4 py-2.5 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <i data-lucide="log-out" class="h-4 w-4"></i>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('user-dropdown-btn');
        const menu = document.getElementById('user-dropdown-menu');
        if (btn && menu) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        }
    });
</script>
