<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rank_id',
        'team_id',
        'sponsor_id',
        'mentor_id',
        'is_active',
        'joined_at',
        'last_login_at',
        'last_login_ip',
        'is_online',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'joined_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_online' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            $relatedRecords = [
                $user->profile(),
                $user->registrationInvitations(),
                $user->mentorAssignments(),
                $user->apprenticeshipAssignments(),
            ];

            foreach ($relatedRecords as $relation) {
                $user->isForceDeleting()
                    ? $relation->withTrashed()->forceDelete()
                    : $relation->delete();
            }
        });
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function sponsoredMembers(): HasMany
    {
        return $this->hasMany(User::class, 'sponsor_id');
    }

    public function ancestorPaths(): HasMany
    {
        return $this->hasMany(UserHierarchyPath::class, 'descendant_id');
    }

    public function descendantPaths(): HasMany
    {
        return $this->hasMany(UserHierarchyPath::class, 'ancestor_id');
    }

    public function sponsorRelationships(): HasMany
    {
        return $this->hasMany(SponsorRelationship::class, 'sponsor_id');
    }

    public function sponsoredRelationship(): HasOne
    {
        return $this->hasOne(SponsorRelationship::class, 'member_id')->where('status', 'active');
    }

    public function teamVisibilityGrants(): HasMany
    {
        return $this->hasMany(TeamVisibilityPermission::class, 'viewer_id');
    }

    public function teamVisibilityGrantedToOthers(): HasMany
    {
        return $this->hasMany(TeamVisibilityPermission::class, 'visible_user_id');
    }

    public function registrationInvitations(): HasMany
    {
        return $this->hasMany(RegistrationInvitation::class, 'sponsor_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function mentorAssignments(): HasMany
    {
        return $this->hasMany(MentorAssignment::class, 'mentor_id');
    }

    public function apprenticeshipAssignments(): HasMany
    {
        return $this->hasMany(MentorAssignment::class, 'apprentice_id');
    }

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class, 'owner_id');
    }

    public function prospectSharesGranted(): HasMany
    {
        return $this->hasMany(ProspectShare::class, 'granted_by');
    }

    public function prospectSharesReceived(): HasMany
    {
        return $this->hasMany(ProspectShare::class, 'shared_with');
    }

    public function bookingEventTypes(): HasMany
    {
        return $this->hasMany(BookingEventType::class, 'owner_id');
    }

    public function availabilitySchedules(): HasMany
    {
        return $this->hasMany(AvailabilitySchedule::class);
    }

    public function bookingLinks(): HasMany
    {
        return $this->hasMany(BookingLink::class, 'owner_id');
    }

    public function cfmBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'cfm_id');
    }

    public function traineeBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'trainee_id');
    }
}
