<?php
session_start();
include 'db.php';
try {
    $writeDB = DB::connectWriteDB();
    $readDB = DB::connectReadDB();
} catch (PDOException $ex) {
    echo  $ex->getMessage();
    exit;
}

if (isset($_POST['makeSql'])) {
    $sourceTbl = $_POST['sourceTbl'];
    $destTable = $_POST['destTable'];
    $colmnName = $readDB->prepare("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='eon_bazar' AND `TABLE_NAME`='$sourceTbl'");
    $colmnName->execute();
    $rowCount = $colmnName->rowCount();
    $srcNameArr = array();
    while ($row = $colmnName->fetch(PDO::FETCH_ASSOC)) {
        $srcNameArr[] = $row['COLUMN_NAME'];
    }
    // print_r($srcNameArr);
    $colmnName = $readDB->prepare("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='eon_bazar' AND `TABLE_NAME`='$destTable'");
    $colmnName->execute();
    $rowCount = $colmnName->rowCount();
    $destNameArr = array();
    while ($row = $colmnName->fetch(PDO::FETCH_ASSOC)) {
        $destNameArr[] = $row['COLUMN_NAME'];
    }

    $srcColName = array();
    $destColName = array();
    for ($i = 0; $i < count($srcNameArr); $i++) {
        if ($_POST['src_' . $srcNameArr[$i]] != '') {
            $srcColName[] = $_POST['src_' . $srcNameArr[$i]];
        }
        if ($_POST['dest_' . $srcNameArr[$i]] != '') {
            $destColName[] = $_POST['dest_' . $srcNameArr[$i]];
        }
    }
    if (count($srcColName) == count($destColName)) {
        $mainSQL = "INSERT INTO " . $destTable . " ( ";

        foreach ($destColName as  $value) {
            $mainSQL = $mainSQL . $value . " ,";
        }
        $mainSQL = substr_replace($mainSQL, "", -1);
        $mainSQL = $mainSQL . ")";
        $srcSql = "SELECT ";
        foreach ($srcColName as $value) {
            $srcSql = $srcSql . $sourceTbl . "." . $value . ",";
        }
        $srcSql = substr_replace($srcSql, "", -1);
        $srcSql = $srcSql . " FROM " . $sourceTbl;
        echo $mainSQL . " " . $srcSql . "<br>";
        echo "<a href='copy-table.php'>Go Back</a>";
    } else {
        echo "Column number and value does not matched." . "<br>";
        echo "<a href='copy-table.php'>Go Back</a>";
    }
}
