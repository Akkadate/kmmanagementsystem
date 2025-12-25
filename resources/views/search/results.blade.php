@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Search Results</h1>
    @if($query)
        <p class="mt-2 text-gray-600">Showing results for: <strong>"{{ $query }}"</strong></p>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-900 mb-3">Refine Search</h3>

            <form action="{{ route('search') }}" method="GET">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keywords</label>
                    <input type="text" name="q" value="{{ $query }}" placeholder="Search..." class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">All Categories</option>
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

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($tags as $tag)
                            <label class="flex items-center">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                    {{ in_array($tag->id, request('tags', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Apply Filters
                </button>

                <a href="{{ route('search') }}" class="block text-center text-sm text-gray-600 hover:text-gray-900 mt-2">
                    Clear filters
                </a>
            </form>
        </div>
    </div>

    <div class="lg:col-span-3">
        @if($articles && $articles->count() > 0)
            <div class="mb-4 text-sm text-gray-600">
                Found {{ $articles->total() }} {{ Str::plural('result', $articles->total()) }}
            </div>

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
                {{ $articles->appends(request()->query())->links() }}
            </div>
        @else
            <div class="bg-white p-12 rounded-lg shadow text-center">
                <p class="text-gray-500">No articles found matching your search criteria.</p>
                <a href="{{ route('articles.index') }}" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">
                    Browse all articles
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
