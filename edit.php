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


    if (!empty($_POST['delete']) && !empty($_GET['id'])) {
        $sql = "DELETE FROM term WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([htmlspecialchars($_GET['id'])]);
        $newUrl = "admin.php";
        header('Location: ' . $newUrl);
    }


    if (!empty($_GET['id'])) {
        $sql = " SELECT * FROM glossary WHERE language_id=1 and term_id=?;";
        $stmt = $conn->prepare($sql);
        $stmt->execute([htmlspecialchars($_GET['id'])]);
        $termEN = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = " SELECT * FROM glossary WHERE language_id=2 and term_id=?;";
        $stmt = $conn->prepare($sql);
        $stmt->execute([htmlspecialchars($_GET['id'])]);
        $termSK = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!empty($_POST['edit']) && !empty($_GET['id'])) {
        $sql = "UPDATE glossary SET term=?, description=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_POST['en_pojem'], $_POST['en_vysvetlenie'], $termEN['id']]);

        $sql = "UPDATE glossary SET term=?, description=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_POST['sk_pojem'], $_POST['sk_vysvetlenie'], $termSK['id']]);
        $newUrl = "admin.php";
        header('Location: ' . $newUrl);
    }

    /*
if (!empty($_GET['id'])) {
    $sql = " Select * FROM glossary WHERE language_id=1 and term_id=?;";
    $stmt = $conn->prepare($sql);
    $stmt->execute([htmlspecialchars($_GET['id'])]);
    $termEN = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = " Select * FROM glossary WHERE language_id=2 and term_id=?;";
    $stmt = $conn->prepare($sql);
    $stmt->execute([htmlspecialchars($_GET['id'])]);
    $termSK = $stmt->fetch(PDO::FETCH_ASSOC);
}*/
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.png">
    <title>Edit</title>
</head>

<body>
    <br>
    <div class="container">
        <br>
        <h1>Glossary Editor</h1>
        <form <?php "action='edit.php?id=" . htmlspecialchars($_GET['id']) . "'";
                ?> method="POST">
            <div class="form-group">
                <label for="en_pojem" class="col-sm-3 col-form-label">
                    en_pojem: </label> <input type="text" class="col-sm-8" name="en_pojem" id="en_pojem" value="<?php echo $termEN['term']; ?>">

            </div>
            <div class="form-group">
                <label for="en_vysvetlenie" class="col-sm-3 col-form-label">
                    en_vysvetlenie:</label> <input type="text" class="col-sm-8" name="en_vysvetlenie" id="en_vysvetlenie" value="<?php echo $termEN['description']; ?>">

            </div>
            <div class="form-group">
                <label for="sk_pojem" class="col-sm-3 col-form-label">
                    sk_pojem:</label> <input type="text" class="col-sm-8" name="sk_pojem" id="sk_pojem" value="<?php echo $termSK['term']; ?>">

            </div>
            <div class="form-group">
                <label for="sk_vysvetlenie" class="col-sm-3 col-form-label">
                    sk_vysvetlenie:</label> <input type="text" class="col-sm-8" name="sk_vysvetlenie" id="sk_vysvetlenie" value="<?php echo $termSK['description']; ?>">

            </div>
            <div class="form-group ">
                <input type="submit" class="btn btn-primary col-sm-2" name="edit" value="Uprav">

            </div>
            <div class="form-group">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-danger col-sm-2" data-toggle="modal" data-target="#exampleModal">
                    Vymaž
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Zmazanie záznamu</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Prajete si naozaj vymazať tento záznam?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavrieť</button>
                                <input type="submit" class="btn btn-danger col-sm-2 " name="delete" value="Zmazať">

                            </div>
                        </div>
                    </div>
                </div>

            </div>



        </form>
        <div class="form-group">
            <button class="btn btn-primary  col-sm-2" onclick="window.location.href='https://site38.webte.fei.stuba.sk/cvicenie2/admin.php'" name="Späť" value="Späť">Späť</button>

        </div>
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </div>
</body>

</html>