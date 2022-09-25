<?php

namespace App\Core\Invitation;

class InvitationStatus
{

    public const ON_HOLD = 0;

    public const ACCEPTED = 1;

    public const CANCELED = -1;

    public const DECLINED = 2;

    public const STATUS = [

         "Accepted" => self::ACCEPTED,
         "On hold" => self::ON_HOLD,
         "Declined" => self::DECLINED,
         "Canceled" => self::CANCELED
    ];


    public static function resolve(int $status): string
    {
    
        return array_flip(self::STATUS)[$status];

    
    }


}