<?php

class Team
{
    private $_name = null;

    public function __construct($teamName)
    {
    	$this->_name = $teamName;
    }

    public function getName()
    {
    	return $this->_name;
    }

    public function __toString()
    {
    	return $this->getName();
    }

    public function isEqual(Team $team)
    {
    	return $this->getName() === $team->getName();
    }
}