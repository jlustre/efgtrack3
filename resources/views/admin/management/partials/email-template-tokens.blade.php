@if (! empty($config['token_reference']))
    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-[#0B1F3A]">Available merge tokens</h2>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Insert tokens in the subject or body using
            <code class="rounded bg-white px-1 py-0.5 text-xs text-[#0B1F3A]">&#123;&#123; token_name &#125;&#125;</code>
            (spaces optional). They are replaced when the email is sent. The body supports HTML formatting.
        </p>
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($config['token_reference'] as $token)
                <code class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-[#0B1F3A]">&#123;&#123; {{ $token }} &#125;&#125;</code>
            @endforeach
        </div>
    </div>
@endif
