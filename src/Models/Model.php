<?php

namespace VladimirNikotin\AaVehicleImport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as ORModel;

class Model extends ORModel
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = false;
}
