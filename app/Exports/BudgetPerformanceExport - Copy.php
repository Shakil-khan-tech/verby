<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BudgetPerformanceExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents
{
    protected $device, $period, $matrix, $title, $description, $functions;
    protected $lastColumnIndex, $lastColumn;

    public function __construct($device, $period, $matrix, $title, $description, $functions)
    {
        $this->device = $device;
        $this->period = $period;
        $this->matrix = $matrix;
        $this->title = $title;
        $this->description = $description;
        $this->functions = $functions;

        $this->lastColumnIndex = 1 + ($this->period->count() * 4) + 5;
        $this->lastColumn = $this->getColumnLetter($this->lastColumnIndex);
    }

    protected function getColumnLetter($index)
    {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26);
        }
        return $letter;
    }

    public function collection()
    {
        $data = collect();
        $grouped = collect($this->matrix)->groupBy('function');

        foreach ($this->functions as $fun => $functionName) {
            $employees = $grouped->get($fun, collect());

            if ($employees->count()) {
                $data->push([$functionName]);

                foreach ($employees as $employee) {
                    $row = [$employee['fullname']];

                    foreach ($this->period as $day) {
                        $dayKey = $day->format('d.m.Y');
                        $daily = $employee['daily_data'][$dayKey] ??  [
                            'depas' => 0,
                            'restants' => 0,
                            'work_seconds' => 0
                        ];

                        $depas = $daily['depas'] ?? 0;
                        $restants = $daily['restants'] ?? 0;
                        $workSeconds = $daily['work_seconds'] ?? 0;

                        $totalDailyTime = number_format($workSeconds / 3600, 2);
                        $budget = $employee['function'] == 0
                            ? round((($depas + $restants) * 3) / 60, 2)
                            : round((($depas * 20 + $restants * 10) / 60), 2);
                        $diff = number_format($budget - $totalDailyTime, 2);

                        $row = array_merge($row, [
                            $depas == 0 ? '0' : $depas,
                            $restants == 0 ? '0' : $restants,
                            $totalDailyTime,
                            $diff
                        ]);
                    }

                    $depas = $employee['depas'] ?? 0;
                    $restants = $employee['restants'] ?? 0;
                    $workSeconds = $employee['work_seconds'] ?? 0;

                    $zeitTime = $workSeconds / 3600;
                    $zeitTimeTotal = number_format($zeitTime, 2);

                    $requiredTotal = $employee['function'] == 0
                        ? round((($depas + $restants) * 3) / 60, 2)
                        : round((($depas * 20 + $restants * 10) / 60), 2);
                    $difference = number_format($requiredTotal - $zeitTimeTotal, 2);

                    $percentage = $zeitTime > 0 ? (($requiredTotal - $zeitTime) / $zeitTime) * 100 : 0;
                    $sign = $percentage >= 0 ? '+' : '';
                    $formattedPercentage = $sign . number_format($percentage, 2) . '%';

                    $row = array_merge($row, [
                        $depas == 0 ? '0' : $depas,
                        $restants == 0 ? '0' : $restants,
                        $zeitTimeTotal,
                        $difference,
                        $formattedPercentage
                    ]);

                    $data->push($row);
                }

                $data->push([]); // Blank row after each function group
            }
        }
        return $data;
    }

    public function headings(): array
    {
        $headerRow1 = ['Employee'];
        $headerRow2 = [''];

        foreach ($this->period as $day) {
            $headerRow1[] = $day->format('d');
            $headerRow1 = array_merge($headerRow1, ['', '', '']);
            $headerRow2 = array_merge($headerRow2, ['D', 'R', 'Z', 'B']);
        }

        $headerRow1 = array_merge($headerRow1, ['Total', '', '', '', '']);
        $headerRow2 = array_merge($headerRow2, ['Depa', 'Restant', 'Zeit', 'Budget', '%']);

        return [$headerRow1, $headerRow2];
    }

    public function title(): string
    {
        return 'Budget Performance';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $this->lastColumn . '2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                // Merge headers
                $colIndex = 2;
                foreach ($this->period as $day) {
                    $start = $this->getColumnLetter($colIndex);
                    $end = $this->getColumnLetter($colIndex + 3);
                    $sheet->mergeCells("$start" . "1:$end" . "1");
                    $colIndex += 4;
                }

                $startTotal = $this->getColumnLetter($colIndex);
                $endTotal = $this->getColumnLetter($colIndex + 4);
                $sheet->mergeCells("$startTotal" . "1:$endTotal" . "1");

                // Style function rows
                $row = 3;
                foreach ($this->functions as $fun => $functionName) {
                    $employees = collect($this->matrix)->where('function', $fun);
                    if ($employees->count()) {
                        $sheet->mergeCells("A$row:" . $this->lastColumn . "$row");
                        $sheet->getStyle("A$row")->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'D9D9D9'],
                            ],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                        ]);
                        $row += $employees->count() + 1;
                    }
                }

                // Apply red/green text color to budget difference & percentage columns
                $diffCol = $this->getColumnLetter($this->lastColumnIndex - 1);
                $percentCol = $this->getColumnLetter($this->lastColumnIndex);

                // Get all "B" columns from daily data (every 4th column starting from column 5)
                $bColumns = [];
                $startCol = 5; // First "B" column is E (D=2, R=3, Z=4, B=5)
                for ($i = $startCol; $i < $this->lastColumnIndex - 5; $i += 4) {
                    $bColumns[] = $this->getColumnLetter($i);
                }

                // Add the total difference column
                $bColumns[] = $diffCol;

                for ($r = 3; $r <= $highestRow; $r++) {
                    foreach ($bColumns as $col) {
                        $cell = $sheet->getCell("$col$r");
                        $val = $cell->getValue();

                        if (is_numeric($val)) {
                            $value = (float) $val;
                            $color = $value >= 0 ? Color::COLOR_GREEN : Color::COLOR_RED;
                            $sheet->getStyle("$col$r")->getFont()->getColor()->setARGB($color);
                        }
                    }

                    // Handle percentage column separately
                    $cell = $sheet->getCell("$percentCol$r");
                    $val = $cell->getValue();
                    if (preg_match('/([-+]?\d+(\.\d+)?)%/', $val, $matches)) {
                        $value = (float) $matches[1];
                        $color = $value >= 0 ? Color::COLOR_GREEN : Color::COLOR_RED;
                        $sheet->getStyle("$percentCol$r")->getFont()->getColor()->setARGB($color);
                    }
                }
            }
        ];
    }
}
