<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} - {{ config('app.name') }}</title>
    <meta name="description" content="{{ $article->excerpt ?? Str::limit(strip_tags($article->content), 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Ensure images display properly in article content */
        .prose img {
            display: block !important;
            max-width: 100% !important;
            height: auto !important;
            margin: 1.5rem auto !important;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.navbar')

    <!-- Article Header -->
    <header class="relative bg-gradient-primary overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm text-white/80 mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">หน้าแรก</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                @if($article->category)
                    <a href="{{ route('categories.show', $article->category->slug) }}" class="hover:text-white transition-colors">
                        {{ $article->category->name }}
                    </a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                @endif
                <span class="text-white font-medium">{{ Str::limit($article->title, 50) }}</span>
            </nav>

            <!-- Category Badge -->
            @if($article->category)
                <a href="{{ route('categories.show', $article->category->slug) }}" class="inline-block mb-6">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white text-primary-700 border-2 border-primary-200 hover:bg-primary-50 hover:border-primary-300 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ $article->category->name }}
                    </span>
                </a>
            @endif

            <!-- Title -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-heading font-bold text-white mb-6 leading-tight animate-fade-in">
                {{ $article->title }}
            </h1>

            <!-- Excerpt -->
            @if($article->excerpt)
                <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed font-light">
                    {{ $article->excerpt }}
                </p>
            @endif

            <!-- Featured Image -->
            @if($article->featured_image)
                <div class="mt-8 mb-6">
                    <img
                        src="{{ asset('storage/' . $article->featured_image) }}"
                        alt="{{ $article->title }}"
                        class="w-full rounded-2xl shadow-2xl"
                    >
                </div>
            @endif

            <!-- Meta Info -->
            <div class="flex flex-wrap items-center gap-6 mb-6">
                <div class="flex items-center space-x-2 text-white/90">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-white/70">เขียนโดย</span>
                        <span class="font-semibold text-white">{{ $article->author->name }}</span>
                    </div>
                </div>
                <div class="h-12 w-px bg-white/30"></div>
                <div class="flex flex-col text-white/90">
                    <span class="text-xs text-white/70">เผยแพร่เมื่อ</span>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium text-white">{{ $article->published_at?->locale('th')->isoFormat('D MMMM YYYY') }}</span>
                    </div>
                </div>
                <div class="h-12 w-px bg-white/30"></div>
                <div class="flex flex-col text-white/90">
                    <span class="text-xs text-white/70">จำนวนผู้เข้าชม</span>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span class="font-bold text-white">{{ number_format($article->view_count) }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
                @auth
                    @php
                        $isBookmarked = auth()->user()->bookmarks()->where('article_id', $article->id)->exists();
                    @endphp
                    <form action="{{ route('bookmarks.store', $article->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="article_id" value="{{ $article->id }}">
                        <button type="submit" class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5
                            {{ $isBookmarked
                                ? 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white hover:from-yellow-500 hover:to-yellow-600'
                                : 'bg-white text-gray-700 border-2 border-gray-200 hover:border-primary-400 hover:text-primary-600 hover:bg-primary-50' }}">
                            <svg class="w-5 h-5 {{ $isBookmarked ? 'fill-current' : '' }}" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span>{{ $isBookmarked ? 'บันทึกแล้ว' : 'บันทึกบทความ' }}</span>
                        </button>
                    </form>
                @endauth

                @can('update', $article)
                    <a href="{{ route('articles.edit', $article->slug) }}" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl font-semibold hover:from-primary-700 hover:to-primary-800 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>แก้ไขบทความ</span>
                    </a>
                @endcan

                <button onclick="window.print()" class="px-6 py-2.5 bg-white text-gray-700 border-2 border-gray-200 rounded-lg font-medium hover:border-gray-300 transition-all flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>พิมพ์</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Content -->
            <div class="px-6 md:px-12 py-10">
                <div class="prose prose-lg max-w-none
                    prose-headings:font-heading prose-headings:font-bold prose-headings:text-gray-900
                    prose-h1:text-4xl prose-h2:text-3xl prose-h3:text-2xl
                    prose-p:text-gray-700 prose-p:leading-relaxed
                    prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline
                    prose-strong:text-gray-900 prose-strong:font-semibold
                    prose-ul:list-disc prose-ol:list-decimal
                    prose-li:text-gray-700
                    prose-code:text-primary-600 prose-code:bg-primary-50 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
                    prose-pre:bg-gray-900 prose-pre:text-gray-100
                    prose-img:rounded-xl prose-img:shadow-lg
                    prose-blockquote:border-l-4 prose-blockquote:border-primary-500 prose-blockquote:bg-primary-50 prose-blockquote:py-2 prose-blockquote:px-4">
                    {!! $article->content !!}
                </div>

                <!-- Tags -->
                @if($article->tags->count() > 0)
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">แท็ก</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->tags as $tag)
                                <a href="{{ route('articles.index', ['tag' => $tag->id]) }}"
                                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-primary-100 hover:text-primary-700 transition-colors">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Feedback Section -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <div class="bg-gradient-to-r from-primary-50 to-blue-50 rounded-xl p-8">
                        <h3 class="text-2xl font-heading font-bold text-gray-900 mb-3">บทความนี้มีประโยชน์ไหม?</h3>
                        <p class="text-gray-600 mb-6">ความคิดเห็นของคุณช่วยให้เราปรับปรุงเนื้อหาให้ดีขึ้น</p>

                        @auth
                            <form action="{{ route('feedback.store', $article) }}" method="POST" class="flex flex-wrap gap-3">
                                @csrf
                                <button type="submit" name="is_helpful" value="1"
                                        class="px-8 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                    </svg>
                                    <span>มีประโยชน์</span>
                                </button>
                                <button type="submit" name="is_helpful" value="0"
                                        class="px-8 py-3 bg-white text-gray-700 border-2 border-gray-300 rounded-lg font-medium hover:border-gray-400 hover:bg-gray-50 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                                    </svg>
                                    <span>ไม่มีประโยชน์</span>
                                </button>
                            </form>
                        @else
                            <div class="bg-white rounded-lg p-6 border-2 border-gray-200">
                                <p class="text-gray-600">
                                    กรุณา <a href="{{ route('login') }}" class="link-hover font-semibold">เข้าสู่ระบบ</a> เพื่อให้ความคิดเห็น
                                </p>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Author Info -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-gradient-primary rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">
                                    {{ strtoupper(substr($article->author->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-heading font-semibold text-gray-900 mb-1">{{ $article->author->name }}</h4>
                            <p class="text-sm text-gray-500 mb-2">ผู้เขียน</p>
                            <p class="text-gray-600">
                                เผยแพร่เมื่อ {{ $article->published_at?->locale('th')->isoFormat('D MMMM YYYY เวลา HH:mm น.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-gray-50">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-heading font-bold text-gray-900 mb-3">บทความที่เกี่ยวข้อง</h2>
                <p class="text-gray-600">บทความอื่นๆ ที่คุณอาจสนใจ</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedArticles as $related)
                    <article class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200 transform hover:-translate-y-0.5 animate-slide-up">
                        <!-- Colored Top Border -->
                        <div class="h-1 bg-gradient-to-r from-primary-500 to-primary-600"></div>

                        <div class="p-6">
                            <!-- Category & Date -->
                            <div class="flex items-center justify-between mb-3">
                                <a href="{{ route('categories.show', $related->category->slug) }}" class="inline-block">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700 hover:bg-primary-100 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ $related->category->name }}
                                    </span>
                                </a>
                                <span class="text-xs text-gray-400">
                                    {{ $related->published_at?->locale('th')->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="mb-3">
                                <a href="{{ route('articles.show', $related->slug) }}" class="text-lg font-heading font-bold text-gray-900 hover:text-primary-600 transition-colors line-clamp-2 leading-snug group-hover:text-primary-600">
                                    {{ $related->title }}
                                </a>
                            </h3>

                            <!-- Excerpt -->
                            <p class="text-gray-600 mb-4 line-clamp-2 text-sm leading-relaxed">
                                {{ $related->excerpt ?? Str::limit(strip_tags($related->content), 120) }}
                            </p>

                            <!-- Meta -->
                            <div class="flex items-center space-x-1 text-xs text-gray-400 pt-4 border-t border-gray-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span class="font-medium">{{ number_format($related->view_count) }}</span>
                            </div>
                        </div>

                        <!-- Hover Effect Corner -->
                        <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-primary-400/0 to-primary-500/10 rounded-tr-xl rounded-bl-full transform translate-x-8 -translate-y-8 group-hover:translate-x-4 group-hover:-translate-y-4 transition-transform duration-300"></div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @include('partials.footer')
</body>
</html>
