<?php

namespace VladimirNikotin\AaVehicleImport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use VladimirNikotin\AaVehicleImport\Models\Equipment;

class Vehicle extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = false;

    protected $attributes = [
        'uin' => null,
        'dealer_id' => null,
        'category_id' => null,
        'subcategory_id' => null,
        'brand_id' => null,
        'model_id' => null,
        'generation_id' => null,
        'body_configuration_id' => null,
        'modification_id' => null,
        'complectation_id' => null,
        'country_id' => null,
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}
