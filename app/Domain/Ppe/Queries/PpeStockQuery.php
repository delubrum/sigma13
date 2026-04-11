<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Queries;

use App\Domain\Ppe\Models\PpeEntry;
use App\Domain\Ppe\Models\PpeItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class PpeStockQuery
{
    public static function stocks(int $page, int $size, array $filters = [], array $sorts = [])
    {
        // Legacy logic: SUM(epp_register.qty) - COUNT(epp where name = item.name)
        // This is tricky because deliveries (epp) use NAME not ID.
        
        $query = PpeItem::query()
            ->select('epp_db.id', 'epp_db.name')
            ->addSelect([
                'total_in' => PpeEntry::selectRaw('SUM(qty)')
                    ->whereColumn('item_id', 'epp_db.id')
            ])
            ->addSelect([
                'total_out' => DB::table('epp')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('name', 'epp_db.name')
            ]);

        foreach ($filters as $field => $value) {
            if (blank($value)) continue;
            $query->where("epp_db.$field", 'ilike', "%$value%");
        }

        foreach ($sorts as $field => $dir) {
            $query->orderBy("epp_db.$field", $dir);
        }
        if (empty($sorts)) $query->orderBy('name');

        return $query->paginate($size, ['*'], 'page', $page);
    }
}
