<?php
    require "../autoload.php";
    use Memory\Card;

    $pairs = "";
    $pairsErr = "";

    if($_SERVER["REQUEST_METHOD"] === "POST") {
        if(isset($_POST["pairs"])) {
            $_SESSION["gameStarted"] = true;
            $_SESSION["list_of_cards"] = "";
            Card::CreateCards($_POST["pairs"]);
            header("location: ../game.php");
            exit();
        }

        
        if(isset($_POST["card_id"])) {
            Card::setStateById($_POST["card_id"]);
            header("location: ../game.php");
            exit();
        }
        
    }

