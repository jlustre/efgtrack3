<?php

namespace App\Livewire\CfmEffectiveness;

use App\Models\CfmEffectiveness\CfmReview;
use App\Services\CfmEffectiveness\CfmEffectivenessFeedbackService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CfmReviewForm extends Component
{
    public CfmReview $review;

    /** @var array<int, int> */
    public array $ratings = [];

    public string $helpedMost = '';

    public string $improvements = '';

    public string $comments = '';

    public string $suggestions = '';

    public function mount(CfmReview $review): void
    {
        abort_unless(auth()->user()->can('submit mentor feedback'), 403);
        abort_unless($review->trainee_id === auth()->id(), 403);
        abort_unless($review->isPending(), 404);

        $this->review = $review->load(['cfm.profile', 'reviewCycle']);
    }

    public function submit(CfmEffectivenessFeedbackService $feedback): void
    {
        $this->validate([
            'ratings' => ['required', 'array', 'min:1'],
            'ratings.*' => ['integer', 'min:1', 'max:5'],
        ]);

        $feedback->submitReview($this->review, auth()->user(), $this->ratings, [
            'helped_most' => $this->helpedMost,
            'improvements' => $this->improvements,
            'comments' => $this->comments,
            'suggestions' => $this->suggestions,
        ]);

        session()->flash('cfm_effectiveness_status', 'Thank you. Your anonymous feedback has been submitted.');

        $this->redirect(route('cfm.effectiveness.reviews'), navigate: true);
    }

    public function render(CfmEffectivenessFeedbackService $feedback): View
    {
        return view('livewire.cfm-effectiveness.review-form', [
            'questions' => $feedback->activeQuestions(),
        ]);
    }
}
