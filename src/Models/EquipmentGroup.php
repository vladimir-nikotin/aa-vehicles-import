<?php

namespace VladimirNikotin\AaVehicleImport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use VladimirNikotin\AaVehicleImport\Models\EquipmentElement;

class EquipmentGroup extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = false;

    public function elements()
    {
        return $this->hasMany(EquipmentElement::class);
    }
}
