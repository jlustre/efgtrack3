<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminManagementController extends Controller
{
    public function index(): View
    {
        abort_unless($this->canViewManagementIndex(), 403);

        return view('admin.management.index', [
            'resources' => $this->resources(),
        ]);
    }

    public function resourceIndex(Request $request, string $resource): View
    {
        $config = $this->resourceConfig($resource);
        abort_unless($this->canViewResource($resource), 403);

        $search = $request->string('search')->toString();
        $trashed = $request->string('trashed')->toString();

        $records = DB::table($config['table'])
            ->when($trashed === 'with', fn ($query) => $query)
            ->when($trashed === 'only', fn ($query) => $query->whereNotNull('deleted_at'))
            ->when($trashed !== 'only', fn ($query) => $query->whereNull('deleted_at'))
            ->when($search, function ($query) use ($config, $search): void {
                $query->where(function ($query) use ($config, $search): void {
                    foreach ($config['search'] as $column) {
                        $query->orWhere($column, 'like', "%{$search}%");
                    }
                });
            })
            ->orderBy($config['order_by'] ?? 'id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.management.resource-index', [
            'resource' => $resource,
            'config' => $config,
            'records' => $records,
            'filters' => compact('search', 'trashed'),
            'canManage' => $this->canManageResource($resource),
            'canUpdateSeeder' => $this->canManageResource($resource) && $this->isChecklistResource($resource),
            'options' => $this->formOptions(),
        ]);
    }

    public function create(string $resource): View
    {
        $config = $this->resourceConfig($resource);
        abort_unless($this->canManageResource($resource), 403);

        return view('admin.management.create', [
            'resource' => $resource,
            'config' => $config,
            'record' => null,
            'options' => $this->formOptions(),
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        abort_unless($this->canManageResource($resource), 403);

        $validated = $this->validatedData($request, $config);

        if (array_key_exists('slug', $validated) && blank($validated['slug']) && isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (($config['uses_creator'] ?? false) && ! array_key_exists('created_by', $validated)) {
            $validated['created_by'] = $request->user()->id;
        }

        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        $id = DB::table($config['table'])->insertGetId($validated);

        return redirect()
            ->route('admin.management.edit', [$resource, $id])
            ->with('status', 'record-created');
    }

    public function show(string $resource, int $record): View
    {
        $config = $this->resourceConfig($resource);
        abort_unless($this->canViewResource($resource), 403);

        $row = DB::table($config['table'])->where('id', $record)->firstOrFail();

        return view('admin.management.show', [
            'resource' => $resource,
            'config' => $config,
            'record' => $row,
            'canManage' => $this->canManageResource($resource),
        ]);
    }

    public function edit(string $resource, int $record): View
    {
        $config = $this->resourceConfig($resource);
        abort_unless($this->canManageResource($resource), 403);

        $row = DB::table($config['table'])->where('id', $record)->firstOrFail();

        return view('admin.management.edit', [
            'resource' => $resource,
            'config' => $config,
            'record' => $row,
            'options' => $this->formOptions(),
        ]);
    }

    public function update(Request $request, string $resource, int $record): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        abort_unless($this->canManageResource($resource), 403);

        $validated = $this->validatedData($request, $config, $record);

        if (array_key_exists('slug', $validated) && blank($validated['slug']) && isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['updated_at'] = now();

        DB::table($config['table'])->where('id', $record)->update($validated);

        return redirect()
            ->route('admin.management.edit', [$resource, $record])
            ->with('status', 'record-updated');
    }

    public function toggleStatus(string $resource, int $record): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        abort_unless($this->canManageResource($resource), 403);
        abort_unless($this->hasColumn($config['table'], 'is_active'), 404);

        $row = DB::table($config['table'])->where('id', $record)->firstOrFail();

        DB::table($config['table'])->where('id', $record)->update([
            'is_active' => ! (bool) $row->is_active,
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.management.resource.index', [$resource, 'trashed' => request('trashed')])
            ->with('status', (bool) $row->is_active ? 'record-deactivated' : 'record-activated');
    }

    public function updateSeeder(string $resource): RedirectResponse
    {
        $this->resourceConfig($resource);
        abort_unless($this->canManageResource($resource) && $this->isChecklistResource($resource), 403);

        $seederContent = match ($resource) {
            'onboarding-steps' => $this->buildOnboardingStepSeeder(),
            'licensing-steps' => $this->buildLicensingStepSeeder(),
            'apprenticeship-steps' => $this->buildFieldApprenticeshipProgramSeeder(),
            'cfm-training-modules' => $this->buildCfmTrainingModuleSeeder(),
        };

        File::put($this->seederPath($resource), $seederContent);

        return redirect()
            ->route('admin.management.resource.index', [$resource, 'trashed' => request('trashed')])
            ->with('status', 'seeder-updated');
    }

    public function destroy(string $resource, int $record): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        abort_unless($this->canManageResource($resource), 403);

        DB::table($config['table'])->where('id', $record)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.management.resource.index', [$resource, 'trashed' => 'with'])
            ->with('status', 'record-archived');
    }

    public function restore(string $resource, int $record): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        abort_unless($this->canManageResource($resource), 403);

        DB::table($config['table'])->where('id', $record)->update([
            'deleted_at' => null,
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.management.edit', [$resource, $record])
            ->with('status', 'record-restored');
    }

    private function validatedData(Request $request, array $config, ?int $record = null): array
    {
        $rules = [];

        foreach ($config['fields'] as $field) {
            $fieldRules = $field['rules'];

            if (($field['unique'] ?? false) === true) {
                $fieldRules[] = Rule::unique($config['table'], $field['name'])->ignore($record);
            }

            $rules[$field['name']] = $fieldRules;
        }

        return $request->validate($rules);
    }

    private function resourceConfig(string $resource): array
    {
        $resources = $this->resources();

        abort_unless(isset($resources[$resource]), 404);

        return $resources[$resource];
    }

    private function fieldMap(array $config): array
    {
        return collect($config['fields'])->keyBy('name')->all();
    }

    private function canViewManagementIndex(): bool
    {
        return auth()->user()->hasAnyRole(['super-admin', 'admin']);
    }

    private function canViewResource(string $resource): bool
    {
        if (auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        return $this->isChecklistResource($resource)
            && auth()->user()->hasAnyRole(['agency-owner', 'team-leader', 'certified-field-mentor', 'trainer']);
    }

    private function canManageResource(string $resource): bool
    {
        if (auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        return $this->isChecklistResource($resource)
            && auth()->user()->hasRole('agency-owner');
    }

    private function isChecklistResource(string $resource): bool
    {
        return in_array($resource, [
            'onboarding-steps',
            'licensing-steps',
            'apprenticeship-steps',
            'cfm-training-modules',
        ], true);
    }

    private function hasColumn(string $table, string $column): bool
    {
        static $columns = [];

        $columns[$table] ??= DB::getSchemaBuilder()->getColumnListing($table);

        return in_array($column, $columns[$table], true);
    }

    private function seederPath(string $resource): string
    {
        return match ($resource) {
            'onboarding-steps' => database_path('seeders/OnboardingStepSeeder.php'),
            'licensing-steps' => database_path('seeders/LicensingStepSeeder.php'),
            'apprenticeship-steps' => database_path('seeders/FieldApprenticeshipProgramSeeder.php'),
            'cfm-training-modules' => database_path('seeders/CfmTrainingModuleSeeder.php'),
        };
    }

    private function buildOnboardingStepSeeder(): string
    {
        $steps = DB::table('onboarding_steps')
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(fn ($step) => [
                'title' => $step->title,
                'description' => $step->description,
                'sort_order' => (int) $step->sort_order,
                'responsible_parties' => $step->responsible_parties ?: 'Self',
                'notified_parties' => $step->notified_parties,
                'is_active' => (bool) $step->is_active,
                'is_required' => (bool) $step->is_required,
                'country' => $step->country,
            ])
            ->all();

        $exportedSteps = $this->exportPhpArray($steps, 2);

        return <<<PHP
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class OnboardingStepSeeder extends Seeder
{
    public function run(): void
    {
        \$steps = {$exportedSteps};

        foreach (\$steps as \$step) {
            DB::table('onboarding_steps')->updateOrInsert(
                ['title' => \$step['title']],
                [
                    'description' => \$step['description'],
                    'sort_order' => \$step['sort_order'],
                    'responsible_parties' => \$step['responsible_parties'],
                    'notified_parties' => \$step['notified_parties'],
                    'is_active' => \$step['is_active'],
                    'is_required' => \$step['is_required'],
                    'country' => \$step['country'],
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

PHP;
    }

    private function buildLicensingStepSeeder(): string
    {
        $steps = DB::table('licensing_steps')
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(fn ($step) => [
                'title' => $step->title,
                'description' => $step->description,
                'sort_order' => (int) $step->sort_order,
                'responsible_parties' => $step->responsible_parties ?: 'Self',
                'notified_parties' => $step->notified_parties,
                'is_active' => (bool) $step->is_active,
                'is_required' => (bool) $step->is_required,
            ])
            ->all();

        $exportedSteps = $this->exportPhpArray($steps, 2);

        return <<<PHP
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class LicensingStepSeeder extends Seeder
{
    public function run(): void
    {
        \$steps = {$exportedSteps};

        foreach (\$steps as \$step) {
            DB::table('licensing_steps')->updateOrInsert(
                ['title' => \$step['title']],
                [
                    'description' => \$step['description'],
                    'sort_order' => \$step['sort_order'],
                    'responsible_parties' => \$step['responsible_parties'],
                    'notified_parties' => \$step['notified_parties'],
                    'is_active' => \$step['is_active'],
                    'is_required' => \$step['is_required'],
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

PHP;
    }

    private function buildFieldApprenticeshipProgramSeeder(): string
    {
        $program = DB::table('apprenticeship_programs')
            ->whereNull('deleted_at')
            ->where('name', 'Field Apprenticeship Program')
            ->first()
            ?? DB::table('apprenticeship_programs')->whereNull('deleted_at')->orderBy('name')->first();

        abort_unless($program, 404);

        $programData = [
            'name' => $program->name,
            'description' => $program->description,
            'is_active' => (bool) $program->is_active,
        ];

        $steps = DB::table('apprenticeship_steps')
            ->whereNull('deleted_at')
            ->where('apprenticeship_program_id', $program->id)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(fn ($step) => [
                'title' => $step->title,
                'description' => $step->description,
                'sort_order' => (int) $step->sort_order,
                'responsible_parties' => $step->responsible_parties ?: 'Self',
                'notified_parties' => $step->notified_parties,
                'is_active' => (bool) $step->is_active,
            ])
            ->all();

        $exportedProgram = $this->exportPhpArray($programData, 2);
        $exportedSteps = $this->exportPhpArray($steps, 2);

        return <<<PHP
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class FieldApprenticeshipProgramSeeder extends Seeder
{
    public function run(): void
    {
        \$program = {$exportedProgram};

        DB::table('apprenticeship_programs')->updateOrInsert(
            ['name' => \$program['name']],
            [
                'description' => \$program['description'],
                'is_active' => \$program['is_active'],
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        \$programId = DB::table('apprenticeship_programs')
            ->where('name', \$program['name'])
            ->value('id');

        \$steps = {$exportedSteps};

        foreach (\$steps as \$step) {
            DB::table('apprenticeship_steps')->updateOrInsert(
                [
                    'apprenticeship_program_id' => \$programId,
                    'title' => \$step['title'],
                ],
                [
                    'description' => \$step['description'],
                    'sort_order' => \$step['sort_order'],
                    'responsible_parties' => \$step['responsible_parties'],
                    'notified_parties' => \$step['notified_parties'],
                    'is_active' => \$step['is_active'],
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

PHP;
    }

    private function buildCfmTrainingModuleSeeder(): string
    {
        $modules = DB::table('cfm_training_modules')
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(fn ($module) => [
                'title' => $module->title,
                'description' => $module->description,
                'sort_order' => (int) $module->sort_order,
                'responsible_parties' => $module->responsible_parties ?: 'Self',
                'notified_parties' => $module->notified_parties,
                'is_active' => (bool) $module->is_active,
                'is_required' => (bool) $module->is_required,
            ])
            ->all();

        $exportedModules = $this->exportPhpArray($modules, 2);

        return <<<PHP
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class CfmTrainingModuleSeeder extends Seeder
{
    public function run(): void
    {
        \$modules = {$exportedModules};

        foreach (\$modules as \$module) {
            DB::table('cfm_training_modules')->updateOrInsert(
                ['title' => \$module['title']],
                [
                    'description' => \$module['description'],
                    'sort_order' => \$module['sort_order'],
                    'responsible_parties' => \$module['responsible_parties'],
                    'notified_parties' => \$module['notified_parties'],
                    'is_active' => \$module['is_active'],
                    'is_required' => \$module['is_required'],
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

PHP;
    }

    private function exportPhpArray(array $value, int $indent = 0): string
    {
        $export = var_export($value, true);
        $export = preg_replace('/^([ ]*)array \\(/m', '$1[', $export);
        $export = preg_replace('/\\)(,?)$/m', ']$1', $export);

        if ($indent === 0) {
            return $export;
        }

        $spaces = str_repeat(' ', $indent * 4);

        return preg_replace('/^/m', $spaces, $export);
    }

    private function formOptions(): array
    {
        return [
            'users' => DB::table('users')->whereNull('deleted_at')->orderBy('name')->get(['id', 'name', 'email']),
            'teams' => DB::table('teams')->whereNull('deleted_at')->orderBy('name')->get(['id', 'name']),
            'training_categories' => DB::table('training_categories')->whereNull('deleted_at')->orderBy('name')->get(['id', 'name']),
            'training_modules' => DB::table('training_modules')->whereNull('deleted_at')->orderBy('title')->get(['id', 'title']),
            'assessments' => DB::table('assessments')->whereNull('deleted_at')->orderBy('title')->get(['id', 'title']),
            'questions' => DB::table('questions')->whereNull('deleted_at')->orderBy('question')->get(['id', 'question']),
            'ranks' => DB::table('ranks')->whereNull('deleted_at')->orderBy('sort_order')->get(['id', 'code', 'name']),
            'apprenticeship_programs' => DB::table('apprenticeship_programs')->whereNull('deleted_at')->orderBy('name')->get(['id', 'name']),
            'calendar_categories' => DB::table('calendar_categories')->whereNull('deleted_at')->orderBy('sort_order')->get(['id', 'name']),
            'calendar_event_types' => DB::table('calendar_event_types')->whereNull('deleted_at')->orderBy('sort_order')->get(['id', 'name']),
            'booking_event_types' => DB::table('booking_event_types')->whereNull('deleted_at')->orderBy('title')->get(['id', 'title']),
            'availability_schedules' => DB::table('availability_schedules')->whereNull('deleted_at')->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function resources(): array
    {
        return [
            'ranks' => [
                'table' => 'ranks',
                'label' => 'Ranks',
                'description' => 'Manage rank codes, names, ordering, and active status.',
                'order_by' => 'sort_order',
                'search' => ['code', 'name'],
                'columns' => ['code', 'name', 'sort_order', 'is_active'],
                'fields' => [
                    ['name' => 'code', 'label' => 'Code', 'type' => 'text', 'rules' => ['required', 'string', 'max:20'], 'unique' => true],
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'teams' => [
                'table' => 'teams',
                'label' => 'Teams',
                'description' => 'Manage team hierarchy, owners, leaders, and status.',
                'order_by' => 'name',
                'search' => ['name', 'description'],
                'columns' => ['name', 'owner_id', 'leader_id', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'owner_id', 'label' => 'Owner', 'type' => 'user', 'rules' => ['nullable', 'integer', 'exists:users,id']],
                    ['name' => 'leader_id', 'label' => 'Leader', 'type' => 'user', 'rules' => ['nullable', 'integer', 'exists:users,id']],
                    ['name' => 'parent_id', 'label' => 'Parent Team', 'type' => 'team', 'rules' => ['nullable', 'integer', 'exists:teams,id']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'onboarding-steps' => $this->stepResource('onboarding_steps', 'Onboarding Steps', 'Manage onboarding checklist steps.', true),
            'licensing-steps' => $this->stepResource('licensing_steps', 'Licensing Steps', 'Manage licensing checklist steps.'),
            'training-categories' => [
                'table' => 'training_categories',
                'label' => 'Training Categories',
                'description' => 'Manage training category names, slugs, and ordering.',
                'order_by' => 'sort_order',
                'search' => ['name', 'slug', 'description'],
                'columns' => ['name', 'slug', 'sort_order'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255'], 'unique' => true],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                ],
            ],
            'training-modules' => [
                'table' => 'training_modules',
                'label' => 'Training Modules',
                'description' => 'Manage training modules under each training category.',
                'order_by' => 'sort_order',
                'search' => ['title', 'slug', 'description'],
                'columns' => ['title', 'slug', 'sort_order', 'is_published'],
                'fields' => [
                    ['name' => 'training_category_id', 'label' => 'Training Category', 'type' => 'training_category', 'rules' => ['required', 'integer', 'exists:training_categories,id']],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255'], 'unique' => true],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    ['name' => 'is_published', 'label' => 'Published', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'training-lessons' => [
                'table' => 'training_lessons',
                'label' => 'Training Lessons',
                'description' => 'Manage lesson content and video links inside training modules.',
                'order_by' => 'sort_order',
                'search' => ['title', 'content', 'video_url'],
                'columns' => ['title', 'video_url', 'sort_order'],
                'fields' => [
                    ['name' => 'training_module_id', 'label' => 'Training Module', 'type' => 'training_module', 'rules' => ['required', 'integer', 'exists:training_modules,id']],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'content', 'label' => 'Content', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'video_url', 'label' => 'Video URL', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                ],
            ],
            'assessments' => [
                'table' => 'assessments',
                'label' => 'Assessments',
                'description' => 'Manage assessment definitions, passing scores, and publication status.',
                'order_by' => 'title',
                'search' => ['title', 'description'],
                'columns' => ['title', 'passing_score', 'is_published'],
                'fields' => [
                    ['name' => 'training_module_id', 'label' => 'Training Module', 'type' => 'training_module_optional', 'rules' => ['nullable', 'integer', 'exists:training_modules,id']],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'passing_score', 'label' => 'Passing Score', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0', 'max:100']],
                    ['name' => 'is_published', 'label' => 'Published', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'questions' => [
                'table' => 'questions',
                'label' => 'Questions',
                'description' => 'Manage assessment questions and question type.',
                'order_by' => 'sort_order',
                'search' => ['question', 'type'],
                'columns' => ['question', 'type', 'sort_order'],
                'fields' => [
                    ['name' => 'assessment_id', 'label' => 'Assessment', 'type' => 'assessment', 'rules' => ['required', 'integer', 'exists:assessments,id']],
                    ['name' => 'question', 'label' => 'Question', 'type' => 'textarea', 'rules' => ['required', 'string']],
                    ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => ['multiple_choice' => 'Multiple Choice', 'true_false' => 'True / False', 'short_answer' => 'Short Answer'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                ],
            ],
            'answers' => [
                'table' => 'answers',
                'label' => 'Answers',
                'description' => 'Manage possible answers and correct answer flags.',
                'order_by' => 'id',
                'search' => ['answer'],
                'columns' => ['answer', 'is_correct'],
                'fields' => [
                    ['name' => 'question_id', 'label' => 'Question', 'type' => 'question', 'rules' => ['required', 'integer', 'exists:questions,id']],
                    ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea', 'rules' => ['required', 'string']],
                    ['name' => 'is_correct', 'label' => 'Correct Answer', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'rank-requirements' => [
                'table' => 'rank_requirements',
                'label' => 'Rank Requirements',
                'description' => 'Manage advancement requirements tied to each rank.',
                'order_by' => 'sort_order',
                'search' => ['title', 'description'],
                'columns' => ['title', 'sort_order'],
                'fields' => [
                    ['name' => 'rank_id', 'label' => 'Rank', 'type' => 'rank', 'rules' => ['required', 'integer', 'exists:ranks,id']],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                ],
            ],
            'resources' => [
                'table' => 'resources',
                'label' => 'Resources',
                'description' => 'Manage resource links, files, and published status.',
                'uses_creator' => true,
                'order_by' => 'title',
                'search' => ['title', 'description', 'type', 'url'],
                'columns' => ['title', 'type', 'is_published'],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => ['link' => 'Link', 'file' => 'File', 'video' => 'Video', 'document' => 'Document'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'url', 'label' => 'URL', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                    ['name' => 'file_path', 'label' => 'File Path', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                    ['name' => 'is_published', 'label' => 'Published', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'events' => [
                'table' => 'events',
                'label' => 'Events',
                'description' => 'Manage calendar events, locations, and times.',
                'uses_creator' => true,
                'order_by' => 'starts_at',
                'search' => ['title', 'description', 'location'],
                'columns' => ['title', 'location', 'starts_at', 'ends_at'],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                    ['name' => 'starts_at', 'label' => 'Starts At', 'type' => 'datetime-local', 'rules' => ['required', 'date']],
                    ['name' => 'ends_at', 'label' => 'Ends At', 'type' => 'datetime-local', 'rules' => ['nullable', 'date', 'after_or_equal:starts_at']],
                ],
            ],
            'calendar-categories' => [
                'table' => 'calendar_categories',
                'label' => 'Calendar Categories',
                'description' => 'Manage visible calendar groups such as Team, Training, Licensing, Prospects, FAP, CFM, and Organization.',
                'order_by' => 'sort_order',
                'search' => ['name', 'slug', 'icon'],
                'columns' => ['name', 'slug', 'color', 'sort_order', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'rules' => ['required', 'string', 'max:255'], 'unique' => true],
                    ['name' => 'color', 'label' => 'Color', 'type' => 'text', 'rules' => ['required', 'string', 'max:20']],
                    ['name' => 'icon', 'label' => 'Icon', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:100']],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'calendar-event-types' => [
                'table' => 'calendar_event_types',
                'label' => 'Calendar Event Types',
                'description' => 'Manage reusable event types and colors for calendar workflows.',
                'order_by' => 'sort_order',
                'search' => ['name', 'slug', 'icon'],
                'columns' => ['name', 'slug', 'calendar_category_id', 'color', 'is_active'],
                'fields' => [
                    ['name' => 'calendar_category_id', 'label' => 'Calendar Category', 'type' => 'calendar_category', 'rules' => ['nullable', 'integer', 'exists:calendar_categories,id']],
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'rules' => ['required', 'string', 'max:255'], 'unique' => true],
                    ['name' => 'color', 'label' => 'Color', 'type' => 'text', 'rules' => ['required', 'string', 'max:20']],
                    ['name' => 'icon', 'label' => 'Icon', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:100']],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'calendar-events' => [
                'table' => 'calendar_events',
                'label' => 'Calendar Events',
                'description' => 'Manage module calendar events and their schedule, visibility, organizer, and status.',
                'order_by' => 'starts_at',
                'search' => ['title', 'description', 'location', 'meeting_link'],
                'columns' => ['title', 'organizer_id', 'starts_at', 'status', 'visibility'],
                'fields' => [
                    ['name' => 'organizer_id', 'label' => 'Organizer', 'type' => 'user', 'rules' => ['required', 'integer', 'exists:users,id']],
                    ['name' => 'calendar_event_type_id', 'label' => 'Event Type', 'type' => 'calendar_event_type', 'rules' => ['nullable', 'integer', 'exists:calendar_event_types,id']],
                    ['name' => 'calendar_category_id', 'label' => 'Calendar Category', 'type' => 'calendar_category', 'rules' => ['nullable', 'integer', 'exists:calendar_categories,id']],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'starts_at', 'label' => 'Starts At', 'type' => 'datetime-local', 'rules' => ['required', 'date']],
                    ['name' => 'ends_at', 'label' => 'Ends At', 'type' => 'datetime-local', 'rules' => ['nullable', 'date', 'after_or_equal:starts_at']],
                    ['name' => 'timezone', 'label' => 'Timezone', 'type' => 'text', 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                    ['name' => 'meeting_link', 'label' => 'Meeting Link', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                    ['name' => 'visibility', 'label' => 'Visibility', 'type' => 'select', 'options' => ['private' => 'Private', 'shared_team' => 'Shared Team', 'shared_downline' => 'Shared Downline', 'public_organization' => 'Public Organization'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'draft' => 'Draft'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'color', 'label' => 'Color', 'type' => 'text', 'rules' => ['required', 'string', 'max:20']],
                    ['name' => 'is_all_day', 'label' => 'All Day', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                    ['name' => 'is_recurring', 'label' => 'Recurring', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                    ['name' => 'recurrence_rule', 'label' => 'Recurrence Rule', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                    ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                ],
            ],
            'booking-event-types' => [
                'table' => 'booking_event_types',
                'label' => 'Booking Event Types',
                'description' => 'Manage CFM booking event types, duration, buffers, approval behavior, location, and link rules.',
                'order_by' => 'title',
                'search' => ['title', 'slug', 'description', 'event_category'],
                'columns' => ['title', 'owner_id', 'duration_minutes', 'approval_required', 'is_active'],
                'fields' => [
                    ['name' => 'owner_id', 'label' => 'Owner / CFM', 'type' => 'user', 'rules' => ['required', 'integer', 'exists:users,id']],
                    ['name' => 'calendar_category_id', 'label' => 'Calendar Category', 'type' => 'calendar_category', 'rules' => ['nullable', 'integer', 'exists:calendar_categories,id']],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'duration_minutes', 'label' => 'Duration Minutes', 'type' => 'number', 'rules' => ['required', 'integer', 'min:5', 'max:480']],
                    ['name' => 'event_category', 'label' => 'Event Category', 'type' => 'select', 'options' => ['mentor_session' => 'Mentor Session', 'field_apprenticeship' => 'Field Apprenticeship', 'prospect_support' => 'Prospect Support', 'licensing' => 'Licensing', 'rank_coaching' => 'Rank Coaching'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'location_type', 'label' => 'Location Type', 'type' => 'select', 'options' => ['zoom' => 'Zoom', 'phone' => 'Phone', 'in_person' => 'In Person', 'custom' => 'Custom'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'meeting_link', 'label' => 'Meeting Link', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                    ['name' => 'approval_required', 'label' => 'Approval Required', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                    ['name' => 'visibility', 'label' => 'Visibility', 'type' => 'select', 'options' => ['assigned_apprentices' => 'Assigned Apprentices', 'team' => 'Team', 'private_invite' => 'Private Invite', 'public' => 'Public'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'color', 'label' => 'Color', 'type' => 'text', 'rules' => ['required', 'string', 'max:20']],
                    ['name' => 'buffer_before_minutes', 'label' => 'Buffer Before', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0', 'max:240']],
                    ['name' => 'buffer_after_minutes', 'label' => 'Buffer After', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0', 'max:240']],
                    ['name' => 'minimum_notice_minutes', 'label' => 'Minimum Notice Minutes', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    ['name' => 'maximum_booking_days_ahead', 'label' => 'Maximum Booking Days Ahead', 'type' => 'number', 'rules' => ['required', 'integer', 'min:1', 'max:365']],
                    ['name' => 'daily_booking_limit', 'label' => 'Daily Booking Limit', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:1', 'max:50']],
                    ['name' => 'weekly_booking_limit', 'label' => 'Weekly Booking Limit', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:1', 'max:200']],
                    ['name' => 'confirmation_message', 'label' => 'Confirmation Message', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'cancellation_policy', 'label' => 'Cancellation Policy', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                ],
            ],
            'booking-links' => [
                'table' => 'booking_links',
                'label' => 'Booking Links',
                'description' => 'Manage personal, event-type, apprentice-specific, team, private, one-time, and expiring booking links.',
                'order_by' => 'name',
                'search' => ['name', 'slug', 'token', 'link_type'],
                'columns' => ['name', 'owner_id', 'link_type', 'visibility', 'is_active'],
                'fields' => [
                    ['name' => 'owner_id', 'label' => 'Owner / CFM', 'type' => 'user', 'rules' => ['required', 'integer', 'exists:users,id']],
                    ['name' => 'booking_event_type_id', 'label' => 'Booking Event Type', 'type' => 'booking_event_type', 'rules' => ['nullable', 'integer', 'exists:booking_event_types,id']],
                    ['name' => 'availability_schedule_id', 'label' => 'Availability Schedule', 'type' => 'availability_schedule', 'rules' => ['nullable', 'integer', 'exists:availability_schedules,id']],
                    ['name' => 'apprentice_id', 'label' => 'Apprentice', 'type' => 'user', 'rules' => ['nullable', 'integer', 'exists:users,id']],
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                    ['name' => 'token', 'label' => 'Token', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'link_type', 'label' => 'Link Type', 'type' => 'select', 'options' => ['personal' => 'Personal CFM Page', 'event_type' => 'Event Type', 'apprentice' => 'Apprentice Specific', 'team' => 'Team', 'private_invite' => 'Private Invite', 'one_time' => 'One Time'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'visibility', 'label' => 'Visibility', 'type' => 'select', 'options' => ['public' => 'Public', 'private' => 'Private', 'invite_only' => 'Invite Only', 'team' => 'Team'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                    ['name' => 'is_one_time', 'label' => 'One Time', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                    ['name' => 'expires_at', 'label' => 'Expires At', 'type' => 'datetime-local', 'rules' => ['nullable', 'date']],
                    ['name' => 'max_uses', 'label' => 'Max Uses', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:1']],
                ],
            ],
            'bookings' => [
                'table' => 'bookings',
                'label' => 'Bookings',
                'description' => 'Review mentor session bookings, requests, approvals, cancellations, and connected calendar events.',
                'order_by' => 'starts_at',
                'search' => ['status', 'reason', 'topics', 'meeting_link'],
                'columns' => ['booking_event_type_id', 'cfm_id', 'trainee_id', 'starts_at', 'status'],
                'fields' => [
                    ['name' => 'booking_event_type_id', 'label' => 'Booking Event Type', 'type' => 'booking_event_type', 'rules' => ['required', 'integer', 'exists:booking_event_types,id']],
                    ['name' => 'cfm_id', 'label' => 'CFM', 'type' => 'user', 'rules' => ['required', 'integer', 'exists:users,id']],
                    ['name' => 'trainee_id', 'label' => 'Trainee', 'type' => 'user', 'rules' => ['nullable', 'integer', 'exists:users,id']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'pending_approval' => 'Pending Approval', 'confirmed' => 'Confirmed', 'declined' => 'Declined', 'cancelled' => 'Cancelled', 'rescheduled' => 'Rescheduled', 'completed' => 'Completed', 'no_show' => 'No Show', 'expired' => 'Expired'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'starts_at', 'label' => 'Starts At', 'type' => 'datetime-local', 'rules' => ['required', 'date']],
                    ['name' => 'ends_at', 'label' => 'Ends At', 'type' => 'datetime-local', 'rules' => ['required', 'date', 'after:starts_at']],
                    ['name' => 'timezone', 'label' => 'Timezone', 'type' => 'text', 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'location_type', 'label' => 'Location Type', 'type' => 'select', 'options' => ['zoom' => 'Zoom', 'phone' => 'Phone', 'in_person' => 'In Person', 'custom' => 'Custom'], 'rules' => ['required', 'string', 'max:100']],
                    ['name' => 'meeting_link', 'label' => 'Meeting Link', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                    ['name' => 'reason', 'label' => 'Reason', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'topics', 'label' => 'Topics', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'internal_notes', 'label' => 'Internal Notes', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                ],
            ],
            'announcements' => [
                'table' => 'announcements',
                'label' => 'Announcements',
                'description' => 'Manage team announcements and publish timing.',
                'uses_creator' => true,
                'order_by' => 'title',
                'search' => ['title', 'body'],
                'columns' => ['title', 'published_at'],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'body', 'label' => 'Body', 'type' => 'textarea', 'rules' => ['required', 'string']],
                    ['name' => 'published_at', 'label' => 'Published At', 'type' => 'datetime-local', 'rules' => ['nullable', 'date']],
                ],
            ],
            'badges' => [
                'table' => 'badges',
                'label' => 'Badges',
                'description' => 'Manage recognition badges and icon labels.',
                'order_by' => 'name',
                'search' => ['name', 'slug', 'description'],
                'columns' => ['name', 'slug', 'icon'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255'], 'unique' => true],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'icon', 'label' => 'Icon', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                ],
            ],
            'email-templates' => [
                'table' => 'email_templates',
                'label' => 'Email Templates',
                'description' => 'Manage database-backed email copy and activation.',
                'order_by' => 'name',
                'search' => ['key', 'name', 'subject'],
                'columns' => ['key', 'name', 'subject', 'is_active'],
                'fields' => [
                    ['name' => 'key', 'label' => 'Key', 'type' => 'text', 'rules' => ['required', 'string', 'max:255'], 'unique' => true],
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'subject', 'label' => 'Subject', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'body', 'label' => 'Body', 'type' => 'textarea', 'rules' => ['required', 'string']],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'apprenticeship-programs' => [
                'table' => 'apprenticeship_programs',
                'label' => 'Apprenticeship Programs',
                'description' => 'Manage Field Apprenticeship Program definitions.',
                'order_by' => 'name',
                'search' => ['name', 'description'],
                'columns' => ['name', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'apprenticeship-steps' => [
                'table' => 'apprenticeship_steps',
                'label' => 'Apprenticeship Steps',
                'description' => 'Manage Field Apprenticeship Program step definitions.',
                'order_by' => 'sort_order',
                'search' => ['title', 'description'],
                'columns' => ['title', 'responsible_parties', 'notified_parties', 'is_active', 'sort_order'],
                'fields' => [
                    ['name' => 'apprenticeship_program_id', 'label' => 'Apprenticeship Program', 'type' => 'apprenticeship_program', 'rules' => ['required', 'integer', 'exists:apprenticeship_programs,id']],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    ['name' => 'responsible_parties', 'label' => 'Responsible Parties', 'type' => 'responsible_parties', 'rules' => ['required', 'string', 'max:255']],
                    ['name' => 'notified_parties', 'label' => 'Notified Parties', 'type' => 'notified_parties', 'rules' => ['nullable', 'string', 'max:255']],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
                ],
            ],
            'cfm-training-modules' => $this->stepResource('cfm_training_modules', 'CFM Training Modules', 'Manage CFM certification training modules.'),
        ];
    }

    private function stepResource(string $table, string $label, string $description, bool $countryAware = false): array
    {
        $columns = ['title', 'responsible_parties', 'notified_parties', 'is_active', 'sort_order', 'is_required'];
        $fields = [
            ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
            ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
            ['name' => 'responsible_parties', 'label' => 'Responsible Parties', 'type' => 'responsible_parties', 'rules' => ['required', 'string', 'max:255']],
            ['name' => 'notified_parties', 'label' => 'Notified Parties', 'type' => 'notified_parties', 'rules' => ['nullable', 'string', 'max:255']],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
            ['name' => 'is_required', 'label' => 'Required', 'type' => 'boolean', 'rules' => ['required', 'boolean']],
        ];

        if ($countryAware) {
            $columns[] = 'country';
            $fields[] = [
                'name' => 'country',
                'label' => 'Country Applicability',
                'type' => 'select',
                'options' => [
                    '' => 'Global - all countries',
                    'Canada' => 'Canada',
                    'United States' => 'United States',
                    'Philippines' => 'Philippines',
                    'Mexico' => 'Mexico',
                ],
                'rules' => ['nullable', 'string', 'in:Canada,United States,Philippines,Mexico'],
            ];
        }

        return [
            'table' => $table,
            'label' => $label,
            'description' => $description,
            'order_by' => 'sort_order',
            'search' => $countryAware ? ['title', 'description', 'country'] : ['title', 'description'],
            'columns' => $columns,
            'fields' => $fields,
        ];
    }
}
