<?php

namespace App\Services\Leave\Types;

abstract class Base
{
    public function isNeedPassProbation(): bool
    {
        return false;
    }

    public function notify(): bool
    {
        // send a email to supervisor
        return true;
    }
}
