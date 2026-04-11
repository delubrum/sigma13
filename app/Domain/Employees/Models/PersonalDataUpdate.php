<?php

declare(strict_types=1);

namespace App\Domain\Employees\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'employee_id', 'status', 'photo_path', 'created_at',
    'tipo_sangre', 'lugar_nacimiento', 'fecha_nacimiento', 'sexo',
    'licencia_conduccion', 'categoria_licencia', 'libreta_militar', 'tiene_vehiculo',
    'tipo_vehiculo', 'placa_vehiculo', 'estado_civil', 'direccion', 'barrio',
    'municipio', 'departamento', 'estrato_socioeconomico', 'tiempo_vivienda',
    'tenencia_vivienda', 'telefono_fijo', 'celular', 'email', 'relacion_tecnoglass',
    'relacion_tecnoglass_detalle_json', 'ultimo_estudio_json', 'otros_estudios_list_json',
    'contacto_emergencia_json', 'hijos_json', 'nombre_conyuge', 'cedula_conyuge',
    'ocupacion_conyuge', 'email_conyuge', 'telefono_conyuge', 'numero_hijos',
    'tiene_carnet_arl', 'seguro_exequias', 'carnet_empresa', 'talla_pantalon',
    'talla_camisa', 'talla_zapatos', 'eps', 'fondo_pensiones', 'arl',
    'medico_presion_arterial', 'medico_diabetes', 'medico_alergias',
    'medico_otra_condicion', 'medico_observaciones'
])]
final class PersonalDataUpdate extends Model
{
    /** @var string */
    protected $table = 'personal_data_updates';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'relacion_tecnoglass_detalle_json' => 'array',
            'ultimo_estudio_json' => 'array',
            'otros_estudios_list_json' => 'array',
            'contacto_emergencia_json' => 'array',
            'hijos_json' => 'array',
        ];
    }

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
