<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use VladimirNikotin\AaVehicleImport\Models\Dealer;
use VladimirNikotin\AaVehicleImport\Models\Category;
use VladimirNikotin\AaVehicleImport\Models\Subcategory;
use VladimirNikotin\AaVehicleImport\Models\Brand;
use VladimirNikotin\AaVehicleImport\Models\Model;
use VladimirNikotin\AaVehicleImport\Models\Generation;
use VladimirNikotin\AaVehicleImport\Models\BodyConfiguration;
use VladimirNikotin\AaVehicleImport\Models\Modification;
use VladimirNikotin\AaVehicleImport\Models\Complectation;
use VladimirNikotin\AaVehicleImport\Models\Country;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');
            $table->string('uin')->nullable();
            $table->string('vin')->nullable();
            $table->foreignIdFor(Dealer::class)->nullable();
            $table->foreignIdFor(Category::class)->nullable();
            $table->foreignIdFor(Subcategory::class)->nullable();
            $table->string('type')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->foreignIdFor(Brand::class)->nullable();
            $table->foreignIdFor(Model::class)->nullable();
            $table->foreignIdFor(Generation::class)->nullable();
            $table->foreignIdFor(BodyConfiguration::class)->nullable();
            $table->foreignIdFor(Modification::class)->nullable();
            $table->foreignIdFor(Complectation::class)->nullable();
            $table->string('brandComplectationCode')->nullable();
            $table->string('engineType')->nullable();
            $table->unsignedSmallInteger('engineVolume')->nullable();
            $table->unsignedSmallInteger('enginePower')->nullable();
            $table->string('bodyNumber')->nullable();
            $table->string('bodyType')->nullable();
            $table->string('bodyDoorCount')->nullable();
            $table->string('bodyColor')->nullable();
            $table->string('bodyColorMetallic')->nullable();
            $table->string('driveType')->nullable();
            $table->string('gearboxType')->nullable();
            $table->unsignedTinyInteger('gearboxGearCount')->nullable();
            $table->string('steeringWheel')->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->string('mileageUnit')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedInteger('specialOffer')->nullable();
            $table->unsignedInteger('specialOfferPreviousPrice')->nullable();
            $table->unsignedInteger('tradeinDiscount')->nullable();
            $table->unsignedInteger('creditDiscount')->nullable();
            $table->unsignedInteger('insuranceDiscount')->nullable();
            $table->unsignedInteger('maxDiscount')->nullable();
            $table->string('availability')->nullable();
            $table->string('ptsType')->nullable();
            $table->foreignIdFor(Country::class)->nullable();
            $table->string('operatingTime')->nullable();
            $table->string('ecoClass')->nullable();
            $table->string('driveWheel')->nullable();
            $table->unsignedTinyInteger('axisCount')->nullable();
            $table->string('brakeType')->nullable();
            $table->string('cabinType')->nullable();
            $table->unsignedSmallInteger('maximumPermittedMass')->nullable();
            $table->unsignedSmallInteger('saddleHeight')->nullable();
            $table->string('cabinSuspension')->nullable();
            $table->string('chassisSuspension')->nullable();
            $table->unsignedSmallInteger('length')->nullable();
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedTinyInteger('bodyVolume')->nullable();
            $table->unsignedTinyInteger('bucketVolume')->nullable();
            $table->string('tractionClass')->nullable();
            $table->string('refrigeratorClass')->nullable();
            $table->unsignedSmallInteger('craneArrowRadius')->nullable();
            $table->unsignedSmallInteger('craneArrowLength')->nullable();
            $table->unsignedSmallInteger('craneArrowPayload')->nullable();
            $table->unsignedSmallInteger('loadHeight')->nullable();
            $table->unsignedTinyInteger('photoCount')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('ownersCount')->nullable();
            $table->string('vehicleCondition')->nullable();
            $table->string('brandColorCode')->nullable();
            $table->string('brandInteriorCode')->nullable();
            $table->string('certificationProgram')->nullable();
            $table->string('acquisitionSource')->nullable();
            $table->date('acquisitionDate')->nullable();
            $table->date('manufactureDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
