<?php

namespace Modules\Entry\Exports;

use Modules\Entry\Entities\BulkSavingsEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BulkSavingsEntryExport implements FromCollection, WithHeadings
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get()->map(function ($entry) {
            return [
                'ID' => $entry->id,
                'Officer' => $entry->savings_officer->first_name . ' ' . $entry->savings_officer->last_name,
                'Created By' => $entry->created_by->first_name . ' ' . $entry->created_by->last_name,
                'Items' => $entry->items->count(),
                'Status' => $entry->status,
                'Created At' => $entry->created_at->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Officer',
            'Created By',
            'Items',
            'Status',
            'Created At',
        ];
    }
}
