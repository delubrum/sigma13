<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Evaluación de Competencias</title>
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2ecc71;
            --grey-light: #f5f7fa;
            --grey-border: #e0e4e8;
            --radius: 4px;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            --text-color: #333;
            --transition: all 0.2s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: var(--text-color);
            line-height: 1.5;
            padding: 16px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        header {
            background-color: white;
            padding: 24px 16px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            text-align: center;
        }

        h1 {
            font-size: 1.75rem;
        }

        .card {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .card-header {
            padding: 16px;
            background-color: var(--grey-light);
            border-bottom: 1px solid var(--grey-border);
            font-weight: bold;
        }

        .card-body {
            padding: 16px;
        }

        .description {
            background-color: var(--grey-light);
            padding: 12px;
            border-radius: var(--radius);
            margin-bottom: 12px;
            font-style: italic;
            font-size: 0.9rem;
        }

        .indicator-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--grey-border);
        }

        .indicator-item:last-child {
            border-bottom: none;
        }

        .indicator-text {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .radio-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .radio-label {
            flex: 1;
            text-align: center;
            padding: 8px 0;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 0.85rem;
            border: 1px solid #ddd;
            transition: var(--transition);
            user-select: none;
        }

        .radio-label:hover {
            background-color: #f0f0f0;
        }

        .radio-label.selected {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
            font-weight: 500;
        }

        .btn-submit {
            background-color: var(--secondary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            margin: 24px auto;
            display: block;
        }

        .btn-submit:hover {
            background-color: #27ae60;
        }

        footer {
            text-align: center;
            margin-top: 32px;
            color: #777;
            font-size: 0.85rem;
        }

        @media (max-width: 600px) {
            .radio-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Evaluación de Competencias</h1>
        </header>

        <form method="post" id="<?php echo $u ?>Form">
            <?php $q = 0;
        foreach ($data as $r) { ?>
                <div class="card">
                    <div class="card-header">
                        <?php echo htmlspecialchars($r['competencia']); ?>
                    </div>
                    <div class="card-body">
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
                                        <label class="radio-label<?php echo (isset($answers[$q]) && $answers[$q] == $value) ? ' selected' : ''; ?>">
                                            <input type="radio" name="answers[<?php echo $q; ?>]" value="<?php echo $value; ?>" required <?php echo (isset($answers[$q]) && $answers[$q] == $value) ? 'checked' : ''; ?> hidden>
                                            <?php echo $label; ?>
                                        </label>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php $q++;
                        } ?>
                    </div>
                </div>
            <?php } ?>
        </form>

        <footer>
            © 2025 Sistema de Evaluación de Competencias
        </footer>
    </div>
</body>
</html>