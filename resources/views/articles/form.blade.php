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
            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                Featured Image
            </label>
            <input
                type="file"
                id="featured_image"
                name="featured_image"
                accept="image/*"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            @if($article && $article->hasMedia('featured_image'))
                <p class="mt-2 text-sm text-gray-500">
                    Current image: {{ $article->getFirstMedia('featured_image')->file_name }}
                </p>
            @endif
        </div>
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
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 500,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic forecolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | code | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        promotion: false,
        branding: false
    });
</script>
@endpush
