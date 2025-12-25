@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-8">
            <div class="mb-6">
                @if($article->category)
                    <a href="{{ route('categories.show', $article->category->slug) }}" class="text-sm text-blue-600 hover:text-blue-800">
                        {{ $article->category->name }}
                    </a>
                @endif
            </div>

            <div class="flex items-center justify-between mb-4">
                <h1 class="text-4xl font-bold text-gray-900">{{ $article->title }}</h1>
                <div class="flex gap-2">
                    @auth
                        @php
                            $isBookmarked = auth()->user()->bookmarks()->where('article_id', $article->id)->exists();
                        @endphp
                        <form action="{{ route('bookmarks.store', $article->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="article_id" value="{{ $article->id }}">
                            <button type="submit" class="px-4 py-2 rounded text-sm flex items-center
                                {{ $isBookmarked ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                <svg class="w-5 h-5 mr-1 {{ $isBookmarked ? 'fill-current' : '' }}" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                                {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                            </button>
                        </form>
                    @endauth
                    @can('update', $article)
                        <a href="{{ route('articles.edit', $article->slug) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                            Edit Article
                        </a>
                    @endcan
                </div>
            </div>

            <div class="flex items-center text-sm text-gray-500 mb-6 pb-6 border-b">
                <span>By {{ $article->author->name }}</span>
                <span class="mx-2">&bull;</span>
                <span>{{ $article->published_at?->format('F j, Y') }}</span>
                <span class="mx-2">&bull;</span>
                <span>{{ $article->view_count }} views</span>
            </div>

            @if($article->excerpt)
                <div class="text-lg text-gray-700 mb-6 italic">
                    {{ $article->excerpt }}
                </div>
            @endif

            @if($article->hasMedia('featured_image'))
                <div class="mb-6">
                    <img src="{{ $article->getFirstMediaUrl('featured_image') }}"
                         alt="{{ $article->title }}"
                         class="w-full h-auto rounded-lg shadow-lg">
                </div>
            @endif

            <div class="prose prose-lg max-w-none mb-8">
                {!! $article->content !!}
            </div>

            @if($article->tags->count() > 0)
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($article->tags as $tag)
                        <a href="{{ route('articles.index', ['tag' => $tag->id]) }}" class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm hover:bg-blue-100">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Was this article helpful?</h3>
                @auth
                    <form action="{{ route('feedback.store', $article) }}" method="POST" class="flex gap-4">
                        @csrf
                        <button type="submit" name="is_helpful" value="1" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Yes, helpful
                        </button>
                        <button type="submit" name="is_helpful" value="0" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            No, not helpful
                        </button>
                    </form>
                @else
                    <p class="text-gray-500">
                        Please <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">login</a> to provide feedback.
                    </p>
                @endauth
            </div>
        </div>
    </div>

    @if($relatedArticles->count() > 0)
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Related Articles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($relatedArticles as $related)
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-md transition">
                        <a href="{{ route('articles.show', $related->slug) }}" class="block">
                            <h3 class="font-semibold text-gray-900 hover:text-blue-600 mb-2">
                                {{ $related->title }}
                            </h3>
                        </a>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ Str::limit($related->excerpt, 100) }}
                        </p>
                        <div class="text-xs text-gray-500">
                            {{ $related->published_at?->diffForHumans() }} &bull; {{ $related->view_count }} views
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
