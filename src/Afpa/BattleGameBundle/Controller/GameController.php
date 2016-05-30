<?php

namespace Afpa\BattleGameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class GameController extends Controller {

    /**
     * @Route("/game", name="home_game")
     * @Template()
     */
    public function indexAction() {
        $oBoard = new \Afpa\BattleGameBundle\Model\Board();

        return array(
            'board_pieces' => $oBoard->getBoardPieces(),
            'board_shoot' => $oBoard->getBoardShoot(),
        );
    }

}
