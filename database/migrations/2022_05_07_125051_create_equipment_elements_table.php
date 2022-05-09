<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\EquipmentGroup;

class CreateEquipmentElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_elements', function (Blueprint $table) {
            $table->unsignedSmallInteger('id');
            $table->primary('id');
            $table->foreignIdFor(EquipmentGroup::class);
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_elements');
    }
}
