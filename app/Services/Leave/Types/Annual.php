<?php

namespace App\Services\Leave\Types;

class Annual extends Base
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
