<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación de Competencias</title>
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2ecc71;
            --grey-light: #f5f7fa;
            --grey-border: #e0e4e8;
            --radius: 4px;
            --shadow: 0 1px 3px rgba(0,0,0,0.12);
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
            max-width: 900px;
            margin: 0 auto;
        }
        
        header {
            background-color: white;
            padding: 16px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 16px;
            text-align: center;
        }
        
        h1 {
            color: var(--text-color);
            font-size: 1.5rem;
            margin-bottom: 4px;
        }
        
        .instrucciones {
            padding: 12px;
            border-left: 3px solid var(--primary);
            background-color: var(--grey-light);
            margin-bottom: 16px;
            font-size: 0.9rem;
            border-radius: 0 var(--radius) var(--radius) 0;
        }
        
        /* Cards */
        .card {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 16px;
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card-header {
            padding: 12px 16px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        
        .eval-header {
            background-color: var(--primary);
            color: white;
            cursor: pointer;
        }
        
        .comp-header {
            background-color: var(--grey-light);
            border-bottom: 1px solid var(--grey-border);
        }
        
        .eval-header::after {
            content: '+';
            font-size: 1.2rem;
            transition: var(--transition);
        }
        
        .eval-header.active::after {
            content: '−';
            transform: rotate(0deg);
        }
        
        .card-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        
        .card-body.active {
            padding: 16px;
            max-height: 5000px;
        }
        
        /* Siempre visible para competencias */
        .comp-body {
            padding: 16px;
            max-height: none !important;
            overflow: visible;
        }
        
        /* Elementos de evaluación */
        .description {
            background-color: var(--grey-light);
            padding: 12px;
            border-radius: var(--radius);
            margin-bottom: 12px;
            font-style: italic;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .indicador-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--grey-border);
        }
        
        .indicador-item:last-child {
            border-bottom: none;
        }
        
        .indicador-texto {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        
        
        .radio-group {
            display: flex;
            gap: 5px;
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
            margin: 16px auto;
            display: block;
            transition: var(--transition);
        }
        
        .btn-submit:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        footer {
            text-align: center;
            margin: 20px 0;
            color: #777;
            font-size: 0.85rem;
        }

        #loading {
            display: none;
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 20000;
            background: url(assets/img/loader.gif) center no-repeat #fff;
            background-size: 10vw;
            opacity: 0.9;
        }
        
        /* Mejora de accesibilidad y responsive */
        @media (max-width: 600px) {
            .radio-grupo {
                flex-direction: column;
                gap: 4px;
            }
            
            .radio-label {
                padding: 10px 0;
            }
            
            .card-header {
                padding: 12px;
            }
            
            .card-body.active {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
<div id="loading"></div>

<a href="?c=Performance&a=Logout" style="float:right">Cerrar Sesión</a>
    <div class="container">
<br><br>


    <table style='text-align:center;width:100%;padding:0;background:white' class="mb-4">
        <tr>
            <td style='width:33%'><img style='width:200px' src='app/assets/img/logoES.png'></td>
            <td style='width:33%'><h1>Evaluación de Competencias</h1></td>
            <td style='width:33%;font-size:18px'>
                <b>Code:</b> F01-PRRH-03
                <br>
                <b>Date:</b> 2025-05-15
                <br>
                <b>Version:</b> 03
            </td>
        </tr>
    </table>
    <br>
    <header><h3><?php echo $name ?></h3></hearder>
        
        <div class="instrucciones">
            <p>Seleccione el nivel que mejor represente el desempeño para cada indicador.</p>
        </div>
        <?php
            $typeIndex = 0;
    $q = '';
    foreach ($users as $k => $u) {
        $kind = $this->model->get('kind', 'test', " and user_id = '$u'")->kind;
        $typeName = $this->model->get('kind', 'test_questions', " and id = '$kind'")->kind;
        $array1 = json_decode($this->model->get('content', 'test_questions', ' and id = 1')->content, true);
        $array2 = json_decode($this->model->get('content', 'test_questions', " and id = '$kind'")->content, true);
        $data = array_merge($array1, $array2);
        if (! empty($this->model->get('answers', 'test_answers', " and user_id = $u and tester_id = $user_id")->answers)) {
            $color = 'style="background-color: gray"';
            $atr = 'disabled';
            $onclick = '';
            $answers = json_decode($this->model->get('answers', 'test_answers', " and user_id = $u and tester_id = $user_id")->answers, true);
            $save = '';
        } else {
            $color = '';
            $atr = '';
            $onclick = 'onclick="selectOption(this)"';
            $selected = '';
            $answers = '';
            $save = '<button type="submit" class="btn-submit">Guardar Evaluaciones</button>';
        }

        $admin_ids = [111, 71770184];

        $typeText = match ($k) {
            0 => 'Autoevaluación',
            1 => (
                // Comprueba si $user_id está en la lista $admin_ids
                in_array($user_id, $admin_ids)
            ) ? 'Evaluación Compañero' : 'Evaluación Jefe',
            2,3 => (
                // Comprueba si $user_id está en la lista $admin_ids
                in_array($user_id, $admin_ids)
            ) ? 'Evaluación Colaboradores' : 'Evaluación Compañeros',
            default => 'Evaluación Colaboradores',
        };

        ?>
            <div class="card">
                <div class="card-header eval-header active" <?php echo $color ?> id="<?php echo $u ?>Header" onclick="toggleSection('<?php echo $u?>Header', '<?php echo $u?>Form')">
                <?php echo $typeText?> - <?php echo $this->model->get('name', 'test_db', " and id= '$u'")->name ?> 
                <!-- (<?php echo $typeName ?>) -->
                </div>
                <div class="card-body" id="<?php echo $u?>Form">
                    <form>
                        <input type="hidden" name="kind" value="<?php echo $kind ?>">
                        <input type="hidden" name="user_id" value="<?php echo $u ?>">
                        <?php $q = 0;
        foreach ($data as $r) {
            include 'new-test.php';
        } ?>
                        <?php echo $save ?>
                    </form>
                </div>
            </div>
        <?php $typeIndex++;
    } ?>



          
        <footer>
            &copy; 2025 Sistema de Evaluación de Competencias
        </footer>
    </div>

    <script>
        function toggleSection(headerId, bodyId) {
            const header = document.getElementById(headerId);
            const body = document.getElementById(bodyId);
            
            header.classList.toggle('active');
            body.classList.toggle('active');
        }

        // Función para seleccionar una opción
        function selectOption(element) {
            const opciones = element.parentNode.querySelectorAll('.radio-label');
            
            // Quitar selección de todas las opciones en el grupo
            opciones.forEach(op => {
                op.classList.remove('selected');
                op.setAttribute('aria-checked', 'false');
            });

            // Seleccionar la opción actual
            element.classList.add('selected');
            element.setAttribute('aria-checked', 'true');

            // Marcar el radio oculto como seleccionado
            element.querySelector('input[type="radio"]').checked = true;
        }

        document.addEventListener('DOMContentLoaded', () => {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formdata = new FormData(form);
                document.getElementById("loading").style.display = "block";
                try {
                    const res = await fetch("?c=Performance&a=SaveAnswers", {
                        method: "POST",
                        body: new FormData(e.target),
                    });
                    const data = await res.json();
                    if (data.error) notyf.error(data.error);
                } catch {
                    location.reload();
                } finally {
                    location.reload();
                }
            });
        });
    });
        
    </script>
</body>
</html>
