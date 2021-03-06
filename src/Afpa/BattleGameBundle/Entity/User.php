<?php

namespace Afpa\BattleGameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255)
     */
    protected $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="topscore", type="integer")
     */
    protected $topscore;

    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="users")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    protected $game;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return User
     */
    public function setLogin($login) {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set topscore
     *
     * @param integer $topscore
     * @return User
     */
    public function setTopscore($topscore) {
        $this->topscore = $topscore;

        return $this;
    }

    /**
     * Get topscore
     *
     * @return integer
     */
    public function getTopscore() {
        return $this->topscore;
    }

    public function verifAuth($password) {
        //comparer mot de passe et du formulaire et de la BDD
        return ($this->password == $password);
    }

    /**
     * Set game
     *
     * @param \Afpa\BattleGameBundle\Entity\Game $game
     * @return User
     */
    public function setGame(\Afpa\BattleGameBundle\Entity\Game $game = null) {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \Afpa\BattleGameBundle\Entity\Game
     */
    public function getGame() {
        return $this->game;
    }

}
