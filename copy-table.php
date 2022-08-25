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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Import Excel Data into database in PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Import Excel Data into database</h4>
                    </div>
                    <div class="card-body">
                        <form action="generate-sql.php" method="post">
                            <label for="">Select Source Table</label>
                            <select name="sourceTbl" id="sourceTbl" class="form-control">
                                <?php
                                // column name from database
                                $colmnName = $readDB->prepare("SELECT table_name FROM information_schema.tables
                                WHERE table_schema = 'eon_bazar'");
                                $colmnName->execute();
                                $rowCount = $colmnName->rowCount();
                                $colmnNameArr = array();
                                while ($row = $colmnName->fetch(PDO::FETCH_ASSOC)) {
                                    $colmnNameArr[] = $row['table_name'];
                                }
                                foreach ($colmnNameArr as $value) {
                                ?>
                                    <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <label for="">Select Destination Table </label>
                            <select name="destTable" id="destTable" class="form-control">
                                <?php
                                // column name from database
                                $colmnName = $readDB->prepare("SELECT table_name FROM information_schema.tables
                                WHERE table_schema = 'eon_bazar'");
                                $colmnName->execute();
                                $rowCount = $colmnName->rowCount();
                                $colmnNameArr = array();
                                while ($row = $colmnName->fetch(PDO::FETCH_ASSOC)) {
                                    $colmnNameArr[] = $row['table_name'];
                                }
                                foreach ($colmnNameArr as $value) {
                                ?>
                                    <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <div id="colMatched" class="row">

                            </div>
                            <button type="button" id="check" name="save_excel_data" class="btn btn-primary mt-3" onclick="checkData()">Check</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function checkData() {

            var sourceTbl = $("#sourceTbl").val();
            var destTable = $("#destTable").val();
            var check = "checkTable";
            $.ajax({
                url: "copy-action.php",
                data: {
                    sourceTbl: sourceTbl,
                    destTable: destTable,
                    check: check
                },
                type: 'post',
                success: function(response) {
                    console.log(response);
                    // if (response == 'error') {
                    //     $("#file-error").show();
                    // } else {
                    //     $("#my_form").attr('action', 'code.php');
                    //     $("#my_form").attr('id', '');
                    $("#check").hide();
                    $("#colMatched").html(response);
                    // }

                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>