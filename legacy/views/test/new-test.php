<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<div class="card competency-card">
    <div class="card-header comp-header">
        <?php echo htmlspecialchars($r['competencia']); ?>
    </div>
    <div class="card-body comp-body">
        <div class="description"><?php echo htmlspecialchars($r['descripcion']); ?></div>
        <?php foreach ($r['indicadores'] as $index => $i) { ?>
            <div class="indicator-item">
                <div class="indicator-text"><?php echo htmlspecialchars($i); ?></div>
                <div class="radio-group" role="radiogroup">
                    <?php
                    $options = [
                        0 => 'Nunca',
                        1 => 'Rara vez',
                        2 => 'A veces',
                        3 => 'Frecuente',
                        4 => 'Siempre',
                    ];
            foreach ($options as $value => $label) { ?>
                        <div class="radio-label" 
                             role="radio" 
                             aria-checked="<?php echo (isset($answers[$q]) && $answers[$q] == $value) ? 'true' : 'false'; ?>" 
                             <?php echo isset($onclick) ? $onclick : ''; ?>
                             <?php echo (isset($answers[$q]) && $answers[$q] == $value) ? 'style="background:gray"' : ''; ?>
                             >
                            <input type="radio" 
                                   name="answers[<?php echo $q; ?>]" 
                                   value="<?php echo $value; ?>" 
                                   required 
                                   <?php echo isset($atr) ? $atr : ''; ?> 
                                   <?php echo (isset($answers[$q]) && $answers[$q] == $value) ? 'checked' : ''; ?>>
                            <?php echo $label; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php $q++;
        } ?>
    </div>
</div>