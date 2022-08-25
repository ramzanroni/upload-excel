<?php
include 'db.php';
try {
    $writeDB = DB::connectWriteDB();
    $readDB = DB::connectReadDB();
} catch (PDOException $ex) {
    echo  $ex->getMessage();
    exit;
}
if ($_POST['check'] == "checkTable") {
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
    // print_r($destNameArr);
    $index = 0;
    foreach ($srcNameArr as $key => $srcValue) {
?>
        <div class="col-md-6 float-left pt-1 mt-2">
            <div class="row">
                <div class="col-md-1 float-left">
                    <input type="checkbox" checked class="form-check-input" id="src_<?php echo $srcValue; ?>" name="src_<?php echo $srcValue; ?>" value="<?php echo $srcValue; ?>">
                </div>

                <div class="col-md-9 float-left">
                    <?php echo $srcValue; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 float-left pt-1 mt-2">
            <select name="dest_<?php echo $srcValue; ?>" id="dest_<?php echo $srcValue; ?>" class="form-control">
                <option value="">Select Column</option>
                <?php
                foreach ($destNameArr as $key => $value) {
                ?>
                    <option value="<?php echo $value; ?>" <?php if ($value == $srcValue) {
                                                                echo "selected";
                                                            } ?>><?php echo $value; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    <?php
        $index++;
    }
    ?>
    <input type="submit" id="makeSql" class="btn btn-info mt-3" name="makeSql" value="Generate SQL">
<?php
}
