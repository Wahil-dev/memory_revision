<?php
    namespace Memory;
    
    class Card {
        private $id_card;
        private $path_of_img = "assets/img/";
        private $img_face_down = "default.jpg";
        private $img_face_up;
        private $state = false;
        private $completed = false;
        private static $list_of_name_card = ["dargon-ball-1.jpg", "dargon-ball-2.jpg", "dargon-ball-3.jpg", "dargon-ball-4.jpg", "dargon-ball-5.jpg", "dargon-ball-6.jpg", "dargon-ball-7.jpg", "dargon-ball-8.jpg", "dargon-ball-9.jpg", "dargon-ball-10.jpg", "dargon-ball-11.jpg", "dargon-ball-12.jpg"];    
        private static $list_of_cards;

        public function __construct($id_card)
        {
            $this->id_card = $id_card;
        }

        /*--------------------- Getters and Setters ------------------- */
        public function getIdCard()
        {
            return $this->id_card;
        }

        public function setIdCard($id_card): self
        {
            $this->id_card = $id_card;
            return $this;
        }

        public function getImgFaceDown()
        {
            $target_img = $this->getPathOfImg().$this->img_face_down;
            return $target_img;
        }

        public function setImgFaceDown($img_face_down): self
        {
            $this->img_face_down = $img_face_down;
            return $this;
        }

        public function getImgFaceUp()
        {
            $target_img = $this->getPathOfImg().$this->img_face_up;
            return $target_img;
        }

        public function setImgFaceUp($img_face_up): self
        {
            $this->img_face_up = $img_face_up;
            return $this;
        }

        public function getState()
        {
            return $this->state;
        }

        public function setState($state): self
        {
            $this->state = $state;
            return $this;
        }

        public function getCompleted()
        {
            return $this->completed;
        }

        public function setCompleted($completed): self
        {
            $this->completed = $completed;
            return $this;
        }

        public function getPathOfImg()
        {
            return $this->path_of_img;
        }

        /*--------------------- Static Methods ------------------- */
        public static function createCards($pairs) {
            $_SESSION["cardClicked"] = [];
            $_SESSION["coup"] = 0;

            $cards = [];
            for($id = 1; $id < $pairs*2+1; $id++) {
                array_push($cards, new Card($id));
            }

            //Partager les images a toutes les cartes
            $random_cards = self::setImgFaceUpToAllCard($cards);
            shuffle($random_cards);

            //var_dump($random_cards);
            self::updateListOfCards($random_cards);
        }
        
        public static function getListOfNameCard()
        {
            return self::$list_of_name_card;
        }

        public static function getListOfCards() {
            $cards = unserialize($_SESSION["list_of_cards"]);
            return $cards;
        }

        public static function setStateById($card_id) {
            $_SESSION["coup"] ++;
            $cards = self::getListOfCards();
            $new_list = [];
            foreach((array)$cards as $card) {
                if($card->getIdCard() == $card_id) {
                    $card->setState("disabled");
                    //on peut pas clicker sur la meme carte deux fois
                    if(count($_SESSION["cardClicked"]) > 0) {
                        if(unserialize($_SESSION["cardClicked"][0])->getIdCard() != $card->getIdCard()) {
                            array_push($_SESSION["cardClicked"], serialize($card));
                        } else {
                            return false;
                        }
                    } else {
                        array_push($_SESSION["cardClicked"], serialize($card));
                    }
                }
                array_push($new_list, $card);
            }
            self::updateListOfCards($new_list);
            
            if(count($_SESSION["cardClicked"]) >= 2) {
                //Vérifier si ouvre 2 carte resemble
                if(self::checkIfTwoCardsFind()) {
                    self::updateTwoCardsToCompleted(unserialize($_SESSION["cardClicked"][0])->getImgFaceUp());
                    $_SESSION["cardClicked"] =  [];
                } else {
                    array_shift($_SESSION["cardClicked"]);
                    self::setStatCardsToFalse($card_id);//sauf la carte clicker ce fois ci
                    return false;
                }
            } 

            if(self::isWin()) {
                $_SESSION["win"] = true;
                $_SESSION["score"] = $_SESSION["coup"] / $_SESSION["pairs"];
            }
        }

        public static function updateListOfCards($cards) {
            $_SESSION["list_of_cards"] = serialize($cards);
        }

        public static function setImgFaceUpToAllCard($cards) {
            $half_of_list_cards = count((array)$cards) / 2;
            $new_list = [];
            for($i = 0; $i<$half_of_list_cards; $i++) {
                $cards[$i]->setImgFaceUp(self::getListOfNameCard()[$i]);
                array_push($new_list, $cards[$i]);
            }
            $i = 0;
            for($j = $half_of_list_cards; isset($cards[$j]); $j++) {
                $cards[$j]->setImgFaceUp(self::getListOfNameCard()[$i]);
                array_push($new_list, $cards[$j]);
                $i++;
            }
            //var_dump($new_list);
            return $new_list;
        }

        public static function displayCards() {?>
            <section class="content">
                <?php if(!isset($_SESSION["win"])) : ?>
                    <div class="menu-game">
                        <a href="inc/quit_game.php">Quit Partie</a>
                        <p>Click <span><?= $_SESSION["coup"] ?></span></p>
                    </div>
                    <form action="inc/b_game.php" method="post" class="form-cards flex-r">
                        <?php foreach(self::getListOfCards() as $card) :?>
                            <?php if($card->getState()) :?>
                                <div class="card">
                                    <button type="submit" class="card-btn <?php echo $card->getState()?>" name="card_id" value="<?= $card->getIdCard()?>" <?php echo $card->getState()?>>
                                        <img src="<?= $card->getImgFaceUp()?>" alt="<?= $card->getImgFaceUp()?>">
                                    </button>
                                </div>
                            <?php else :?>
                                <div class="card">
                                    <button type="submit" class="card-btn <?php echo $card->getState()?>" name="card_id" value="<?= $card->getIdCard()?>" <?php echo $card->getState()?>>
                                        <img src="<?= $card->getImgFaceDown()?>" alt="<?php echo  $card->getImgFaceDown()?>">
                                    </button>
                                </div>
                            <?php endif ?>
                        <?php endforeach ?>
                    </form>
                <?php else :?>
                    <div class="win-box">
                        <h2>Vous avez gagner !</h2>
                        <p>Score <span><?= $_SESSION["score"] ?></span></p>
                        <p>Click <span><?= $_SESSION["coup"] ?></span></p>
                        <a href="inc/quit_game.php">nouveau partie</a>
                    </div>
                <?php endif ?>
            </section>
    <?php }

        private static function checkIfTwoCardsFind() {
            $cardClicked = $_SESSION["cardClicked"];
            var_dump($cardClicked);
            $card_1 = unserialize($cardClicked[0]);
            $card_2 = unserialize($cardClicked[1]);
            if($card_1->getImgFaceUp() == $card_2->getImgFaceUp()) {
                return true;
            } 
            return false;
        }

        private static function setStatCardsToFalse($card_id) {
            $cards = self::getListOfCards();
            $new_list = [];
            foreach($cards as $card) {
                if($card->getCompleted() != "disabled") {
                    $card->setState(false);
                }
                if($card->getIdCard() == $card_id) {
                    $card->setState("disabled");
                }
                array_push($new_list, $card);
            }
            self::updateListOfCards($new_list);
        }

        private static function updateTwoCardsToCompleted($img_of_card) {
            $cards = self::getListOfCards();
            $new_list = [];
            foreach($cards as $card) {
                if($card->getImgFaceUp() == $img_of_card) {
                    $card->setCompleted("disabled");
                }
                array_push($new_list, $card);
            }
            self::updateListOfCards($new_list);
        }

        private static function isWin() {
            $cards = self::getListOfCards();
            $is_win = true;
            foreach($cards as $card) {
                if($card->getCompleted() == false) {
                    $is_win = false;
                }
            }
            return $is_win;
        }

        public static function quitGame() {
            unset($_SESSION["gameStarted"]);
            unset($_SESSION["list_of_cards"]);
            unset($_SESSION["cardClicked"]);
            unset($_SESSION["win"]);
            unset($_SESSION["pairs"]);
            unset($_SESSION["score"]);
            unset($_SESSION["coup"]);
            header("location: ../game.php");
            exit();
        }
    }