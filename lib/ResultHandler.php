<?php

require_once 'util/ObserverInterface.php';
require_once 'processors/Interface.php';
require_once 'ChampionshipBuilder.php';

class ResultHandler implements Observer
{
    /**
     * @var ChampionshipBuilder
     */
    private $_builder = null;

    /**
     * @var ProcessorInterface
     */
    private $_procStrategy = null;

    /**
     * @return ResultHandler
     */
    public static function factory(ProcessorInterface $procStrategy, ChampionshipBuilder $builder)
    {
    	$instance = new self();
        $instance->setProcessingStrategy($procStrategy);
        $instance->setChampionshipBuilder($builder);
        return $instance;
    }

    /**
     * @return ResultHandler
     */
    public function setProcessingStrategy(ProcessorInterface $procStrategy)
    {
    	$this->_procStrategy = $procStrategy;
    	return $this;
    }

    /**
     * @return ResultHandler
     */
    public function setChampionshipBuilder(ChampionshipBuilder $builder)
    {
    	$this->_builder = $builder;
    	return $this;
    }

    /**
     * Реализация Observer
     * Вызывается когда все данные получены
     *
     * @param $champTableData array — большой массив данных о всех играх чемпионата
     */
	public function update($champTableData)
    {
        /**
         * из «сырых» данных билдим объекты нужных типов (Championship, Team, Game)
         * распихиваем их друг в друга в нужной последовательности
         */
        $championship = $this->_buildChampionshipFromArray($champTableData);

        /* передаем управление ProcessingStrategy — которая в свою очередь знает, что с этими данными дальше надо делать */
        $this->_procStrategy->process($championship);
    }


    /* PRIVATE */

    private function __construct()
    {
    }


    /**
     * @return Championship
     */
    private function _buildChampionshipFromArray($champData)
    {
        $builder = $this->_builder;

        $championship = $builder->createChampionship();

        foreach ($champData as $teamNumber => $teamData) {

            $mainTeam   = $builder->createTeam($teamData['team']);
            $teamSeason = $builder->createTeamSeason($mainTeam, $teamNumber);

            foreach ($teamData['games'] as $gameNumber => $gameData) {

                $game = $builder->createGame()
                    ->setNumber($gameNumber)
                    ->setDate($gameData['date'])
                    ->setHomeTeam($builder->createTeam($gameData['homeTeam']))
                    ->setHomeTeamScore($gameData['homeScore'])
                    ->setGuestTeam($builder->createTeam($gameData['guestTeam']))
                    ->setGuestTeamScore($gameData['guestScore']);

                $teamSeason->addGame($game);
            }

            $championship->addTeamSeason($teamSeason);
        }

        return $championship;
    }

}