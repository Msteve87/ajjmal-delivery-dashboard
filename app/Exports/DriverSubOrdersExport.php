<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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

                // === Logo at top-center ===
                // $logo = new Drawing();
                // $logo->setName('Logo');
                // $logo->setDescription('Company Logo');
                // $logo->setPath(public_path('../public/images/logo.jpg'));
    
                // $logo->setHeight(560);
                // $logo->setCoordinates('E1');
                // $logo->setWorksheet($sheet);
                // $sheet->getRowDimension(1)->setRowHeight(70);
    
                // Settlement ID top-left (row 2, after logo)
                $settlementId = $this->records->first()->settlement_id ?? '-';
                $sheet->setCellValue('E2', "Settlement ID: {$settlementId}");
                $sheet->getStyle('E2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 32, 'color' => ['argb' => '000000']],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $row = 4; // leave space after settlement ID
                $grouped = $this->records->groupBy(fn($record) => $record->order->reference);

                foreach ($grouped as $ref => $records) {
                    if ($row !== 4)
                        $row += 2;

                    // Reference label
                    $sheet->setCellValue("A{$row}", "#{$ref}");
                    $sheet->getStyle("A{$row}")
                        ->getFont()->setBold(true)->setSize(34)->getColor()->setARGB('FF000000');
                    $sheet->getStyle("A{$row}")
                        ->getAlignment()->setHorizontal('left');

                    $row++;

                    // Table headers
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

                    // Header style (gray, bold, bigger font, centered)
                    $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 30],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFD9D9D9'],
                        ],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(40);
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

                        $sheet->getRowDimension($row)->setRowHeight(30);
                        $sheet->getStyle("A{$row}:H{$row}")->getFont()->setSize(18);
                        $row++;
                    }

                    // Borders + alignment for the table
                    $start = $row - count($records) - 1;
                    $end = $row - 1;
                    $sheet->getStyle("A{$start}:H{$end}")
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->getStyle("A{$start}:H{$end}")
                        ->getAlignment()->setHorizontal('center')
                        ->setVertical('center');
                }

                // Grand total
                $grandTotal = $this->records->sum(fn($record) => $record->total);
                $row += 1;
                $sheet->setCellValue("E{$row}", 'Grand Total:');
                $sheet->setCellValue("F{$row}", $grandTotal);
                $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 40],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(40);

                // Auto-size columns
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
