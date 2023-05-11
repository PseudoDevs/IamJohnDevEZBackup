<?php

namespace IamJohnDevEZBackup;

class BackupAndRestore
{
    private $pdo;
    private $export_file;
    private $import_file;

    public function setConfig($host, $user, $pass, $db, $export_file = null, $import_file = null)
    {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $this->pdo = new PDO($dsn, $user, $pass);
        $this->export_file = $export_file;
        $this->import_file = $import_file;
    }

    public function backupToCSV()
    {
        // Fetch data from database
        $data = $this->getData();

        // Open the export file
        $fp = fopen($this->export_file, 'w');

        // Write the header row
        fputcsv($fp, array_keys($data[0]));

        // Write the data rows
        foreach ($data as $row) {
            fputcsv($fp, $row);
        }

        // Close the export file
        fclose($fp);
    }

    public function backupToJSON()
    {
        // Fetch data from database
        $data = $this->getData();

        // Convert data to JSON
        $json_data = json_encode($data, JSON_PRETTY_PRINT);

        // Write data to the export file
        file_put_contents($this->export_file, $json_data);
    }

    public function backupToXLS()
    {
        // Fetch data from database
        $data = $this->getData();

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("BackupAndRestore")
            ->setLastModifiedBy("BackupAndRestore")
            ->setTitle("BackupAndRestore Data")
            ->setSubject("BackupAndRestore Data")
            ->setDescription("BackupAndRestore Data");

        // Add header row
        $header = array_keys($data[0]);
        $row = 1;
        foreach ($header as $col => $value) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
        }

        // Add data rows
        foreach ($data as $row => $values) {
            foreach ($values as $col => $value) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row + 2, $value);
            }
        }

        // Save the export file
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save($this->export_file);
    }

    public function restoreFromCSV($file)
    {
        // Open the import file
        $fp = fopen($file['tmp_name'], 'r');

        // Read the header row
        $header = fgetcsv($fp);

        // Read the data rows
        $values = array();
        while (($row = fgetcsv($fp)) !== false) {
            $values[] = $row;
        }

        // Close the import file
        fclose($fp);

        // Truncate the table
        $this->pdo->exec("TRUNCATE table_name");

        // Insert the data
        $stmt = $this->pdo->prepare("INSERT INTO table_name (" . implode(',', $header) . ") VALUES (" . implode(',', array_fill(0, count($header), '?')) . ")");
        foreach ($values as $row) {
            $stmt->execute($row);
        }
    }
    public function restoreFromJSON($file)
    {
        // Read the import file
        $json_data = file_get_contents($file['tmp_name']);

        // Convert JSON to data
        $data = json_decode($json_data, true);

        // Truncate the table
        $this->pdo->exec("TRUNCATE table_name");

        // Insert the data
        foreach ($data as $row) {
            $stmt = $this->pdo->prepare("INSERT INTO table_name (col1, col2, col3) VALUES (?, ?, ?)");
            $stmt->execute(array(
                $row['col1'],
                $row['col2'],
                $row['col3'],
            ));
        }
    }

    public function restoreFromXLS($file)
    {
        // Load the import file
        $objPHPExcel = PHPExcel_IOFactory::load($file['tmp_name']);

        // Get the data from the worksheet
        $worksheet = $objPHPExcel->getActiveSheet();
        $data = array();
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $values = array();
            foreach ($cellIterator as $cell) {
                $values[] = $cell->getValue();
            }

            $data[] = $values;
        }

        // Truncate the table
        $this->pdo->exec("TRUNCATE table_name");

        // Insert the data
        foreach ($data as $row) {
            $stmt = $this->pdo->prepare("INSERT INTO table_name (col1, col2, col3) VALUES (?, ?, ?)");
            $stmt->execute(array(
                $row[0],
                $row[1],
                $row[2],
            ));
        }
    }
}
