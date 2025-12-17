<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceLabel;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * Service Seeder
 * 
 * Migrates existing OPD Lab configuration to the new Service system.
 * Creates a default "OPD Lab" service for each tenant that has OPD Lab enabled.
 */
class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get OPD Lab password from config (for migration)
        $opdPassword = config('opd.password', '1234');
        
        // Create OPD Lab service for each tenant
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            // Check if tenant already has an OPD Lab service
            $existingService = Service::where('tenant_id', $tenant->id)
                ->where('name', 'OPD Lab')
                ->first();
            
            if ($existingService) {
                $this->command->info("Tenant {$tenant->name} already has OPD Lab service. Skipping...");
                continue;
            }
            
            // Create OPD Lab service
            $service = Service::create([
                'tenant_id' => $tenant->id,
                'name' => 'OPD Lab',
                'type' => 'range', // OPD Lab uses range-based calling
                'password_hash' => $this->isHashed($opdPassword) ? $opdPassword : Hash::make($opdPassword),
                'password_migrated_at' => now(),
                'is_active' => true,
            ]);
            
            // Create default OPD Lab labels (test types)
            $labels = [
                [
                    'label' => 'Urine Test',
                    'color' => 'white',
                    'translations' => [
                        'en' => 'Urine Test',
                        'ta' => 'சிறுநீர் பரிசோதனை',
                        'si' => 'මූත්‍ර පරීක්ෂණය'
                    ],
                    'sort_order' => 1,
                ],
                [
                    'label' => 'Full Blood Count',
                    'color' => 'green',
                    'translations' => [
                        'en' => 'FBC',
                        'ta' => 'குருதி கல எண்ணிக்கை பரிசோதனை',
                        'si' => 'රුධිර සෙලුල ගණන පරීක්ෂණය'
                    ],
                    'sort_order' => 2,
                ],
                [
                    'label' => 'ESR',
                    'color' => 'red',
                    'translations' => [
                        'en' => 'ESR',
                        'ta' => 'செங்குருதி கல அடைவு பரிசோதனை',
                        'si' => 'රතු රුධිර සෙලුල ප්‍රතිසංස්කරණ පරීක්ෂණය'
                    ],
                    'sort_order' => 3,
                ],
            ];
            
            foreach ($labels as $labelData) {
                ServiceLabel::create([
                    'service_id' => $service->id,
                    'label' => $labelData['label'],
                    'color' => $labelData['color'],
                    'translations' => $labelData['translations'],
                    'sort_order' => $labelData['sort_order'],
                    'is_active' => true,
                ]);
            }
            
            $this->command->info("Created OPD Lab service for tenant: {$tenant->name}");
        }
        
        $this->command->info("Service migration completed!");
    }
    
    /**
     * Check if password is already hashed
     */
    private function isHashed(?string $password): bool
    {
        if (empty($password)) {
            return false;
        }
        
        return str_starts_with($password, '$2y$');
    }
}
