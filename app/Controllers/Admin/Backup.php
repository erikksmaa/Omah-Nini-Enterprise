<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Backup extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Backup Database',
            'tables' => $this->getTableList(),
        ];

        return view('admin/backup/index', $data);
    }

    private function getTableList()
    {
        $tables = $this->db->query("SHOW TABLES")->getResultArray();
        $tableNames = [];
        foreach ($tables as $table) {
            $tableNames[] = array_values($table)[0];
        }
        return $tableNames;
    }

    public function export()
    {
        $isAjax = $this->request->isAJAX();

        if ($isAjax) {
            $json = $this->request->getJSON();
            $selectedTables = $json->tables ?? [];
        } else {
            $selectedTables = $this->request->getPost('tables');
        }

        if (empty($selectedTables)) {
            $selectedTables = $this->getTableList();
        }

        // Urutkan tabel (parent tables first)
        $orderedTables = $this->getOrderedTables($selectedTables);

        // Generate SQL dengan urutan yang benar
        $output = $this->generateSQL($orderedTables);

        $filename = 'backup_batik_' . date('Y-m-d_H-i-s') . '.sql';

        return $this->response
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Content-Length', strlen($output))
            ->setBody($output);
    }

    private function generateSQL($tables)
    {
        $output = '';

        $output .= "-- ----------------------------------------\n";
        $output .= "-- BATIK INVENTORY DATABASE BACKUP\n";
        $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- ----------------------------------------\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";  // ← TAMBAHKAN INI
        $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $output .= "START TRANSACTION;\n";
        $output .= "SET time_zone = \"+00:00\";\n\n";
        $output .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
        $output .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
        $output .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
        $output .= "/*!40101 SET NAMES utf8mb4 */;\n\n";

        foreach ($tables as $table) {
            $output .= "-- ----------------------------------------\n";
            $output .= "-- Table structure for table `{$table}`\n";
            $output .= "-- ----------------------------------------\n\n";

            $createTable = $this->db->query("SHOW CREATE TABLE `{$table}`")->getRow();
            $createTableSQL = $createTable->{'Create Table'};
            $output .= $createTableSQL . ";\n\n";

            $data = $this->db->query("SELECT * FROM `{$table}`")->getResultArray();

            if (!empty($data)) {
                $output .= "-- Dumping data for table `{$table}`\n\n";

                foreach ($data as $row) {
                    $columns = array_keys($row);
                    $values = [];

                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } elseif (is_numeric($value)) {
                            $values[] = $value;
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }

                    $columnsStr = '`' . implode('`, `', $columns) . '`';
                    $valuesStr = implode(', ', $values);
                    $output .= "INSERT INTO `{$table}` ({$columnsStr}) VALUES ({$valuesStr});\n";
                }
                $output .= "\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";  // ← TAMBAHKAN INI
        $output .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
        $output .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
        $output .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
        $output .= "COMMIT;\n";
        return $output;
    }

    /**
     * Get table order based on dependencies (parent tables first)
     */
    private function getOrderedTables($selectedTables)
    {
        // Tabel yang tidak memiliki foreign key (parent) harus duluan
        $parentTables = [];
        $childTables = [];

        foreach ($selectedTables as $table) {
            // Cek apakah tabel memiliki foreign key
            $fkQuery = $this->db->query("
            SELECT TABLE_NAME, REFERENCED_TABLE_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '{$table}'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->getResultArray();

            if (empty($fkQuery)) {
                // Tidak ada foreign key (parent table)
                $parentTables[] = $table;
            } else {
                // Memiliki foreign key (child table)
                $childTables[] = $table;
            }
        }

        // Gabungkan parent dulu, baru child
        return array_merge($parentTables, $childTables);
    }
}