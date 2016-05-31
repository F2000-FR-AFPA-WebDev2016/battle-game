<?php

namespace Afpa\BattleGameBundle\Model;

abstract class Piece {

    const PICTURE = NULL;
    const SIZE = NULL;

    protected $posX;
    protected $posY;
    protected $orientation;

    public function __construct($x, $y, $orientation) {
        $this->posX = $x;
        $this->posY = $y;
        $this->orientation = $orientation;
    }

}
