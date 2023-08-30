<?php

namespace App\Enums;

enum CompanyTypeEnum: string
{
    case PUBLIC='public';
    case JOINT_STOCK='joint_stock';
    case LTD='ltd';
    case COOP='coop';
    case SOLIDARITY='solidarity';
    case INSTITUTION='institution';

}
