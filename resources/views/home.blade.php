<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - ระบบจัดการความรู้</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('partials.navbar')

    <!-- Hero Section -->
    <section class="bg-gradient-primary py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center animate-fade-in">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-heading font-bold text-white mb-6">
                {{ setting('home_hero_title', 'ยินดีต้อนรับสู่ระบบจัดการความรู้') }}
            </h1>
            <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                {{ setting('home_hero_subtitle', 'ค้นหาความรู้ที่คุณต้องการ เรียนรู้สิ่งใหม่ๆ และแบ่งปันประสบการณ์ของคุณ') }}
            </p>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('articles.index') }}" method="GET" class="relative">
                    <input
                        type="text"
                        name="search"
                        placeholder="ค้นหาบทความ หมวดหมู่ หรือคำสำคัญ..."
                        class="w-full px-6 py-4 rounded-full text-lg focus:ring-4 focus:ring-white/30 focus:border-white border-2 border-white/20 bg-white shadow-2xl"
                    >
                    <button
                        type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-primary-600 text-white px-8 py-2.5 rounded-full font-medium hover:bg-primary-700 transition-colors"
                    >
                        ค้นหา
                    </button>
                </form>
            </div>

            <!-- Quick Stats -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 text-white animate-scale-in">
                    <div class="text-4xl font-bold mb-2">{{ number_format($stats['total_articles']) }}</div>
                    <div class="text-white/80">บทความทั้งหมด</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 text-white animate-scale-in" style="animation-delay: 0.1s">
                    <div class="text-4xl font-bold mb-2">{{ number_format($stats['total_categories']) }}</div>
                    <div class="text-white/80">หมวดหมู่</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 text-white animate-scale-in" style="animation-delay: 0.2s">
                    <div class="text-4xl font-bold mb-2">{{ number_format($stats['total_views']) }}</div>
                    <div class="text-white/80">ครั้งที่อ่าน</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Articles -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-heading font-bold text-gray-900">บทความยอดนิยม</h2>
            <a href="{{ route('articles.index') }}" class="link-hover font-medium">ดูทั้งหมด →</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredArticles as $article)
                <article class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary-200 transform hover:-translate-y-1 animate-slide-up">
                    <!-- Colored Top Border -->
                    <div class="h-1.5 bg-gradient-to-r from-primary-500 to-primary-600"></div>

                    <div class="p-6">
                        <!-- Category Badge -->
                        <a href="{{ route('categories.show', $article->category->slug) }}" class="inline-block">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700 hover:bg-primary-100 transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                {{ $article->category->name }}
                            </span>
                        </a>

                        <!-- Title -->
                        <h3 class="mt-4 mb-3">
                            <a href="{{ route('articles.show', $article->slug) }}" class="text-xl font-heading font-bold text-gray-900 hover:text-primary-600 transition-colors line-clamp-2 leading-snug">
                                {{ $article->title }}
                            </a>
                        </h3>

                        <!-- Excerpt -->
                        <p class="text-gray-600 text-sm leading-relaxed mb-5 line-clamp-3">
                            {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 150) }}
                        </p>

                        <!-- Meta -->
                        <div class="flex items-center justify-between text-sm pt-4 border-t border-gray-100">
                            <div class="flex items-center space-x-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="font-medium">{{ $article->author->name }}</span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-400">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span class="text-xs font-medium">{{ number_format($article->view_count) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hover Effect Corner -->
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-primary-400/0 to-primary-500/10 rounded-bl-full transform translate-x-10 -translate-y-10 group-hover:translate-x-6 group-hover:-translate-y-6 transition-transform duration-300"></div>
                </article>
            @endforeach
        </div>
    </section>

    <!-- Recent Articles -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-heading font-bold text-gray-900">บทความล่าสุด</h2>
                <a href="{{ route('articles.index') }}?sort=latest" class="link-hover font-medium">ดูทั้งหมด →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($recentArticles as $article)
                    <article class="group bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 hover:border-primary-200 p-5 animate-slide-up">
                        <!-- Category & Date -->
                        <div class="flex items-center justify-between mb-3">
                            <a href="{{ route('categories.show', $article->category->slug) }}" class="inline-block">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    {{ $article->category->name }}
                                </span>
                            </a>
                            <span class="text-xs text-gray-400">
                                {{ $article->published_at->locale('th')->diffForHumans() }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="mb-2">
                            <a href="{{ route('articles.show', $article->slug) }}" class="text-base font-heading font-semibold text-gray-900 hover:text-primary-600 transition-colors line-clamp-2 leading-snug group-hover:text-primary-600">
                                {{ $article->title }}
                            </a>
                        </h3>

                        <!-- Excerpt (if available) -->
                        @if($article->excerpt)
                            <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                {{ Str::limit($article->excerpt, 80) }}
                            </p>
                        @endif

                        <!-- Meta -->
                        <div class="flex items-center space-x-3 text-xs text-gray-400 pt-3 border-t border-gray-100">
                            <div class="flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ Str::limit($article->author->name, 15) }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>{{ number_format($article->view_count) }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Top Categories -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-heading font-bold text-gray-900">หมวดหมู่ยอดนิยม</h2>
            <a href="{{ route('categories.index') }}" class="link-hover font-medium">ดูทั้งหมด →</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach($topCategories as $category)
                <a href="{{ route('categories.show', $category->slug) }}" class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 text-center border border-gray-100 hover:border-primary-200 transform hover:-translate-y-1">
                    <!-- Gradient Background -->
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-blue-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl"></div>

                    <!-- Content -->
                    <div class="relative">
                        <h3 class="font-heading font-bold text-gray-900 group-hover:text-primary-600 mb-2 text-lg transition-colors">
                            {{ $category->name }}
                        </h3>
                        <p class="text-sm font-medium text-gray-500">
                            {{ number_format($category->articles_count) }} บทความ
                        </p>
                    </div>

                    <!-- Decorative Corner -->
                    <div class="absolute top-0 right-0 w-12 h-12 bg-gradient-to-br from-primary-400/10 to-transparent rounded-tr-xl"></div>
                </a>
            @endforeach
        </div>
    </section>

    @include('partials.footer')

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
