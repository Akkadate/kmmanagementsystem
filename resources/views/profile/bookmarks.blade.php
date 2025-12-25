@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Bookmarks</h1>
            <p class="mt-2 text-gray-600">Articles you've saved for later</p>
        </div>
        <a href="{{ route('profile.index') }}" class="text-gray-600 hover:text-gray-800">
            ← Back to Profile
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow">
    @if($bookmarks->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($bookmarks as $bookmark)
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                                </svg>
                                <a href="{{ route('articles.show', $bookmark->article->slug) }}"
                                   class="text-xl font-medium text-gray-900 hover:text-blue-600">
                                    {{ $bookmark->article->title }}
                                </a>
                            </div>

                            @if($bookmark->article->excerpt)
                                <p class="text-gray-600 mb-3">{{ Str::limit($bookmark->article->excerpt, 200) }}</p>
                            @endif

                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $bookmark->article->author->name }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ $bookmark->article->category->name }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ number_format($bookmark->article->view_count) }} views
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Bookmarked {{ $bookmark->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        <div class="ml-6 flex flex-col space-y-2">
                            <a href="{{ route('articles.show', $bookmark->article->slug) }}"
                               class="text-blue-600 hover:underline text-sm whitespace-nowrap">
                                Read Article
                            </a>
                            <form action="{{ route('bookmarks.destroy', $bookmark->id) }}" method="POST"
                                  onsubmit="return confirm('Remove this bookmark?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($bookmarks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $bookmarks->links() }}
            </div>
        @endif
    @else
        <div class="p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No bookmarks yet</h3>
            <p class="text-gray-600 mb-4">Start bookmarking articles to save them for later reading</p>
            <a href="{{ route('articles.index') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Browse Articles
            </a>
        </div>
    @endif
</div>

<!-- Summary Card -->
@if($bookmarks->count() > 0)
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
        </svg>
        <span class="text-sm text-blue-800">
            You have <strong>{{ $bookmarks->total() }}</strong> {{ Str::plural('bookmark', $bookmarks->total()) }}
        </span>
    </div>
</div>
@endif
@endsection
