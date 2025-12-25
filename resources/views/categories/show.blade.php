@extends('layouts.app')

@section('content')
<div class="mb-6">
    <nav class="text-sm mb-4">
        <a href="{{ route('categories.index') }}" class="text-blue-600 hover:text-blue-800">Categories</a>
        @if($category->parent)
            <span class="mx-2">/</span>
            <a href="{{ route('categories.show', $category->parent->slug) }}" class="text-blue-600 hover:text-blue-800">
                {{ $category->parent->name }}
            </a>
        @endif
    </nav>

    <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
    @if($category->description)
        <p class="mt-2 text-gray-600">{{ $category->description }}</p>
    @endif
</div>

@if($category->children->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Subcategories</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($category->children as $child)
                <a href="{{ route('categories.show', $child->slug) }}" class="block p-4 border rounded hover:border-blue-600 hover:bg-blue-50 transition">
                    <h3 class="font-medium text-gray-900">{{ $child->name }}</h3>
                    @if($child->description)
                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($child->description, 80) }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif

<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-4">
        Articles in this category
        @if($descendantArticlesCount > 0)
            <span class="text-sm font-normal text-gray-500">(+{{ $descendantArticlesCount }} in subcategories)</span>
        @endif
    </h2>

    @if($articles->count() > 0)
        <div class="space-y-4">
            @foreach($articles as $article)
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                    <a href="{{ route('articles.show', $article->slug) }}" class="block">
                        <h3 class="text-xl font-semibold text-gray-900 hover:text-blue-600">
                            {{ $article->title }}
                        </h3>
                    </a>
                    <p class="mt-2 text-gray-600 text-sm">
                        {{ Str::limit($article->excerpt, 200) }}
                    </p>
                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <span>By {{ $article->author->name }}</span>
                        <span class="mx-2">&bull;</span>
                        <span>{{ $article->published_at?->diffForHumans() }}</span>
                        <span class="mx-2">&bull;</span>
                        <span>{{ $article->view_count }} views</span>
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
            <p class="text-gray-500">No articles in this category yet.</p>
        </div>
    @endif
</div>
@endsection
