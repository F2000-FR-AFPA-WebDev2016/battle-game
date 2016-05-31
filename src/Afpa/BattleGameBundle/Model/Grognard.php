<?php

namespace Afpa\BattleGameBundle\Model;

/**
 * Description of Grognard
 */
class Grognard extends Piece {

    const SIZE = 1;
    const PICTURE = 'grognard.png';

    public function __toString() {
        return 'Gr';
    }

}
