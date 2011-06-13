<?php

require_once 'Team.php';
require_once 'Game.php';

class TeamSeason
{
    private
        $_team           = null,
        $_positionNumber = null,
        $_games          = array();

    public function __construct(Team $team, $positionNumber = null)
    {
    	$this->_team           = $team;
    	$this->_positionNumber = $positionNumber;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->_team;
    }

    public function getPositionNumber()
    {
    	return $this->_positionNumber;
    }

    public function setPositionNumber($number)
    {
    	$this->_positionNumber = $number;
    	return $this;
    }

    public function addGame(Game $game)
    {
    	$this->_games[$game->getNumber()] = $game;
    	return $this;
    }

    /**
     * @return Game
     */
    public function getGame($number)
    {
    	return isset($this->_games[$number]) ? $this->_games[$number] : null;
    }

    /**
     * @return array
     */
    public function getGames()
    {
    	ksort($this->_games);
        return $this->_games;
    }

    public function getYears()
    {
    	$years = array();
    	foreach ($this->getGames() as $game) {
            $years[date('Y', $game->getDate())] = 1;
    	}
    	return array_keys($years);
    }

    public function isHomeGame(Game $game)
    {
    	return $this->getTeam()->isEqual($game->getHomeTeam());
    }
}