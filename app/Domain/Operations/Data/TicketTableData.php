<?php

declare(strict_types=1);

namespace App\Domain\Operations\Data;

use App\Domain\Shared\Attributes\Column;
use Spatie\LaravelData\Data;

final class TicketTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', field: 'id', width: 60)]
        public int $id,

        #[Column(title: 'Type', field: 'type')]
        public string $type,

        #[Column(title: 'Date', field: 'date')]
        public string $date,

        #[Column(title: 'User', field: 'user')]
        public string $user,

        #[Column(title: 'Facility', field: 'facility')]
        public string $facility,

        #[Column(title: 'Priority', field: 'priority')]
        public string $priority,

        #[Column(title: 'Description', field: 'description')]
        public string $description,

        #[Column(title: 'Days', field: 'days')]
        public int $days,

        #[Column(title: 'Started', field: 'started')]
        public ?string $started,

        #[Column(title: 'Closed', field: 'closed')]
        public ?string $closed,

        #[Column(title: 'Status', field: 'status')]
        public string $status,
    ) {}
}
