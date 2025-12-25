@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
    <p class="mt-2 text-gray-600">Insights and statistics for your knowledge base</p>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Articles -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Total Articles</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($metrics['total_articles']) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    <span class="text-green-600">{{ $metrics['published_articles'] }} published</span>
                    / {{ $metrics['draft_articles'] }} drafts
                </p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Views -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Total Views</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($metrics['total_views']) }}</p>
                <p class="text-xs text-gray-500 mt-1">All time</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Users -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Active Users</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($metrics['active_users']) }}</p>
                <p class="text-xs text-gray-500 mt-1">of {{ $metrics['total_users'] }} total</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- This Month -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">This Month</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($metrics['articles_this_month']) }}</p>
                <p class="text-xs text-gray-500 mt-1">New articles</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top Articles -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Top Articles</h2>
                <a href="{{ route('admin.analytics.top-articles') }}" class="text-blue-600 hover:underline text-sm">View all</a>
            </div>
        </div>
        <div class="p-6">
            @if(count($topArticles) > 0)
                <div class="space-y-4">
                    @foreach($topArticles as $item)
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <a href="{{ route('articles.show', $item['article']['slug']) }}"
                                   class="text-gray-900 hover:text-blue-600 font-medium"
                                   target="_blank">
                                    {{ $item['article']['title'] }}
                                </a>
                                <div class="flex items-center mt-1 text-xs text-gray-500">
                                    <span>{{ number_format($item['view_count']) }} views</span>
                                    @if($item['helpful_ratio'] !== null)
                                        <span class="mx-2">•</span>
                                        <span class="text-green-600">{{ $item['helpful_ratio'] }}% helpful</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No articles yet</p>
            @endif
        </div>
    </div>

    <!-- Category Stats -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Articles by Category</h2>
        </div>
        <div class="p-6">
            @if(count($categoryStats) > 0)
                <div class="space-y-3">
                    @foreach(array_slice($categoryStats, 0, 5) as $category)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                                <span class="text-sm text-gray-600">{{ $category->article_count }} articles</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $maxArticles = count($categoryStats) > 0 ? max(array_column($categoryStats, 'article_count')) : 1;
                                    $percentage = $maxArticles > 0 ? ($category->article_count / $maxArticles) * 100 : 0;
                                @endphp
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No categories yet</p>
            @endif
        </div>
    </div>
</div>

<!-- Top Contributors -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Top Contributors</h2>
    </div>
    <div class="p-6">
        @if(count($userContributions) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($userContributions as $contribution)
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg">
                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <span class="text-lg font-bold text-blue-800">
                                {{ strtoupper(substr($contribution['user']['name'], 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $contribution['user']['name'] }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $contribution['published_articles'] }} published
                                @if($contribution['draft_articles'] > 0)
                                    / {{ $contribution['draft_articles'] }} drafts
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No contributions yet</p>
        @endif
    </div>
</div>

<!-- Quick Links -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="{{ route('admin.analytics.top-articles') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center">
            <div class="bg-blue-100 rounded-full p-3 mr-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-gray-900">Top Articles</h3>
                <p class="text-sm text-gray-600">View most viewed articles</p>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.analytics.feedback') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center">
            <div class="bg-green-100 rounded-full p-3 mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-gray-900">Feedback Stats</h3>
                <p class="text-sm text-gray-600">View article ratings</p>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.analytics.activity-logs') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center">
            <div class="bg-purple-100 rounded-full p-3 mr-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-gray-900">Activity Logs</h3>
                <p class="text-sm text-gray-600">View recent actions</p>
            </div>
        </div>
    </a>
</div>
@endsection
