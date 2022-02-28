<?php

namespace App\Services\Leave\Types;

class Sick extends Base
{
    public function isNeedPassProbation(): bool
    {
        return true;
    }

    public function notify(): bool
    {
        parent::notify();

        // send a email to managers

        return true;
    }
}
