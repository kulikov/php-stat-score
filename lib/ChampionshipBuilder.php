<?php

require_once 'Championship.php';
require_once 'Team.php';
require_once 'TeamSeason.php';
require_once 'Game.php';

class ChampionshipBuilder
{
    private static
        $_instance = null;

    private
        $_teams = array();


    /**
     * @return ChampionshipBuilder
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    /**
     * @return Championship
     */
    public function createChampionship()
    {
    	return new Championship();
    }


    /**
     * @return Game
     */
    public function createGame()
    {
    	return new Game();
    }


    /**
     * @return Team
     */
    public function createTeam($teamName)
    {
        if (!isset($this->_teams[$teamName])) {
            $this->_teams[$teamName] = new Team($teamName);
        }
    	return $this->_teams[$teamName];
    }


    /**
     * @return TeamSeason
     */
    public function createTeamSeason(Team $team, $positionNumber)
    {
    	return new TeamSeason($team, $positionNumber);
    }


    /* PRIVATE */

    private function __construct()
    {
    }
}