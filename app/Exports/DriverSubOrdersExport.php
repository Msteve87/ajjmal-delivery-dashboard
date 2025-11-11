<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DriverSubOrdersExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected Collection $records;

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return collect(); // handled manually in AfterSheet
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Get settlement ID from first record
                $settlementId = $this->records->first()->settlement_id ?? '-';

                // Settlement ID top-left
                $sheet->setCellValue('A1', "Settlement ID: {$settlementId}");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => '000000']],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $row = 3; // leave space after settlement ID
    
                $grouped = $this->records->groupBy(fn($record) => $record->order->reference);

                foreach ($grouped as $ref => $records) {
                    if ($row !== 3)
                        $row += 2;

                    // Reference label
                    $sheet->setCellValue("A{$row}", "#{$ref}");
                    $sheet->getStyle("A{$row}")
                        ->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FF000000');
                    $sheet->getStyle("A{$row}")
                        ->getAlignment()->setHorizontal('left');

                    $row++;

                    // Headers
                    $headers = [
                        'Tracking ID',
                        'Seller Name',
                        'Base Price',
                        'Shipping Price',
                        'Total',
                        'Payment Method',
                        'Status',
                        'Delivered At',
                    ];

                    $col = 'A';
                    foreach ($headers as $header) {
                        $sheet->setCellValue("{$col}{$row}", $header);
                        $col++;
                    }

                    // Header style (simple, no colors)
                    $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $row++;

                    // Sub-order rows
                    foreach ($records as $record) {
                        $sheet->fromArray([
                            [
                                $record->tracking_id,
                                $record->products[0]['details']['seller']['name'] ?? '-',
                                $record->base_price,
                                $record->shipping_price,
                                $record->total,
                                $record->order->payment_method ?? '-',
                                $record->subOrderStatus?->name ?? '-',
                                $record->delivered_at
                                ? \Carbon\Carbon::parse($record->delivered_at)->format('Y-m-d H:i')
                                : '-',
                            ]
                        ], null, "A{$row}");
                        $row++;
                    }

                    // Borders + alignment
                    $start = $row - count($records) - 1;
                    $end = $row - 1;
                    $sheet->getStyle("A{$start}:H{$end}")
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->getStyle("A{$start}:H{$end}")
                        ->getAlignment()->setHorizontal('center')
                        ->setVertical('center');
                }

                // Auto-size all columns
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
