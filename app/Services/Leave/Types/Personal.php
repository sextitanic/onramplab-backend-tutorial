<?php

namespace App\Services\Leave\Types;

use App\Contracts\Leave\NotFullPaid as NotFullPaidContract;

class Personal extends Base implements NotFullPaidContract
{
    public function subPercentFromSalary(): float
    {
        return 0.5;
    }
}
