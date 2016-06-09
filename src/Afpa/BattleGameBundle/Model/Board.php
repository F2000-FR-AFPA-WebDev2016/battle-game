<?php

namespace Afpa\BattleGameBundle\Model;

use Afpa\BattleGameBundle\Entity\Game;
use Afpa\BattleGameBundle\Model\Piece;

class Board {

    const J1 = 1;
    const J2 = 2;

    protected $aPieces1;
    protected $boardPieces1;
    protected $boardShoot1;
    protected $aPieces2;
    protected $boardPieces2;
    protected $boardShoot2;
    protected $playerTurn;
    protected $playerJ1;
    protected $playerJ2;

    public function __construct() {
// initialisation des plateaux J1
        $this->boardShoot1 = array();
        $this->boardPieces1 = array();

// initialisation des cellules J1
        for ($ligne = 0; $ligne < 10; $ligne++) {
            $this->boardShoot1[$ligne] = array();
            $this->boardPieces1[$ligne] = array();


            for ($col = 0; $col < 10; $col++) {
                $this->boardShoot1[$ligne][$col] = '';
                $this->boardPieces1[$ligne][$col] = '';
            }
        }

// initialisation des pieces J1
        $this->aPieces1 = array();
        $this->aPieces1[] = new Cavalier(Piece::randomOrientation());
        $this->aPieces1[] = new Cavalier(Piece::randomOrientation());
        $this->aPieces1[] = new Cavalier(Piece::randomOrientation());
        $this->aPieces1[] = new Grognard(Piece::randomOrientation());
        $this->aPieces1[] = new Grognard(Piece::randomOrientation());
        $this->aPieces1[] = new Canon(Piece::randomOrientation());

// initialisation J2
        $this->boardShoot2 = $this->boardShoot1;
        $this->boardPieces2 = $this->boardPieces1;
        $this->aPieces2 = $this->aPieces1;

// Placement des pièces auto
        $this->initPiecesAuto($this->boardPieces1, $this->aPieces1);
        $this->initPiecesAuto($this->boardPieces2, $this->aPieces2);

        $this->playerTurn = self::J1;
    }

    public function initPiecesAuto(&$oBoard, &$aPieces) {
        foreach ($aPieces as $idx => $oPiece) {
// Calcul de coordonnées valides
            do {
                $aPos = self::randomCoords();
            } while (!$this->placementEstPossible($oBoard, $aPos, $oPiece));

// Placement de la pièce
            $oPiece->setXY($aPos[0], $aPos[1]);
            for ($i = 0; $i < $oPiece::SIZE; $i++) {
                if ($oPiece->getOrientation() == Piece::ORIENT_H) {
                    $oBoard[$aPos[0]][$aPos[1] + $i] = $oPiece;
                } else {
                    $oBoard[$aPos[0] + $i][$aPos[1]] = $oPiece;
                }
            }

            unset($aPieces[$idx]);
        }
    }

    public function doClick($x, $y) {


// mettre une croix dans (x, y)
        if ($this->playerTurn == self::J1) {
            $this->boardShoot1[$x][$y] = 'x';
            if ($this->boardPieces2[$x][$y] instanceof Piece) {
                $this->boardPieces2[$x][$y] = '0';
            } else {
                $this->boardPieces2[$x][$y] = 'x';
            }
        } else {
            if ($this->playerTurn == self::J2) {
                $this->boardShoot2[$x][$y] = 'x';
                if ($this->boardPieces1[$x][$y] instanceof Piece) {
                    $this->boardPieces1[$x][$y] = '0';
                } else {
                    $this->boardPieces1[$x][$y] = 'x';
                }
            }
        }
        $this->nextPlayer();
    }

    public function nextPlayer() {
        if ($this->playerTurn == self::J1) {
            $this->playerTurn = self::J2;
        } else {
            $this->playerTurn = self::J1;
        }
    }

    public function getPlayer() {
        if ($this->playerTurn == self::J1) {
            return 'C\'est au joueur 1 de jouer';
        } else {
            return 'C\'est au joueur 2 de jouer';
        }
    }

    public function getPlayerId() {
        if ($this->playerTurn == self::J1) {
            return $this->playerJ1;
        } else {
            return $this->playerJ2;
        }
    }

    /**
     * Retourne TRUE si la case est vide
     * @param array $oBoard
     * @param array $aPos
     * @return bool
     */
    public function caseEstVide($oBoard, $aPos) {
        return ($oBoard[$aPos[0]][$aPos[1]] === '');
    }

    /**
     * Retourne TRUE si la case est contenue dans le tableau
     * @param array $oBoard
     * @param array $aPos
     * @return bool
     */
    public function caseEstValide($aPos) {
        if ($aPos[0] > 9 or $aPos[1] > 9) {
            return FALSE;
        }

        return TRUE;
    }

    public function placementEstPossible($oBoard, $aPos, $oPiece) {
        $bPossible = True;

// Est-ce les cases nécessaires sont vides et dans le plateau
        for ($i = 0; $i < $oPiece::SIZE; $i++) {
            if ($oPiece->getOrientation() === Piece::ORIENT_H) {
                $testXY = array($aPos[0], $aPos[1] + $i);
            } else {
                $testXY = array($aPos[0] + $i, $aPos[1]);
            }

            $bPossible = $bPossible &&
                    $this->caseEstValide($testXY) &&
                    $this->caseEstVide($oBoard, $testXY);
        }


        return $bPossible;
    }

    public function getBoardPieces1() {
        return $this->boardPieces1;
    }

    public function getBoardShoot1() {
        return $this->boardShoot1;
    }

    public function getBoardPieces2() {
        return $this->boardPieces2;
    }

    public function getBoardShoot2() {
        return $this->boardShoot2;
    }

    public function getPlayerTurn() {
        return $this->playerTurn;
    }

    public function setPlayerTurn($playerTurn) {
        $this->playerTurn = $playerTurn;
    }

    public static function randomCoords() {
        return array(rand(0, 9), rand(0, 9));
    }

    public function setPlayers(Game $oGame) {
        $aPlayers = $oGame->getUsers()->toArray();
        if (count($aPlayers) == 2) {
            shuffle($aPlayers);

            $this->playerJ1 = $aPlayers[0]->getId();
            $this->playerJ2 = $aPlayers[1]->getId();
        }
    }

    public function getInfosJoueur($idJoueur) {
        $aData = array(
            'board_pieces1' => $this->getBoardPieces1(),
            'board_shoot1' => $this->getBoardShoot1(),
            'board_pieces2' => $this->getBoardPieces2(),
            'board_shoot2' => $this->getBoardShoot2(),
        );

        if ($idJoueur == $this->playerJ1) {
            $aData['board_pieces2'] = null;
            $aData['board_shoot2'] = null;
        }
        if ($idJoueur == $this->playerJ2) {
            $aData['board_pieces1'] = null;
            $aData['board_shoot1'] = null;
        }

        return $aData;
    }

}
