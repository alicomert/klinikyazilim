<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OperationsImport implements ToCollection, WithHeadingRow
{
    /**
     * Collected rows from the Excel file with heading-based keys
     */
    public Collection $rows;

    /**
     * Handle the collection of rows provided by Laravel-Excel
     */
    public function collection(Collection $collection)
    {
        // Normalize keys to snake_case to reduce header variations
        $normalized = $collection->map(function ($row) {
            $normalizedRow = [];
            foreach ($row as $key => $value) {
                $k = strtolower(trim(str_replace([' ', '-', '.', '\t'], '_', $key)));
                $k = preg_replace('/__+/', '_', $k);
                $normalizedRow[$k] = $value;
            }
            return $normalizedRow;
        });
        $this->rows = $normalized;
    }
}