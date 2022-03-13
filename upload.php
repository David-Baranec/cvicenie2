<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
require_once("config.php");

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if (isset($_POST["submit"])) {

    /*$tmpName = $_FILES['csv']['tmp_name'];
    $csvAsArray = array_map('str_getcsv', file($tmpName));*/

    $csv = array();

    // check there are no errors
    if ($_FILES['csv']['error'] == 0) {
        $name = $_FILES['csv']['name'];
        $ext = strtolower(end(explode('.', $_FILES['csv']['name'])));
        $type = $_FILES['csv']['type'];
        $tmpName = $_FILES['csv']['tmp_name'];

        // check the file is a csv
        if ($ext === 'csv') {
            if (($handle = fopen($tmpName, 'r')) !== FALSE) {
                // necessary if a large csv file
                set_time_limit(0);

                $row = 0;

                while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                    // number of fields in the csv
                    $col_count = count($data);
                    //echo $col_count;

                    // get the values from the csv
                    $csv[$row]['en_pojem'] = $data[0];
                    $csv[$row]['en_vysvetlenie'] = $data[1];
                    $csv[$row]['sk_pojem'] = $data[2];
                    $csv[$row]['sk_vysvetlenie'] = $data[3];

                    $sql = 'SELECT id, name FROM term WHERE name=?';
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$data[0]]);
                    $terms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    /*echo "<pre>";
                    var_dump($terms);
                    echo "</pre>";*/

                    if (!(count($terms) > 0)) {
                        $sql = "INSERT INTO term (name) VALUES (?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$csv[$row]['en_pojem']]);
                        /* echo "<pre>";
                        var_dump($stmt);
                        echo "</pre>";*/
                        $sql = 'SELECT id FROM term WHERE name=?';
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$data[0]]);
                        $id = $stmt->fetch(PDO::FETCH_ASSOC);
                        /*echo "<pre>";
                        var_dump($id['id']);
                        echo "</pre>";*/

                        $sql = "INSERT INTO glossary (term,description,language_id,term_id) VALUES (?,?,?,?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$csv[$row]['en_pojem'], $csv[$row]['en_vysvetlenie'], 1, $id['id']]);

                        $sql = "INSERT INTO glossary (term,description,language_id,term_id) VALUES (?,?,?,?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$csv[$row]['sk_pojem'], $csv[$row]['sk_vysvetlenie'], 2, $id['id']]);
                    }
                    // inc the row
                    $row++;
                    //echo "<hr>";
                }
                fclose($handle);
            }
        }

        /*echo "<pre>";
        var_dump($csv);
        echo "</pre>";*/
    }
}
?>

<!doctype html>
<html lang="sk">

<head>
    <title>Uploaded</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body class=" bg-secondary">
    <br>
    <div class="container bg-secondary text-white">
        <br>
        <h1 class="text-center">Uploaded</h1>
        <br>
        <button class="btn btn-primary" onclick="window.location.href='https://site38.webte.fei.stuba.sk/cvicenie2/admin.php'">Späť</button>

    </div>
    <br>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>