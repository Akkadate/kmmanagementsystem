<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บทความทั้งหมด - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('partials.navbar')

    <!-- Header -->
    <header class="relative bg-gradient-primary overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-8 animate-fade-in">
                <h1 class="text-4xl md:text-5xl font-heading font-bold text-white mb-4">
                    บทความทั้งหมด
                </h1>
                <p class="text-xl text-white/90">
                    ค้นพบความรู้และเรียนรู้สิ่งใหม่ๆ จากบทความของเรา
                </p>
            </div>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('articles.index') }}" method="GET" class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
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
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:col-span-1">
                <div class="sticky top-20">
                    <!-- Category Filter -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            หมวดหมู่
                        </h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('articles.index') }}"
                                   class="block px-3 py-2 rounded-lg transition-colors {{ !request('category') ? 'bg-primary-100 text-primary-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                    ทั้งหมด
                                </a>
                            </li>
                            @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('articles.index', ['category' => $category->id]) }}"
                                       class="block px-3 py-2 rounded-lg transition-colors {{ request('category') == $category->id ? 'bg-primary-100 text-primary-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                        {{ $category->name }}
                                    </a>
                                    @if($category->children->count() > 0)
                                        <ul class="ml-4 mt-2 space-y-1">
                                            @foreach($category->children as $child)
                                                <li>
                                                    <a href="{{ route('articles.index', ['category' => $child->id]) }}"
                                                       class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('category') == $child->id ? 'bg-primary-50 text-primary-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                                        {{ $child->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Tags Filter -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            แท็กยอดนิยม
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags->take(15) as $tag)
                                <a href="{{ route('articles.index', ['tag' => $tag->id]) }}"
                                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request('tag') == $tag->id ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Articles List -->
            <main class="lg:col-span-3">
                <!-- Active Filters -->
                @if(request('search') || request('category') || request('tag'))
                    <div class="mb-6 flex flex-wrap items-center gap-3">
                        <span class="text-sm text-gray-600">กำลังกรอง:</span>
                        @if(request('search'))
                            <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-medium flex items-center">
                                ค้นหา: "{{ request('search') }}"
                                <a href="{{ route('articles.index', request()->except('search')) }}" class="ml-2 hover:text-primary-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </span>
                        @endif
                        @if(request('category'))
                            @php
                                $selectedCategory = $categories->flatten()->firstWhere('id', request('category'));
                            @endphp
                            @if($selectedCategory)
                                <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-medium flex items-center">
                                    หมวดหมู่: {{ $selectedCategory->name }}
                                    <a href="{{ route('articles.index', request()->except('category')) }}" class="ml-2 hover:text-primary-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                </span>
                            @endif
                        @endif
                        @if(request('tag'))
                            @php
                                $selectedTag = $tags->firstWhere('id', request('tag'));
                            @endphp
                            @if($selectedTag)
                                <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-medium flex items-center">
                                    แท็ก: #{{ $selectedTag->name }}
                                    <a href="{{ route('articles.index', request()->except('tag')) }}" class="ml-2 hover:text-primary-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                </span>
                            @endif
                        @endif
                        <a href="{{ route('articles.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">
                            ล้างทั้งหมด
                        </a>
                    </div>
                @endif

                <!-- Results Count -->
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-gray-600">
                        พบ <span class="font-semibold text-gray-900">{{ $articles->total() }}</span> บทความ
                    </p>
                </div>

                @if($articles->count() > 0)
                    <!-- Articles Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                        @foreach($articles as $article)
                            <article class="group relative bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200 transform hover:-translate-y-0.5 animate-slide-up">
                                <!-- Colored Left Border -->
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-primary-500 to-primary-600 rounded-l-xl"></div>

                                <div class="pl-6 pr-6 py-6">
                                    <!-- Category Badge & Date -->
                                    <div class="flex items-center justify-between mb-3">
                                        @if($article->category)
                                            <a href="{{ route('categories.show', $article->category->slug) }}" class="inline-block">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700 hover:bg-primary-100 transition-colors">
                                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                    {{ $article->category->name }}
                                                </span>
                                            </a>
                                        @endif
                                        <span class="text-xs text-gray-400">
                                            {{ $article->published_at?->locale('th')->diffForHumans() }}
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h2 class="mb-3">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="text-xl font-heading font-bold text-gray-900 hover:text-primary-600 transition-colors line-clamp-2 leading-snug group-hover:text-primary-600">
                                            {{ $article->title }}
                                        </a>
                                    </h2>

                                    <!-- Excerpt -->
                                    <p class="text-gray-600 mb-4 line-clamp-2 text-sm leading-relaxed">
                                        {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 150) }}
                                    </p>

                                    <!-- Tags -->
                                    @if($article->tags->count() > 0)
                                        <div class="flex flex-wrap gap-1.5 mb-4">
                                            @foreach($article->tags->take(3) as $tag)
                                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs hover:bg-gray-200 transition-colors">
                                                    <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M9.243 3.03a1 1 0 01.727 1.213L9.53 6h2.94l.56-2.243a1 1 0 111.94.486L14.53 6H17a1 1 0 110 2h-2.97l-1 4H15a1 1 0 110 2h-2.47l-.56 2.242a1 1 0 11-1.94-.485L10.47 14H7.53l-.56 2.242a1 1 0 11-1.94-.485L5.47 14H3a1 1 0 110-2h2.97l1-4H5a1 1 0 110-2h2.47l.56-2.243a1 1 0 011.213-.727zM9.03 8l-1 4h2.938l1-4H9.031z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                            @if($article->tags->count() > 3)
                                                <span class="px-2 py-0.5 text-gray-500 text-xs font-medium">
                                                    +{{ $article->tags->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Meta -->
                                    <div class="flex items-center justify-between text-xs pt-4 border-t border-gray-100">
                                        <div class="flex items-center space-x-2 text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="font-medium">{{ $article->author->name }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1 text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span class="font-medium">{{ number_format($article->view_count) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hover Effect Corner -->
                                <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-primary-400/0 to-primary-500/10 rounded-tr-xl rounded-bl-full transform translate-x-8 -translate-y-8 group-hover:translate-x-4 group-hover:-translate-y-4 transition-transform duration-300"></div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $articles->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-xl font-heading font-semibold text-gray-900 mb-2">ไม่พบบทความ</h3>
                        <p class="text-gray-600 mb-6">ลองค้นหาด้วยคำค้นอื่น หรือเลือกหมวดหมู่ที่แตกต่างกัน</p>
                        <a href="{{ route('articles.index') }}" class="btn-primary inline-block">
                            ดูบทความทั้งหมด
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>

    @include('partials.footer')
</body>
</html>
