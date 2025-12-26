@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
    <p class="mt-2 text-gray-600">Content management overview and statistics</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Articles -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Articles</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_articles'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-4">
            <span class="text-green-600 font-medium">{{ $stats['published_articles'] }}</span> published
        </p>
    </div>

    <!-- Draft Articles -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Draft Articles</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['draft_articles'] }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-4">Pending review</p>
    </div>

    <!-- Categories -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Categories</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_categories'] }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-4">
            <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:underline">Manage categories</a>
        </p>
    </div>

    <!-- Total Views -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Views</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_views']) }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-4">All time</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="space-y-3">
            <a href="{{ route('articles.create') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="text-blue-900 font-medium">Create New Article</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="text-purple-900 font-medium">Manage Categories</span>
            </a>
            <a href="{{ route('admin.tags.index') }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                </svg>
                <span class="text-green-900 font-medium">Manage Tags</span>
            </a>
            <a href="{{ route('admin.articles.index') }}" class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span class="text-yellow-900 font-medium">Manage Articles</span>
            </a>
            @if(auth()->user()->canPublishArticle())
            <a href="{{ route('admin.drafts.index') }}" class="flex items-center p-3 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-indigo-900 font-medium">Approve Drafts</span>
            </a>
            @endif
            @can('viewAny', \App\Models\User::class)
            <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="text-orange-900 font-medium">Manage Users</span>
            </a>
            @endcan
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.departments.index') }}" class="flex items-center p-3 bg-teal-50 rounded-lg hover:bg-teal-100 transition">
                <svg class="w-6 h-6 text-teal-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="text-teal-900 font-medium">Manage Departments</span>
            </a>
            @endif
            @if(in_array(auth()->user()->role, ['admin', 'editor']))
            <a href="{{ route('admin.analytics.dashboard') }}" class="flex items-center p-3 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                <svg class="w-6 h-6 text-pink-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span class="text-pink-900 font-medium">View Analytics</span>
            </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Articles</h2>
        <div class="space-y-3">
            @forelse($recent_articles as $article)
                <div class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex-1">
                        <a href="{{ route('articles.show', $article->slug) }}" class="font-medium text-gray-900 hover:text-blue-600">
                            {{ $article->title }}
                        </a>
                        <p class="text-sm text-gray-500 mt-1">
                            by {{ $article->author->name }} •
                            {{ $article->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded
                        {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($article->status) }}
                    </span>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No recent articles</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Top Categories -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Top Categories by Article Count</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Articles</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($top_categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $category->articles_count }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $category->published_articles_count }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('categories.show', $category->slug) }}" class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No categories found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
