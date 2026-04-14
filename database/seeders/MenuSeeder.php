<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Users\Models\Permission;
use Illuminate\Database\Seeder;

final class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Performance Module
        Permission::updateOrCreate(
            ['name' => 'performance'],
            [
                'kind' => 'menu',
                'title' => 'Performance',
                'icon' => 'ri-flashlight-line',
                'url' => '/performance',
                'sort_order' => 100,
            ]
        );

        // Update Operations items if needed
        $ops = [
            ['name' => 'ops_ppe',        'title' => 'Operations', 'subtitle' => 'PPE',         'url' => '/operations/ppe'],
            ['name' => 'ops_equipment',  'title' => 'Operations', 'subtitle' => 'Equipment',   'url' => '/operations/equipment'],
            ['name' => 'ops_inspections', 'title' => 'Operations', 'subtitle' => 'Inspections', 'url' => '/operations/inspections'],
        ];

        foreach ($ops as $item) {
            Permission::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['kind' => 'menu'])
            );
        }

        // Engineering Module
        $eng = [
            ['name' => 'eng_library',   'title' => 'Technical Library', 'icon' => 'ri-book-open-line', 'url' => '/engineering/library'],
            ['name' => 'eng_cbm',       'title' => 'Technical Library', 'subtitle' => 'CBM',          'url' => '/engineering/cbm'],
            ['name' => 'eng_fasteners', 'title' => 'Technical Library', 'subtitle' => 'Fasteners',    'url' => '/engineering/fasteners'],
            ['name' => 'eng_extrusion', 'title' => 'Technical Library', 'subtitle' => 'Extrusion',    'url' => '/engineering/extrusion'],
        ];

        foreach ($eng as $item) {
            Permission::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['kind' => 'menu'])
            );
        }

        // Maintenance Module
        Permission::updateOrCreate(
            ['name' => 'maintenance'],
            [
                'kind' => 'menu',
                'title' => 'Machinery',
                'subtitle' => 'Service Desk',
                'icon' => 'ri-settings-gear-line',
                'url' => '/maintenance',
            ]
        );
        Permission::updateOrCreate(
            ['name' => 'maintenance_locative'],
            [
                'kind' => 'menu',
                'title' => 'Infrastructure',
                'subtitle' => 'Locative',
                'icon' => 'ri-building-line',
                'url' => '/maintenance/locative',
            ]
        );
        Permission::updateOrCreate(
            ['name' => 'it'],
            [
                'kind' => 'menu',
                'title' => 'Infrastructure',
                'subtitle' => 'IT',
                'icon' => 'ri-computer-line',
                'url' => '/it',
            ]
        );
        Permission::updateOrCreate(
            ['name' => 'quality_docs'],
            [
                'kind' => 'menu',
                'title' => 'Infrastructure',
                'subtitle' => 'Documents',
                'icon' => 'ri-file-search-line',
                'url' => '/quality/documents',
            ]
        );
    }
}
