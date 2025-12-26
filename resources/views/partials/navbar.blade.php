<!-- Navigation -->
<nav class="glass-effect sticky top-0 z-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    @if(setting('site_logo'))
                        <img src="{{ asset(setting('site_logo')) }}" alt="{{ setting('site_name') }}" class="h-8">
                    @else
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    @endif
                    <span class="text-xl font-heading font-bold text-gray-900">{{ setting('site_name', 'Knowledge Base') }}</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors">หน้าแรก</a>
                <a href="{{ route('articles.index') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors">บทความทั้งหมด</a>
                <a href="{{ route('categories.index') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors">หมวดหมู่</a>

                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors">Admin</a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'editor', 'contributor']))
                        <a href="{{ route('admin.articles.index') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors">จัดการบทความ</a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'editor']))
                        <a href="{{ route('admin.analytics.dashboard') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors">Analytics</a>
                    @endif

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 font-medium transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                             style="display: none;">
                            <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>My Profile</span>
                                </div>
                            </a>
                            <a href="{{ route('bookmarks.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                                    </svg>
                                    <span>Bookmark</span>
                                </div>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Account Settings</span>
                                </div>
                            </a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                        <span>System Settings</span>
                                    </div>
                                </a>
                            @endif
                            <hr class="my-2 border-gray-200">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Logout</span>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors">เข้าสู่ระบบ</a>
                    <a href="{{ route('register') }}" class="btn-primary">สมัครสมาชิก</a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="text-gray-700 hover:text-primary-600" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-white">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('home') }}" class="block text-gray-700 hover:text-primary-600 font-medium">หน้าแรก</a>
            <a href="{{ route('articles.index') }}" class="block text-gray-700 hover:text-primary-600 font-medium">บทความทั้งหมด</a>
            <a href="{{ route('categories.index') }}" class="block text-gray-700 hover:text-primary-600 font-medium">หมวดหมู่</a>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:text-primary-600 font-medium">Admin</a>
                @endif
                @if(in_array(auth()->user()->role, ['admin', 'editor', 'contributor']))
                    <a href="{{ route('admin.articles.index') }}" class="block text-gray-700 hover:text-primary-600 font-medium">จัดการบทความ</a>
                @endif
                @if(in_array(auth()->user()->role, ['admin', 'editor']))
                    <a href="{{ route('admin.analytics.dashboard') }}" class="block text-gray-700 hover:text-primary-600 font-medium">Analytics</a>
                @endif
                <div class="pt-3 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-900 mb-3">{{ auth()->user()->name }}</p>
                    <a href="{{ route('profile.index') }}" class="block text-gray-700 hover:text-primary-600 font-medium mb-2">My Profile</a>
                    <a href="{{ route('bookmarks.index') }}" class="block text-gray-700 hover:text-primary-600 font-medium mb-2">Bookmark</a>
                    <a href="{{ route('profile.edit') }}" class="block text-gray-700 hover:text-primary-600 font-medium mb-3">Settings</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">Logout</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="block text-gray-700 hover:text-primary-600 font-medium">เข้าสู่ระบบ</a>
                <a href="{{ route('register') }}" class="block btn-primary text-center">สมัครสมาชิก</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
</script>
