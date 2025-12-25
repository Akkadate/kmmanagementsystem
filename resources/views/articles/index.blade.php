@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Articles</h1>
    <p class="mt-2 text-gray-600">Browse all knowledge base articles</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-900 mb-3">Filter by Category</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('articles.index') }}" class="text-sm {{ !request('category') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                        All Articles
                    </a>
                </li>
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('articles.index', ['category' => $category->id]) }}" class="text-sm {{ request('category') == $category->id ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                            {{ $category->name }}
                        </a>
                        @if($category->children->count() > 0)
                            <ul class="ml-4 mt-1 space-y-1">
                                @foreach($category->children as $child)
                                    <li>
                                        <a href="{{ route('articles.index', ['category' => $child->id]) }}" class="text-xs {{ request('category') == $child->id ? 'text-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>

            <h3 class="font-semibold text-gray-900 mb-3 mt-6">Popular Tags</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($tags->take(10) as $tag)
                    <a href="{{ route('articles.index', ['tag' => $tag->id]) }}" class="inline-block px-2 py-1 text-xs rounded {{ request('tag') == $tag->id ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $tag->name }} ({{ $tag->articles_count }})
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="lg:col-span-3">
        @if($articles->count() > 0)
            <div class="space-y-4">
                @foreach($articles as $article)
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                        <a href="{{ route('articles.show', $article->slug) }}" class="block">
                            <h2 class="text-xl font-semibold text-gray-900 hover:text-blue-600">
                                {{ $article->title }}
                            </h2>
                        </a>
                        <p class="mt-2 text-gray-600 text-sm">
                            {{ Str::limit($article->excerpt, 200) }}
                        </p>
                        <div class="mt-4 flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-4 text-gray-500">
                                <span>By {{ $article->author->name }}</span>
                                <span>&bull;</span>
                                <span>{{ $article->published_at?->diffForHumans() }}</span>
                                <span>&bull;</span>
                                <span>{{ $article->view_count }} views</span>
                            </div>
                            @if($article->category)
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                    {{ $article->category->name }}
                                </span>
                            @endif
                        </div>
                        @if($article->tags->count() > 0)
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($article->tags as $tag)
                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $articles->links() }}
            </div>
        @else
            <div class="bg-white p-12 rounded-lg shadow text-center">
                <p class="text-gray-500">No articles found.</p>
            </div>
        @endif
    </div>
</div>
@endsection
