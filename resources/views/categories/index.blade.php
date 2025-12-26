<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หมวดหมู่ - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('partials.navbar')

    <!-- Page Header -->
    <header class="relative bg-gradient-primary overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h1 class="text-4xl md:text-5xl font-heading font-bold text-white mb-4 animate-fade-in">
                หมวดหมู่บทความ
            </h1>
            <p class="text-xl text-white/90 max-w-2xl mx-auto">
                ค้นหาความรู้ที่คุณต้องการได้ง่ายขึ้นผ่านหมวดหมู่ต่างๆ
            </p>
        </div>
    </header>

    <!-- Categories Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        @if($categories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <article class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200 transform hover:-translate-y-1 animate-slide-up">
                        <!-- Colored Top Border -->
                        <div class="h-1.5 bg-gradient-to-r from-primary-500 to-primary-600 rounded-t-xl"></div>

                        <div class="p-6">
                            <!-- Category Title -->
                            <a href="{{ route('categories.show', $category->slug) }}" class="block mb-3">
                                <h2 class="text-2xl font-heading font-bold text-gray-900 hover:text-primary-600 transition-colors group-hover:text-primary-600">
                                    {{ $category->name }}
                                </h2>
                            </a>

                            <!-- Description -->
                            @if($category->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-relaxed">
                                    {{ $category->description }}
                                </p>
                            @endif

                            <!-- Article Count -->
                            <div class="flex items-center space-x-2 text-primary-600 mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-semibold">{{ number_format($category->articles_count) }} บทความ</span>
                            </div>

                            <!-- Subcategories -->
                            @if($category->children->count() > 0)
                                <div class="border-t border-gray-100 pt-4 mt-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                        หมวดหมู่ย่อย
                                    </p>
                                    <ul class="space-y-2">
                                        @foreach($category->children->take(3) as $child)
                                            <li>
                                                <a href="{{ route('categories.show', $child->slug) }}" class="text-sm text-primary-600 hover:text-primary-700 hover:underline flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                        @if($category->children->count() > 3)
                                            <li class="text-xs text-gray-500">
                                                +{{ $category->children->count() - 3 }} อื่นๆ
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Hover Effect Corner -->
                        <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-primary-400/0 to-primary-500/10 rounded-tr-xl rounded-bl-full transform translate-x-8 -translate-y-8 group-hover:translate-x-4 group-hover:-translate-y-4 transition-transform duration-300"></div>
                    </article>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <h3 class="text-xl font-heading font-semibold text-gray-900 mb-2">ยังไม่มีหมวดหมู่</h3>
                <p class="text-gray-500">ขณะนี้ยังไม่มีหมวดหมู่ใดๆ ในระบบ</p>
            </div>
        @endif
    </section>

    @include('partials.footer')
</body>
</html>
