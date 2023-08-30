<?php

namespace App\Enums;

enum UserAccessEnum:String
{
    case ADMIN = "admin";
    case USER = "user";
    case SELLER = "seller";

    public static function isAdmin(){
        return auth()->user()->access == UserAccessEnum::ADMIN->value;
    }

    public static function isUser(){
        return auth()->user()->access == UserAccessEnum::USER->value;
    }

    public static function isSeller(){
        return auth()->user()->access == UserAccessEnum::SELLER->value;
    }
}
