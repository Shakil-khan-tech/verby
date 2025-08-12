<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use Maatwebsite\Excel\Events\AfterSheet;

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

        $this->lastColumnIndex = 2 + ($this->period->count()) + 2; // Employee + Datum + days + Total + %
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
                    $zeitTime = $employee['work_seconds'] / 3600;
                    $zeitTimeTotalTime = number_format($zeitTime, 2, '.', '');
                    $requiredTotalTime = ($employee['function'] == 0)
                        ? round((($employee['depas'] + $employee['restants']) * 3) / 60, 2)
                        : round((($employee['depas'] * 20 + $employee['restants'] * 10) / 60), 2);

                    $difference = $requiredTotalTime - $zeitTimeTotalTime;

                    $formattedPercentage = '0%';
                    if ($zeitTime > 0) {
                        $percentage = ($difference / $zeitTime) * 100;
                        $sign = $percentage >= 0 ? '+' : '';
                        $formattedPercentage = $sign . number_format($percentage, 2) . '%';
                    }

                    // Depa Row
                    $rowDepa = [$employee['fullname'], 'Depa'];
                    foreach ($this->period as $day) {
                        $dayKey = $day->format('d.m.Y');
                        $daily = $employee['daily_data'][$dayKey] ?? [];
                        $dailyDepas = $daily['depas'] == 0 ? '0' : $daily['depas'];
                        $rowDepa[] = $dailyDepas;
                    }
                    $rowDepa[] = $employee['depas'] ?? '0';
                    $rowDepa[] = ''; // %
                    $data->push($rowDepa);

                    // Restant Row
                    $rowRest = ['', 'Restant'];
                    foreach ($this->period as $day) {
                        $dayKey = $day->format('d.m.Y');
                        $daily = $employee['daily_data'][$dayKey] ?? [];
                        $dailyRestant = $daily['restants'] == 0 ? '0' : $daily['restants'];
                        $rowRest[] = $dailyRestant;
                    }
                    $rowRest[] = $employee['restants'] ?? '0';
                    $rowRest[] = '';
                    $data->push($rowRest);

                    // Time Row
                    $rowTime = ['', 'Time.'];
                    foreach ($this->period as $day) {
                        $dayKey = $day->format('d.m.Y');
                        $daily = $employee['daily_data'][$dayKey] ?? [];
                        $rowTime[] = isset($daily['work_seconds']) ? number_format(($daily['work_seconds'] / 3600), 2) : '';
                    }
                    $rowTime[] = $zeitTimeTotalTime;
                    $rowTime[] = '';
                    $data->push($rowTime);

                    // Budget Row
                    $rowBudget = ['', 'Budget'];
                    foreach ($this->period as $day) {
                        $dayKey = $day->format('d.m.Y');
                        $daily = $employee['daily_data'][$dayKey] ?? [];
                        $depas = $daily['depas'] ?? 0;
                        $restants = $daily['restants'] ?? 0;
                        $workSeconds = $daily['work_seconds'] ?? 0;
                        $totalDailyTime = $workSeconds / 3600;
                        $budget = $employee['function'] == 0
                            ? round((($depas + $restants) * 3) / 60, 2)
                            : round((($depas * 20 + $restants * 10) / 60), 2);
                        $diff = number_format($budget - $totalDailyTime, 2);
                        $rowBudget[] = $diff;
                    }
                    $rowBudget[] = number_format($difference, 2);
                    $rowBudget[] = $formattedPercentage;
                    $data->push($rowBudget);
                }

                $data->push([]); // spacer
            }
        }

        return $data;
    }

    public function headings(): array
    {
        $header = ['Employees', 'Datum'];
        foreach ($this->period as $day) {
            $header[] = $day->format('d');
        }
        $header[] = 'Total';
        $header[] = '%';
        return [$header];
    }

    public function title(): string
    {
        return 'Budget Performance';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $this->lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'F2F2F2'],
            ],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $budgetColStartIndex = 3; // C
                $budgetColEndIndex = $this->lastColumnIndex - 2; // Before Total and %
                $totalDiffCol = $this->getColumnLetter($this->lastColumnIndex - 1);
                $percentCol = $this->getColumnLetter($this->lastColumnIndex);

                for ($row = 2; $row <= $highestRow; $row++) {
                    $employeeName = $sheet->getCell("A$row")->getValue();
                    $datum = $sheet->getCell("B$row")->getValue();

                    // ✅ Highlight function name rows
                    if (!empty($employeeName) && empty($datum)) {
                        $sheet->getStyle("A$row:" . $this->lastColumn . $row)->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => '4472C4'],
                            ],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        ]);
                    }

                    // ✅ Budget row color logic
                    if (trim($datum) === 'Budget') {
                        for ($col = $budgetColStartIndex; $col <= $budgetColEndIndex; $col++) {
                            $colLetter = $this->getColumnLetter($col);
                            $cell = $sheet->getCell("$colLetter$row");
                            $val = $cell->getValue();

                            if (is_numeric($val)) {
                                $color = $val >= 0 ? Color::COLOR_GREEN : Color::COLOR_RED;
                                $sheet->getStyle("$colLetter$row")->getFont()->getColor()->setARGB($color);
                            }
                        }

                        $budgetDiffCell = $sheet->getCell("$totalDiffCol$row")->getValue();
                        if (is_numeric($budgetDiffCell)) {
                            $color = $budgetDiffCell >= 0 ? Color::COLOR_GREEN : Color::COLOR_RED;
                            $sheet->getStyle("$totalDiffCol$row")->getFont()->getColor()->setARGB($color);
                        }

                        $percentCell = $sheet->getCell("$percentCol$row")->getValue();
                        if (preg_match('/([-+]?[0-9]*\.?[0-9]+)%/', $percentCell, $matches)) {
                            $val = floatval($matches[1]);
                            $color = $val >= 0 ? Color::COLOR_GREEN : Color::COLOR_RED;
                            $sheet->getStyle("$percentCol$row")->getFont()->getColor()->setARGB($color);
                        }
                    }
                }
            }
        ];
    }
}
