<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetAutomationsAction;
use App\Domain\Assets\Data\Tabs\AutomationsTableData;
use App\Domain\Assets\Web\Adapters\Modals\AutomationsModalAdapter;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Web\Actions\SubTableAdapter;
use Lorisleiva\Actions\Concerns\AsAction;

final class AutomationsTabAdapter extends SubTableAdapter
{
    use AsAction;

    public function __construct(private readonly AutomationsModalAdapter $automationConfig) {}

    protected function tabConfig(): Config
    {
        return $this->automationConfig->config();
    }

    protected function tabRoute(): string
    {
        return 'assets.automations';
    }

    /** @return PaginatedResult<AutomationsTableData> */
    protected function tabData(int $parentId, int $page, int $size): PaginatedResult
    {
        return GetAssetAutomationsAction::run(
            assetId: $parentId,
            page:    $page,
            size:    $size,
        );
    }
}
