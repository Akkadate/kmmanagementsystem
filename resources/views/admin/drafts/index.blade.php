@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Draft Approval Queue</h1>
    <p class="mt-2 text-gray-600">Review and approve articles from contributors</p>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
@endif

<!-- Draft Articles Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($drafts as $article)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $article->title }}</div>
                        @if($article->excerpt)
                            <div class="text-sm text-gray-500">{{ Str::limit($article->excerpt, 80) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                <span class="text-sm font-medium text-blue-800">
                                    {{ strtoupper(substr($article->author->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-900">{{ $article->author->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $article->category->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $article->created_at->diffForHumans() }}
                        <div class="text-xs text-gray-500">{{ $article->created_at->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                        <a href="{{ route('articles.show', $article->slug) }}"
                           class="text-blue-600 hover:underline"
                           target="_blank">Preview</a>
                        <a href="{{ route('articles.edit', $article->slug) }}"
                           class="text-indigo-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.drafts.approve', $article->id) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Approve and publish this article?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-green-600 hover:underline font-medium">
                                Approve & Publish
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 text-lg">No drafts awaiting approval</p>
                            <p class="text-sm text-gray-400 mt-1">Articles from contributors will appear here</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($drafts->hasPages())
    <div class="mt-6">
        {{ $drafts->links() }}
    </div>
@endif

<!-- Statistics Summary -->
@if($drafts->total() > 0)
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm text-blue-800">
            <strong>{{ $drafts->total() }}</strong> {{ Str::plural('draft', $drafts->total()) }} awaiting approval
        </span>
    </div>
</div>
@endif
@endsection
