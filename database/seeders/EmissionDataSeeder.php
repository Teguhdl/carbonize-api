<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FoodItem;
use App\Models\FuelType;
use App\Models\VehicleType;
use App\Models\TransitVehicle;

class EmissionDataSeeder extends Seeder
{
    public function run(): void
    {
        /* --------------------------------------------------------------------------
         | FOOD & PACKAGING ITEMS
         -------------------------------------------------------------------------- */

        // Fixed calculation — emission_factor diketahui secara lokal
        $foodFixed = [
            ['name' => 'Cardboard Boxes',   'emission_factor' => 0.85,  'climatiq_id' => null, 'calculation_method' => 'fixed'],
            ['name' => 'Plastic Bags/Films', 'emission_factor' => 0.2,   'climatiq_id' => null, 'calculation_method' => 'fixed'],
            ['name' => 'Plastic Bottles',    'emission_factor' => 0.083,  'climatiq_id' => null, 'calculation_method' => 'fixed'],
            ['name' => 'Tissue Paper',       'emission_factor' => 0.692,  'climatiq_id' => null, 'calculation_method' => 'fixed'],
        ];

        // Climatiq calculation — emisi dihitung via Climatiq API
        $foodClimatiq = [
            ['name' => 'Apples',                  'emission_factor' => null, 'climatiq_id' => 'arable_farming-type_apples-origin_region_global',                                                                                       'calculation_method' => 'climatiq'],
            ['name' => 'Cardboard Boxes (Climatiq)','emission_factor' => null, 'climatiq_id' => 'paper_and_cardboard-type_carton_board_box_production_with_offset_printing_market_for_carton_board_box_production_with_offset_printing', 'calculation_method' => 'climatiq'],
            ['name' => 'Cocoa Fruit',              'emission_factor' => null, 'climatiq_id' => 'agriculture_fishing_forestry-type_fish_all_species-origin_region_multi_region',                                                        'calculation_method' => 'climatiq'],
            ['name' => 'Fresh Fish',               'emission_factor' => null, 'climatiq_id' => 'agriculture_fishing_forestry-type_fish_all_species-origin_region_multi_region',                                                        'calculation_method' => 'climatiq'],
            ['name' => 'Plastic Bags (Climatiq)',  'emission_factor' => null, 'climatiq_id' => 'chemicals-type_polyethylene_linear_low_density_granulate_market_for_polyethylene_linear_low_density_granulate',                        'calculation_method' => 'climatiq'],
            ['name' => 'Plastic Bottles (Climatiq)','emission_factor'=> null, 'climatiq_id' => 'chemicals-type_polyethylene_terephthalate_granulate_bottle_grade_market_for_polyethylene_terephthalate_granulate_bottle_grade',       'calculation_method' => 'climatiq'],
            ['name' => 'Rice',                     'emission_factor' => null, 'climatiq_id' => 'arable_farming-type_apples-origin_region_global',                                                                                       'calculation_method' => 'climatiq'],
            ['name' => 'Tissue Paper (Climatiq)',  'emission_factor' => null, 'climatiq_id' => 'paper_and_cardboard-type_tissue_paper_market_for_tissue_paper',                                                                        'calculation_method' => 'climatiq'],
        ];

        foreach (array_merge($foodFixed, $foodClimatiq) as $item) {
            FoodItem::updateOrCreate(['name' => $item['name']], $item);
        }

        /* --------------------------------------------------------------------------
         | FUEL TYPES — untuk kendaraan pribadi
         -------------------------------------------------------------------------- */

        $fuels = [
            ['name' => 'Dexlite',              'emission_factor' => 2.65],
            ['name' => 'Pertalite',             'emission_factor' => 2.31],
            ['name' => 'Pertamax',              'emission_factor' => 2.31],
            ['name' => 'Pertamax Turbo',        'emission_factor' => 2.31],
            ['name' => 'Pertamina Dex',         'emission_factor' => 2.68],
            ['name' => 'Shell Super',           'emission_factor' => 2.31],
            ['name' => 'Shell V-Power',         'emission_factor' => 2.346],
            ['name' => 'Shell V-Power Diesel',  'emission_factor' => 2.68],
            ['name' => 'Shell V-Power Nitro+',  'emission_factor' => 2.346],
            ['name' => 'Solar / Bio Solar',     'emission_factor' => 2.58],
        ];

        foreach ($fuels as $fuel) {
            FuelType::updateOrCreate(['name' => $fuel['name']], $fuel);
        }

        /* --------------------------------------------------------------------------
         | VEHICLE TYPES — kendaraan pribadi + efisiensi default (km/liter)
         -------------------------------------------------------------------------- */

        $vehicles = [
            ['name' => 'City Car',          'default_efficiency' => 20],
            ['name' => 'Diesel Car',        'default_efficiency' => 15],
            ['name' => 'Hybrid Car',        'default_efficiency' => 22],
            ['name' => 'Motorcycle',        'default_efficiency' => 40],
            ['name' => 'SUV / MPV',         'default_efficiency' => 12],
            ['name' => 'Sedan / Medium Car','default_efficiency' => 14],
        ];

        foreach ($vehicles as $vehicle) {
            VehicleType::updateOrCreate(['name' => $vehicle['name']], $vehicle);
        }

        /* --------------------------------------------------------------------------
         | TRANSIT VEHICLES — kendaraan umum
         | emission_factor : kgCO2e per km per kendaraan (total)
         | avg_passengers  : rata-rata penumpang per perjalanan
         -------------------------------------------------------------------------- */

        $transitVehicles = [
            ['name' => 'City Bus',             'emission_factor' => 1.085,  'avg_passengers' => 20],
            ['name' => 'Intercity Bus',         'emission_factor' => 0.1085, 'avg_passengers' => 25],
            ['name' => 'MRT',                   'emission_factor' => 0.026,  'avg_passengers' => 300],
            ['name' => 'Minibus / Angkot',      'emission_factor' => 0.1085, 'avg_passengers' => 8],
            ['name' => 'Online Motorcycle',     'emission_factor' => 0.1824, 'avg_passengers' => 1],
            ['name' => 'Online Taxi',           'emission_factor' => 0.1669, 'avg_passengers' => 4],
        ];

        foreach ($transitVehicles as $vehicle) {
            TransitVehicle::updateOrCreate(['name' => $vehicle['name']], $vehicle);
        }

        /* --------------------------------------------------------------------------
         | SUMMARY
         -------------------------------------------------------------------------- */
        $this->command->info('');
        $this->command->info('✅ Emission data seeded successfully!');
        $this->command->info('   🍎 Food Items (Fixed)   : ' . count($foodFixed));
        $this->command->info('   🌐 Food Items (Climatiq): ' . count($foodClimatiq));
        $this->command->info('   ⛽ Fuel Types           : ' . count($fuels));
        $this->command->info('   🚗 Vehicle Types        : ' . count($vehicles));
        $this->command->info('   🚌 Transit Vehicles     : ' . count($transitVehicles));
    }
}
