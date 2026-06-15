<?php

namespace App\Livewire\Fna;

use App\Models\FnaClientInvite;
use App\Models\FnaRecord;
use App\Models\Prospect;
use App\Models\User;
use App\Services\Fna\FnaClientInviteRecipientService;
use App\Services\Fna\FnaClientInviteService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FnaClientInvitePanel extends Component
{
    public ?Prospect $prospect = null;

    public ?User $recipientMember = null;

    public ?FnaRecord $fna = null;

    public string $recipient_name = '';

    public string $recipient_email = '';

    public string $recipient_phone = '';

    public ?string $personal_message = null;

    public ?string $createdSecurityCode = null;

    public ?string $createdInviteUrl = null;

    public function mount(?Prospect $prospect = null, ?User $recipientMember = null, ?FnaRecord $fna = null): void
    {
        $this->prospect = $prospect;
        $this->recipientMember = $recipientMember;
        $this->fna = $fna;

        if ($prospect) {
            $this->authorize('view', $prospect);
            $this->recipient_name = $prospect->displayName();
            $this->recipient_email = (string) ($prospect->email ?? '');
            $this->recipient_phone = (string) ($prospect->phone ?? '');
        }

        if ($recipientMember) {
            abort_unless(
                app(FnaClientInviteRecipientService::class)->canInviteRecipient(auth()->user(), null, $recipientMember),
                403,
            );

            $prefill = app(FnaClientInviteRecipientService::class)->prefillFromMember($recipientMember);
            $this->recipient_name = $prefill['recipient_name'];
            $this->recipient_email = $prefill['recipient_email'];
            $this->recipient_phone = $prefill['recipient_phone'];
        }

        if ($fna) {
            $this->authorize('view', $fna);
            $this->recipient_name = $this->recipient_name ?: ($fna->client_name ?? '');
            $this->recipient_email = $this->recipient_email ?: (string) ($fna->client_email ?? '');
            $this->recipient_phone = $this->recipient_phone ?: (string) ($fna->client_phone ?? '');
        }

        $this->authorize('create', FnaClientInvite::class);
    }

    public function sendInvite(FnaClientInviteService $invites): void
    {
        $this->authorize('create', FnaClientInvite::class);

        $this->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_email' => ['nullable', 'email', 'max:255'],
            'recipient_phone' => ['nullable', 'string', 'max:60'],
            'personal_message' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($this->prospect) {
            $this->authorize('view', $this->prospect);
        }

        if ($this->recipientMember) {
            abort_unless(
                app(FnaClientInviteRecipientService::class)->canInviteRecipient(auth()->user(), null, $this->recipientMember),
                403,
            );
        }

        $result = $invites->createInvite(auth()->user(), $this->prospect, [
            'recipient_name' => $this->recipient_name,
            'recipient_email' => $this->recipient_email ?: null,
            'recipient_phone' => $this->recipient_phone ?: null,
            'personal_message' => $this->personal_message,
        ], $this->recipientMember);

        $this->createdSecurityCode = $result['security_code'];
        $this->createdInviteUrl = $result['invite']->inviteUrl();
        $this->reset('personal_message');

        $recipientLabel = $this->recipientMember ? 'member' : 'prospect';
        session()->flash('fna_invite_status', "Client portal invite created. Share the link and security code with your {$recipientLabel}.");
        $this->dispatch('fna-client-invite-created');
    }

    public function revokeInvite(string $inviteId, FnaClientInviteService $invites): void
    {
        $invite = FnaClientInvite::query()->findOrFail($inviteId);
        $this->authorize('revoke', $invite);

        $invites->revoke($invite, auth()->user());
        session()->flash('fna_invite_status', 'Invite revoked.');
    }

    public function render(): View
    {
        $query = FnaClientInvite::query()
            ->with(['fnaRecord', 'recipientUser'])
            ->where('sender_user_id', auth()->id())
            ->latest('created_at');

        if ($this->prospect) {
            $query->where('prospect_id', $this->prospect->id);
        } elseif ($this->recipientMember) {
            $query->where('recipient_user_id', $this->recipientMember->id);
        } elseif ($this->fna) {
            $query->where('fna_record_id', $this->fna->id);
        }

        return view('livewire.fna.fna-client-invite-panel', [
            'invites' => $query->limit(20)->get(),
            'canSend' => auth()->user()->can('create', FnaClientInvite::class),
            'recipientContext' => $this->recipientMember
                ? 'member'
                : ($this->prospect ? 'prospect' : 'general'),
        ]);
    }
}
