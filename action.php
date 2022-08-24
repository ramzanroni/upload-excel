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

if ($_POST['check'] == "checkExcel") {
    $tblName = $_POST['tblName'];
    $fileName = $_FILES['import_file']['name'];
    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $allowed_ext = ['xls', 'csv', 'xlsx'];
    if (in_array($file_ext, $allowed_ext)) {
        $inputFileNamePath = $_FILES['import_file']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $colmnName = $readDB->prepare("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='eon_bazar' AND `TABLE_NAME`='$tblName'");
        $colmnName->execute();
        $rowCount = $colmnName->rowCount();
        $colmnNameArr = array();
        while ($row = $colmnName->fetch(PDO::FETCH_ASSOC)) {
            $colmnNameArr[] = $row['COLUMN_NAME'];
        }
        $excelCol = $data[0];
        for ($i = 0; $i < count($colmnNameArr); $i++) {
?>
            <div class="col-md-3 float-left mt-1 mb-1">
                <?php echo $colmnNameArr[$i]; ?>
            </div>
            <div class="col-md-9 float-left mt-1 mb-1">
                <div class="row">
                    <div class="col-md-6 float-left">
                        <select name="col_<?php echo $colmnNameArr[$i]; ?>" id="col_<?php echo $colmnNameArr[$i]; ?>" class="form-control">
                            <option value="">Select Column</option>
                            <?php
                            foreach ($excelCol as $key => $value) {
                            ?>
                                <option value="<?php echo $key; ?>" <?php if ($colmnNameArr[$i] == $value) {
                                                                        echo 'selected';
                                                                    } ?>><?php echo $value; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 float-left">
                        <input type="text" class="form-control" name="custom_<?php echo $colmnNameArr[$i]; ?>" id="custom_<?php echo $colmnNameArr[$i]; ?>">
                    </div>
                </div>
            </div>

        <?php
        }
        ?>
        <button type="submit" name="save_excel" class="btn btn-primary mt-3">Import</button>
<?php
    } else {
        unset($_SESSION['excelData']);
        unset($_SESSION['upload']);
        $_SESSION['message'] = "Invalid File";
        echo "error";
    }
}
