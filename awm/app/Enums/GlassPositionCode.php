<?php

namespace App\Enums;

enum GlassPositionCode: string
{
    case LFW = 'LFW';
    case LFD = 'LFD';
    case RFW = 'RFW';
    case RFD = 'RFD';
    case FDR = 'FDR';
    case FDL = 'FDL';
    case RDR = 'RDR';
    case RDL = 'RDL';
    case RW = 'RW';
    case RR = 'RR';
    case QTG = 'QTG';
    case QTS = 'QTS';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::LFW => 'Left Front Windshield',
            self::LFD => 'Left Front Door',
            self::RFW => 'Right Front Windshield',
            self::RFD => 'Right Front Door',
            self::FDR => 'Front Door Right',
            self::FDL => 'Front Door Left',
            self::RDR => 'Rear Door Right',
            self::RDL => 'Rear Door Left',
            self::RW => 'Rear Windshield',
            self::RR => 'Rear Roof',
            self::QTG => 'Quarter Glass',
            self::QTS => 'Quarter Side',
            self::OTHER => 'Other',
        };
    }
}
