<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmissionFactorCategory;
use App\Models\EmissionFactorItem;

class EmissionFactorSeeder extends Seeder
{
    /**
     * Seed emission factors data from Firebase export
     * Total: 40 items across 6 data collections
     * 
     * Database Schema:
     * - emission_factor_categories: id, category_name, timestamps
     * - emission_factor_items: id, factor_category_id, name, value, climatiq_id, timestamps
     */
    public function run(): void
    {
        // ============================================
        // STEP 1: CREATE CATEGORIES (4 categories)
        // ============================================
        $foodCategory = EmissionFactorCategory::firstOrCreate(
            ['category_name' => 'Food & Packaging']
        );

        $fuelCategory = EmissionFactorCategory::firstOrCreate(
            ['category_name' => 'Fuel Consumption']
        );

        $publicTransportCategory = EmissionFactorCategory::firstOrCreate(
            ['category_name' => 'Public Transport']
        );

        $vehicleEfficiencyCategory = EmissionFactorCategory::firstOrCreate(
            ['category_name' => 'Vehicle Efficiency']
        );

        // ============================================
        // STEP 2: FOOD & PACKAGING - FIXED (4 items)
        // ============================================
        // Formula: emissions = quantity * value
        $foodFixedItems = [
            ['factor_category_id' => $foodCategory->id, 'name' => 'Cardboard Boxes', 'value' => '0.85', 'climatiq_id' => null],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Plastic Bags/Films', 'value' => '0.2', 'climatiq_id' => null],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Plastic Bottles', 'value' => '0.083', 'climatiq_id' => null],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Tissue Paper', 'value' => '0.692', 'climatiq_id' => null],
        ];

        // ============================================
        // STEP 3: FOOD & PACKAGING - CLIMATIQ (8 items)
        // ============================================
        // Formula: Call Climatiq API with climatiq_id
        $foodClimatiqItems = [
            ['factor_category_id' => $foodCategory->id, 'name' => 'Apples', 'value' => '0', 'climatiq_id' => 'arable_farming-type_apples-origin_region_global'],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Cardboard Boxes (Climatiq)', 'value' => '0', 'climatiq_id' => 'paper_and_cardboard-type_carton_board_box_production_with_offset_printing_market_for_carton_board_box_production_with_offset_printing'],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Cocoa Fruit', 'value' => '0', 'climatiq_id' => 'agriculture_fishing_forestry-type_fish_all_species-origin_region_multi_region'],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Fresh Fish', 'value' => '0', 'climatiq_id' => 'agriculture_fishing_forestry-type_fish_all_species-origin_region_multi_region'],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Plastic Bags/Films (Climatiq)', 'value' => '0', 'climatiq_id' => 'chemicals-type_polyethylene_linear_low_density_granulate_market_for_polyethylene_linear_low_density_granulate'],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Plastic Bottles (Climatiq)', 'value' => '0', 'climatiq_id' => 'chemicals-type_polyethylene_terephthalate_granulate_bottle_grade_market_for_polyethylene_terephthalate_granulate_bottle_grade'],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Rice', 'value' => '0', 'climatiq_id' => 'arable_farming-type_apples-origin_region_global'],
            ['factor_category_id' => $foodCategory->id, 'name' => 'Tissue Paper (Climatiq)', 'value' => '0', 'climatiq_id' => 'paper_and_cardboard-type_tissue_paper_market_for_tissue_paper'],
        ];

        // ============================================
        // STEP 4: FUEL CONSUMPTION (10 items)
        // ============================================
        // Formula: emissions = (distance / efficiency) * fuel_factor
        $fuelItems = [
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Dexlite', 'value' => '2.65', 'climatiq_id' => null],
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Pertalite', 'value' => '2.31', 'climatiq_id' => null],
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Pertamax', 'value' => '2.31', 'climatiq_id' => null],
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Pertamax Turbo', 'value' => '2.31', 'climatiq_id' => null],
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Pertamina Dex', 'value' => '2.68', 'climatiq_id' => null],
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Shell Super', 'value' => '2.31', 'climatiq_id' => null],
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Shell V-Power', 'value' => '2.346', 'climatiq_id' => null],
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Shell V-Power Diesel', 'value' => '2.68', 'climatiq_id' => null],
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Shell V-Power Nitro+', 'value' => '2.346', 'climatiq_id' => null],
            ['factor_category_id' => $fuelCategory->id, 'name' => 'Solar / Bio Solar', 'value' => '2.58', 'climatiq_id' => null],
        ];

        // ============================================
        // STEP 5: VEHICLE EFFICIENCY (6 items)
        // ============================================
        // Used for: Default efficiency when custom efficiency not provided
        // Value in km/liter
        $vehicleItems = [
            ['factor_category_id' => $vehicleEfficiencyCategory->id, 'name' => 'City Car', 'value' => '20', 'climatiq_id' => null],
            ['factor_category_id' => $vehicleEfficiencyCategory->id, 'name' => 'Diesel Car', 'value' => '15', 'climatiq_id' => null],
            ['factor_category_id' => $vehicleEfficiencyCategory->id, 'name' => 'Hybrid Car', 'value' => '22', 'climatiq_id' => null],
            ['factor_category_id' => $vehicleEfficiencyCategory->id, 'name' => 'Motorcycle', 'value' => '40', 'climatiq_id' => null],
            ['factor_category_id' => $vehicleEfficiencyCategory->id, 'name' => 'SUV / MPV', 'value' => '12', 'climatiq_id' => null],
            ['factor_category_id' => $vehicleEfficiencyCategory->id, 'name' => 'Sedan / Medium Car', 'value' => '14', 'climatiq_id' => null],
        ];

        // ============================================
        // STEP 6: PUBLIC TRANSPORT EMISSIONS (6 items)
        // ============================================
        // Formula: emissions = (emission_factor * distance) / avg_passengers
        // Value in kg CO2e/km (total vehicle)
        $transportItems = [
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'City Bus (Emission)', 'value' => '1.085', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'Intercity Bus (Emission)', 'value' => '0.1085', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'MRT (Emission)', 'value' => '0.026', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'Minibus / Angkot (Emission)', 'value' => '0.1085', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'Online Motorcycle (Emission)', 'value' => '0.1824', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'Online Taxi (Emission)', 'value' => '0.1669', 'climatiq_id' => null],
        ];

        // ============================================
        // STEP 7: PUBLIC TRANSPORT PASSENGERS (6 items)
        // ============================================
        // Used for: Dividing total emissions by passenger count
        $passengerItems = [
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'City Bus (Passengers)', 'value' => '20', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'Intercity Bus (Passengers)', 'value' => '25', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'MRT (Passengers)', 'value' => '300', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'Minibus / Angkot (Passengers)', 'value' => '8', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'Online Motorcycle (Passengers)', 'value' => '1', 'climatiq_id' => null],
            ['factor_category_id' => $publicTransportCategory->id, 'name' => 'Online Taxi (Passengers)', 'value' => '4', 'climatiq_id' => null],
        ];

        // ============================================
        // STEP 8: INSERT ALL ITEMS (40 total)
        // ============================================
        $allItems = array_merge(
            $foodFixedItems,      // 4 items
            $foodClimatiqItems,   // 8 items
            $fuelItems,           // 10 items
            $vehicleItems,        // 6 items
            $transportItems,      // 6 items
            $passengerItems       // 6 items
        );

        foreach ($allItems as $item) {
            EmissionFactorItem::updateOrCreate(
                [
                    'factor_category_id' => $item['factor_category_id'],
                    'name' => $item['name']
                ],
                $item
            );
        }

        // ============================================
        // SUMMARY
        // ============================================
        $this->command->info('✅ Emission factors seeded successfully!');
        $this->command->info('📊 Total items seeded: ' . count($allItems));
        $this->command->info('');
        $this->command->info('Breakdown:');
        $this->command->info('  • Food (Fixed): 4 items');
        $this->command->info('  • Food (Climatiq): 8 items');
        $this->command->info('  • Fuel Factors: 10 items');
        $this->command->info('  • Vehicle Efficiency: 6 items');
        $this->command->info('  • Transport Emissions: 6 items');
        $this->command->info('  • Transport Passengers: 6 items');
    }
}
