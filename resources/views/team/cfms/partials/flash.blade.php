@if (session('status'))
    <div class="mb-6 rounded-xl border border-green-500/30 bg-green-900/20 px-4 py-3 text-sm text-green-300">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-500/30 bg-red-900/20 px-4 py-3 text-sm text-red-300">
        <p class="font-semibold text-red-200">Please fix the following:</p>
        <ul class="mt-2 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
