<div class="p-2 space-y-2">
    <div class="flex justify-end">
        <button class="btn-sm btn-primary"
                hx-get="{{ route('recruitment.candidates.create', $id) }}"
                hx-target="#modal-body-2"
                hx-swap="innerHTML">
            <i class="ri-user-add-line mr-1"></i> Nuevo Candidato
        </button>
    </div>

    <div id="dt_candidates_{{ $id }}"
         data-widget="tabulator"
         data-url="{{ route('recruitment.candidates.data', $id) }}"
         data-columns='@json(\App\Domain\Shared\Services\SchemaGenerator::toColumns(\App\Domain\Recruitment\Data\CandidateTableData::class))'
         data-row-click="{{ route('recruitment.candidates.edit', ['recruitmentId' => $id, 'candidateId' => '__id__']) }}"
         data-row-target="#modal-body-2">
    </div>
</div>
