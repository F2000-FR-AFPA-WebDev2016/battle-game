<?php

namespace Afpa\BattleGameBundle\Model;

abstract class Piece {

    const PICTURE = NULL;
    const SIZE = NULL;
    const ORIENT_H = 0;
    const ORIENT_V = 1;

    protected $posX;
    protected $posY;
    protected $orientation;

    public function __construct($orientation) {
        $this->orientation = $orientation;
    }

    public function setXY($x, $y) {
        $this->posX = $x;
        $this->posY = $y;
    }

    public static function randomOrientation() {
        return rand(0, 1);
    }

}
