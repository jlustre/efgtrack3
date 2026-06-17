<x-app-layout>
    <div class="mb-6">
        <a href="{{ route('goals.index') }}" class="text-sm font-semibold text-[#8A6A1F] hover:underline">&larr; Back to Goals</a>
        <h1 class="mt-3 text-2xl font-semibold text-[#0B1F3A]">Activity Scorecard</h1>
        <p class="mt-1 text-sm text-slate-600">Track daily, weekly, monthly, and annual activity performance against your plan.</p>
    </div>
    <livewire:goals.activity-scorecard />
</x-app-layout>
