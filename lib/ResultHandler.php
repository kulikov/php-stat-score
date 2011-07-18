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
     * ���������� Observer
     * ���������� ����� ��� ������ ��������
     *
     * @param $champTableData array ��������� ������ ������ � ���� ����� ����������
     */
	public function update($champTableData)
    {
        /**
         * �� ������� ������ ������ ������� ������ ����� (Championship, Team, Game)
         * ����������� �� ���� � ����� � ������ ������������������
         */
        $championship = $this->_buildChampionshipFromArray($champTableData);

        /* �������� ���������� ProcessingStrategy � ������� � ���� ������� �����, ��� � ����� ������� ������ ���� ������ */
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