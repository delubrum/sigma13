<?php

declare(strict_types=1);

namespace App\Domain\Users\Data;

use App\Domain\Shared\Data\Column;
use App\Domain\Users\Models\User;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 70, hozAlign: 'center', headerFilter: 'input')]
        public int $id,

        #[Column(title: 'Nombre', headerFilter: 'input')]
        public string $name,

        #[Column(title: 'Cédula', headerFilter: 'input')]
        public ?string $document,

        #[Column(title: 'Email', headerFilter: 'input')]
        public string $email,

        #[Column(
            title: 'Estado',
            width: 110,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: ['values' => [1 => 'Activo', 0 => 'Inactivo'], 'clearable' => true]
        )]
        public string $isActive,

        #[Column(title: 'Creado', width: 140, hozAlign: 'center', headerFilter: 'input')]
        public string $createdAt,
    ) {}

    public static function fromModel(User $user): self
    {
        $color = $user->is_active
            ? 'border-green-500 text-green-500'
            : 'border-red-500 text-red-500';
        $label = $user->is_active ? 'Activo' : 'Inactivo';

        return new self(
            id: $user->id,
            name: $user->name,
            document: $user->document,
            email: $user->email,
            isActive: "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$label}</span>",
            createdAt: $user->created_at?->format('Y-m-d H:i') ?? '',
        );
    }
}
