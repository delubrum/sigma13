<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetPreventiveAction;
use App\Domain\Shared\Web\Adapters\AbstractTabAdapter;

final class PreventiveTabAdapter extends AbstractTabAdapter
{
    protected function view(): string { return 'assets::tabs.preventive'; }

    protected function viewKey(): string { return 'automations'; }

    protected function getData(int $id): mixed { return GetAssetPreventiveAction::run($id); }
}
