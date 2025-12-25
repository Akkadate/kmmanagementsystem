@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Article</h1>
        <p class="mt-2 text-gray-600">Update your knowledge base article</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('articles.update', $article->slug) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('articles.form', ['article' => $article, 'categories' => $categories, 'tags' => $tags])

            <div class="flex items-center justify-between mt-6 pt-6 border-t">
                <div>
                    <a href="{{ route('articles.show', $article->slug) }}" class="text-gray-600 hover:text-gray-800">
                        Cancel
                    </a>
                    @can('delete', $article)
                        <button type="button" onclick="if(confirm('Are you sure?')) document.getElementById('delete-form').submit();" class="ml-4 text-red-600 hover:text-red-800">
                            Delete Article
                        </button>
                    @endcan
                </div>
                <div class="flex gap-4">
                    <button type="submit" name="status" value="draft" class="px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Save as Draft
                    </button>
                    <button type="submit" name="status" value="published" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ $article->status === 'published' ? 'Update' : 'Publish' }}
                    </button>
                </div>
            </div>
        </form>

        @can('delete', $article)
            <form id="delete-form" action="{{ route('articles.destroy', $article->slug) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endcan
    </div>
</div>
@endsection
