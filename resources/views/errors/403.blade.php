@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-16">
    <div class="max-w-2xl w-full text-center">
        <!-- Error Icon -->
        <div class="mb-8">
            <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <!-- Error Message -->
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Access Denied
        </h1>

        <p class="text-xl text-gray-600 mb-8">
            คุณไม่มีสิทธิ์เข้าถึงบทความนี้
        </p>

        <!-- Detailed Message -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
            <p class="text-gray-700 mb-4">
                บทความนี้อาจมีข้อจำกัดในการเข้าถึงด้วยเหตุผลดังต่อไปนี้:
            </p>
            <ul class="text-left text-gray-600 space-y-2 max-w-md mx-auto">
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>บทความสำหรับสมาชิกเท่านั้น (ต้อง Login)</span>
                </li>
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>บทความภายในสำหรับเจ้าหน้าที่เท่านั้น</span>
                </li>
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>บทความเฉพาะแผนก/หน่วยงาน</span>
                </li>
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>บทความส่วนตัว (เฉพาะผู้เขียนและผู้ดูแลระบบ)</span>
                </li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                กลับหน้าแรก
            </a>

            @guest
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    เข้าสู่ระบบ
                </a>
            @else
                <a href="{{ route('articles.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    ดูบทความทั้งหมด
                </a>
            @endguest
        </div>

        <!-- Additional Help -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-500">
                หากคุณคิดว่านี่เป็นข้อผิดพลาด กรุณาติดต่อผู้ดูแลระบบที่
                <a href="mailto:kms@northbkk.ac.th" class="text-blue-600 hover:text-blue-800 underline">kms@northbkk.ac.th</a>
            </p>
        </div>
    </div>
</div>
@endsection
