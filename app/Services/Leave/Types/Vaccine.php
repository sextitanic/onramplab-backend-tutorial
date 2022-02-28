<?php

namespace App\Services\Leave\Types;

class Vaccine extends Base
{
    public function notify()
    {
        parent::notify();

        // send a email to managers

        return true;
    }
}
