<?php

namespace App\Services\Leave\Types;

class Sick extends Base
{
    public function isNeedPassProbation()
    {
        return true;
    }

    public function notify()
    {
        parent::notify();

        // send a email to managers

        return true;
    }
}
