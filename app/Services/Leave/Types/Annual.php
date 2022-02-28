<?php

namespace App\Services\Leave\Types;

class Annual extends Base
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
