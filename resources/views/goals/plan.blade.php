<x-app-layout>
    <div class="mb-6">
        <a href="{{ route('goals.index') }}" class="text-sm font-semibold text-[#8A6A1F] hover:underline">&larr; Back to Goals</a>
    </div>
    <livewire:goals.performance-planner-wizard />
</x-app-layout>
