<?php

namespace App\Services\Leave\Types;

use App\Services\Notify\Base as Notify;

abstract class Base
{
    public function isNeedPassProbation(): bool
    {
        return false;
    }

    public function notify(Notify $notify): bool
    {
        // send a email to supervisor
        $notify->send();
        return true;
    }
}
