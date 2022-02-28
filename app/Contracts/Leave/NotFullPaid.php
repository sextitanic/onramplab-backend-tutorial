<?php

namespace App\Contracts\Leave;

interface NotFullPaid
{
    public function subPercentFromSalary(): float;
}
