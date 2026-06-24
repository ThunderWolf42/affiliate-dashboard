<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabung UKRIDA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white shadow-lg rounded-xl p-8 mx-4">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-blue-900">Mulai Masa Depanmu</h1>
            <p class="text-gray-500 text-sm">Direkomendasikan oleh: <span
                    class="font-bold text-orange-600">{{ $affiliate->name }}</span></p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="affiliate_id" value="{{ $affiliate->id }}">

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="lead_name"  value="{{ old('lead_name') }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-50 focus:ring-blue-500 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email Aktif</label>
                <input type="email" name="email"  value="{{ old('email') }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-50 focus:ring-blue-500 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                <input type="text" name="wa_number"  value="{{ old('wa_number') }}" required placeholder="0812..."
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-50 focus:ring-blue-500 border">
            </div>

            <button type="submit"
                class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                Daftar Sekarang
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-400 italic">
            {{-- *Dengan menekan tombol, Anda akan diarahkan ke portal pendaftaran resmi UKRIDA. --}}
        </p>
    </div>
</body>

</html>
