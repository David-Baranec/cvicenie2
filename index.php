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
$languages = [];
$searchResult = [];
$sql = 'SELECT * FROM language';
$stmt = $conn->prepare($sql);
$stmt->execute();
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['search'])) {
    $search_term = "%" . $_GET['search'] . "%";
    $both = null;
    $both = $_GET['ajPreklad'];
    $fullText = null;
    $fullText = $_GET['fulltext'];
    //var_dump($_GET['language_id']);
    if ($both != null && $fullText != null) {
        $sql = "SELECT table1.term as search, 
                    table1.description as searchDescription,
                    table2.term  as result,
                    table2.description as resultDescription
                    FROM `glossary` table1 join `glossary` table2 on table1.term_id=table2.term_id 
                    WHERE table1.id <>table2.id and table1.language_id=:language_id and 
                    ((table1.term like :search_term) OR (table1.description like :search_term2) OR (table2.term like :search_term3) OR (table2.description like :search_term4));";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':language_id', $_GET['language_id'], PDO::PARAM_INT);
        $stmt->bindParam(':search_term', $search_term, PDO::PARAM_STR);    
        $stmt->bindParam(':search_term2', $search_term, PDO::PARAM_STR); 
        $stmt->bindParam(':search_term3', $search_term, PDO::PARAM_STR); 
        $stmt->bindParam(':search_term4', $search_term, PDO::PARAM_STR); 
}
    if ($both != null && $fullText == null) {
        $sql = "SELECT table1.term as search, 
                    table1.description as searchDescription,
                    table2.term  as result,
                    table2.description as resultDescription
                    FROM `glossary` table1 join `glossary` table2 on table1.term_id=table2.term_id 
                    WHERE table1.id <>table2.id and table1.language_id=:language_id and table1.term like :search_term;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':language_id', $_GET['language_id'], PDO::PARAM_INT);
        $stmt->bindParam(':search_term', $search_term, PDO::PARAM_STR);    
    }
    if ($both == null && $fullText != null) {
        $sql = "SELECT table1.term as search, 
        table1.description as searchDescription
        FROM `glossary` table1
        WHERE  table1.language_id=:language_id and ((table1.term like :search_term )OR (table1.description like :search_term2));";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':language_id', $_GET['language_id'], PDO::PARAM_INT);
        $stmt->bindParam(':search_term', $search_term, PDO::PARAM_STR);
        $stmt->bindParam(':search_term2', $search_term, PDO::PARAM_STR);
    }
    if ($both == null && $fullText == null) {
        $sql = "SELECT table1.term as search, 
                    table1.description as searchDescription
                    FROM `glossary` table1
                    WHERE  table1.language_id=:language_id and table1.term like :search_term;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':language_id', $_GET['language_id'], PDO::PARAM_INT);
        $stmt->bindParam(':search_term', $search_term, PDO::PARAM_STR);            
    }

    
    $stmt->execute();
    $searchResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1>Vitajte v Glosari</h1>
        <form action="index.php" method="get" id="search-form">
            <div class="form-group">
                <label for="language" class="col-sm-1 col-form-label">Jazyk</label>
                <select name="language_id" id="language">
                    <?php
                    foreach ($languages as $language) {
                        echo "<option value='" . $language['id'] . "'>" . $language['name'] . "</option>";
                    }
                    ?>
                </select>

            </div>
            <div class="form-group">
                <div class="form-check">

                    <label class="form-check-label col-sm-2" for="ajPreklad">
                        Vyhladat aj preklad
                    </label>
                    <input class="form-check-input " type="checkbox" name="ajPreklad" id="ajPreklad">
                </div>
            </div>
            <div class="form-group">
                <div class="form-check">

                    <label class="form-check-label col-sm-2" for="fullText">
                        Vyhladavat fulltextovo
                    </label>
                    <input class="form-check-input " type="checkbox" name="fullText" id="fullText">
                </div>
            </div>
            <div class="form-group">
                <label for="search" class="col-sm-1 col-form-label">Hladane slovo</label>
                <input type="text" name="search" id="search">
                <input type="submit" value="Vyhladaj">
            </div>


        </form>

        <hr>
        <hr>
        <?php

        /*echo "<pre>";
        var_dump($searchResult);
        echo "</pre>";*/
        ?>



        <table class="table  table-stripped">
            <thead>
                <td>Pojem</td>
                <td>Vysvetlenie</td>
                <td>Preklad pojmu</td>
                <td>Vysvetlenie prekladu</td>
            </thead>


            <tbody>

                <?php
                if (count($searchResult) != 0) {
                    /* echo "<pre>";
                    var_dump($terms);
                    echo "</pre>";*/
                    foreach ($searchResult as $value) {
                        /*echo "<pre>";
                        var_dump($value);
                        echo "</pre>";*/
                        if ($value['result'] != null) {
                            echo "<tr><td>" . $value['search'] . "</td><td>" . $value['searchDescription'] . "</td><td>" . $value['result'] . "</td><td>" . $value['resultDescription'] . "</td><tr>";
                        } else {
                            echo "<tr><td>" . $value['search'] . "</td><td>" . $value['searchDescription'] . "</td><td><td>-</td><td>-</td><tr>";
                        }
                    }
                } else echo "<tr><td>-</td><td>-</td><td>-</td><td>-</td><tr>";

                ?>
            </tbody>

        </table>
    </div>

    <script>
        /*
        const button = document.querySelector('#search-button');
        const form = document.querySelector('#search-form');
        button.addEventListener('click', () => {
            const data = new FormData(form);
            fetch("search.php?language_id=" + data.get("language_id") + "&search=" + data.get("search"), {
                    method: 'get'
                })
                .then((response) => response.json())
                .then(console.log)
        })
         */

        // $("#search").autocomplete();
    </script>





    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>