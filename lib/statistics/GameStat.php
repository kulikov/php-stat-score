<?php

class GameStat
{
    private
        $_teamSeason = null,
        $_game       = null;

    public function __construct(Game $game, TeamSeason $teamSeason)
    {
        $this->_game = $game;
        $this->setTeamSeason($teamSeason);
    }


    /**
     * @return Game
     */
    public function getGame()
    {
    	return $this->_game;
    }

    /**
     * @return TeamSeason
     */
    public function getTeamSeason()
    {
    	return $this->_teamSeason;
    }

    /**
     * @return TeamSeason
     */
    public function setTeamSeason(TeamSeason $teamSeason)
    {
    	$this->_teamSeason = $teamSeason;
    	return $this;
    }

    public function getScoreDiff()
    {
        return $this->_getMainScore() - $this->_getSecondScore();

    }

    public function getScoreDiffAbs()
    {
        return abs($this->getScoreDiff());
    }

    public function isWin()
    {
        return $this->_getMainScore() > $this->_getSecondScore();
    }

    public function isLost()
    {
        return $this->_getMainScore() < $this->_getSecondScore();
    }

    public function isDraw()
    {
    	return $this->_getMainScore() == $this->_getSecondScore();
    }

    public function totalMore($value)
    {
        return $this->getTotal() > $value;
    }

    public function totalLess($value)
    {
        return $this->getTotal() < $value;
    }

    public function isEvenTotal()
    {
        return $this->getTotal() % 2 == 0;
    }

    public function getTotal()
    {
        return $this->_game->getHomeTeamScore() + $this->_game->getGuestTeamScore();
    }



    /* PRIVATE */



    private function _getSecondScore()
    {
    	if ($this->getGame()->getHomeTeam() === $this->getTeamSeason()->getTeam()) {
    	    return $this->getGame()->getGuestTeamScore();
    	}
    	return $this->getGame()->getHomeTeamScore();
    }

    private function _getMainScore()
    {
    	if ($this->getGame()->getHomeTeam() === $this->getTeamSeason()->getTeam()) {
    	    return $this->getGame()->getHomeTeamScore();
    	}
    	return $this->getGame()->getGuestTeamScore();
    }
}