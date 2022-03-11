<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("config.php");

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
    $sql = "SELECT * FROM term ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $terms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>


<!doctype html>
<html lang="sk">

<head>
    <title>Glosar</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>


<body>
    <div class="container">
        <h1>Admin panel</h1>



        <table class="table  table-stripped">
            <thead>
                <td>ID</td>
                <td>nazov</td>
                <td>akcia</td>
            </thead>


            <tbody>

                <?php
                if (count($terms) != 0) {
                    /* echo "<pre>";
                    var_dump($terms);
                    echo "</pre>";*/
                    foreach ($terms as $term) {
                        /*echo "<pre>";
                var_dump($term);
                echo "</pre>";*/
                        echo "<tr><td>" . $term['id'] . "</td><td>" . $term['name'] . "</td><td><a href='edit.php?id={$term['id']}'>Edit</a></td></tr>";
                    }
                }
                ?>
            </tbody>

        </table>
        <hr>
        <hr>
        <?php

        if (!empty($_POST['Upload'])) {
            if ($_POST['en_pojem'] != null) {
                $sql = 'SELECT id, name FROM term WHERE name=:name';
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':name', $_POST['en_pojem'], PDO::PARAM_STR);
                $stmt->execute();
                $terms = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!(count($terms) > 0)) {
                    $sql = "INSERT INTO `term` (name) VALUES (:name)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':name', $_POST['en_pojem'], PDO::PARAM_STR);
                    $result = $stmt->execute();

                    $sql = 'SELECT id FROM term WHERE name=:name';
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':name', $_POST['en_pojem'], PDO::PARAM_STR);
                    $stmt->execute();
                    $id = $stmt->fetch(PDO::FETCH_ASSOC);

                    $sql = "INSERT INTO glossary (term,description,language_id,term_id) VALUES (:en_pojem, :en_vysvetlenie,1,:id)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':en_pojem', $_POST['en_pojem'], PDO::PARAM_STR);
                    $stmt->bindParam(':en_vysvetlenie', $_POST['en_vysvetlenie'], PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id['id'], PDO::PARAM_INT);
                    $result = $stmt->execute();

                    $sql = "INSERT INTO glossary (term,description,language_id,term_id) VALUES (:sk_pojem, :sk_vysvetlenie,2,:id)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':sk_pojem', $_POST['sk_pojem'], PDO::PARAM_STR);
                    $stmt->bindParam(':sk_vysvetlenie', $_POST['sk_vysvetlenie'], PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id['id'], PDO::PARAM_INT);
                    $result = $stmt->execute();

                    $newUrl = "admin.php";
                    header('Location: ' . $newUrl);
                }
            }
        }

        ?>
        <h1>Pridaj novu glosu:</h1>
        <form method="POST">
            <div class="form-group">
                <label for="en_pojem" class="col-sm-2 col-form-label">
                    en_pojem: </label> <input type="text" class="col-sm-6" name="en_pojem" id="en_pojem" placeholder="pojem v anglickom jazyku">

            </div>
            <div class="form-group">
                <label for="en_vysvetlenie" class="col-sm-2 col-form-label">
                    en_vysvetlenie:</label> <input type="text" class="col-sm-6" name="en_vysvetlenie" id="en_vysvetlenie" placeholder="vysvetlenie pojmu v anglickom jazyku">

            </div>
            <div class="form-group">
                <label for="sk_pojem" class="col-sm-2 col-form-label">
                    sk_pojem:</label> <input type="text" class="col-sm-6" name="sk_pojem" id="sk_pojem" placeholder="pojem v slovenskom jazyku">

            </div>
            <div class="form-group">
                <label for="sk_vysvetlenie" class="col-sm-2 col-form-label">
                    sk_vysvetlenie:</label> <input type="text" class="col-sm-6" name="sk_vysvetlenie" id="sk_vysvetlenie" placeholder="vysvetlenie pojmu v slovenskom jazyku">

            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary col-sm-2" name="Upload" value="Upload">
            </div>



        </form>
        <hr>
        <hr>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                Select CSV file:
                <input id="csv" type="file" name="csv">
                <input type="submit" class="btn btn-primary" value="Upload CSV" name="submit">
            </div>
        </form>



        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </div>
</body>

</html>