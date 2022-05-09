<?php

namespace VladimirNikotin\AaVehicleImport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use VladimirNikotin\AaVehicleImport\Models\EquipmentGroup;

class EquipmentElement extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = false;

    public function group()
    {
        return $this->belongsTo(EquipmentGroup::class);
    }
}
