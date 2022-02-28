<?php

namespace App\Services\Leave\Types;

abstract class Base
{
    public function isNeedPassProbation()
    {
        return false;
    }

    public function notify()
    {
        // send a email to supervisor
        return true;
    }
}
