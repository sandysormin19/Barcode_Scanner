<?php

namespace App\Enums;

enum StatusPengiriman: string
{
    case Pending = 'pending';
        case Packed = 'packed';
    case InTransit = 'in_transit';
        case Delivered = 'delivered';
    case Cancelled = 'cancelled';


    public function label(): string
    {
        return match($this){
            self::Pending => 'Pending',
            self::Packed => 'Packed',
            self::InTransit => 'In_transit',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeColor(): string
    {
        return match($this){
            self::Pending => 'warning',
            self::Packed => 'info',
            self::InTransit => 'primary',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this){
            self::Pending => 'fa-clock',
            self::Packed => 'fa-box',
            self::InTransit => 'fa-truck',
            self::Delivered => 'fa-check-circle',
            self::Cancelled => 'fa-times-circle',
        };
    }

    public function nextValidStatueses(): array
    {
        return match($this){
            self::Pending => [self::Packed, self::Cancelled],
            self::Packed => [self::InTransit, self::Cancelled],
            self::InTransit => [self::Delivered, self::Cancelled],
            self::Delivered => [],
            self::Cancelled => [],
        };
    }
}
