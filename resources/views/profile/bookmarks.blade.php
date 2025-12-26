<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บุ๊กมาร์กของฉัน - {{ config('app.name') }}</title>
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
            <div class="flex items-center justify-between mb-4">
                <nav class="text-sm flex items-center space-x-2 text-white/80">
                    <a href="{{ route('profile.index') }}" class="hover:text-white transition-colors">โปรไฟล์</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-white font-medium">บุ๊กมาร์ก</span>
                </nav>
            </div>

            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-heading font-bold text-white mb-4 animate-fade-in">
                    บุ๊กมาร์กของฉัน
                </h1>
                <p class="text-xl text-white/90">
                    บทความที่คุณบันทึกไว้สำหรับอ่านภายหลัง
                </p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-lg shadow-sm animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Summary Card -->
        @if($bookmarks->count() > 0)
            <div class="mb-8 bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                    </svg>
                    <span class="text-lg text-gray-800">
                        คุณมี <strong class="text-yellow-700 font-heading">{{ $bookmarks->total() }}</strong> บุ๊กมาร์ก
                    </span>
                </div>
            </div>
        @endif

        @if($bookmarks->count() > 0)
            <!-- Bookmarks Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                @foreach($bookmarks as $bookmark)
                    <article class="group relative bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-yellow-200 transform hover:-translate-y-0.5 animate-slide-up">
                        <!-- Colored Left Border -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-yellow-500 to-yellow-600 rounded-l-xl"></div>

                        <div class="pl-6 pr-6 py-6">
                            <!-- Bookmark Icon & Date -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                                    </svg>
                                    @if($bookmark->article->category)
                                        <a href="{{ route('categories.show', $bookmark->article->category->slug) }}" class="inline-block">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700 hover:bg-primary-100 transition-colors">
                                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                {{ $bookmark->article->category->name }}
                                            </span>
                                        </a>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-400">
                                    {{ $bookmark->created_at->locale('th')->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h2 class="mb-3">
                                <a href="{{ route('articles.show', $bookmark->article->slug) }}" class="text-xl font-heading font-bold text-gray-900 hover:text-primary-600 transition-colors line-clamp-2 leading-snug group-hover:text-primary-600">
                                    {{ $bookmark->article->title }}
                                </a>
                            </h2>

                            <!-- Excerpt -->
                            @if($bookmark->article->excerpt)
                                <p class="text-gray-600 mb-4 line-clamp-2 text-sm leading-relaxed">
                                    {{ $bookmark->article->excerpt }}
                                </p>
                            @endif

                            <!-- Meta -->
                            <div class="flex items-center justify-between text-xs pt-4 border-t border-gray-100">
                                <div class="flex items-center space-x-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="font-medium">{{ $bookmark->article->author->name }}</span>
                                </div>
                                <div class="flex items-center space-x-1 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span class="font-medium">{{ number_format($bookmark->article->view_count) }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex items-center space-x-3">
                                <a href="{{ route('articles.show', $bookmark->article->slug) }}" class="flex-1 text-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg hover:from-primary-700 hover:to-primary-800 transition-all transform hover:-translate-y-0.5">
                                    อ่านบทความ
                                </a>
                                <form action="{{ route('bookmarks.destroy', $bookmark->id) }}" method="POST" onsubmit="return confirm('ต้องการลบบุ๊กมาร์กนี้หรือไม่?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg border border-red-200 hover:bg-red-100 hover:border-red-300 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Hover Effect Corner -->
                        <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-yellow-400/0 to-yellow-500/10 rounded-tr-xl rounded-bl-full transform translate-x-8 -translate-y-8 group-hover:translate-x-4 group-hover:-translate-y-4 transition-transform duration-300"></div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($bookmarks->hasPages())
                <div class="mt-8">
                    {{ $bookmarks->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <h3 class="text-xl font-heading font-semibold text-gray-900 mb-2">ยังไม่มีบุ๊กมาร์ก</h3>
                <p class="text-gray-600 mb-6">เริ่มบันทึกบทความที่สนใจเพื่ออ่านภายหลัง</p>
                <a href="{{ route('articles.index') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl hover:from-primary-700 hover:to-primary-800 transition-all transform hover:-translate-y-0.5">
                    เรียกดูบทความ
                </a>
            </div>
        @endif
    </div>

    @include('partials.footer')
</body>
</html>
