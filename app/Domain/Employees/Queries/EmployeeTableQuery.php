<?php

declare(strict_types=1);

namespace App\Domain\Employees\Queries;

use App\Domain\Employees\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class EmployeeTableQuery
{
    /** @var Builder<Employee> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<Employee> $query */
        $query = Employee::query()
            ->select('employees.*')
            ->addSelect([
                'latest_update' => \App\Domain\Employees\Models\PersonalDataUpdate::select('created_at')
                    ->whereColumn('employee_id', 'employees.id')
                    ->latest()
                    ->limit(1)
            ]);
        
        // Note: Legacy used hr_db and job_profiles joins. 
        // I'll assume relations if they exist OR just use join if I can't find models for those yet.
        // Let's check if JobProfile and HrDb exist.
        $this->query = $query;
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     */
    public function apply(array $filters, array $sorts): self
    {
        return $this->filter($filters)->sort($sorts);
    }

    /** @param array<string, mixed> $filters */
    private function filter(array $filters): self
    {
        foreach ($filters as $field => $value) {
            if (blank($value)) continue;

            match ((string) $field) {
                'id'         => $this->query->where('employees.id', 'ilike', "%$value%"),
                'name'       => $this->query->where('employees.name', 'ilike', "%$value%"),
                'start_date' => $this->applyDateFilter((string) $value, 'start_date'),
                'status'     => $this->query->where('status', $value),
                default      => $this->query->where("employees.$field", 'ilike', "%$value%"),
            };
        }
        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        if (empty($sorts)) {
            $this->query->orderByDesc('status')->orderByDesc('start_date');
            return $this;
        }

        foreach ($sorts as $field => $dir) {
            $this->query->orderBy((string) $field, $dir);
        }
        return $this;
    }

    /** @return LengthAwarePaginator<Employee> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }

    private function applyDateFilter(string $value, string $column): void
    {
        str_contains($value, ' to ')
            ? $this->query->whereBetween($column, explode(' to ', $value))
            : $this->query->whereDate($column, $value);
    }
}
