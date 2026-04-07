<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

use DVDoug\BoxPacker\Packer;
use DVDoug\BoxPacker\Rotation;
use DVDoug\BoxPacker\Test\TestBox;
use DVDoug\BoxPacker\Test\TestItem;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CBMController
{
    public function __construct(public $model, private $auth) {}

    public function Index(): void
    {
        $user = $this->auth->authorize(164);
        $tabulator = true;
        $title = 'Technical Library / CBM';
        $content = 'app/components/list.php';
        $button = 'New CBM';
        $columns = '[
            { "title": "ID", "field": "id", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Project", "field": "project", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "User", "field": "user", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Date", "field": "date", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Items", "field": "total_items", "hozAlign": "center" },
            { "title": "Actions", "field": "id", "hozAlign": "center", "formatter": "html", "headerSort": false }
        ]';
        require_once 'app/views/index.php';
    }

    public function New()
    {
        $this->auth->authorize(164);
        require_once 'app/views/cbm/new.php';
    }

    public function Save(): void
    {
        $user = $this->auth->authorize(164);
        header('Content-Type: application/json');

        if (empty($_FILES['excel_file']['name'])) {
            http_response_code(400);
            echo json_encode(['message' => 'No excel file found']);

            return;
        }

        try {
            $spreadsheet = IOFactory::load($_FILES['excel_file']['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $total_qty = 0;
            $items_to_save = [];

            // Empezamos en 1 para saltar el encabezado del Excel
            for ($i = 1; $i < count($sheetData); $i++) {
                if (empty($sheetData[$i][0])) {
                    continue;
                }

                $width = (float) $sheetData[$i][0];
                $height = (float) $sheetData[$i][1];
                $item_length = (float) $sheetData[$i][2]; // Columna 2 del Excel -> item_length
                $qty = (int) $sheetData[$i][3];
                $weight = (float) $sheetData[$i][4];

                for ($j = 0; $j < $qty; $j++) {
                    $items_to_save[] = [
                        'width' => $width,
                        'height' => $height,
                        'item_length' => $item_length,
                        'weight' => $weight,
                    ];
                    $total_qty++;
                }
            }

            $cbm = new stdClass;
            $cbm->project = $_POST['project'] ?? 'Untitled Project';
            $cbm->user_id = (int) $user->id;
            $cbm->total_items = $total_qty;
            $cbm_id = (int) $this->model->save('cbm', $cbm);

            foreach ($items_to_save as $data) {
                $item = new stdClass;
                $item->cbm_id = $cbm_id;
                $item->width = $data['width'];
                $item->height = $data['height'];
                $item->item_length = $data['item_length']; // Guardado con el nuevo nombre de campo
                $item->weight = $data['weight'];
                $item->qty = 1;
                $this->model->save('cbm_items', $item);
            }

            header('HX-Trigger: '.json_encode(['eventChanged' => true, 'showMessage' => '{"type": "success", "message": "Saved", "close": "closeNewModal"}']));
            echo json_encode(['status' => 'success', 'id' => $cbm_id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    public function Detail()
    {
        $user = $this->auth->authorize(164);
        $id = (int) $_REQUEST['id'];
        $cbm = $this->model->get('*', 'cbm', "and id = '$id'");

        // Obtenemos los ítems usando el nuevo esquema de la tabla
        $db_items = $this->model->list('*', 'cbm_items', "and cbm_id = '$id'");

        // 1. ITEMS A GUACALES
        $cratePacker = new Packer;
        $cratePacker->addBox(new TestBox('S (48x42x80)', 48, 42, 80, 10, 48, 42, 80, 1500));
        $cratePacker->addBox(new TestBox('M (72x42x80)', 72, 42, 80, 15, 72, 42, 80, 1500));
        $cratePacker->addBox(new TestBox('L (120x42x80)', 120, 42, 80, 20, 120, 42, 80, 1500));

        foreach ($db_items as $it) {
            // Se mapea $it->item_length al constructor del Packer
            $cratePacker->addItem(new TestItem('P_'.$it->id, (int) $it->width, (int) $it->item_length, (int) $it->height, (int) $it->weight, Rotation::BestFit), 1);
        }
        $packedCrates = $cratePacker->pack();

        // 2. GUACALES A 40FT
        $containerPacker = new Packer;
        $containerPacker->addBox(new TestBox('40ft_CONT', 468, 92, 94, 0, 468, 92, 94, 60000));

        $cratesMetadata = [];
        $totalItemsPacked = 0;

        foreach ($packedCrates as $idx => $pCrate) {
            $cBox = $pCrate->box;
            $crateKey = 'CRATE_'.($idx + 1);

            $innerParts = [];
            foreach ($pCrate->items as $pItem) {
                $totalItemsPacked++;
                $innerParts[] = [
                    'l' => (float) $pItem->width,
                    'b' => (float) $pItem->length, // Propiedad interna de BoxPacker (no de la BD)
                    'h' => (float) $pItem->depth,
                    'px' => (float) $pItem->x, 'py' => (float) $pItem->y, 'pz' => (float) $pItem->z,
                    'w' => (float) $pItem->item->getWeight(),
                ];
            }

            $containerPacker->addItem(new TestItem($crateKey, (int) $cBox->getInnerWidth(), (int) $cBox->getInnerLength(), (int) $cBox->getInnerDepth(), (int) $pCrate->getWeight(), Rotation::BestFit), 1);

            $cratesMetadata[$crateKey] = [
                'type' => $cBox->getReference(),
                'utility' => (float) $pCrate->getVolumeUtilisation(),
                'totalItems' => count($innerParts),
                'weight' => (float) $pCrate->getWeight(),
                'parts' => $innerParts,
            ];
        }

        $finalLoad = $containerPacker->pack();
        $packedBins = [];
        foreach ($finalLoad as $pBox) {
            $itemsData = [];
            foreach ($pBox->items as $pItem) {
                $ref = $pItem->item->getDescription();
                $itemsData[] = array_merge($cratesMetadata[$ref], [
                    'id' => $ref,
                    'l' => (float) $pItem->width, 'b' => (float) $pItem->length, 'h' => (float) $pItem->depth,
                    'px' => (float) $pItem->x, 'py' => (float) $pItem->y, 'pz' => (float) $pItem->z,
                ]);
            }
            $packedBins[] = [
                'dims' => ['l' => (float) $pBox->box->getInnerWidth(), 'b' => (float) $pBox->box->getInnerLength(), 'h' => (float) $pBox->box->getInnerDepth()],
                'items' => $itemsData,
                'totalWeight' => (float) $pBox->getWeight(),
                'utility' => (float) $pBox->getVolumeUtilisation(),
                'totalItemsPacked' => $totalItemsPacked,
            ];
        }

        require_once 'app/views/cbm/detail.php';
    }

    public function Stats(): void {}

    public function Data(): void
    {
        $this->auth->authorize(164);
        header('Content-Type: application/json');
        $page = (int) ($_GET['page'] ?? 1);
        $size = (int) ($_GET['size'] ?? 15);
        $offset = ($page - 1) * $size;
        $joins = 'LEFT JOIN users b ON a.user_id = b.id';
        $rows = $this->model->list('a.id, a.project, a.total_items, a.created_at as date, b.username as user', 'cbm a', "ORDER BY a.created_at DESC LIMIT $offset, $size", $joins);
        $total = $this->model->get('COUNT(id) as total', 'cbm')->total;
        echo json_encode(['data' => $rows, 'last_page' => ceil($total / $size)]);
    }
}
