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


if(isset($_POST['query'])){
    $output = '';
    $query = "SELECT term FROM glossary WHERE /*language_id=:language_id AND*/ term LIKE '". $_POST["query"]. "%';";
    $stmt = $conn->prepare($query);
   // $stmt->bindParam(':language_id', $_POST['language_id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output= '<ul class="list-unstyled">';
    /*echo "<pre>";
    var_dump($_POST['language_id']);
    echo "</pre>";*/
    if(count($result) > 0){
        foreach($result as $result){
            $output .= '<li>'.$result['term'].'</li>';
        }
    }else{
        $output.='<li>Žiadne výsledky v zozname pojmov. Skúste zvoliť full-text vyhľadávanie.</li>';
    }
    $output .= '</ul>';
    echo $output;
}
/*
$languages = [];
$searchResult = null;
$sql = 'SELECT * FROM language';
$stmt = $conn->prepare($sql);
$stmt->execute();
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['search'])) {
    $search_term = "%" . $_GET['search'] . "%";

    var_dump($_GET['language_id']);
    $sql = "SELECT table1.term as search, 
                    table1.description as searchDescription,
                    table2.term  as result,
                    table2.description as resultDescription
    FROM `glossary` table1 join `glossary` table2 on table1.term_id=table2.term_id 
    WHERE table1.id <>table2.id and table1.language_id=:language_id and table1.term like :search_term;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':language_id', $_GET['language_id'],PDO::PARAM_INT);
    $stmt->bindParam(':search_term', $search_term,PDO::PARAM_STR);
    $stmt->execute();
    $searchResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($searchResult);*/
