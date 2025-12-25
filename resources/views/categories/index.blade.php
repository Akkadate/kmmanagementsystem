@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
    <p class="mt-2 text-gray-600">Browse knowledge base by category</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($categories as $category)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
            <a href="{{ route('categories.show', $category->slug) }}" class="block">
                <h2 class="text-xl font-semibold text-gray-900 hover:text-blue-600 mb-2">
                    {{ $category->name }}
                </h2>
            </a>
            @if($category->description)
                <p class="text-sm text-gray-600 mb-4">
                    {{ $category->description }}
                </p>
            @endif
            <div class="text-sm text-gray-500 mb-3">
                {{ $category->articles_count }} {{ Str::plural('article', $category->articles_count) }}
            </div>

            @if($category->children->count() > 0)
                <div class="border-t pt-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Subcategories</p>
                    <ul class="space-y-1">
                        @foreach($category->children as $child)
                            <li>
                                <a href="{{ route('categories.show', $child->slug) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    {{ $child->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endforeach
</div>

@if($categories->isEmpty())
    <div class="bg-white p-12 rounded-lg shadow text-center">
        <p class="text-gray-500">No categories available.</p>
    </div>
@endif
@endsection
