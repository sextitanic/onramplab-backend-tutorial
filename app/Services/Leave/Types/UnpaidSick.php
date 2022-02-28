<?php

namespace App\Services\Leave\Types;

use App\Contracts\Leave\NotFullPaid as NotFullPaidContract;

class UnpaidSick extends Base implements NotFullPaidContract
{
    public function subPercentFromSalary(): float
    {
        return 1;
    }
}
