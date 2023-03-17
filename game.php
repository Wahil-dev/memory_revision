<?php 
    require_once("inc/classes/Card.php");
    require_once("inc/head.php");
?>

    <?php if(!isset($_SESSION["gameStarted"])) :?>

        <section class="content">
            <form action="inc/b_game.php" method="post">
                <select name="pairs" id="pairs">
                    <option value="3">3</option>
                    <option value="6">6</option>
                    <option value="9">9</option>
                    <option value="12">12</option>
                </select>
                <input type="submit" value="Start Game">
            </form>
        </section>
    <?php else :
        Card::displayCards();?>
    <?php endif?>
<?php 
    echo "<pre>";
        //var_dump(Card::getListOfCards());
    echo "</pre>";
    //require_once("inc/footer.php");
?>
