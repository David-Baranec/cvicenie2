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
    $fullText = $_GET['fullText'];
    //var_dump($fullText);
    if ($_GET['search'] != null) {
        if ($both != null && $fullText != null) {
            $sql = "SELECT table1.term as search, 
                        table1.description as searchDescription,
                        table2.term  as result,
                        table2.description as resultDescription
                        FROM `glossary` table1 join `glossary` table2 on table1.term_id=table2.term_id 
                        WHERE table1.id <>table2.id and table1.language_id=:language_id and 
                        ((table1.term like '$search_term') OR (table1.description like :search_term) OR (table2.term like :search_term) OR (table2.description like :search_term));";
        }
        if ($both != null && $fullText == null) {
            $sql = "SELECT table1.term as search, 
                        table1.description as searchDescription,
                        table2.term  as result,
                        table2.description as resultDescription
                        FROM `glossary` table1 join `glossary` table2 on table1.term_id=table2.term_id 
                        WHERE table1.id <>table2.id and table1.language_id=:language_id and table1.term like :search_term;";
        }
        if ($both == null && $fullText != null) {
            $sql = "SELECT table1.term as search, 
            table1.description as searchDescription
            FROM `glossary` table1
            WHERE  table1.language_id=:language_id and ((table1.term like :search_term )OR (table1.description like :search_term));";
        }
        if ($both == null && $fullText == null) {
            $sql = "SELECT table1.term as search, 
                        table1.description as searchDescription
                        FROM `glossary` table1
                        WHERE  table1.language_id=:language_id and table1.term like :search_term;";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':language_id', $_GET['language_id'], PDO::PARAM_INT);
        $stmt->bindParam(':search_term', $search_term, PDO::PARAM_STR);
        $stmt->execute();
        $searchResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!doctype html>
<html lang="sk">

<head>
    <title>Glosár</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="favicon.png">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body class=" bg-dark">
    <br>
    <div class="container bg-secondary text-white">
        <br>
        <h1 class="text-center">Vitajte v Glosari</h1>
        <form action="index.php" method="get" id="search-form">
            <div class="form-group mb-2">
                <label for="language" class="col-sm-2 ">Jazyk pre zobrazenie</label>
                <select name="language_id" id="language">
                    <?php
                    foreach ($languages as $language) {
                        echo "<option value='" . $language['id'] . "'>" . $language['name'] . "</option>";
                    }
                    ?>
                </select>

            </div>
            <div class="form-group mb-2">

                <label class="form-check-label col-sm-3" for="ajPreklad">
                    Vyhľadať aj preklad
                </label>
                <input class="form-check-input " type="checkbox" name="ajPreklad" id="ajPreklad">

            </div>
            <div class="form-group mb-2">

                <label class="form-check-label col-sm-3" for="fullText">
                    Vyhľadávať fulltextovo
                </label>
                <input class="form-check-input " type="checkbox" name="fullText" id="fullText">

            </div>
            <div class="form-group mb-2">
                <label for="search" class="col-sm-2 col-form-label">Hľadané slovo</label>
                <input type="text" name="search" id="search" placeholder="Zadaj výraz" autocomplete="off">
                <div id="searchList"></div>
                <input type="submit" class="btn btn-primary col-sm-2" value="Vyhľadaj">
            </div>



        </form>

        <hr>
        <hr>
        <?php

        /*echo "<pre>";
        var_dump($searchResult);
        echo "</pre>";*/
        ?>



        <table class="table table-bordered table-striped table-hover text-white">
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
                            echo "<tr><td>" . $value['search'] . "</td><td>" . $value['searchDescription'] . "</td><td>-</td><td>-</td><tr>";
                        }
                    }
                } else echo "<tr><td>-</td><td>-</td><td>-</td><td>-</td><tr>";

                ?>
            </tbody>

        </table>
        <br>
    </div>

    <script>
        $(document).ready(function() {
            $('#search').keyup(function() {
                var query = $(this).val();
                /*if(parseInt($('#language').val>2)){
                    lang_id=1;
                }else{
                    lang_id=parseInt($('#language').val);
                }
                console.log(parseInt($('#language').val));*/
                if (query != '' && query.length > 2) {
                    $.ajax({
                        url: "search.php",
                        method: "POST",
                        data: {
                            query: query
                            //language_id: lang_id
                        },
                        success: function(data) {
                            $('#searchList').fadeIn();
                            $('#searchList').html(data);
                        }
                    });
                } else {
                    $('#searchList').html('');
                }
            });
            $(document).on('click', 'li', function() {
                $('#search').val($(this).text());
                $('#searchList').fadeOut();
            });
        });
    </script>





    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>