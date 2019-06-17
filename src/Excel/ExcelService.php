<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/11/28
 * Time: 12:40 PM
 */

namespace Cly\Excel;


use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cly\Spreadsheet\NoScienceValueBinder;

class ExcelService
{
    public function spreadSheet($path, $data)
    {
        //=开头的加空字符
        foreach ($data as &$row) {
            foreach ($row as &$value) {
                if (strpos($value, '=') === 0) {
                    $value = "'" . $value;
                }
            }
        }

        Cell::setValueBinder(new NoScienceValueBinder());
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data);
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);
    }

    public function phpXLSXWriter($path, $data)
    {
        $row1 = $data[1] ?? null;
        $row0 = $row1 ?: $data[0] ?? null;
        if (!$row0) {
            throw new \Exception('no data');
        }

        $header = [];
        $i = 1;
        foreach ($row0 as $col) {
            $colName = 'c' . $i++;
            $header[$colName] = 'string';
        }

        $writer = new \XLSXWriter();
        $writer->writeSheetHeader('Sheet1', $header);
        $writer->writeSheet($data);
        $writer->writeToFile($path);
    }

    public function csv($path, $data)
    {
        $fp = fopen($path, 'w');
        fputs($fp, chr(239) . chr(187) . chr(191));
        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }
            fputcsv($fp, $row);
        }
        fclose($fp);
    }
}