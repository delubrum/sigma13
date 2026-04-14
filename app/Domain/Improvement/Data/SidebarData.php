<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Data;

use App\Domain\Improvement\Models\Improvement;
use Spatie\LaravelData\Data;

final class SidebarData extends Data
{
    public function __construct(
        public readonly int    $id,
        public readonly string $code,
        public readonly string $status,
        public readonly string $creator,
        public readonly string $createdAt,
        public readonly ?string $responsible,
        public readonly ?int   $responsibleId,
        public readonly ?string $process,
        public readonly ?string $perspective,
        public readonly ?string $type,
        public readonly ?string $source,
        public readonly ?string $sourceOther,
        public readonly ?string $description,
        public readonly ?string $immediateAction,
        public readonly ?string $aim,
        public readonly ?string $goal,
        public readonly ?string $repeated,
        public readonly ?string $closedAt,
        public readonly ?string $notes,
        public readonly ?string $convenience,
        public readonly ?string $adequacy,
        public readonly ?string $effectiveness,
        public readonly ?string $rejectionReason,
        public readonly ?string $cdate,
        /** @var array<int,int> */
        public readonly array  $userIds,
        /** @var array<int,array{id:int,name:string}> */
        public readonly array  $allUsers,
        public readonly int    $causesCount,
        public readonly int    $activitiesCount,
        public readonly bool   $canEdit,
    ) {}

    /** @param array<int,array{id:int,name:string}> $allUsers */
    public static function fromModel(Improvement $improvement, array $allUsers = []): self
    {
        $status = $improvement->status ?? 'Analysis';
        $canEdit = ! in_array($status, ['Closed', 'Rejected', 'Canceled'], true);

        return new self(
            id:              $improvement->id,
            code:            (string) ($improvement->code ?? $improvement->id),
            status:          $status,
            creator:         (string) ($improvement->creator_name ?? ''),
            createdAt:       $improvement->created_at?->format('Y-m-d') ?? '',
            responsible:     (string) ($improvement->responsible_name ?? ''),
            responsibleId:   $improvement->responsible_id,
            process:         $improvement->process,
            perspective:     $improvement->perspective,
            type:            $improvement->type,
            source:          $improvement->source,
            sourceOther:     $improvement->source_other,
            description:     $improvement->description,
            immediateAction: $improvement->immediate_action,
            aim:             $improvement->aim,
            goal:            $improvement->goal,
            repeated:        $improvement->repeated ? 'Sí' : 'No',
            closedAt:        $improvement->closed_at?->format('Y-m-d'),
            notes:           $improvement->notes,
            convenience:     $improvement->convenience,
            adequacy:        $improvement->adequacy,
            effectiveness:   $improvement->effectiveness,
            rejectionReason: $improvement->rejection_reason,
            cdate:           $improvement->cdate,
            userIds:         is_array($improvement->user_ids) ? $improvement->user_ids : [],
            allUsers:        $allUsers,
            causesCount:     $improvement->causes()->count(),
            activitiesCount: $improvement->activities()->count(),
            canEdit:         $canEdit,
        );
    }
}
