<?php

declare(strict_types=1);

namespace App\Domain\Users\Data;

use App\Domain\Shared\Data\Column;
use App\Domain\Users\Models\User;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 70, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Nombre', headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'Cédula', headerFilter: 'input')]
        public readonly ?string $document,

        #[Column(title: 'Email', headerFilter: 'input')]
        public readonly string $email,

        #[MapInputName('status_label')]
        #[Column(
            title: 'Estado', 
            width: 110, 
            hozAlign: 'center', 
            formatter: 'html', 
            headerFilter: 'list', 
            headerFilterParams: [
                'values' => [true => 'Activo', false => 'Inactivo'], 
                'clearable' => true
            ]
        )]
        public readonly string $isActive,

        #[MapInputName('created_at')]
        #[Column(title: 'Creado', width: 140, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $createdAt,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            document: $user->document,
            email: $user->email,
            isActive: self::renderStatusBadge($user->is_active), // Asumiendo que el modelo tiene is_active
            createdAt: $user->created_at->format('Y-m-d H:i'),
        );
    }

    private static function renderStatusBadge(bool $isActive): string
    {
        $color = $isActive 
            ? 'border-green-500 text-green-500' 
            : 'border-red-500 text-red-500';

        $label = $isActive ? 'Activo' : 'Inactivo';

        return sprintf(
            '<span class="px-2 py-0.5 rounded border %s font-bold uppercase text-[10px]">%s</span>',
            $color,
            $label
        );
    }
}