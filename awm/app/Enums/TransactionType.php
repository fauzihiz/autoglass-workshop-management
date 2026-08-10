<?php

namespace App\Enums;

enum TransactionType: string
{
    case GlassSale = 'glass_sale';
    case GlassInstallation = 'glass_installation';
    case ServiceOnly = 'service_only';

    public function label(): string
    {
        return match ($this) {
            self::GlassSale => 'Glass Sale',
            self::GlassInstallation => 'Glass Installation',
            self::ServiceOnly => 'Service Only',
        };
    }
}
