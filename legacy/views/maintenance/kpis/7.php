<table class="table table-hover col-6 bg-white">
    <tr>
        <th>Mes</th>
        <!-- Encabezados con las máquinas -->
        <?php foreach ($machines as $m) { ?>
            <th><?php echo $m; ?></th>
        <?php } ?>
    </tr>

    <!-- Filas con los meses y los datos de tiempo -->
    <?php foreach ($months as $mo) { ?>
        <tr>
            <td><?php echo $mo; ?></td>
            <?php foreach ($machines as $m) { ?>
                <td>
                    <?php
                    // Muestra el tiempo o un 0 si no hay datos
                    echo isset($data[$mo][$m]) ? $data[$mo][$m] : '100%';
                ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>
