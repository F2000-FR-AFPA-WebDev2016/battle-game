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
                $this->boardShoot[$ligne][$col] = '';
                $this->boardPieces[$ligne][$col] = '';
            }
        }

        // Placement des pièces auto
        $this->initPiecesAuto();
    }

    public function initPiecesAuto() {
        // initialisation des pieces
        $oPiece = new Cavalier(Piece::randomOrientation());

        // debug
        print_r($oPiece);
        print_r($oPiece::SIZE);

        do {
            $aPos = self::randomCoords();
        } while (!$this->caseEstVide($aPos) && !$this->placementEstPossible($aPos, $oPiece));

        $oPiece->setXY($aPos[0], $aPos[1]);

        // debug
        print_r($aPos);

        /*  $oCav2 = new Cavalier($x, $y, $orientation);
          $oCav3 = new Cavalier($x, $y, $orientation); */
    }

    public function caseEstVide($aPos) {
        // Est-ce que la case est vide
        return TRUE;
    }

    public function placementEstPossible($aPos, $oPiece) {
        // Est-ce les cases nécessaires sont vides et dans le plateau
        return TRUE;
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

    public static function randomCoords() {
        return array(rand(0, 9), rand(0, 9));
    }

}
