<?php

namespace App\Exceptions;

use Exception;

class ApplicantAlreadyHiredException extends Exception
{
    public function __construct()
    {
        parent::__construct('This applicant has already been hired.');
    }
}
