<?php
session_start();
include 'db.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

try {
    $writeDB = DB::connectWriteDB();
    $readDB = DB::connectReadDB();
} catch (PDOException $ex) {
    echo  $ex->getMessage();
    exit;
}
$writeDB = DB::connectWriteDB();
$readDB = DB::connectReadDB();

if (isset($_POST['save_excel'])) {

    $tblName = $_POST['tblName'];
    $fileName = $_FILES['import_file']['name'];
    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

    $allowed_ext = ['xls', 'csv', 'xlsx'];

    if (in_array($file_ext, $allowed_ext)) {
        $inputFileNamePath = $_FILES['import_file']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
        $data = $spreadsheet->getActiveSheet()->toArray();
        echo "Total Data: " . $excelDataCount = (count($data) - 1) . "<br>";
        $counter = 0;
        $failed = 0;
        // column name from database
        $colmnName = $readDB->prepare("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='eon_bazar' AND `TABLE_NAME`='$tblName'");
        $colmnName->execute();
        $rowCount = $colmnName->rowCount();
        $colmnNameArr = array();
        while ($row = $colmnName->fetch(PDO::FETCH_ASSOC)) {
            $colmnNameArr[] = $row['COLUMN_NAME'];
        }
        $colstring = implode(",", $colmnNameArr);
        $valueStr = ':' . implode(",:", $colmnNameArr);
        // if (($data[0] === $colmnNameArr) && (count($colmnNameArr) == count($data[0]))) {
        foreach ($data as $row) {
            if ($count > 0) {
                // if ($row[0] != '') {
                $dataArr = array();
                for ($i = 0; $i < count($row); $i++) {
                    if ($_POST["custom_" . $colmnNameArr[$i]] != '') {
                        $dataArr[] = "'" . $_POST["custom_" . $colmnNameArr[$i]] . "'";
                    } elseif ($_POST["col_" . $colmnNameArr[$i]] != '') {
                        if ($row[$_POST["col_" . $colmnNameArr[$i]]] === 0) {
                            $dataArr[] = "0";
                        } elseif ($row[$_POST["col_" . $colmnNameArr[$i]]] == 'NULL') {
                            $dataArr[] = "NULL";
                        } else {
                            $dataArr[] = "'" . $row[$_POST["col_" . $colmnNameArr[$i]]] . "'";
                        }
                    }
                }
                // echo "<br>";
                // print_r($dataArr);

                $valueOfRow = implode(",", $dataArr);
                // echo "<hr>";
                // echo 'INSERT INTO ' . $tblName . ' (' . $colstring . ') VALUES (' . $valueOfRow . ')';
                // echo "<hr>";
                try {
                    $query = $readDB->prepare('INSERT INTO ' . $tblName . ' (' . $colstring . ') VALUES (' . $valueOfRow . ')');
                    $query->execute();
                    $rowCount = $query->rowCount();
                    if ($rowCount == 1) {
                        $counter++;
                        echo "Success fully uploaded row: " . $row[0] . "<br>";
                    } else {
                        echo "Failed row ID: " . $row[0] . "<br>";
                        echo "<a href='index.php'>Go Back</a>";
                    }
                } catch (PDOException $th) {
                    $failed++;
                    // echo $th->getMessage();
                }
            }


            $count++;
        }
        echo "Total uploaded: " . $counter . "<br>";
        echo "Total Failed: " . $failed . "<br>";
        echo "<a href='index.php'>Go Back</a>";
    } else {
        $_SESSION['message'] = "Invalid File";
        header('Location: index.php');
        exit(0);
    }
}
