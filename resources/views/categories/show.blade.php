<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('partials.navbar')

    <!-- Header -->
    <header class="relative bg-gradient-primary overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Breadcrumb -->
            <nav class="text-sm mb-6 flex items-center space-x-2 text-white/80">
                <a href="{{ route('categories.index') }}" class="hover:text-white transition-colors">หมวดหมู่</a>
                @if($category->parent)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('categories.show', $category->parent->slug) }}" class="hover:text-white transition-colors">
                        {{ $category->parent->name }}
                    </a>
                @endif
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-white font-medium">{{ $category->name }}</span>
            </nav>

            <!-- Category Info -->
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-heading font-bold text-white mb-4 animate-fade-in">
                    {{ $category->name }}
                </h1>
                @if($category->description)
                    <p class="text-xl text-white/90 max-w-3xl mx-auto">
                        {{ $category->description }}
                    </p>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Subcategories -->
        @if($category->children->count() > 0)
            <div class="mb-12">
                <h2 class="text-2xl font-heading font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    หมวดหมู่ย่อย
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($category->children as $child)
                        <a href="{{ route('categories.show', $child->slug) }}" class="group relative bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary-200 transform hover:-translate-y-1 animate-fade-in">
                            <!-- Colored Top Border -->
                            <div class="h-1.5 bg-gradient-to-r from-primary-500 to-primary-600"></div>

                            <div class="p-6">
                                <h3 class="text-lg font-heading font-bold text-gray-900 group-hover:text-primary-600 transition-colors mb-2">
                                    {{ $child->name }}
                                </h3>
                                @if($child->description)
                                    <p class="text-gray-600 text-sm line-clamp-2">
                                        {{ $child->description }}
                                    </p>
                                @endif

                                <!-- Article count -->
                                <div class="mt-4 flex items-center text-xs text-gray-500">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ $child->articles_count ?? 0 }} บทความ
                                </div>
                            </div>

                            <!-- Hover Effect Corner -->
                            <div class="absolute bottom-0 right-0 w-20 h-20 bg-gradient-to-tl from-primary-500/10 to-transparent rounded-tr-full transform translate-x-10 translate-y-10 group-hover:translate-x-6 group-hover:translate-y-6 transition-transform duration-300"></div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Articles Section -->
        <div>
            <h2 class="text-2xl font-heading font-bold text-gray-900 mb-6 flex items-center justify-between">
                <span class="flex items-center">
                    <svg class="w-6 h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    บทความในหมวดหมู่นี้
                </span>
                @if($descendantArticlesCount > 0)
                    <span class="text-sm font-normal text-gray-500">
                        +{{ $descendantArticlesCount }} ในหมวดหมู่ย่อย
                    </span>
                @endif
            </h2>

            @if($articles->count() > 0)
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
                    {{ $articles->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-xl font-heading font-semibold text-gray-900 mb-2">ยังไม่มีบทความ</h3>
                    <p class="text-gray-600 mb-6">ยังไม่มีบทความในหมวดหมู่นี้</p>
                    <a href="{{ route('articles.index') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl hover:from-primary-700 hover:to-primary-800 transition-all transform hover:-translate-y-0.5">
                        ดูบทความทั้งหมด
                    </a>
                </div>
            @endif
        </div>
    </div>

    @include('partials.footer')
</body>
</html>
