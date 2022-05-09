<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use VladimirNikotin\AaVehicleImport\Models\Vehicle;
use VladimirNikotin\AaVehicleImport\Models\EquipmentElement;

class CreateEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->foreignIdFor(Vehicle::class);
            $table->index('vehicle_id');
            $table->foreignIdFor(EquipmentElement::class);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment');
    }
}
