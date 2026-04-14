<?php

declare(strict_types=1);

namespace App\Domain\Operations\Data;

use App\Domain\Shared\Attributes\Column;
use Spatie\LaravelData\Data;

final class PrintTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', field: 'id', width: 60)]
        public string $id,

        #[Column(title: 'Date', field: 'date')]
        public string $date,

        #[Column(title: 'Project', field: 'project')]
        public string $project,

        #[Column(title: 'User', field: 'user')]
        public string $user,

        #[Column(title: 'ES ID', field: 'es')]
        public string $es,
    ) {}
}
