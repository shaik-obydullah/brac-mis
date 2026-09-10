<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericExport implements FromCollection, WithHeadings
{
    private string $modelClass;
    private array $columns;

    public function __construct(string $modelClass, array $columns)
    {
        $this->modelClass = $modelClass;
        $this->columns = $columns;
    }

    public function collection()
    {
        return $this->modelClass::all($this->columns);
    }

    public function headings(): array
    {
        return array_map(fn($col) => str_replace('_', ' ', ucfirst($col)), $this->columns);
    }
}
