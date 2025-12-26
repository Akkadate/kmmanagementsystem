<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลการค้นหา - {{ config('app.name') }}</title>
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
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-heading font-bold text-white mb-4">
                    ผลการค้นหา
                </h1>
                @if($query)
                    <p class="text-xl text-white/90">
                        ค้นหาคำว่า: <strong class="text-white">"{{ $query }}"</strong>
                    </p>
                @endif
            </div>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    @foreach(request('tags', []) as $tag)
                        <input type="hidden" name="tags[]" value="{{ $tag }}">
                    @endforeach
                    <input
                        type="text"
                        name="q"
                        value="{{ $query }}"
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
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            กรองผลการค้นหา
                        </h3>

                        <form action="{{ route('search') }}" method="GET">
                            <input type="hidden" name="q" value="{{ $query }}">

                            <!-- Category Filter -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">หมวดหมู่</label>
                                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">ทุกหมวดหมู่</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @foreach($category->children as $child)
                                            <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;{{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tags Filter -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">แท็ก</label>
                                <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                                    @foreach($tags as $tag)
                                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                                {{ in_array($tag->id, request('tags', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg hover:from-primary-700 hover:to-primary-800 transition-all">
                                    ใช้ตัวกรอง
                                </button>
                                <a href="{{ route('search', ['q' => $query]) }}" class="block text-center text-sm text-gray-600 hover:text-gray-900 py-2 hover:bg-gray-50 rounded transition-colors">
                                    ล้างตัวกรอง
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Results -->
            <main class="lg:col-span-3">
                @if($articles && $articles->count() > 0)
                    <!-- Results Count -->
                    <div class="mb-6 flex items-center justify-between">
                        <p class="text-gray-600">
                            พบ <span class="font-semibold text-gray-900">{{ $articles->total() }}</span> บทความ
                        </p>
                    </div>

                    <!-- Articles Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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
                        {{ $articles->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-heading font-semibold text-gray-900 mb-2">ไม่พบบทความ</h3>
                        <p class="text-gray-600 mb-6">ไม่พบบทความที่ตรงกับเงื่อนไขการค้นหาของคุณ</p>
                        <a href="{{ route('articles.index') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl hover:from-primary-700 hover:to-primary-800 transition-all transform hover:-translate-y-0.5">
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
