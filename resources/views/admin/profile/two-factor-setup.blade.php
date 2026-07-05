@extends('layouts.admin')

@section('title', 'Setup 2FA')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Aktifkan 2FA</h1>

    <div class="bg-white rounded-lg border p-6 max-w-lg">
        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2 mb-6">
            <li>Buka aplikasi authenticator (Google Authenticator / Authy / dsb) di HP Anda.</li>
            <li>Scan QR code di bawah ini — atau pilih "Masukkan kode setup manual" kalau kamera tidak tersedia.</li>
        </ol>

        <div class="flex justify-center mb-6">
            <div id="qrcode" class="p-3 bg-white border rounded-md inline-block"></div>
        </div>

        <details class="mb-6 text-sm">
            <summary class="cursor-pointer text-gray-600 hover:text-gray-900">Tidak bisa scan? Masukkan manual</summary>
            <div class="bg-gray-100 rounded-md p-4 text-center mt-2">
                <p class="font-mono text-base tracking-widest break-all">{{ $secret }}</p>
            </div>
        </details>

        @if ($errors->has('code'))
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                {{ $errors->first('code') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.profile.2fa.confirm') }}" class="space-y-3">
            @csrf
            <label class="block text-sm text-gray-700 mb-1">Masukkan kode 6-digit dari aplikasi Anda untuk konfirmasi</label>
            <input type="text" name="code" required inputmode="numeric" placeholder="000000"
                   class="w-full text-center text-xl tracking-widest rounded-md border-gray-300 border px-3 py-2">

            <button type="submit" class="w-full bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800">
                Konfirmasi & Aktifkan
            </button>
        </form>
    </div>

    {{-- QR rendered fully client-side from the otpauth:// URI - server never
         needs a QR-generation package, and the secret never leaves this page
         via a third-party image request (no Google Charts API, etc). --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: @json($uri),
            width: 200,
            height: 200,
        });
    </script>
@endsection
