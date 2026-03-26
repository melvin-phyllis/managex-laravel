<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Demande de stage - ManageX</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="max-w-3xl mx-auto px-4 py-10">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
            <h1 class="text-2xl font-bold text-[#1B3C35]">Demande de stage</h1>
            <p class="text-sm text-slate-600 mt-1">Remplissez ce formulaire. Votre demande sera visible directement dans l'administration.</p>

            @if(session('success'))
                <div class="mt-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('stage-request.store') }}" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Nom complet *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full rounded-lg border-slate-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-slate-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Telephone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Ecole / Universite</label>
                    <input type="text" name="school" value="{{ old('school') }}" class="w-full rounded-lg border-slate-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Niveau (L3, M1, BTS...)</label>
                    <input type="text" name="level" value="{{ old('level') }}" class="w-full rounded-lg border-slate-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Poste souhaite</label>
                    <input type="text" name="desired_role" value="{{ old('desired_role') }}" placeholder="Ex: Developpeur web" class="w-full rounded-lg border-slate-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Message</label>
                    <textarea name="message" rows="5" class="w-full rounded-lg border-slate-300 focus:border-[#2D5A4E] focus:ring-[#2D5A4E]">{{ old('message') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-white font-medium" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">
                        Envoyer la demande
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

