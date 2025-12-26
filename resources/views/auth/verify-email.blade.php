@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full">
        <!-- Icon -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">ยืนยันอีเมลของคุณ</h2>
        </div>

        <!-- Main Message -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <p class="text-gray-700 mb-4">
                ขอบคุณที่สมัครสมาชิก! ก่อนเริ่มใช้งาน กรุณายืนยันอีเมลของคุณโดยคลิกลิงก์ที่เราส่งไปให้ทางอีเมล
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-4">
                <p class="text-sm text-blue-800">
                    <strong>อีเมลของคุณ:</strong> {{ auth()->user()->email }}
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="bg-green-50 border border-green-200 rounded p-4 mb-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-green-800 font-medium">
                            ส่งลิงก์ยืนยันใหม่ไปยังอีเมลของคุณแล้ว!
                        </p>
                    </div>
                </div>
            @endif

            <p class="text-sm text-gray-600">
                หากคุณไม่ได้รับอีเมล กรุณาตรวจสอบในกล่อง Spam หรือคลิกปุ่มด้านล่างเพื่อส่งอีเมลใหม่
            </p>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    ส่งอีเมลยืนยันอีกครั้ง
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    ออกจากระบบ
                </button>
            </form>
        </div>

        <!-- Help Text -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                หากคุณต้องการความช่วยเหลือ กรุณาติดต่อ
                <a href="mailto:kms@northbkk.ac.th" class="text-blue-600 hover:underline">kms@northbkk.ac.th</a>
            </p>
        </div>
    </div>
</div>
@endsection
