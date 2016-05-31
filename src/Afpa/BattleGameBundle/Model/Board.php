<?php

namespace Afpa\BattleGameBundle\Model;

class Board {

    protected $boardPieces;
    protected $boardShoot;
    protected $playerTurn;

    public function __construct() {
        // initialisation des plateaux
        $this->boardShoot = array();
        $this->boardPieces = array();

        // initialisation des cellules
        for ($ligne = 0; $ligne < 10; $ligne++) {
            $this->boardShoot[$ligne] = array();
            $this->boardPieces[$ligne] = array();


            for ($col = 0; $col < 10; $col++) {
                $this->boardShoot[$ligne][$col] = 'X';
                $this->boardPieces[$ligne][$col] = 'X';
            }
        }

        // initialisation des pieces
        /* $oCav1 = new Cavalier($x, $y, $orientation);
          $oCav2 = new Cavalier($x, $y, $orientation);
          $oCav3 = new Cavalier($x, $y, $orientation); */
    }

    public function getBoardPieces() {
        return $this->boardPieces;
    }

    public function getBoardShoot() {
        return $this->boardShoot;
    }

    public function getPlayerTurn() {
        return $this->playerTurn;
    }

    public function setBoardPieces($boardPieces) {
        $this->boardPieces = $boardPieces;
    }

    public function setBoardShoot($boardShoot) {
        $this->boardShoot = $boardShoot;
    }

    public function setPlayerTurn($playerTurn) {
        $this->playerTurn = $playerTurn;
    }

}
