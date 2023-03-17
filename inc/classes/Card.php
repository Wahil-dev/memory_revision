<?php
    require_once("Bdd.php");
    class Card {
        private $id_card;
        private $path_of_img = "assets/img/";
        private $img_face_down = "default.jpg";
        private $img_face_up;
        private $state = false;
        private $completed = false;
        private static $list_of_name_card = ["dargon-ball-1.jpg", "dargon-ball-2.jpg", "dargon-ball-3.jpg", "dargon-ball-4.jpg", "dargon-ball-5.jpg", "dargon-ball-6.jpg", "dargon-ball-7.jpg", "dargon-ball-8.jpg", "dargon-ball-9.jpg", "dargon-ball-10.jpg", "dargon-ball-11.jpg", "dargon-ball-12.jpg"];    
        private static $list_of_cards = [];

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
        public static function createCards($num_of_card) {
            for($id = 1; $id < $num_of_card*2+1; $id++) {
                array_push(self::$list_of_cards, new Card($id));
            }
            //Partager les images a toutes les cartes
            self::setImgFaceUpToAllCard();
        }
        
        public static function getListOfNameCard()
        {
            return self::$list_of_name_card;
        }

        public static function getListOfCards() {
            if(!isset($_SESSION["list_of_cards"])) {
                shuffle(self::$list_of_cards);
                $_SESSION["list_of_cards"] = serialize(self::$list_of_cards);
            }

            self::$list_of_cards = unserialize($_SESSION["list_of_cards"]);
            var_dump(self::$list_of_cards);
            return self::$list_of_cards;
        }

        public static function setStateById($card_id) {
            $cards = self::getListOfCards();
            $new_list = [];
            foreach((array)$cards as $card) {
                if($card->getIdCard() == $card_id) {
                    $card->setState(true);
                }
                array_push($new_list, $card);
            }
            self::updateListOfCards($new_list);
        }

        public static function updateListOfCards($cards) {
            $_SESSION["list_of_cards"] = serialize($cards);
        }

        public static function setImgFaceUpToAllCard() {
            $cards = self::getListOfCards();
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
            self::updateListOfCards($new_list);
        }

        public static function displayCards() {?>
            <section class="content">
                <div class="menu-game">
                    <a href="inc/quit_game.php">Quit Partie</a>
                </div>
                <form action="inc/b_game.php" method="post" class="form-cards flex-r">
                    <?php foreach(self::getListOfCards() as $card) :?>
                        <?php if($card->getState()) :?>
                            <div class="card">
                                <button type="submit" class="card-btn" name="card_id" value="<?= $card->getIdCard()?>">
                                    <img src="<?= $card->getImgFaceUp()?>" alt="<?= $card->getImgFaceUp()?>">
                                </button>
                            </div>
                        <?php else :?>
                            <div class="card">
                                <button type="submit" class="card-btn" name="card_id" value="<?= $card->getIdCard()?>">
                                    <img src="<?= $card->getImgFaceDown()?>" alt="<?= $card->getImgFaceDown()?>">
                                </button>
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>
                </form>
            </section>
    <?php }

        public static function quitGame() {
            unset($_SESSION["gameStarted"]);
            unset($_SESSION["list_of_cards"]);
            header("location: ../game.php");
            exit();
        }
    }