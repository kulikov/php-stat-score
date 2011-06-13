<?php

require_once 'TeamSeason.php';

class Championship
{
    private $_teamSeasons = array();

    public function addTeamSeason(TeamSeason $teamSeason)
    {
        $this->_teamSeasons[] = $teamSeason;
        return $this;
    }

    public function getTeamSeasons()
    {
    	return $this->_teamSeasons;
    }
}