<?php

namespace App\Livewire\CfmEffectiveness;

use App\Models\CfmEffectiveness\CfmReview;
use App\Services\CfmEffectiveness\CfmEffectivenessFeedbackService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CfmReviewCenter extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->can('view own mentor feedback requests'), 403);
    }

    public function render(CfmEffectivenessFeedbackService $feedback): View
    {
        return view('livewire.cfm-effectiveness.review-center', [
            'reviews' => $feedback->pendingReviewsFor(auth()->user()),
        ]);
    }
}
