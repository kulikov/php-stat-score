<?php

require_once 'statistics/GameStat.php';

class Game
{
    const
        HOME  = 'home',
        GUEST = 'guest';

    private
        $_number         = null,
        $_date           = null,
        $_homeTeam       = null,
        $_homeTeamScore  = null,
        $_guestTeam      = null,
        $_guestTeamScore = null;

    private
        $_gameStat = null;


    public function getGameStat(TeamSeason $teamSeason)
    {
    	if ($this->_gameStat === null) {
    	    $this->_gameStat = new GameStat($this, $teamSeason);
    	}

    	$this->_gameStat->setTeamSeason($teamSeason);
    	return $this->_gameStat;
    }

	public function getNumber()
    {
        return $this->_number;
    }

	public function setNumber($number)
    {
        $this->_number = $number;
        return $this;
    }

	public function getDate()
    {
        return $this->_date;
    }

	public function setDate($date)
    {
        $this->_date = is_numeric($date) ? $date : strtotime($date);
        return $this;
    }

	public function getHomeTeam()
    {
        return $this->_homeTeam;
    }

	public function setHomeTeam(Team $team)
    {
        $this->_homeTeam = $team;
        return $this;
    }

	public function getHomeTeamScore()
    {
        return $this->_homeTeamScore;
    }

	public function setHomeTeamScore($score)
    {
        $this->_homeTeamScore = (int) $score;
        return $this;
    }

	public function getGuestTeam()
    {
        return $this->_guestTeam;
    }

	public function setGuestTeam(Team $team)
    {
        $this->_guestTeam = $team;
        return $this;
    }

	public function getGuestTeamScore()
    {
        return $this->_guestTeamScore;
    }

	public function setGuestTeamScore($score)
    {
        $this->_guestTeamScore = (int) $score;
        return $this;
    }
}