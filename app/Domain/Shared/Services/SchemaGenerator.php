<?php

declare(strict_types=1);

namespace App\Domain\Shared\Services;

use App\Domain\Shared\Data\Column;
use App\Domain\Shared\Data\Field;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;
use Spatie\LaravelData\Attributes\Validation\Required;

final class SchemaGenerator
{
    /**
     * @template T of \Spatie\LaravelData\Data
     *
     * @param  class-string<T>  $dataClass
     * @return list<Field>
     */
    public static function toFields(string $dataClass): array
    {
        $reflection = new ReflectionClass($dataClass);
        $fields = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            /** @var Field|null $field */
            $field = self::getAttribute($property, Field::class);

            if ($field instanceof Field && $field->hide) {
                continue;
            }

            // Omitir el campo ID por defecto en formularios si no se especifica #[Field]
            if ($property->getName() === 'id' && ! $field) {
                continue;
            }

            // Si no hay atributo, creamos una base
            if (! $field) {
                $field = new Field;
            }

            // Inyectar/Sobrescribir datos de Reflexión
            $field->name = $property->getName();
            $field->label = $field->label ?: Str::title(str_replace('_', ' ', $property->getName()));
            $field->type = $field->type === 'text' ? self::inferType($property, $field) : $field->type;
            $field->required = self::isRequired($property);

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * @template T of \Spatie\LaravelData\Data
     *
     * @param  class-string<T>  $dataClass
     * @return list<Column>
     */
    public static function toColumns(string $dataClass): array
    {
        $reflection = new ReflectionClass($dataClass);
        $columns = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            /** @var Column|null $column */
            $column = self::getAttribute($property, Column::class);
            if ($column instanceof Column && $column->hide) {
                continue;
            }
            if ($column instanceof Column && $column->visible === false) {
                continue;
            }

            if (! $column) {
                $column = new Column;
            }

            // Inyectar/Sobrescribir datos de Reflexión
            $column->field = $column->field ?: $property->getName();
            $column->title = $column->title ?: Str::title(str_replace('_', ' ', $property->getName()));

            // Valores por defecto si no se especifican
            $column->headerFilter ??= 'input';
            $column->headerFilterPlaceholder ??= 'Filtro...';

            $columns[] = $column;
        }

        return $columns;
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $attributeClass
     * @return T|null
     */
    private static function getAttribute(ReflectionProperty $property, string $attributeClass): ?object
    {
        $attributes = $property->getAttributes($attributeClass);

        /** @var T|null $instance */
        $instance = $attributes !== [] ? $attributes[0]->newInstance() : null;

        return $instance;
    }

    private static function isRequired(ReflectionProperty $property): bool
    {
        return $property->getAttributes(Required::class) !== [];
    }

    private static function inferType(ReflectionProperty $property, Field $field): string
    {
        if ($field->widget === 'sigma-file') {
            return 'file';
        }
        if ($field->widget === 'slimselect' || $field->options !== []) {
            return 'select';
        }

        $type = $property->getType();
        if ($type instanceof \ReflectionNamedType) {
            if (! $type->isBuiltin()) {
                $typeName = $type->getName();
                if (Str::contains($typeName, ['Carbon', 'DateTime'])) {
                    return 'date';
                }
            }

            return match ($type->getName()) {
                'int', 'float' => 'number',
                'bool' => 'checkbox',
                default => 'text',
            };
        }

        return 'text';
    }
}
