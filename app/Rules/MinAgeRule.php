<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class MinAgeRule implements Rule
{
    protected $minAge;

    public function __construct($minAge)
    {
        $this->minAge = $minAge;
    }

    public function passes($attribute, $value)
    {
        $minDate = Carbon::now()->subYears($this->minAge)->format('Y-m-d');

        return $value <= $minDate;
    }

    public function message()
    {
        return "The driver must be at least {$this->minAge} years old.";
    }
}
