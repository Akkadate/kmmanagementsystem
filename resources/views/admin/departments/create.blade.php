@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Department</h1>
        <p class="mt-2 text-gray-600">เพิ่มหน่วยงาน/แผนกใหม่เข้าสู่ระบบ</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.departments.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Department Name <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Department Code <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        value="{{ old('code') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror"
                        placeholder="e.g., IT, HR, FIN"
                        required
                    >
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">รหัสย่อของหน่วยงาน (ไม่ซ้ำกัน)</p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                        placeholder="คำอธิบายเกี่ยวกับหน่วยงาน"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                        >
                        <span class="text-sm text-gray-700">Active Department</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500 ml-6">หน่วยงานที่ไม่ active จะไม่แสดงในตัวเลือก</p>
                </div>
            </div>

            <div class="flex items-center justify-between mt-8 pt-6 border-t">
                <a href="{{ route('admin.departments.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create Department
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
