<?php

namespace App\Events;

use App\Models\BpEmployee;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicantHired
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public BpEmployee $bpEmployee,
        public User $hiredBy,
    ) {}
}
