@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-6">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
            Title <span class="text-red-600">*</span>
        </label>
        <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', $article->title ?? '') }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
        >
    </div>

    <div>
        <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
            Excerpt
        </label>
        <textarea
            id="excerpt"
            name="excerpt"
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="A brief summary of this article (max 500 characters)"
        >{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
        <p class="mt-1 text-sm text-gray-500">This will be shown in article listings and search results</p>
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
            Content <span class="text-red-600">*</span>
        </label>
        <textarea
            id="content"
            name="content"
            rows="20"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
        >{{ old('content', $article->content ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                Category <span class="text-red-600">*</span>
            </label>
            <select
                id="category_id"
                name="category_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
            >
                <option value="">Select a category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $article->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @if($category->children->count() > 0)
                        @foreach($category->children as $child)
                            <option value="{{ $child->id }}" {{ old('category_id', $article->category_id ?? '') == $child->id ? 'selected' : '' }}>
                                &nbsp;&nbsp;&nbsp;└─ {{ $child->name }}
                            </option>
                        @endforeach
                    @endif
                @endforeach
            </select>
        </div>

        <div>
            <label for="visibility" class="block text-sm font-medium text-gray-700 mb-2">
                Visibility <span class="text-red-600">*</span>
            </label>
            <select
                id="visibility"
                name="visibility"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
            >
                <option value="public" {{ old('visibility', $article->visibility ?? 'public') == 'public' ? 'selected' : '' }}>
                    Public - Everyone can view
                </option>
                <option value="members_only" {{ old('visibility', $article->visibility ?? '') == 'members_only' ? 'selected' : '' }}>
                    Members Only - Any logged-in user
                </option>
                <option value="staff_only" {{ old('visibility', $article->visibility ?? '') == 'staff_only' ? 'selected' : '' }}>
                    Staff Only - Users with department assigned
                </option>
                <option value="internal" {{ old('visibility', $article->visibility ?? '') == 'internal' ? 'selected' : '' }}>
                    Internal - Staff roles only (admin/editor/contributor)
                </option>
                <option value="private" {{ old('visibility', $article->visibility ?? '') == 'private' ? 'selected' : '' }}>
                    Private - Only author and admins
                </option>
            </select>
            <p class="mt-1 text-xs text-gray-500">Control who can view this article</p>
        </div>
    </div>

    @if(auth()->user()->isAdmin() || auth()->user()->isEditor())
    <div>
        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
            Department Restriction
        </label>
        <select
            id="department_id"
            name="department_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
            <option value="">No restriction - All departments</option>
            @foreach(\App\Models\Department::where('is_active', true)->orderBy('name')->get() as $department)
                <option value="{{ $department->id }}" {{ old('department_id', $article->department_id ?? '') == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Restrict article to specific department</p>
    </div>
    @endif

    <div>
        <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
            Featured Image
        </label>

        @if($article && $article->featured_image)
            <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-start gap-4">
                    <img
                        src="{{ asset('storage/' . $article->featured_image) }}"
                        alt="Current featured image"
                        class="w-32 h-32 object-cover rounded-lg shadow-sm"
                    >
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700 mb-4">Current Featured Image</p>
                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                name="remove_featured_image"
                                value="1"
                                class="rounded border-gray-300 text-red-600 focus:ring-red-500 mr-2"
                            >
                            <span class="text-sm text-red-600 font-medium">Remove this image</span>
                        </label>
                    </div>
                </div>
            </div>
        @endif

        <input
            type="file"
            id="featured_image"
            name="featured_image"
            accept="image/*"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
        <p class="mt-1 text-xs text-gray-500">
            @if($article && $article->featured_image)
                Upload a new image to replace the current one, or check "Remove this image" to delete it
            @else
                Upload an image (JPG, PNG, max 2MB)
            @endif
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Tags
        </label>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($tags as $tag)
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        name="tags[]"
                        value="{{ $tag->id }}"
                        {{ (old('tags') && in_array($tag->id, old('tags'))) || ($article && $article->tags->contains($tag->id)) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                    >
                    <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
@vite('resources/js/tinymce-init.js')
@endpush
