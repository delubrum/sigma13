<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('kind');
            $table->string('name'); // Eliminado ->unique()
            $table->string('category')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('link')->nullable();
            // Usamos text para que DBeaver no falle con el error 42804
            $table->text('props')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('expiry')->nullable();
            $table->string('code')->nullable();
            $table->text('url')->nullable();
            $table->integer('asset_id')->nullable();
            $table->integer('user_id')->nullable();
        });

        Schema::create('asset_events', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 10)->nullable();
            $table->integer('asset_id');
            $table->integer('employee_id')->nullable();
            $table->jsonb('software')->nullable();
            $table->jsonb('hardware')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id')->nullable();
            $table->string('wipe', 20)->nullable();
            $table->date('expiry')->nullable();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('area')->nullable();
            $table->string('hostname')->nullable();
            $table->string('serial')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('kind')->nullable();
            $table->string('cpu')->nullable();
            $table->string('ram')->nullable();
            $table->string('ssd')->nullable();
            $table->string('hdd')->nullable();
            $table->string('so')->nullable();
            $table->string('sap')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->date('acquisition_date')->nullable();
            $table->string('invoice')->nullable();
            $table->string('supplier')->nullable();
            $table->string('warranty')->nullable();
            $table->string('status', 20)->nullable();
            $table->string('classification')->nullable();
            $table->integer('confidentiality')->nullable();
            $table->integer('integrity')->nullable();
            $table->integer('availability')->nullable();
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('work_mode')->nullable();
            $table->string('url')->nullable();
            $table->softDeletesTz();
            $table->string('operator', 100)->nullable();
        });

        Schema::create('cbm', function (Blueprint $table) {
            $table->id();
            $table->string('project')->default('N/A');
            $table->integer('user_id');
            $table->integer('total_items')->default(0);
            $table->integer('total_crates')->default(0);
            $table->longText('api_result')->nullable()->comment('Almacena el JSON de la API para el 3D');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('cbm_items', function (Blueprint $table) {
            $table->id();
            $table->integer('cbm_id');
            $table->decimal('width', 10, 2);
            $table->decimal('height', 10, 2);
            $table->decimal('item_length', 10, 2);
            $table->integer('qty');
            $table->decimal('weight', 10, 2);
            $table->foreign('cbm_id')->references('id')->on('cbm')->onDelete('cascade');
        });

        Schema::create('development', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->date('started_at')->nullable();
            $table->integer('user_id');
            $table->text('description');
            $table->text('answer')->nullable();
            $table->date('dev_approval')->nullable();
            $table->date('approval')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('status', 100)->nullable();
            $table->string('area', 100)->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('development_task', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('development_id');
            $table->text('task');
            $table->timestamp('tested_at')->nullable();
            $table->text('result')->nullable();
            $table->foreign('development_id')->references('id')->on('development');
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('expiry')->nullable();
            $table->string('code')->nullable();
            $table->string('url', 100)->nullable();
            $table->integer('employee_id')->nullable();
            $table->integer('user_id')->nullable();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('cc')->nullable();
            $table->string('city', 100)->nullable();
            $table->timestamp('start_date')->useCurrent();
            $table->boolean('status')->nullable();
            $table->string('talla_pantalon', 10)->nullable();
            $table->string('talla_camisa', 10)->nullable();
            $table->string('talla_zapatos', 10)->nullable();
            $table->string('kind', 100)->nullable();
            $table->date('end_date')->nullable();
            $table->integer('profile')->nullable();
        });

        Schema::create('epp', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id')->nullable();
            $table->integer('employee_id')->nullable();
            $table->binary('img')->nullable();
            $table->text('notes')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('kind', 10)->nullable();
            $table->boolean('is_optimized')->nullable();
        });

        Schema::create('epp_db', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('code')->nullable();
            $table->integer('price')->nullable();
            $table->integer('min_stock')->nullable();
            $table->unique('name');
        });

        Schema::create('epp_register', function (Blueprint $table) {
            $table->id();
            $table->integer('qty')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id')->nullable();
            $table->integer('item_id')->nullable();
        });

        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id')->nullable();
            $table->integer('employee_id')->nullable();
            $table->binary('img')->nullable();
            $table->text('notes')->nullable();
            $table->string('name', 100)->nullable();
            $table->integer('qty')->nullable();
        });

        Schema::create('equipment_db', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('code')->nullable();
            $table->integer('price')->nullable();
            $table->integer('min_stock')->nullable();
        });

        Schema::create('equipment_register', function (Blueprint $table) {
            $table->id();
            $table->integer('qty')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id')->nullable();
            $table->integer('item_id')->nullable();
        });

        Schema::create('extrusion_db', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('kind', 100)->nullable();
        });

        Schema::create('hr_db', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('kind', 100)->nullable();
            $table->string('area', 100)->nullable();
        });

        Schema::create('improvement', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->string('kind', 100)->nullable();
            $table->text('source')->nullable();
            $table->string('perspective', 100)->nullable();
            $table->text('description')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('is_repeated')->nullable();
            $table->integer('responsible_id')->nullable();
            $table->string('status', 100)->nullable();
            $table->text('user_ids')->nullable();
            $table->text('aim')->nullable();
            $table->text('goal')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('convenience')->nullable();
            $table->text('adequacy')->nullable();
            $table->text('effectiveness')->nullable();
            $table->text('notes')->nullable();
            $table->string('process', 100)->nullable();
            $table->date('occurrence_date')->nullable();
            $table->text('acim')->nullable();
            $table->date('cdate')->nullable();
            $table->text('reason')->nullable();
        });

        Schema::create('improvement_activities', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('improvement_id')->nullable();
            $table->text('activity')->nullable();
            $table->text('how')->nullable();
            $table->date('whenn')->nullable();
            $table->date('done')->nullable();
            $table->integer('responsible_id')->nullable();
            $table->boolean('fulfill')->nullable();
            $table->integer('user_id')->nullable();
            $table->text('results')->nullable();
        });

        Schema::create('improvement_causes', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('improvement_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->text('content')->nullable();
            $table->text('probable')->nullable();
            $table->text('reason')->nullable();
            $table->tinyInteger('method_id')->nullable();
        });

        Schema::create('infraimprovement', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->string('kind', 100)->nullable();
            $table->string('priority', 100)->nullable();
            $table->text('description')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('responsible_id')->nullable();
            $table->string('status', 100)->nullable();
            $table->timestamp('status_at')->nullable();
        });

        Schema::create('infraimprovement_events', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('infraimprovement_id');
            $table->integer('user_id');
            $table->string('kind', 20);
            $table->text('description');
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->integer('responsible_id')->nullable();
            $table->integer('plan_id')->nullable();
            $table->text('rating')->nullable();
            $table->timestamp('closed_at')->nullable();
        });

        Schema::create('inspection_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('asset_id')->nullable();
            $table->string('category', 50)->nullable();
            $table->text('activity')->nullable();
        });

        Schema::create('inspection_automations', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('asset_id');
            $table->string('frequency', 100);
            $table->date('anchor_date')->nullable();
            $table->boolean('status')->default(true);
            $table->string('kind', 20)->nullable();
        });

        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->integer('inspection_id')->nullable();
            $table->integer('activity_id')->nullable();
            $table->text('answer')->nullable();
            $table->text('obs')->nullable();
            $table->text('url')->nullable();
        });

        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->integer('automation_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id')->nullable();
            $table->string('status', 20)->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
        });

        Schema::create('it', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id');
            $table->text('kind');
            $table->text('priority');
            $table->text('description');
            $table->integer('assignee_id')->nullable();
            $table->text('complexity')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->text('attends')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('status', 10)->nullable();
            $table->string('sgc', 100)->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->string('urgency_level', 100)->nullable();
            $table->text('facility');
            $table->text('reason')->nullable();
            $table->integer('asset_id')->default(0);
            $table->string('subtype', 100)->default('Corrective');
        });

        Schema::create('it_items', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->string('attends', 10)->nullable();
            $table->integer('it_id')->nullable();
            $table->string('complexity', 10)->nullable();
            $table->integer('duration')->nullable();
            $table->string('attendant', 100)->nullable();
        });

        Schema::create('job_profiles', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->string('name', 100)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('status', 100)->nullable();
            $table->integer('division_id')->nullable();
            $table->integer('reports_to')->nullable();
            $table->string('work_mode', 50)->nullable();
            $table->string('rank', 50)->nullable();
            $table->string('schedule', 50)->nullable();
            $table->string('travel', 2)->nullable();
            $table->string('relocation', 2)->nullable();
            $table->text('mission')->nullable();
            $table->text('experience')->nullable();
            $table->text('obs')->nullable();
            $table->text('lang')->nullable();
            $table->text('reports')->nullable();
            $table->string('code', 100)->nullable();
        });

        Schema::create('job_profile_items', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('jp_id');
            $table->integer('user_id');
            $table->text('content');
            $table->string('kind', 20)->nullable();
            $table->foreign('jp_id')->references('id')->on('job_profiles');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('log', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->string('kind', 20);
            $table->text('query');
            $table->integer('user_id');
            $table->string('ip', 100)->nullable();
            $table->string('device', 250)->nullable();
        });

        Schema::create('matrices', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->string('geometry_shape', 100)->nullable();
            $table->string('company_id', 100)->nullable();
            $table->string('category_id', 100)->nullable();
            $table->decimal('b', 19, 3)->nullable();
            $table->decimal('h', 19, 3)->nullable();
            $table->decimal('e1', 19, 3)->nullable();
            $table->decimal('e2', 19, 3)->nullable();
            $table->text('products')->nullable();
            $table->text('clicks')->nullable();
            $table->text('systema')->nullable();
        });

        Schema::create('matrices_db', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('kind', 100)->nullable();
        });

        Schema::create('mnt', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id');
            $table->text('kind');
            $table->text('priority');
            $table->integer('asset_id')->nullable();
            $table->text('description');
            $table->integer('assignee_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('rating')->default(0);
            $table->timestamp('closed_at')->nullable();
            $table->string('status', 10)->nullable();
            $table->string('sgc', 100)->nullable();
            $table->text('root_cause')->nullable();
            $table->text('facility');
            $table->string('subtype', 50)->default('Corrective');
            $table->text('reason')->nullable();
        });

        Schema::create('mnt_items', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->string('attends', 10)->nullable();
            $table->integer('mnt_id')->nullable();
            $table->string('complexity', 10)->nullable();
            $table->integer('duration')->nullable();
        });

        Schema::create('mnt_preventive', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->date('scheduled_start')->nullable();
            $table->date('scheduled_end')->nullable();
            $table->integer('preventive_id');
            $table->timestamp('closed_at')->nullable();
            $table->string('kind', 100);
            $table->string('status', 100)->nullable();
            $table->timestamp('started')->nullable();
            $table->timestamp('attended')->nullable();
            $table->text('activity')->nullable();
            $table->integer('asset_id')->nullable();
            $table->integer('activity_id')->nullable();
        });

        Schema::create('mnt_preventive_form', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->string('asset_id', 100);
            $table->string('frequency', 100);
            $table->text('activity');
            $table->date('last_performed_at')->nullable();
            $table->boolean('status')->default(true);
            $table->string('kind', 20)->nullable();
        });

        Schema::create('mntp_items', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->string('attends', 10)->nullable();
            $table->integer('mntp_id')->nullable();
            $table->string('complexity', 10)->nullable();
            $table->integer('duration')->nullable();
        });

        Schema::create('personal_data_updates', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->comment('ID del empleado que realiza la solicitud');
            $table->timestamp('created_at')->useCurrent();
            $table->enum('status', ['pending_review', 'approved', 'rejected'])->default('pending_review');
            $table->string('tipo_sangre', 5)->nullable();
            $table->string('lugar_nacimiento', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['M', 'F'])->nullable();
            $table->enum('licencia_conduccion', ['Si', 'No'])->nullable();
            $table->string('categoria_licencia', 10)->nullable();
            $table->enum('libreta_militar', ['Si', 'No'])->nullable();
            $table->enum('tiene_vehiculo', ['Si', 'No'])->nullable();
            $table->string('tipo_vehiculo', 50)->nullable();
            $table->string('placa_vehiculo', 10)->nullable();
            $table->string('estado_civil', 50)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('barrio', 100)->nullable();
            $table->string('municipio', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->integer('estrato_socioeconomico')->nullable();
            $table->string('tiempo_vivienda', 50)->nullable();
            $table->enum('tenencia_vivienda', ['Propia', 'Familiar', 'Arrendada'])->nullable();
            $table->string('telefono_fijo', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('relacion_tecnoglass', ['Si', 'No'])->nullable();
            $table->text('relacion_tecnoglass_detalle_json')->nullable();
            $table->string('nombre_conyuge', 200)->nullable();
            $table->string('cedula_conyuge', 20)->nullable();
            $table->string('ocupacion_conyuge', 100)->nullable();
            $table->string('email_conyuge', 100)->nullable();
            $table->string('telefono_conyuge', 20)->nullable();
            $table->integer('numero_hijos')->nullable();
            $table->text('contacto_emergencia_json')->nullable();
            $table->text('otros_estudios_list_json')->nullable();
            $table->string('talla_pantalon', 10)->nullable();
            $table->string('talla_camisa', 10)->nullable();
            $table->string('talla_zapatos', 10)->nullable();
            $table->enum('tiene_carnet_arl', ['Si', 'No'])->nullable();
            $table->string('eps', 100)->nullable();
            $table->string('fondo_pensiones', 100)->nullable();
            $table->string('arl', 100)->nullable();
            $table->enum('seguro_exequias', ['Si', 'No'])->nullable();
            $table->enum('medico_presion_arterial', ['Si', 'No'])->nullable();
            $table->enum('medico_diabetes', ['Si', 'No'])->nullable();
            $table->text('medico_alergias')->nullable();
            $table->text('medico_otra_condicion')->nullable();
            $table->text('medico_observaciones')->nullable();
            $table->enum('carnet_empresa', ['Si', 'No'])->nullable();
            $table->string('photo_path', 255)->nullable()->comment('Ruta del archivo de la foto cargado');
            $table->text('hijos_json')->nullable();
            $table->text('ultimo_estudio_json')->nullable();
        });

        Schema::create('preop', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('km')->nullable();
            $table->string('status', 20)->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('preop_items', function (Blueprint $table) {
            $table->id();
            $table->integer('preop_id')->nullable();
            $table->string('question_id', 50)->nullable();
            $table->text('answer')->nullable();
            $table->text('obs')->nullable();
            $table->text('url')->nullable();
        });

        Schema::create('preop_questions', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 10)->nullable();
            $table->string('category', 50)->nullable();
            $table->text('question')->nullable();
        });

        Schema::create('preoperational', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('km')->nullable();
            $table->string('status', 20)->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('preoperational_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('q_id')->comment('1: KM, 52: HRS');
            $table->string('activity', 255);
            $table->integer('target_usage');
            $table->integer('opened_at')->default(200);
            $table->integer('due')->default(300);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('preoperational_items', function (Blueprint $table) {
            $table->id();
            $table->integer('preop_id')->nullable();
            $table->string('question_id', 50)->nullable();
            $table->text('answer')->nullable();
            $table->text('obs')->nullable();
            $table->text('url')->nullable();
            $table->text('ticket_ids')->nullable();
        });

        Schema::create('preoperational_questions', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 10)->nullable();
            $table->text('question')->nullable();
            $table->tinyInteger('subtype')->nullable();
            $table->text('items')->nullable();
            $table->tinyInteger('ticket')->nullable();
            $table->string('category', 50)->nullable();
            $table->tinyInteger('sort')->nullable();
        });

        Schema::create('recruitment', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('profile_id')->nullable();
            $table->text('city')->nullable();
            $table->integer('qty')->nullable();
            $table->text('contract')->nullable();
            $table->text('cause')->nullable();
            $table->text('srange')->nullable();
            $table->text('replaces')->nullable();
            $table->date('start_date')->nullable();
            $table->text('others')->nullable();
            $table->string('status', 100)->nullable();
            $table->integer('complexity')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->integer('ceo_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('approver', 200)->nullable();
            $table->text('rejection')->nullable();
            $table->integer('assignee_id')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('resources')->nullable();
            $table->string('work_mode', 100)->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('profile_id')->references('id')->on('job_profiles');
        });

        Schema::create('recruitment_candidates', function (Blueprint $table) {
            $table->id();
            $table->timestamp('appointment')->nullable();
            $table->string('status', 100)->nullable();
            $table->text('concept')->nullable();
            $table->integer('cc')->nullable();
            $table->integer('recruitment_id')->nullable();
            $table->date('done_at')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('wage', 100)->nullable();
            $table->string('user_id', 100)->nullable();
            $table->timestamp('disc_date')->nullable();
            $table->text('disc_answers')->nullable();
            $table->timestamp('disc_email')->nullable();
            $table->text('candidate_list')->nullable();
            $table->text('pf_answers')->nullable();
            $table->timestamp('pf_email')->nullable();
            $table->timestamp('pf_date')->nullable();
            $table->timestamp('contract_email')->nullable();
            $table->boolean('hired')->nullable();
            $table->integer('age')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->string('source', 255)->nullable();
            $table->string('maritalstatus', 50)->nullable();
            $table->string('liveswith', 100)->nullable();
            $table->boolean('relativework')->nullable();
            $table->boolean('relativesupply')->nullable();
            $table->boolean('relativebond')->nullable();
            $table->text('relatives_data')->nullable();
            $table->string('educationlevel', 50)->nullable();
            $table->string('degree', 100)->nullable();
            $table->string('school', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('job_position', 100)->nullable();
            $table->string('reason', 255)->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->boolean('has_knowledge')->nullable();
            $table->text('shortgoals')->nullable();
            $table->text('longgoals')->nullable();
            $table->text('reasons')->nullable();
            $table->text('otherquestion')->nullable();
            $table->boolean('hired_disc')->nullable();
            $table->boolean('hired_pf')->nullable();
            $table->date('polygraph_date')->nullable();
            $table->boolean('polygraph_result')->nullable();
            $table->date('security_date')->nullable();
            $table->boolean('security_result')->nullable();
            $table->date('medical_date')->nullable();
            $table->boolean('medical_result')->nullable();
            $table->date('home_date')->nullable();
            $table->boolean('home_result')->nullable();
            $table->text('list_review_result')->nullable();
            $table->timestamp('status_at')->nullable();
            $table->string('kind', 20)->nullable();
            $table->text('work_experience')->nullable();
            $table->string('cv_source', 100)->nullable();
            $table->string('psychometrics', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->text('resources')->nullable();
            $table->boolean('data_consent')->nullable();
            $table->string('talla_pantalon', 100)->nullable();
            $table->string('talla_camisa', 100)->nullable();
            $table->string('talla_zapatos', 100)->nullable();
            $table->string('appointment_mode', 20)->nullable();
            $table->string('appointment_location', 10)->nullable();
            $table->text('additional_instructions')->nullable();
            $table->string('teams_link', 200)->nullable();
            $table->integer('recruiter_id')->nullable();
            $table->foreign('recruitment_id')->references('id')->on('recruitment');
        });

        Schema::create('recruitment_items', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('recruitment_id');
            $table->integer('user_id');
            $table->text('content');
            $table->string('kind', 20)->nullable();
        });

        Schema::create('recruitment_resources', function (Blueprint $table) {
            $table->id();
            $table->integer('stage');
            $table->string('kind', 20)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('category', 100)->nullable();
        });

        Schema::create('screws', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('description', 255)->nullable();
            $table->string('category', 255)->nullable();
            $table->string('head', 255)->nullable();
            $table->string('screwdriver', 255)->nullable();
            $table->string('diameter', 255)->nullable();
            $table->string('item_length', 255)->nullable();
            $table->string('observation', 255)->nullable();
            $table->text('withs')->nullable();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nit', 100);
            $table->string('name', 100);
            $table->string('email', 50)->nullable();
            $table->string('sap', 100)->nullable();
            $table->date('date')->nullable();
            $table->unique(['name', 'email']);
        });

        Schema::create('suppliers_evaluation', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 100);
            $table->text('answers');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id');
            $table->text('notes')->nullable();
            $table->string('nit', 100)->nullable();
            $table->string('supplier', 100)->nullable();
        });

        Schema::create('suppliers_questions', function (Blueprint $table) {
            $table->id();
            $table->text('kind')->nullable();
            $table->text('question')->nullable();
            $table->string('process', 2)->nullable();
        });

        Schema::create('test', function (Blueprint $table) {
            $table->id();
            $table->string('created_at', 4);
            $table->timestamp('closed_at')->nullable();
            $table->string('kind', 10);
            $table->text('answers')->nullable();
            $table->integer('creator_id')->nullable();
            $table->string('status', 10);
            $table->text('users')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->decimal('self', 4, 2)->nullable();
            $table->decimal('leader', 4, 2)->nullable();
            $table->decimal('upward', 4, 2)->nullable();
            $table->decimal('peer', 4, 2)->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('done')->default(0);
            $table->integer('total')->default(0);
        });

        Schema::create('test_answers', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 100);
            $table->text('answers');
            $table->bigInteger('user_id');
            $table->bigInteger('tester_id');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('test_db', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('division', 100)->nullable();
            $table->string('charge', 100)->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->integer('kind')->nullable();
            $table->integer('grp')->nullable();
            $table->string('area', 100)->nullable();
            $table->integer('rd1')->nullable();
            $table->integer('rd2')->nullable();
            $table->boolean('active')->default(true);
        });

        Schema::create('test_plans', function (Blueprint $table) {
            $table->id();
            $table->text('plan')->nullable();
            $table->string('status', 10);
            $table->string('competency', 100)->nullable();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id')->nullable();
            $table->text('result')->nullable();
            $table->date('follow')->nullable();
        });

        Schema::create('test_plan_follow', function (Blueprint $table) {
            $table->id();
            $table->text('notes')->nullable();
            $table->date('follow')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('progress')->default(0);
            $table->integer('plan_id')->nullable();
            $table->foreign('plan_id')->references('id')->on('test_plans');
        });

        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 100)->nullable();
            $table->text('content')->nullable();
        });

        Schema::create('ticket_items', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->timestamp('date')->useCurrent();
            $table->text('notes')->nullable();
            $table->string('attends', 10)->nullable();
            $table->integer('ticket_id')->nullable();
            $table->string('complexity', 10)->nullable();
            $table->string('attendant', 100)->nullable();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('user_id');
            $table->text('kind');
            $table->text('priority');
            $table->text('description');
            $table->timestamp('started_at')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('status', 10)->nullable();
            $table->text('facility');
            $table->integer('asset_id')->nullable();
            $table->string('url', 100)->nullable();
        });

        Schema::create('token_auth', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('user_agent', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('password_hash', 255);
            $table->string('selector_hash', 255);
            $table->boolean('is_expired')->default(false);
            $table->timestamp('expiry_date')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('wo', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->text('project');
            $table->integer('user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->string('es_id', 250)->nullable();
            $table->unique('code');
        });

        Schema::create('wo_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->string('wo_code', 50);
            $table->text('fuc')->nullable();
            $table->integer('qty');
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wo_items');
        Schema::dropIfExists('wo');
        Schema::dropIfExists('token_auth');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_items');
        Schema::dropIfExists('test_questions');
        Schema::dropIfExists('test_plan_follow');
        Schema::dropIfExists('test_plans');
        Schema::dropIfExists('test_db');
        Schema::dropIfExists('test_answers');
        Schema::dropIfExists('test');
        Schema::dropIfExists('suppliers_questions');
        Schema::dropIfExists('suppliers_evaluation');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('screws');
        Schema::dropIfExists('recruitment_resources');
        Schema::dropIfExists('recruitment_items');
        Schema::dropIfExists('recruitment_candidates');
        Schema::dropIfExists('recruitment');
        Schema::dropIfExists('preoperational_questions');
        Schema::dropIfExists('preoperational_items');
        Schema::dropIfExists('preoperational_activities');
        Schema::dropIfExists('preoperational');
        Schema::dropIfExists('preop_questions');
        Schema::dropIfExists('preop_items');
        Schema::dropIfExists('preop');
        Schema::dropIfExists('personal_data_updates');
        Schema::dropIfExists('mntp_items');
        Schema::dropIfExists('mnt_preventive_form');
        Schema::dropIfExists('mnt_preventive');
        Schema::dropIfExists('mnt_items');
        Schema::dropIfExists('mnt');
        Schema::dropIfExists('matrices_db');
        Schema::dropIfExists('matrices');
        Schema::dropIfExists('log');
        Schema::dropIfExists('job_profile_items');
        Schema::dropIfExists('job_profiles');
        Schema::dropIfExists('it_items');
        Schema::dropIfExists('it');
        Schema::dropIfExists('inspections');
        Schema::dropIfExists('inspection_items');
        Schema::dropIfExists('inspection_automations');
        Schema::dropIfExists('inspection_activities');
        Schema::dropIfExists('infraimprovement_events');
        Schema::dropIfExists('infraimprovement');
        Schema::dropIfExists('improvement_causes');
        Schema::dropIfExists('improvement_activities');
        Schema::dropIfExists('improvement');
        Schema::dropIfExists('hr_db');
        Schema::dropIfExists('extrusion_db');
        Schema::dropIfExists('equipment_register');
        Schema::dropIfExists('equipment_db');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('epp_register');
        Schema::dropIfExists('epp_db');
        Schema::dropIfExists('epp');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('development_task');
        Schema::dropIfExists('development');
        Schema::dropIfExists('cbm_items');
        Schema::dropIfExists('cbm');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_events');
        Schema::dropIfExists('asset_documents');
        Schema::dropIfExists('permissions');
    }
};
