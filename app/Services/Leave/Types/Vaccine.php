<?php

namespace App\Services\Leave\Types;

class Vaccine extends Base
{
    public function notify(Notify $notify): bool
    {
        parent::notify($notify);

        $notify->send();

        return true;
    }
}
