<div class="ctab cactive text-gray-800 text-gray-500 border-gray-800 border-b-2 px-3 py-2.5 cursor-pointer font-medium transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
    hx-get="?c=Recruitment&a=DetailCandidate&tab=cv&id=<?= $id->id ?>"
    hx-target="#ctabContentContainer"
    hx-indicator="#loading">Details</div>

<div class="ctab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
    hx-get="?c=Recruitment&a=DetailCandidate&tab=interview&id=<?= $id->id ?>"
    hx-target="#ctabContentContainer"
    hx-indicator="#loading">Interview</div>

<?php if ($id->hired == 1 and $user->id != 505) { ?>
    <div class="ctab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
        hx-get="?c=Recruitment&a=DetailCandidate&tab=test&id=<?= $id->id ?>"
        hx-target="#ctabContentContainer"
        hx-indicator="#loading">Screening</div>
<?php } ?>

<?php if ($id->status == 'qualified' and $user->id != 505) { ?>
    <div class="ctab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
        hx-get="?c=Recruitment&a=DetailCandidate&tab=contract&id=<?= $id->id ?>"
        hx-target="#ctabContentContainer"
        hx-indicator="#loading">Contract</div>
<?php } ?>