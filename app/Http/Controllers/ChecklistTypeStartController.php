<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ChecklistService;
use App\Services\DownlineHierarchyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChecklistTypeStartController extends Controller
{
    public function __construct(
        private readonly ChecklistService $checklists,
        private readonly DownlineHierarchyService $hierarchy,
    ) {}

    public function store(Request $request, User $user, string $typeCode): RedirectResponse
    {
        abort_unless($this->hierarchy->canViewMember($request->user(), $user), 403);
        abort_unless($this->checklists->canStartChecklistTypesFor($request->user(), $user), 403);
        abort_unless(in_array($typeCode, $this->checklists->memberFacingTypeCodes(), true), 404);

        $validated = $request->validate([
            'started_at' => ['required', 'date'],
        ]);

        try {
            $this->checklists->startChecklistType(
                $user,
                $typeCode,
                $request->user(),
                $validated['started_at'],
            );
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }

        return back()->with('status', 'checklist-type-started');
    }
}
