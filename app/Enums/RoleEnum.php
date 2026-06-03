<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleEnum: string
{
    case SuperAdmin = 'Super Admin';
    case Director = 'Giám đốc';
    case IT = 'IT';
    case Sales = 'Phòng Kinh doanh';
    case Consultant = 'Tư vấn';
    case Accountant = 'Kế toán';
}
