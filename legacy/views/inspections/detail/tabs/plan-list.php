<?php if (! empty($array)) { ?>
    <?php foreach ($array as $index => $r) { ?>
        <tr class="hover:bg-gray-100 odd:bg-white even:bg-gray-50">
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->description ?></td>
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->created_at ?></td>
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->username ?></td>
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->start ?></td>
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->end ?></td>
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->rating ?></td>
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->actions ?></td>
        </tr>
    <?php } ?>
<?php } ?>

<?php if (! empty($array) && $hasMore) { ?>
    <tr
        hx-post="?c=Infraimprovement&a=GetEvents&kind=<?= $type ?>&page=<?= $nextPage ?>&id=<?= $id ?>"
        hx-trigger="intersect once"
        hx-ext="intersect"
        hx-swap="beforeend"
        hx-include="#searchEvent"
    >
        <td colspan="5" class="text-center text-sm text-gray-400 py-2">Loading more...</td>
    </tr>
<?php } elseif (empty($array)) { ?>
    <tr>
        <td colspan="5" class="py-4 text-center text-gray-500 text-sm">
            <?php if (! empty($search)) { ?>
                No more items found matching your search.
            <?php } else { ?>
                No more items available.
            <?php } ?>
        </td>
    </tr>
<?php } ?>
