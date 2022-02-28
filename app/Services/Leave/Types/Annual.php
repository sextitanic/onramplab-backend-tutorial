<?php

namespace App\Services\Leave\Types;

class Annual extends Base
{
    public function isNeedPassProbation(): bool
    {
        return true;
    }

    public function notify(Notify $notify): bool
    {
        parent::notify($notify);

        $notify->send();

        return true;
    }
}
