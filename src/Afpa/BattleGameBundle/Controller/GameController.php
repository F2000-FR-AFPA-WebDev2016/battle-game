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
            'board_pieces1' => $oBoard->getBoardPieces1(),
            'board_shoot1' => $oBoard->getBoardShoot1(),
            'board_pieces2' => $oBoard->getBoardPieces2(),
            'board_shoot2' => $oBoard->getBoardShoot2(),
        );
    }

}
