<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function download()
    {
        return Excel::download(new class() implements FromArray, WithEvents, WithHeadings {

            public function array(): array
            {
                return [];
            }

            public function headings(): array
            {
                return [
                    [
                        '员工信息', '销售维度',
                    ],
                    [
                        '', '销售额', '销量', '分销佣金',
                    ],
                ];
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        $cells = ['A1:A2', 'B1:D1'];
                        foreach ($cells as $v) {
                            //设置区域单元格垂直居中
                            $event->sheet->getDelegate()->getStyle($v)->getAlignment()->setVertical('center');
                            //设置区域单元格水平居中
                            $event->sheet->getDelegate()->getStyle($v)->getAlignment()->setHorizontal('center');
                            $event->sheet->getDelegate()->mergeCells($v);
                        }
                    },
                ];
            }
        }, '00.xlsx');
    }
}