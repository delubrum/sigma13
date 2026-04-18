<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetDetailsAction;
use App\Domain\Shared\Web\Adapters\AbstractTabAdapter;

final class DetailsTabAdapter extends AbstractTabAdapter
{
    protected function view(): string { return 'assets::tabs.details'; }

    protected function viewKey(): string { return 'details'; }

    protected function getData(int $id): mixed { return GetAssetDetailsAction::run($id); }
}
