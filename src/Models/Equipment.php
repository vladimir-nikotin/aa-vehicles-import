<?php

namespace VladimirNikotin\AaVehicleImport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use VladimirNikotin\AaVehicleImport\Models\EquipmentElement;

class Equipment extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = false;

    public function element()
    {
        return $this->hasOne(EquipmentElement::class);
    }
}
