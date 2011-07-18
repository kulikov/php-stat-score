<?php

class GameSeriesCalculator
{
    private static $_instance = null;

    private $_descriptions = array(
        'SeriesNonDraw'             => '����� ��� ������',
        'SeriesDraw'                => '����� ����� ������',
        'SeriesWin'                 => '������ ������ ������',
        'SeriesNonLost'             => '�� ��������� ������',
        'SeriesNonWin'              => '�� �������� ������',
        'SeriesLost'                => '��������� ������',
        'SeriesTotalMore15'         => '����� ������ ������ 1.5',
        'SeriesTotalLess15'         => '����� ������ ������ 1.5',
        'RotationTotal15'           => '����� ����������� 1.5',
        'SeriesTotalMore25'         => '����� ������ ������ 2.5',
        'SeriesTotalLess25'         => '����� ������ ������ 2.5',
        'RotationTotal25'           => '����� ����������� 2.5',
        'SeriesTotalMore35'         => '����� ������ ������ 3.5',
        'SeriesTotalLess35'         => '����� ������ ������ 3.5',
        'RotationTotal35'           => '����� ����������� 3.5',
        'SeriesTotalMore45'         => '����� ������ ������ 4.5',
        'SeriesTotalLess45'         => '����� ������ ������ 4.5',
        'RotationTotal45'           => '����� ����������� 4.5',
        'SeriesTotalMore55'         => '����� ������ ������ 5.5',
        'SeriesTotalLess55'         => '����� ������ ������ 5.5',
        'RotationTotal55'           => '����� ����������� 5.5',
        'SeriesTotalMore65'         => '����� ������ ������ 6.5',
        'SeriesTotalLess65'         => '����� ������ ������ 6.5',
        'RotationTotal65'           => '����� ����������� 6.5',
        'SeriesEven'                => '��� ������',
        'SeriesOdd'                 => '����� ������',
        'RotationEvenOdd'           => '���/����� �����������',
        'RotationDrawResult'        => '�����/��������� �����������',
        'RotationDrawWin'           => '�����/������ �����������',
        'RotationDrawLost'          => '�����/��������� �����������',
        'SeriesTotalMore25AndEven'  => '������������ ����� ������ 2,5 � ��� ������',
        'SeriesTotalMore25AndOdd'   => '������������ ����� ������ 2,5 � ����� ������',
        'SeriesTotalLess25AndEven'  => '������������ ����� ������ 2,5 � ��� ������',
        'SeriesTotalLess25AndOdd'   => '������������ ����� ������ 2,5 � ����� ������',
        'SeriesAllTeamScored'       => '��� ������ ������',
        'SeriesOneTeamScored'       => '������ ������ ����',
        'SeriesNoneTeamScored'      => '����� �� �����',
        'SeriesOneOrNoneTeamScored' => '������ ������ ���� ��� ����� �� �����',
        'RotationAllOneTeamScored'  => '������ ����/��� �����������',

        'SeriesAllTeamScoredAndTotalMore25'  => '��� ������ + �� 2,5',
        'SeriesAllTeamScoredAndTotalLess25'  => '��� ������ + �� 2,5',
        'SeriesOneTeamScoredAndTotalMore25'  => '������ ������ ���� + �� 2,5',
        'SeriesOneTeamScoredAndTotalLess25'  => '������ ������ ���� + �� 2,5',

        'SeriesAllTeamScoredAndTotalMore35'  => '��� ������ + �� 3,5',
        'SeriesAllTeamScoredAndTotalLess35'  => '��� ������ + �� 3,5',
        'SeriesOneTeamScoredAndTotalMore35'  => '������ ������ ���� + �� 3,5',
        'SeriesOneTeamScoredAndTotalLess35'  => '������ ������ ���� + �� 3,5',



        'SeriesTotal1'              => '����� = 1',
        'SeriesTotal2'              => '����� = 2',
        'SeriesTotal3'              => '����� = 3',
        'SeriesTotal4'              => '����� = 4',
        'SeriesTotal5'              => '����� = 5',
        'SeriesTotal6'              => '����� = 6',
        'SeriesTotal7'              => '����� = 7',
        'SeriesOddAndWin'           => '������������ ����� � ������ ������',
        'SeriesOddAndLost'          => '������������ ����� � ��������� ������',
        'SeriesEvenAndWin'          => '������������ ��� � ������ ������',
        'SeriesEvenAndLost'         => '������������ ��� � ��������� ������',
        'SeriesTotal0Or1'           => '����� ����� 0-1 ������',
        'SeriesTotal2Or3'           => '����� ����� 2-3 ������',
        'SeriesTotal4OrMore'        => '����� ����� 4 � ������',
        'SeriesDiff1'               => '������� ����� +1 ������',
        'SeriesDiffMinus1'          => '������� ����� -1 ������',
        'SeriesDiff1Abs'            => '������� ����� ������ 1 ������',
        'SeriesDiff2'               => '������� ����� +2 ������',
        'SeriesDiffMinus2'          => '������� ����� -2 ������',
        'SeriesDiff2Abs'            => '������� ����� ������ 2 ������',
        'SeriesDiff3'               => '������� ����� +3 ������',
        'SeriesDiffMinus3'          => '������� ����� -3 ������',
        'SeriesDiff3Abs'            => '������� ����� ������ 3 ������',
        'RotationWinLost'           => '����������� ������/���������',
    );


    /**
     * @return GameSeriesCalculator
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function calcAllStatistics(TeamSeason $teamSeason, $gameType = null)
    {
        $calcMethods = array_filter(get_class_methods($this), create_function('$name', 'return preg_match("/^(_series|_rotation)/", $name);'));
        $output      = array();
        $counter     = array();
        $listValues  = array();

        foreach ($calcMethods as $method) {

            $isRotation = strpos($method, '_rotation') === 0;
            $key = ($gameType ? $gameType : 'main') . '::' . ucfirst(substr($method, 1));

            $output[$key]  = array();
            $counter[$key] = 0;
            foreach ($teamSeason->getGames() as $game) {

                if ($gameType == Game::HOME && !$teamSeason->isHomeGame($game)) continue;
                if ($gameType == Game::GUEST && $teamSeason->isHomeGame($game)) continue;

                $value = $this->{$method}($game->getGameStat($teamSeason));

                if ($isRotation) {
                    $calculate = isset($listValues[$key]) && ($prev = end($listValues[$key])) !== null && $value !== null && $prev !== $value;
                    if ($calculate && empty($counter[$key])) {
                        $counter[$key] = 1; // �������� ��� ����������� �� ���� ������ ����
                    }
                } else {
                    $calculate = $value;
                }

                if ($calculate) {
                    $counter[$key]++;
                    @$output[$key][$counter[$key]]++;
                } else {
                    $counter[$key] = 0;
                }

                $listValues[$key][] = $value;
            }
        }

        return $output;
    }

    public function getDescription($name)
    {
        if (!isset($this->_descriptions[$name])) {
            return null;
//          $methodRefl = new ReflectionMethod($this, '_' . strtolower($name[0]) . substr($name, 1));
//        	$this->_descriptions[$name] = trim(str_replace(array('*', '/'), '', $methodRefl->getDocComment()));
        }
        return $this->_descriptions[$name];
    }



    /* ************************************************** */


    /**
     * ����� ��� ������
     */
    private function _seriesNonDraw(GameStat $gameStat)
    {
        return !$gameStat->isDraw();
    }

    /**
     * ����� ����� ������
     */
    private function _seriesDraw(GameStat $gameStat)
    {
        return $gameStat->isDraw();
    }

    /**
     * ������ ������ ������
     */
    private function _seriesWin(GameStat $gameStat)
    {
        return $gameStat->isWin();
    }

    /**
     * �� ��������� ������
     */
    private function _seriesNonLost(GameStat $gameStat)
    {
        return !$gameStat->isLost();
    }

    /**
     * �� �������� ������
     */
    private function _seriesNonWin(GameStat $gameStat)
    {
        return !$gameStat->isWin();
    }

    /**
     * ��������� ������
     */
    private function _seriesLost(GameStat $gameStat)
    {
        return $gameStat->isLost();
    }

    /**
     * ����� ������ ������ 1.5
     */
    private function _seriesTotalMore15(GameStat $gameStat)
    {
        return $gameStat->totalMore(1.5);
    }

    /**
     * ����� ������ ������ 1.5
     */
    private function _seriesTotalLess15(GameStat $gameStat)
    {
        return $gameStat->totalLess(1.5);
    }

    /**
     * ����� ����������� 1.5
     */
    private function _rotationTotal15(GameStat $gameStat)
    {
        return $gameStat->totalLess(1.5);
    }

    /**
     * ����� ������ ������ 2.5
     */
    private function _seriesTotalMore25(GameStat $gameStat)
    {
        return $gameStat->totalMore(2.5);
    }

    /**
     * ����� ������ ������ 2.5
     */
    private function _seriesTotalLess25(GameStat $gameStat)
    {
        return $gameStat->totalLess(2.5);
    }

    /**
     * ����� ����������� 2.5
     */
    private function _rotationTotal25(GameStat $gameStat)
    {
        return $gameStat->totalLess(2.5);
    }

    /**
     * ����� ������ ������ 3.5
     */
    private function _seriesTotalMore35(GameStat $gameStat)
    {
        return $gameStat->totalMore(3.5);
    }

    /**
     * ����� ������ ������ 3.5
     */
    private function _seriesTotalLess35(GameStat $gameStat)
    {
        return $gameStat->totalLess(3.5);
    }

    /**
     * ����� ����������� 3.5
     */
    private function _rotationTotal35(GameStat $gameStat)
    {
        return $gameStat->totalLess(3.5);
    }

    /**
     * ����� ������ ������ 4.5
     */
    private function _seriesTotalMore45(GameStat $gameStat)
    {
        return $gameStat->totalMore(4.5);
    }

    /**
     * ����� ������ ������ 4.5
     */
    private function _seriesTotalLess45(GameStat $gameStat)
    {
        return $gameStat->totalLess(4.5);
    }

    /**
     * ����� ����������� 4.5
     */
    private function _rotationTotal45(GameStat $gameStat)
    {
        return $gameStat->totalLess(4.5);
    }

    /**
     * ����� ������ ������ 5.5
     */
    private function _seriesTotalMore55(GameStat $gameStat)
    {
        return $gameStat->totalMore(5.5);
    }

    /**
     * ����� ������ ������ 5.5
     */
    private function _seriesTotalLess55(GameStat $gameStat)
    {
        return $gameStat->totalLess(5.5);
    }

    /**
     * ����� ����������� 5.5
     */
    private function _rotationTotal55(GameStat $gameStat)
    {
        return $gameStat->totalLess(5.5);
    }

    /**
     * ����� ������ ������ 6.5
     */
    private function _seriesTotalMore65(GameStat $gameStat)
    {
        return $gameStat->totalMore(6.5);
    }

    /**
     * ����� ������ ������ 6.5
     */
    private function _seriesTotalLess65(GameStat $gameStat)
    {
        return $gameStat->totalLess(6.5);
    }

    /**
     * ����� ����������� 6.5
     */
    private function _rotationTotal65(GameStat $gameStat)
    {
        return $gameStat->totalLess(6.5);
    }

    /**
     * ��� ������
     */
    private function _seriesEven(GameStat $gameStat)
    {
        return $gameStat->isEvenTotal();
    }

    /**
     * ����� ������
     */
    private function _seriesOdd(GameStat $gameStat)
    {
        return !$gameStat->isEvenTotal();
    }

    /**
     * ���/����� �����������
     */
    private function _rotationEvenOdd(GameStat $gameStat)
    {
        return $gameStat->isEvenTotal();
    }

    /**
     * �����/��������� �����������
     */
    private function _rotationDrawResult(GameStat $gameStat)
    {
        return $gameStat->isDraw();
    }

    /**
     * �����/������ �����������
     */
    private function _rotationDrawWin(GameStat $gameStat)
    {
        return $gameStat->isDraw() ? 1 : ($gameStat->isWin() ? 2 : null);
    }

    /**
     * �����/��������� �����������
     */
    private function _rotationDrawLost(GameStat $gameStat)
    {
        return $gameStat->isDraw() ? 1 : ($gameStat->isLost() ? 2 : null);
    }

    /**
     * ������������ ����� ������ 2,5 � ��� ������
     */
    private function _seriesTotalMore25AndEven(GameStat $gameStat)
    {
        return $gameStat->totalMore(2.5) && $gameStat->isEvenTotal();
    }

    /**
     * ������������ ����� ������ 2,5 � ����� ������
     */
    private function _seriesTotalMore25AndOdd(GameStat $gameStat)
    {
        return $gameStat->totalMore(2.5) && !$gameStat->isEvenTotal();
    }

    /**
     * ������������ ����� ������ 2,5 � ��� ������
     */
    private function _seriesTotalLess25AndEven(GameStat $gameStat)
    {
        return $gameStat->totalLess(2.5) && $gameStat->isEvenTotal();
    }

    /**
     * ������������ ����� ������ 2,5 � ����� ������
     */
    private function _seriesTotalLess25AndOdd(GameStat $gameStat)
    {
        return $gameStat->totalLess(2.5) && !$gameStat->isEvenTotal();
    }


    /**
     * ��� ������ ������
     */
    private function _seriesAllTeamScored(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore();
    }

    /**
     * ������ ������ ����
     */
    private function _seriesOneTeamScored(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return ($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1;
    }

    /**
     * ����� �� �����
     */
    private function _seriesNoneTeamScored(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return !$game->getGuestTeamScore() && !$game->getHomeTeamScore();
    }

    /**
     * ������ ������ ���� ��� ����� �������
     */
    private function _seriesOneOrNoneTeamScored(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return ($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) < 2;
    }

    /**
     * ������ ����/��� �����������
     */
    private function _rotationAllOneTeamScored(GameStat $gameStat)
    {
        if ($this->_seriesAllTeamScored($gameStat)) {
            return 'all';
        }
        if ($this->_seriesOneTeamScored($gameStat)) {
            return 'one';
        }
        return 'none';
    }



    /**
     * ��� ������ + �� 2,5
     */
    private function _seriesAllTeamScoredAndTotalMore25(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore() && $gameStat->totalMore(2.5);
    }

    /**
     * ��� ������ + �� 2,5
     */
    private function _seriesAllTeamScoredAndTotalLess25(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore() && $gameStat->totalLess(2.5);
    }

    /**
     * ������ ������ ���� + �� 2,5
     */
    private function _seriesOneTeamScoredAndTotalMore25(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return (($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1) && $gameStat->totalMore(2.5);
    }

    /**
     * ������ ������ ���� + �� 2,5
     */
    private function _seriesOneTeamScoredAndTotalLess25(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return (($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1) && $gameStat->totalLess(2.5);
    }



    /**
     * ��� ������ + �� 3,5
     */
    private function _seriesAllTeamScoredAndTotalMore35(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore() && $gameStat->totalMore(3.5);
    }

    /**
     * ��� ������ + �� 3,5
     */
    private function _seriesAllTeamScoredAndTotalLess35(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore() && $gameStat->totalLess(3.5);
    }

    /**
     * ������ ������ ���� + �� 3,5
     */
    private function _seriesOneTeamScoredAndTotalMore35(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return (($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1) && $gameStat->totalMore(3.5);
    }

    /**
     * ������ ������ ���� + �� 3,5
     */
    private function _seriesOneTeamScoredAndTotalLess35(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return (($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1) && $gameStat->totalLess(3.5);
    }







    /**
     * ����� = 1
     */
    private function _seriesTotal1(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 1;
    }

    /**
     * ����� = 2
     */
    private function _seriesTotal2(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 2;
    }

    /**
     * ����� = 3
     */
    private function _seriesTotal3(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 3;
    }

    /**
     * ����� = 4
     */
    private function _seriesTotal4(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 4;
    }

    /**
     * ����� = 5
     */
    private function _seriesTotal5(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 5;
    }

    /**
     * ����� = 6
     */
    private function _seriesTotal6(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 6;
    }

    /**
     * ����� = 7
     */
    private function _seriesTotal7(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 7;
    }

    /**
     * ������������ ����� � ������ ������
     */
    private function _seriesOddAndWin(GameStat $gameStat)
    {
        return !$gameStat->isEvenTotal() && $gameStat->isWin();
    }

    /**
     * ������������ ����� � ��������� ������
     */
    private function _seriesOddAndLost(GameStat $gameStat)
    {
        return !$gameStat->isEvenTotal() && $gameStat->isLost();
    }

    /**
     * ������������ ��� � ������ ������
     */
    private function _seriesEvenAndWin(GameStat $gameStat)
    {
        return $gameStat->isEvenTotal() && $gameStat->isWin();
    }

    /**
     * ������������ ��� � ��������� ������
     */
    private function _seriesEvenAndLost(GameStat $gameStat)
    {
        return $gameStat->isEvenTotal() && $gameStat->isLost();
    }

    /**
     * ����� ����� 0-1 ������
     */
    private function _seriesTotal0Or1(GameStat $gameStat)
    {
        return $gameStat->getTotal() < 2;
    }

    /**
     * ����� ����� 2-3 ������
     */
    private function _seriesTotal2Or3(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 2 || $gameStat->getTotal() == 3;
    }

    /**
     * ����� ����� 4 � ������
     */
    private function _seriesTotal4OrMore(GameStat $gameStat)
    {
        return $gameStat->getTotal() >= 4;
    }

    /**
     * ������� ����� +1 ������
     */
    private function _seriesDiff1(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == 1;
    }

    /**
     * ������� ����� -1 ������
     */
    private function _seriesDiffMinus1(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == -1;
    }

    /**
     * ������� ����� ������ 1 ������
     */
    private function _seriesDiff1Abs(GameStat $gameStat)
    {
        return $gameStat->getScoreDiffAbs() == 1;
    }

    /**
     * ������� ����� +2 ������
     */
    private function _seriesDiff2(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == 2;
    }

    /**
     * ������� ����� -2 ������
     */
    private function _seriesDiffMinus2(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == -2;
    }

    /**
     * ������� ����� ������ 2 ������
     */
    private function _seriesDiff2Abs(GameStat $gameStat)
    {
        return $gameStat->getScoreDiffAbs() == 2;
    }

    /**
     * ������� ����� +3 ������
     */
    private function _seriesDiff3(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == 3;
    }

    /**
     * ������� ����� -3 ������
     */
    private function _seriesDiffMinus3(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == -3;
    }

    /**
     * ������� ����� ������ 3 ������
     */
    private function _seriesDiff3Abs(GameStat $gameStat)
    {
        return $gameStat->getScoreDiffAbs() == 3;
    }

    /**
     * ����������� ������/���������
     */
    private function _rotationWinLost(GameStat $gameStat)
    {
        return $gameStat->isWin() ? 1 : ($gameStat->isLost() ? 2 : null);
    }
}