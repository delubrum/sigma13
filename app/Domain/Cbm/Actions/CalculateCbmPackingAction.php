<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Actions;

use App\Domain\Cbm\Models\Cbm;
use DVDoug\BoxPacker\Packer;
use DVDoug\BoxPacker\Rotation;
use DVDoug\BoxPacker\Test\TestBox;
use DVDoug\BoxPacker\Test\TestItem;
use Lorisleiva\Actions\Concerns\AsAction;

final class CalculateCbmPackingAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function handle(int $id): array
    {
        $cbm = Cbm::with('items')->findOrFail($id);
        $dbItems = $cbm->items;

        // 1. ITEMS TO CRATES
        $cratePacker = new Packer();
        // Standard Crates
        $cratePacker->addBox(new TestBox('S (48x42x80)', 48, 42, 80, 10, 48, 42, 80, 1500));
        $cratePacker->addBox(new TestBox('M (72x42x80)', 72, 42, 80, 15, 72, 42, 80, 1500));
        $cratePacker->addBox(new TestBox('L (120x42x80)', 120, 42, 80, 20, 120, 42, 80, 1500));

        foreach ($dbItems as $it) {
            $cratePacker->addItem(new TestItem('P_'.$it->id, (int) $it->width, (int) $it->item_length, (int) $it->height, (int) $it->weight, Rotation::BestFit), 1);
        }
        $packedCrates = $cratePacker->pack();

        // 2. CRATES TO 40FT CONTAINER
        $containerPacker = new Packer();
        $containerPacker->addBox(new TestBox('40ft_CONT', 468, 92, 94, 0, 468, 92, 94, 60000));

        $cratesMetadata = [];
        $totalItemsPacked = 0;

        foreach ($packedCrates as $idx => $pCrate) {
            $cBox = $pCrate->box;
            $crateKey = 'CRATE_'.($idx + 1);

            $innerParts = [];
            foreach ($pCrate->items as $pItem) {
                $totalItemsPacked++;
                $innerParts[] = [
                    'l' => (float) $pItem->width,
                    'b' => (float) $pItem->length, 
                    'h' => (float) $pItem->depth,
                    'px' => (float) $pItem->x, 'py' => (float) $pItem->y, 'pz' => (float) $pItem->z,
                    'w' => (float) $pItem->item->getWeight(),
                ];
            }

            $containerPacker->addItem(new TestItem(
                $crateKey, 
                (int) $cBox->getInnerWidth(), 
                (int) $cBox->getInnerLength(), 
                (int) $cBox->getInnerDepth(), 
                (int) $pCrate->getWeight(), 
                Rotation::BestFit
            ), 1);

            $cratesMetadata[$crateKey] = [
                'type' => $cBox->getReference(),
                'utility' => (float) $pCrate->getVolumeUtilisation(),
                'totalItems' => count($innerParts),
                'weight' => (float) $pCrate->getWeight(),
                'parts' => $innerParts,
            ];
        }

        $finalLoad = $containerPacker->pack();
        $packedBins = [];

        foreach ($finalLoad as $pBox) {
            $itemsData = [];
            foreach ($pBox->items as $pItem) {
                $ref = $pItem->item->getDescription();
                $itemsData[] = array_merge($cratesMetadata[$ref], [
                    'id' => $ref,
                    'l' => (float) $pItem->width, 
                    'b' => (float) $pItem->length, 
                    'h' => (float) $pItem->depth,
                    'px' => (float) $pItem->x, 
                    'py' => (float) $pItem->y, 
                    'pz' => (float) $pItem->z,
                ]);
            }
            $packedBins[] = [
                'dims' => [
                    'l' => (float) $pBox->box->getInnerWidth(), 
                    'b' => (float) $pBox->box->getInnerLength(), 
                    'h' => (float) $pBox->box->getInnerDepth()
                ],
                'items' => $itemsData,
                'totalWeight' => (float) $pBox->getWeight(),
                'utility' => (float) $pBox->getVolumeUtilisation(),
                'totalItemsPacked' => $totalItemsPacked,
            ];
        }

        return $packedBins[0] ?? [];
    }
}
