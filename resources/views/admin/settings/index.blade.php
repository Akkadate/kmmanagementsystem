@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
        <p class="mt-2 text-gray-600">Manage your knowledge base system configuration</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            @foreach($groups as $group)
                @if(isset($settings[$group]) && $settings[$group]->count() > 0)
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900 capitalize">
                                {{ ucfirst($group) }} Settings
                            </h2>
                        </div>

                        <div class="p-6 space-y-6">
                            @foreach($settings[$group] as $setting)
                                <div>
                                    <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $setting->label }}
                                        @if($setting->description)
                                            <span class="text-gray-500 font-normal">- {{ $setting->description }}</span>
                                        @endif
                                    </label>

                                    @if($setting->type === 'text' || $setting->type === 'email' || $setting->type === 'url')
                                        <input
                                            type="{{ $setting->type }}"
                                            id="{{ $setting->key }}"
                                            name="settings[{{ $setting->key }}]"
                                            value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    @elseif($setting->type === 'number')
                                        <input
                                            type="number"
                                            id="{{ $setting->key }}"
                                            name="settings[{{ $setting->key }}]"
                                            value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    @elseif($setting->type === 'textarea')
                                        <textarea
                                            id="{{ $setting->key }}"
                                            name="settings[{{ $setting->key }}]"
                                            rows="3"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >{{ old('settings.' . $setting->key, $setting->value) }}</textarea>
                                    @elseif($setting->type === 'boolean')
                                        <div class="flex items-center">
                                            <input
                                                type="checkbox"
                                                id="{{ $setting->key }}"
                                                name="settings[{{ $setting->key }}]"
                                                {{ old('settings.' . $setting->key, $setting->value) == '1' ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            >
                                            <label for="{{ $setting->key }}" class="ml-2 text-sm text-gray-600">
                                                Enable this option
                                            </label>
                                        </div>
                                    @elseif($setting->type === 'image')
                                        @if($setting->value)
                                            <div class="mb-2">
                                                <img src="{{ asset($setting->value) }}" alt="{{ $setting->label }}" class="h-20 rounded border">
                                            </div>
                                        @endif
                                        <input
                                            type="file"
                                            id="{{ $setting->key }}"
                                            name="settings[{{ $setting->key }}]"
                                            accept="image/*"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-6 flex items-center justify-end space-x-4">
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 text-gray-600 hover:text-gray-800">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
