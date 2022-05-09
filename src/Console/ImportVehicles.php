<?php

namespace VladimirNikotin\AaVehicleImport\Console;

use Exception;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use VladimirNikotin\AaVehicleImport\Models\Vehicle;
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
use VladimirNikotin\AaVehicleImport\Models\Equipment;
use VladimirNikotin\AaVehicleImport\Models\EquipmentGroup;
use VladimirNikotin\AaVehicleImport\Models\EquipmentElement;
use VladimirNikotin\AaVehicleImport\Models\PromoFeature;

class ImportVehicles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:import {uri?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import vehicles from XML';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $vehicleIds;
    protected $dealerIds;
    protected $categoryIds;
    protected $subcategoryIds;
    protected $brandIds;
    protected $modelIds;
    protected $generationIds;
    protected $bodyConfigurationIds;
    protected $modificationIds;
    protected $complectationIds;
    protected $countryIds;
    protected $groupIds;
    protected $elementIds;

    public function handle()
    {
        $uri = $this->argument('uri');
        if (empty($uri)) {
            $uri = config('aavehiclesimport.default_xml_path');
        }

        try {
            $xml = simplexml_load_file($uri);
        } catch (Exception $e) {
            $this->error("Не удалось открыть файл $uri\n" . $e->getMessage());

            return 1;
        }

        if ($xml === false) {
            $this->error("Не удалось открыть файл $uri.");

            return 1;
        }

        if ($xml->getName() !== 'vehicles') {
            $this->error('Корневой элемент XML файла должен быть vehicles, а не ' . $xml->getName());

            return 2;
        }

        $this->vehicleIds = DB::table('vehicles')->select('id')->get()->keyBy('id');
        $this->dealerIds = DB::table('dealers')->select('id')->get();
        $this->categoryIds = DB::table('categories')->select('id')->get();
        $this->subcategoryIds = DB::table('subcategories')->select('id')->get();
        $this->brandIds = DB::table('brands')->select('id')->get();
        $this->modelIds = DB::table('models')->select('id')->get();
        $this->generationIds = DB::table('generations')->select('id')->get();
        $this->bodyConfigurationIds = DB::table('body_configurations')->select('id')->get();
        $this->modificationIds = DB::table('modifications')->select('id')->get();
        $this->complectationIds = DB::table('complectations')->select('id')->get();
        $this->countryIds = DB::table('countries')->select('id')->get();
        $this->groupIds = DB::table('equipment_groups')->select('id')->get();
        $this->elementIds = DB::table('equipment_elements')->select('id')->get();

        $num = 0;
        $processed = 0;
        foreach ($xml as $vehicleXml) {
            $num += 1;
            if ($vehicleXml->getName() !== 'vehicle') {
                $this->warn("Некорректный элемент №$num.");
                continue;
            }
            if (empty($vehicleXml->id)) {
                $this->warn("В элементе №$num отсутствует id.");
                continue;
            }

            $vehicleId = intval($vehicleXml->id);
            if (empty($this->vehicleIds->pull($vehicleId))) {
                $vehicle = new Vehicle();
                $equipment = null;
            } else {
                $vehicle = Vehicle::find($vehicleId);
                $equipment = $vehicle->equipment()->get()->keyBy('equipment_element_id');
            }

            $id = $this->processDealer($vehicleXml->dealer);
            if ($id === false) {
                $this->warn("Элемент №$num (id $vehicleId) не обработан: у дилера не указан id " . strval($vehicleXml->dealer));
                continue;
            }
            $vehicle->dealer_id = $id;
            unset($vehicleXml->dealer[0]);

            $id = $this->processCategory($vehicleXml->category);
            if ($id === false) {
                $this->warn("Элемент №$num (id $vehicleId) не обработан: у категории не указан id " . strval($vehicleXml->dealer));
                continue;
            }
            $vehicle->category_id = $id;
            unset($vehicleXml->category[0]);

            $id = $this->processSubcategory($vehicleXml->subcategory);
            if ($id !== false) {
                $vehicle->subcategory_id = $id;
            }
            unset($vehicleXml->subcategory[0]);

            $id = $this->processBrand($vehicleXml->brand);
            if ($id === false) {
                $this->warn("Элемент №$num (id $vehicleId) не обработан: у производителя не указан id " . strval($vehicleXml->brand));
                continue;
            }
            $vehicle->brand_id = $id;
            unset($vehicleXml->brand[0]);

            $id = $this->processModel($vehicleXml->model);
            if ($id === false) {
                $this->warn("Элемент №$num (id $vehicleId) не обработан: у модели не указан id " . strval($vehicleXml->model));
                continue;
            }
            $vehicle->model_id = $id;
            unset($vehicleXml->model[0]);

            $id = $this->processGeneration($vehicleXml->generation);
            if ($id !== false) {
                $vehicle->generation_id = $id;
            }
            unset($vehicleXml->generation[0]);

            $id = $this->processBodyConfiguration($vehicleXml->bodyConfiguration);
            if ($id !== false) {
                $vehicle->body_configuration_id = $id;
            }
            unset($vehicleXml->bodyConfiguration[0]);

            $id = $this->processModification($vehicleXml->modification);
            if ($id !== false) {
                $vehicle->modification_id = $id;
            }
            unset($vehicleXml->modification[0]);

            $id = $this->processComplectation($vehicleXml->complectation);
            if ($id !== false) {
                $vehicle->complectation_id = $id;
            }
            unset($vehicleXml->complectation[0]);

            $id = $this->processCountry($vehicleXml->country);
            if ($id !== false) {
                $vehicle->country_id = $id;
            }
            unset($vehicleXml->country[0]);

            $this->processPromoFeatures($vehicleXml->promoFeatures, $vehicleId);
            unset($vehicleXml->promoFeatures[0]);

            $this->processEquipment($vehicleXml->equipment, $vehicleId, $equipment);
            unset($vehicleXml->equipment[0]);

            foreach($vehicleXml as $nodeName => $nodeValue) {
                $vehicle->$nodeName = empty($nodeValue) ? null : strval($nodeValue);
            }

            $vehicle->save();

            $processed += 1;
        }

        $this->removeOld();
        $this->info("Файл обработан: $processed из $num записей.");

        return 0;
    }

    protected function processDealer($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->dealerIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new Dealer();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->dealerIds->put($id, $model);

        return $id;
    }

    protected function processCategory($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->categoryIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new Category();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->categoryIds->put($id, $model);

        return $id;
    }

    protected function processSubcategory($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->subcategoryIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new Subcategory();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->subcategoryIds->put($id, $model);

        return $id;
    }

    protected function processBrand($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->brandIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new Brand();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->brandIds->put($id, $model);

        return $id;
    }

    protected function processModel($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->modelIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new Model();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->modelIds->put($id, $model);

        return $id;
    }

    protected function processGeneration($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->generationIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new Generation();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->generationIds->put($id, $model);

        return $id;
    }

    protected function processBodyConfiguration($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->bodyConfigurationIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new BodyConfiguration();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->bodyConfigurationIds->put($id, $model);

        return $id;
    }

    protected function processModification($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->modificationIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new Modification();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->modificationIds->put($id, $model);

        return $id;
    }

    protected function processComplectation($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->complectationIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new Complectation();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->complectationIds->put($id, $model);

        return $id;
    }

    protected function processCountry($node)
    {
        if (empty($node)) {
            return false;
        }

        $id = intval($node->attributes()['id']);
        if (empty($id)) {
            return false;
        }

        if ($this->countryIds->containsStrict('id', $id)) {
            return $id;
        }

        $model = new Country();
        $model->id = $id;
        $model->name = strval($node);
        $model->save();

        $this->countryIds->put($id, $model);

        return $id;
    }

    protected function processEquipment($equipment, $vehicleId, $equipmentStored)
    {
        if (empty($equipment)) {
            Equipment::where('vehicle_id', $vehicleId)->delete();
            return;
        }

        $elementsInput = [];

        foreach ($equipment->group as $egroup) {
            $groupId = intval($egroup->attributes()->id);

            if (!$this->groupIds->containsStrict('id', $groupId)) {
                $group = new EquipmentGroup();
                $group->id = $groupId;
                $group->name = strval($egroup->attributes()->name);
                $group->save();

                $this->groupIds->put($groupId, $group);
            }

            foreach ($egroup->element as $element) {
                $elementId = intval($element->attributes()->id);
                $elementsInput[] = $elementId;

                if (!$this->elementIds->containsStrict('id', $elementId)) {
                    $elementNew = new EquipmentElement();
                    $elementNew->id = $elementId;
                    $elementNew->name = strval($element);
                    $elementNew->equipment_group_id = $groupId;
                    $elementNew->save();

                    $this->elementIds->put($elementId, $elementNew);
                }

                if (
                    empty($equipmentStored) ||
                    !$equipmentStored->contains('equipment_element_id', '=', $elementId)
                ) {
                    $equipmentNew = new Equipment();
                    $equipmentNew->vehicle_id = $vehicleId;
                    $equipmentNew->equipment_element_id = $elementId;
                    $equipmentNew->save();
                }
            }
        }

        Equipment::where('vehicle_id', $vehicleId)
            ->whereNotIn('equipment_element_id', $elementsInput)
            ->delete();
    }

    protected function processPromoFeatures($promoFeatures, $vehicleId)
    {
        PromoFeature::where('vehicle_id', $vehicleId)->delete();

        if (empty($promoFeatures)) {
            return;
        }

        foreach ($promoFeatures->promoFeature as $promoFeatureXml)
        {
            $promoFeature = new PromoFeature();
            $promoFeature->vehicle_id = $vehicleId;
            $promoFeature->text = strval($promoFeatureXml);
            $promoFeature->save();
        }
    }

    protected function removeOld()
    {
        $vehicleIds = array_keys($this->vehicleIds->toArray());

        PromoFeature::whereIn('vehicle_id', $vehicleIds)->delete();
        Equipment::whereIn('vehicle_id', $vehicleIds)->delete();
        Vehicle::whereIn('id', $vehicleIds)->delete();
    }
}
