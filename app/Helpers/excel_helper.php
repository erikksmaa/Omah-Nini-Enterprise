<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

if (!function_exists('exportToExcel')) {
    function exportToExcel($data, $headers, $title, $filename, $subtitle = null, $additionalInfo = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Bersihkan sheet title dari karakter tidak valid
        // Karakter yang tidak diizinkan: * : / \ ? [ ]
        $cleanTitle = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $title);
        $cleanTitle = substr($cleanTitle, 0, 31);
        $sheet->setTitle($cleanTitle ?: 'Laporan');

        // Set page orientation
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setRight(0.5);

        // ============ HEADER SECTION ============
        $row = 1;

        // Logo / Title Utama
        $sheet->setCellValue('A' . $row, 'OMAH NINI ENTERPRISE');
        $sheet->mergeCells('A' . $row . ':' . $sheet->getHighestColumn() . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A' . $row)->getFont()->getColor()->setARGB('FF2C3E50');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Alamat
        $sheet->setCellValue('A' . $row, 'Jl. Contoh No. 123, Kota | Telp: (021) 1234567 | Email: info@omahnini.com');
        $sheet->mergeCells('A' . $row . ':' . $sheet->getHighestColumn() . $row);
        $sheet->getStyle('A' . $row)->getFont()->setSize(9);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Garis pemisah
        $sheet->getStyle('A' . $row . ':' . $sheet->getHighestColumn() . $row)->getBorders()->getTop()
            ->setBorderStyle(Border::BORDER_THIN);
        $row++;

        // Judul Laporan
        $sheet->setCellValue('A' . $row, $title);
        $sheet->mergeCells('A' . $row . ':' . $sheet->getHighestColumn() . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Subtitle (opsional)
        if ($subtitle) {
            $sheet->setCellValue('A' . $row, $subtitle);
            $sheet->mergeCells('A' . $row . ':' . $sheet->getHighestColumn() . $row);
            $sheet->getStyle('A' . $row)->getFont()->setSize(11);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        // Informasi Tambahan (periode, tanggal export, dll)
        foreach ($additionalInfo as $info) {
            $sheet->setCellValue('A' . $row, $info);
            $sheet->mergeCells('A' . $row . ':' . $sheet->getHighestColumn() . $row);
            $sheet->getStyle('A' . $row)->getFont()->setSize(10);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $row++;

        // ============ HEADER TABEL ============
        $headerRow = $row;
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $headerRow, $header);
            $sheet->getStyle($col . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle($col . $headerRow)->getFont()->setSize(11);
            $sheet->getStyle($col . $headerRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF2C3E50');
            $sheet->getStyle($col . $headerRow)->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($col . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $headerRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
        }

        // ============ DATA ============
        $startDataRow = $headerRow + 1;
        $currentRow = $startDataRow;

        foreach ($data as $item) {
            $col = 'A';
            foreach ($item as $value) {
                $sheet->setCellValue($col . $currentRow, $value);

                // Format currency untuk kolom yang mengandung angka besar (total, harga)
                if (is_numeric($value) && $value > 1000) {
                    $sheet->getStyle($col . $currentRow)->getNumberFormat()
                        ->setFormatCode('"Rp" #,##0');
                }

                $col++;
            }
            $currentRow++;
        }

        // ============ STYLING TABEL ============
        $lastColumn = chr(ord('A') + count($headers) - 1);
        $tableRange = 'A' . $headerRow . ':' . $lastColumn . ($currentRow - 1);

        // Border untuk seluruh tabel
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Border tebal untuk header
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM);

        // Zebra styling (warna bergantian untuk baris data)
        for ($i = $startDataRow; $i < $currentRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':' . $lastColumn . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8F9FA');
            }
        }

        // Alignment untuk data
        for ($i = $startDataRow; $i < $currentRow; $i++) {
            // Rata kiri untuk kolom teks
            $sheet->getStyle('A' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('B' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('C' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Rata kanan untuk kolom angka
            if (count($headers) > 3) {
                $sheet->getStyle(chr(ord('A') + 3) . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            if (count($headers) > 4) {
                $sheet->getStyle(chr(ord('A') + 4) . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        }

        // ============ AUTO SIZE COLUMNS ============
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            // Set max width untuk kolom tertentu
            if ($col == 'A')
                $sheet->getColumnDimension($col)->setWidth(8);
            if ($col == 'B')
                $sheet->getColumnDimension($col)->setWidth(18);
            if ($col == 'C')
                $sheet->getColumnDimension($col)->setWidth(20);
        }

        // ============ FOOTER ============
        $footerRow = $currentRow + 2;
        $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . date('d-m-Y H:i:s'));
        $sheet->mergeCells('A' . $footerRow . ':' . $lastColumn . $footerRow);
        $sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
        $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true);
        $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $footerRow++;
        $sheet->setCellValue('A' . $footerRow, 'Dicetak oleh: ' . (session()->get('username') ?? 'System'));
        $sheet->mergeCells('A' . $footerRow . ':' . $lastColumn . $footerRow);
        $sheet->getStyle('A' . $footerRow)->getFont()->setSize(9);
        $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true);
        $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ============ FREEZE PANE ============
        $sheet->freezePane('A' . ($headerRow + 1));

        // ============ OUTPUT ============
        $filename = $filename . '_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    if (!function_exists('exportToExcelWithMultipleSheets')) {
        function exportToExcelWithMultipleSheets($data1, $headers1, $data2, $headers2, $title, $filename, $additionalInfo = [])
        {
            $spreadsheet = new Spreadsheet();

            // ============ SHEET 1: Ringkasan Retur ============
            $sheet1 = $spreadsheet->getActiveSheet();

            // Bersihkan sheet title dari karakter tidak valid
            $cleanTitle1 = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', 'Ringkasan Retur');
            $sheet1->setTitle(substr($cleanTitle1, 0, 31));

            // Header
            $row = 1;
            $sheet1->setCellValue('A' . $row, 'OMAH NINI ENTERPRISE');
            $sheet1->mergeCells('A' . $row . ':' . $sheet1->getHighestColumn() . $row);
            $sheet1->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet1->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;

            $sheet1->setCellValue('A' . $row, $title);
            $sheet1->mergeCells('A' . $row . ':' . $sheet1->getHighestColumn() . $row);
            $sheet1->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet1->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;

            foreach ($additionalInfo as $info) {
                $sheet1->setCellValue('A' . $row, $info);
                $sheet1->mergeCells('A' . $row . ':' . $sheet1->getHighestColumn() . $row);
                $sheet1->getStyle('A' . $row)->getFont()->setSize(10);
                $sheet1->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
            $row++;

            // Header tabel
            $headerRow = $row;
            $col = 'A';
            foreach ($headers1 as $header) {
                $sheet1->setCellValue($col . $headerRow, $header);
                $sheet1->getStyle($col . $headerRow)->getFont()->setBold(true);
                $sheet1->getStyle($col . $headerRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF2C3E50');
                $sheet1->getStyle($col . $headerRow)->getFont()->getColor()->setARGB('FFFFFFFF');
                $col++;
            }

            // Data
            $startDataRow = $headerRow + 1;
            $currentRow = $startDataRow;
            foreach ($data1 as $item) {
                $col = 'A';
                foreach ($item as $value) {
                    $sheet1->setCellValue($col . $currentRow, $value);
                    if (is_numeric($value) && $value > 1000) {
                        $sheet1->getStyle($col . $currentRow)->getNumberFormat()
                            ->setFormatCode('"Rp" #,##0');
                    }
                    $col++;
                }
                $currentRow++;
            }

            // Styling
            $lastColumn = chr(ord('A') + count($headers1) - 1);
            $sheet1->getStyle('A' . $headerRow . ':' . $lastColumn . ($currentRow - 1))->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            foreach (range('A', $lastColumn) as $col) {
                $sheet1->getColumnDimension($col)->setAutoSize(true);
            }

            // ============ SHEET 2: Detail Produk Retur ============
            $sheet2 = $spreadsheet->createSheet();
            $cleanTitle2 = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', 'Detail Produk Retur');
            $sheet2->setTitle(substr($cleanTitle2, 0, 31));

            $row = 1;
            $sheet2->setCellValue('A' . $row, 'DETAIL PRODUK RETUR');
            $sheet2->mergeCells('A' . $row . ':' . $sheet2->getHighestColumn() . $row);
            $sheet2->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet2->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row += 2;

            $col = 'A';
            foreach ($headers2 as $header) {
                $sheet2->setCellValue($col . $row, $header);
                $sheet2->getStyle($col . $row)->getFont()->setBold(true);
                $sheet2->getStyle($col . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF10B981');
                $sheet2->getStyle($col . $row)->getFont()->getColor()->setARGB('FFFFFFFF');
                $col++;
            }

            $dataRow = $row + 1;
            $currentRow = $dataRow;
            foreach ($data2 as $item) {
                $col = 'A';
                foreach ($item as $value) {
                    $sheet2->setCellValue($col . $currentRow, $value);
                    if (is_numeric($value) && $value > 1000) {
                        $sheet2->getStyle($col . $currentRow)->getNumberFormat()
                            ->setFormatCode('"Rp" #,##0');
                    }
                    $col++;
                }
                $currentRow++;
            }

            $lastColumn2 = chr(ord('A') + count($headers2) - 1);
            $sheet2->getStyle('A' . $row . ':' . $lastColumn2 . ($currentRow - 1))->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            foreach (range('A', $lastColumn2) as $col) {
                $sheet2->getColumnDimension($col)->setAutoSize(true);
            }

            // Output
            $filename = $filename . '_' . date('Y-m-d') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        }
    }
}