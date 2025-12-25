@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Article</h1>
        <p class="mt-2 text-gray-600">Write and publish knowledge base articles</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('articles.form', ['article' => null, 'categories' => $categories, 'tags' => $tags])

            <div class="flex items-center justify-between mt-6 pt-6 border-t">
                <a href="{{ route('articles.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <div class="flex gap-4">
                    <button type="submit" name="status" value="draft" class="px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Save as Draft
                    </button>
                    <button type="submit" name="status" value="published" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Publish
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
