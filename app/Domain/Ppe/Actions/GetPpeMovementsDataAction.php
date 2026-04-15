<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Actions;

use App\Domain\Ppe\Data\PpeMovementTableData;
use App\Domain\Shared\Data\PaginatedResult;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetPpeMovementsDataAction
{
    use AsAction;

    /**
     * @param array<string,mixed>  $filters
     * @param array<string,string> $sorts
     * @return PaginatedResult<PpeMovementTableData>
     */
    public function handle(int $itemId, array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        $rows = collect();

        // Entries (In)
        $entries = DB::table('epp_register as r')
            ->join('users as u', 'u.id', '=', 'r.user_id')
            ->where('r.item_id', $itemId)
            ->select('r.created_at as date', DB::raw("'In' as type"), 'r.qty', 'u.username as person')
            ->get();

        foreach ($entries as $e) {
            $rows->push(['date' => $e->date, 'type' => $e->type, 'qty' => $e->qty, 'person' => $e->person]);
        }

        // Deliveries (Out) — join via item name
        $itemName = DB::table('epp_db')->where('id', $itemId)->value('name');

        if ($itemName) {
            $outs = DB::table('epp as e')
                ->leftJoin('employees as emp', 'emp.id', '=', 'e.employee_id')
                ->where('e.name', $itemName)
                ->select('e.created_at as date', DB::raw("'Out' as type"), DB::raw('1 as qty'), 'emp.name as person')
                ->get();

            foreach ($outs as $o) {
                $rows->push(['date' => $o->date, 'type' => $o->type, 'qty' => $o->qty, 'person' => $o->person]);
            }
        }

        // Sort
        $orderField = 'date';
        $orderDir   = 'desc';
        if (! empty($sorts)) {
            $orderField = array_key_first($sorts);
            $orderDir   = strtolower(reset($sorts)) === 'asc' ? 'asc' : 'desc';
        }

        $sorted = $rows->sortBy(
            fn ($a) => $a[$orderField] ?? '',
            SORT_REGULAR,
            $orderDir === 'desc'
        )->values();

        $total   = $sorted->count();
        $sliced  = $sorted->forPage($page, $size);

        return new PaginatedResult(
            items: $sliced->map(static fn ($r) => PpeMovementTableData::fromRow((object) $r))->values()->all(),
            total: $total,
            lastPage: (int) ceil($total / $size),
        );
    }
}
