<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetAutomationsAction;
use App\Domain\Assets\Data\Tabs\AutomationsTableData;
use App\Domain\Assets\Web\Adapters\Modals\AutomationsModalAdapter;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Web\Adapters\AbstractSubTableAdapter;

/** @extends AbstractSubTableAdapter<AutomationsTableData> */
final class AutomationsTabAdapter extends AbstractSubTableAdapter
{
    public function __construct(private readonly AutomationsModalAdapter $automationConfig) {}

    public function config(): Config
    {
        return $this->automationConfig->config();
    }

    protected function route(): string
    {
        return 'assets.automations';
    }

    /** @return PaginatedResult<AutomationsTableData> */
    protected function getData(int $parentId, int $page, int $size): PaginatedResult
    {
        /** @var PaginatedResult<AutomationsTableData> $result */
        return GetAssetAutomationsAction::run(assetId: $parentId, page: $page, size: $size);
    }
}
