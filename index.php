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

                        <div id="file-error" class="row p-2 text-center text-white bg-danger"><?php echo $_SESSION['message']; ?></div>

                        <!-- <form action="code.php" method="POST" enctype="multipart/form-data">
                            <label for="">Select Table </label>
                            <select name="tblName" id="tblName" class="form-control">
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
                            <label for="">Select Excel File </label>
                            <input type="file" name="import_file" class="form-control" />
                            <button type="submit" name="save_excel_data" class="btn btn-primary mt-3">Import</button>

                        </form> -->
                        <form action="" id="my_form" method="post" enctype="multipart/form-data">
                            <select name="tblName" id="tblName" class="form-control">
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
                            <label for="">Select Excel File </label>
                            <input type="file" name="import_file" id="import_file" class="form-control" />
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
        $("#file-error").hide();

        function checkData() {

            var import_file = $("#import_file").prop("files")[0];
            var check = "checkExcel";
            // var formData = new FormData(this);
            var form = $('#my_form')[0]; // You need to use standard javascript object here
            var formData = new FormData(form);
            formData.append("import_file", import_file);
            formData.append("check", check);

            console.log(formData);
            $.ajax({
                url: "action.php",
                // dataType: 'script',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                type: 'post',
                success: function(response) {
                    if (response == 'error') {
                        $("#file-error").show();
                    } else {
                        $("#my_form").attr('action', 'code.php');
                        $("#my_form").attr('id', '');
                        $("#check").hide();
                        $("#colMatched").html(response);
                    }

                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>