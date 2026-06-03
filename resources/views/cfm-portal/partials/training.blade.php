<div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-amber-400">CFM Training Progress</h3>
        <a href="{{ route('cfm-training.index') }}" class="text-xs text-amber-400 hover:text-amber-300">View checklist →</a>
    </div>

    <div class="mb-4">
        <div class="flex justify-between text-sm mb-2">
            <span class="text-gray-400">Overall progress</span>
            <span class="text-white font-medium">{{ $training['percent'] }}%</span>
        </div>
        <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-amber-400 h-2 rounded-full" style="width: {{ $training['percent'] }}%"></div>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            {{ $training['completed'] }}/{{ $training['total'] }} modules complete
            · Required: {{ $training['requiredCompleted'] }}/{{ $training['requiredTotal'] }} ({{ $training['requiredPercent'] }}%)
        </p>
    </div>

    @if (count($training['modules']) > 0)
        <ul class="space-y-2 max-h-64 overflow-y-auto">
            @foreach ($training['modules'] as $module)
                <li class="flex items-center justify-between rounded-lg bg-gray-800/50 px-3 py-2 text-sm">
                    <span class="text-gray-200">{{ $module['title'] }}</span>
                    <span @class([
                        'text-xs font-medium',
                        'text-green-400' => $module['isCompleted'],
                        'text-yellow-400' => $module['isPending'],
                        'text-gray-500' => ! $module['isCompleted'] && ! $module['isPending'],
                    ])>{{ $module['status'] }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-gray-500">No CFM training modules are configured yet.</p>
    @endif
</div>
