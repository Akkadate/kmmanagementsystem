@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Manage Articles</h1>
        <p class="mt-2 text-gray-600">Browse, filter, and manage all articles</p>
    </div>
    <a href="{{ route('articles.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Create Article
    </a>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.articles.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Title or excerpt...">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category_id" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="author_id" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                <select id="author_id" name="author_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>
                            {{ $author->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Apply Filters
            </button>
            <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Bulk Actions Form -->
<form id="bulkForm" method="POST" action="{{ route('admin.articles.bulk-update') }}">
    @csrf
    <div class="bg-white rounded-lg shadow mb-4 p-4 flex items-center space-x-4">
        <label class="flex items-center">
            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-700">Select All</span>
        </label>
        <div class="flex-1"></div>
        <select name="action" class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Bulk Actions</option>
            <option value="publish">Publish</option>
            <option value="draft">Set to Draft</option>
            <option value="delete">Delete</option>
        </select>
        <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" onclick="handleBulkAction()">
            Apply
        </button>
    </div>

    <!-- Articles Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left w-12">
                        <input type="checkbox" class="hidden">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($articles as $article)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="article_ids[]" value="{{ $article->id }}" class="article-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $article->title }}</div>
                            @if($article->excerpt)
                                <div class="text-sm text-gray-500">{{ Str::limit($article->excerpt, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $article->author->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $article->category->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded
                                {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($article->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($article->view_count) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $article->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                            <a href="{{ route('articles.show', $article->slug) }}" class="text-blue-600 hover:underline" target="_blank">View</a>
                            <a href="{{ route('articles.edit', $article->slug) }}" class="text-indigo-600 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            No articles found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

<!-- Pagination -->
@if($articles->hasPages())
    <div class="mt-6">
        {{ $articles->links() }}
    </div>
@endif

@push('scripts')
<script>
// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.article-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Handle bulk action with confirmation
async function handleBulkAction() {
    const selected = document.querySelectorAll('.article-checkbox:checked');
    const action = document.querySelector('select[name="action"]').value;

    if (selected.length === 0) {
        showWarning('Please select at least one article');
        return;
    }

    if (!action) {
        showWarning('Please select an action');
        return;
    }

    const message = action === 'delete'
        ? `Are you sure you want to delete ${selected.length} article(s)?`
        : `Are you sure you want to ${action} ${selected.length} article(s)?`;

    const confirmed = await confirmAction('Confirm Action', message, 'Yes, proceed');

    if (confirmed) {
        document.querySelector('form[action="{{ route('admin.articles.bulk-update') }}"]').submit();
    }
}
</script>
@endpush
@endsection
