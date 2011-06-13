<?php

class GameSeriesCalculator
{
    private static $_instance = null;

    private $_descriptions = array(
        'SeriesNonDraw'             => 'Серии без ничьих',
        'SeriesDraw'                => 'серии ничьи подряд',
        'SeriesWin'                 => 'только победы подряд',
        'SeriesNonLost'             => 'не проиграть подряд',
        'SeriesNonWin'              => 'не выиграть подряд',
        'SeriesLost'                => 'проиграть подряд',
        'SeriesTotalMore15'         => 'тотал больше подряд 1.5',
        'SeriesTotalLess15'         => 'тотал меньше подряд 1.5',
        'RotationTotal15'           => 'тотал чередование 1.5',
        'SeriesTotalMore25'         => 'тотал больше подряд 2.5',
        'SeriesTotalLess25'         => 'тотал меньше подряд 2.5',
        'RotationTotal25'           => 'тотал чередование 2.5',
        'SeriesTotalMore35'         => 'тотал больше подряд 3.5',
        'SeriesTotalLess35'         => 'тотал меньше подряд 3.5',
        'RotationTotal35'           => 'тотал чередование 3.5',
        'SeriesTotalMore45'         => 'тотал больше подряд 4.5',
        'SeriesTotalLess45'         => 'тотал меньше подряд 4.5',
        'RotationTotal45'           => 'тотал чередование 4.5',
        'SeriesTotalMore55'         => 'тотал больше подряд 5.5',
        'SeriesTotalLess55'         => 'тотал меньше подряд 5.5',
        'RotationTotal55'           => 'тотал чередование 5.5',
        'SeriesTotalMore65'         => 'тотал больше подряд 6.5',
        'SeriesTotalLess65'         => 'тотал меньше подряд 6.5',
        'RotationTotal65'           => 'тотал чередование 6.5',
        'SeriesEven'                => 'чет подряд',
        'SeriesOdd'                 => 'нечет подряд',
        'RotationEvenOdd'           => 'чет/нечет чередование',
        'RotationDrawResult'        => 'ничья/результат чередование',
        'RotationDrawWin'           => 'ничья/победа чередование',
        'RotationDrawLost'          => 'ничья/проиграли чередование',
        'SeriesTotalMore25AndEven'  => 'Одновременно тотал больше 2,5 и чет подряд',
        'SeriesTotalMore25AndOdd'   => 'Одновременно тотал больше 2,5 и нечет подряд',
        'SeriesTotalLess25AndEven'  => 'Одновременно тотал меньше 2,5 и чет подряд',
        'SeriesTotalLess25AndOdd'   => 'Одновременно тотал меньше 2,5 и нечет подряд',
        'SeriesAllTeamScored'       => 'Обе забили подряд',
        'SeriesOneTeamScored'       => 'Забила только одна',
        'SeriesNoneTeamScored'      => 'Никто не забил',
        'SeriesOneOrNoneTeamScored' => 'Забила только одна или никто не забил',
        'RotationAllOneTeamScored'  => 'Забила одна/обе чередование',

        'SeriesAllTeamScoredAndTotalMore25'  => 'Обе забили + ТБ 2,5',
        'SeriesAllTeamScoredAndTotalLess25'  => 'Обе забили + ТМ 2,5',
        'SeriesOneTeamScoredAndTotalMore25'  => 'Забила только одна + ТБ 2,5',
        'SeriesOneTeamScoredAndTotalLess25'  => 'Забила только одна + ТМ 2,5',

        'SeriesAllTeamScoredAndTotalMore35'  => 'Обе забили + ТБ 3,5',
        'SeriesAllTeamScoredAndTotalLess35'  => 'Обе забили + ТМ 3,5',
        'SeriesOneTeamScoredAndTotalMore35'  => 'Забила только одна + ТБ 3,5',
        'SeriesOneTeamScoredAndTotalLess35'  => 'Забила только одна + ТМ 3,5',



        'SeriesTotal1'              => 'Тотал = 1',
        'SeriesTotal2'              => 'Тотал = 2',
        'SeriesTotal3'              => 'Тотал = 3',
        'SeriesTotal4'              => 'Тотал = 4',
        'SeriesTotal5'              => 'Тотал = 5',
        'SeriesTotal6'              => 'Тотал = 6',
        'SeriesTotal7'              => 'Тотал = 7',
        'SeriesOddAndWin'           => 'Одновременно нечет и победа подряд',
        'SeriesOddAndLost'          => 'Одновременно нечет и поражение подряд',
        'SeriesEvenAndWin'          => 'Одновременно чет и победа подряд',
        'SeriesEvenAndLost'         => 'Одновременно чет и поражение подряд',
        'SeriesTotal0Or1'           => 'Сумма мячей 0-1 подряд',
        'SeriesTotal2Or3'           => 'Сумма мячей 2-3 подряд',
        'SeriesTotal4OrMore'        => 'Сумма мячей 4 и больше',
        'SeriesDiff1'               => 'Разница мячей +1 подряд',
        'SeriesDiffMinus1'          => 'Разница мячей -1 подряд',
        'SeriesDiff1Abs'            => 'Разница мячей модуль 1 подряд',
        'SeriesDiff2'               => 'Разница мячей +2 подряд',
        'SeriesDiffMinus2'          => 'Разница мячей -2 подряд',
        'SeriesDiff2Abs'            => 'Разница мячей модуль 2 подряд',
        'SeriesDiff3'               => 'Разница мячей +3 подряд',
        'SeriesDiffMinus3'          => 'Разница мячей -3 подряд',
        'SeriesDiff3Abs'            => 'Разница мячей модуль 3 подряд',
        'RotationWinLost'           => 'Чередование победа/поражение',
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
                        $counter[$key] = 1; // сдвигаем все чередования на одну строку вниз
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
     * Серии без ничьих
     */
    private function _seriesNonDraw(GameStat $gameStat)
    {
        return !$gameStat->isDraw();
    }

    /**
     * серии ничьи подряд
     */
    private function _seriesDraw(GameStat $gameStat)
    {
        return $gameStat->isDraw();
    }

    /**
     * только победы подряд
     */
    private function _seriesWin(GameStat $gameStat)
    {
        return $gameStat->isWin();
    }

    /**
     * не проиграть подряд
     */
    private function _seriesNonLost(GameStat $gameStat)
    {
        return !$gameStat->isLost();
    }

    /**
     * не выиграть подряд
     */
    private function _seriesNonWin(GameStat $gameStat)
    {
        return !$gameStat->isWin();
    }

    /**
     * проиграть подряд
     */
    private function _seriesLost(GameStat $gameStat)
    {
        return $gameStat->isLost();
    }

    /**
     * тотал больше подряд 1.5
     */
    private function _seriesTotalMore15(GameStat $gameStat)
    {
        return $gameStat->totalMore(1.5);
    }

    /**
     * тотал меньше подряд 1.5
     */
    private function _seriesTotalLess15(GameStat $gameStat)
    {
        return $gameStat->totalLess(1.5);
    }

    /**
     * тотал чередование 1.5
     */
    private function _rotationTotal15(GameStat $gameStat)
    {
        return $gameStat->totalLess(1.5);
    }

    /**
     * тотал больше подряд 2.5
     */
    private function _seriesTotalMore25(GameStat $gameStat)
    {
        return $gameStat->totalMore(2.5);
    }

    /**
     * тотал меньше подряд 2.5
     */
    private function _seriesTotalLess25(GameStat $gameStat)
    {
        return $gameStat->totalLess(2.5);
    }

    /**
     * тотал чередование 2.5
     */
    private function _rotationTotal25(GameStat $gameStat)
    {
        return $gameStat->totalLess(2.5);
    }

    /**
     * тотал больше подряд 3.5
     */
    private function _seriesTotalMore35(GameStat $gameStat)
    {
        return $gameStat->totalMore(3.5);
    }

    /**
     * тотал меньше подряд 3.5
     */
    private function _seriesTotalLess35(GameStat $gameStat)
    {
        return $gameStat->totalLess(3.5);
    }

    /**
     * тотал чередование 3.5
     */
    private function _rotationTotal35(GameStat $gameStat)
    {
        return $gameStat->totalLess(3.5);
    }

    /**
     * тотал больше подряд 4.5
     */
    private function _seriesTotalMore45(GameStat $gameStat)
    {
        return $gameStat->totalMore(4.5);
    }

    /**
     * тотал меньше подряд 4.5
     */
    private function _seriesTotalLess45(GameStat $gameStat)
    {
        return $gameStat->totalLess(4.5);
    }

    /**
     * тотал чередование 4.5
     */
    private function _rotationTotal45(GameStat $gameStat)
    {
        return $gameStat->totalLess(4.5);
    }

    /**
     * тотал больше подряд 5.5
     */
    private function _seriesTotalMore55(GameStat $gameStat)
    {
        return $gameStat->totalMore(5.5);
    }

    /**
     * тотал меньше подряд 5.5
     */
    private function _seriesTotalLess55(GameStat $gameStat)
    {
        return $gameStat->totalLess(5.5);
    }

    /**
     * тотал чередование 5.5
     */
    private function _rotationTotal55(GameStat $gameStat)
    {
        return $gameStat->totalLess(5.5);
    }

    /**
     * тотал больше подряд 6.5
     */
    private function _seriesTotalMore65(GameStat $gameStat)
    {
        return $gameStat->totalMore(6.5);
    }

    /**
     * тотал меньше подряд 6.5
     */
    private function _seriesTotalLess65(GameStat $gameStat)
    {
        return $gameStat->totalLess(6.5);
    }

    /**
     * тотал чередование 6.5
     */
    private function _rotationTotal65(GameStat $gameStat)
    {
        return $gameStat->totalLess(6.5);
    }

    /**
     * чет подряд
     */
    private function _seriesEven(GameStat $gameStat)
    {
        return $gameStat->isEvenTotal();
    }

    /**
     * нечет подряд
     */
    private function _seriesOdd(GameStat $gameStat)
    {
        return !$gameStat->isEvenTotal();
    }

    /**
     * чет/нечет чередование
     */
    private function _rotationEvenOdd(GameStat $gameStat)
    {
        return $gameStat->isEvenTotal();
    }

    /**
     * ничья/результат чередование
     */
    private function _rotationDrawResult(GameStat $gameStat)
    {
        return $gameStat->isDraw();
    }

    /**
     * ничья/победа чередование
     */
    private function _rotationDrawWin(GameStat $gameStat)
    {
        return $gameStat->isDraw() ? 1 : ($gameStat->isWin() ? 2 : null);
    }

    /**
     * ничья/проиграли чередование
     */
    private function _rotationDrawLost(GameStat $gameStat)
    {
        return $gameStat->isDraw() ? 1 : ($gameStat->isLost() ? 2 : null);
    }

    /**
     * Одновременно тотал больше 2,5 и чет подряд
     */
    private function _seriesTotalMore25AndEven(GameStat $gameStat)
    {
        return $gameStat->totalMore(2.5) && $gameStat->isEvenTotal();
    }

    /**
     * Одновременно тотал больше 2,5 и нечет подряд
     */
    private function _seriesTotalMore25AndOdd(GameStat $gameStat)
    {
        return $gameStat->totalMore(2.5) && !$gameStat->isEvenTotal();
    }

    /**
     * Одновременно тотал меньше 2,5 и чет подряд
     */
    private function _seriesTotalLess25AndEven(GameStat $gameStat)
    {
        return $gameStat->totalLess(2.5) && $gameStat->isEvenTotal();
    }

    /**
     * Одновременно тотал меньше 2,5 и нечет подряд
     */
    private function _seriesTotalLess25AndOdd(GameStat $gameStat)
    {
        return $gameStat->totalLess(2.5) && !$gameStat->isEvenTotal();
    }


    /**
     * обе забили подряд
     */
    private function _seriesAllTeamScored(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore();
    }

    /**
     * забила только одна
     */
    private function _seriesOneTeamScored(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return ($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1;
    }

    /**
     * никто не забил
     */
    private function _seriesNoneTeamScored(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return !$game->getGuestTeamScore() && !$game->getHomeTeamScore();
    }

    /**
     * Забила только одна или никто незабил
     */
    private function _seriesOneOrNoneTeamScored(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return ($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) < 2;
    }

    /**
     * забила одна/обе чередование
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
     * Обе забили + ТБ 2,5
     */
    private function _seriesAllTeamScoredAndTotalMore25(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore() && $gameStat->totalMore(2.5);
    }

    /**
     * Обе забили + ТМ 2,5
     */
    private function _seriesAllTeamScoredAndTotalLess25(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore() && $gameStat->totalLess(2.5);
    }

    /**
     * Забила только одна + ТБ 2,5
     */
    private function _seriesOneTeamScoredAndTotalMore25(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return (($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1) && $gameStat->totalMore(2.5);
    }

    /**
     * Забила только одна + ТМ 2,5
     */
    private function _seriesOneTeamScoredAndTotalLess25(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return (($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1) && $gameStat->totalLess(2.5);
    }



    /**
     * Обе забили + ТБ 3,5
     */
    private function _seriesAllTeamScoredAndTotalMore35(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore() && $gameStat->totalMore(3.5);
    }

    /**
     * Обе забили + ТМ 3,5
     */
    private function _seriesAllTeamScoredAndTotalLess35(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return $game->getGuestTeamScore() && $game->getHomeTeamScore() && $gameStat->totalLess(3.5);
    }

    /**
     * Забила только одна + ТБ 3,5
     */
    private function _seriesOneTeamScoredAndTotalMore35(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return (($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1) && $gameStat->totalMore(3.5);
    }

    /**
     * Забила только одна + ТМ 3,5
     */
    private function _seriesOneTeamScoredAndTotalLess35(GameStat $gameStat)
    {
        $game = $gameStat->getGame();
        return (($game->getGuestTeamScore() ? 1 : 0) + ($game->getHomeTeamScore() ? 1: 0) === 1) && $gameStat->totalLess(3.5);
    }







    /**
     * Тотал = 1
     */
    private function _seriesTotal1(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 1;
    }

    /**
     * Тотал = 2
     */
    private function _seriesTotal2(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 2;
    }

    /**
     * Тотал = 3
     */
    private function _seriesTotal3(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 3;
    }

    /**
     * Тотал = 4
     */
    private function _seriesTotal4(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 4;
    }

    /**
     * Тотал = 5
     */
    private function _seriesTotal5(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 5;
    }

    /**
     * Тотал = 6
     */
    private function _seriesTotal6(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 6;
    }

    /**
     * Тотал = 7
     */
    private function _seriesTotal7(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 7;
    }

    /**
     * Одновременно нечет и победа подряд
     */
    private function _seriesOddAndWin(GameStat $gameStat)
    {
        return !$gameStat->isEvenTotal() && $gameStat->isWin();
    }

    /**
     * Одновременно нечет и поражение подряд
     */
    private function _seriesOddAndLost(GameStat $gameStat)
    {
        return !$gameStat->isEvenTotal() && $gameStat->isLost();
    }

    /**
     * Одновременно чет и победа подряд
     */
    private function _seriesEvenAndWin(GameStat $gameStat)
    {
        return $gameStat->isEvenTotal() && $gameStat->isWin();
    }

    /**
     * Одновременно чет и поражение подряд
     */
    private function _seriesEvenAndLost(GameStat $gameStat)
    {
        return $gameStat->isEvenTotal() && $gameStat->isLost();
    }

    /**
     * Сумма мячей 0-1 подряд
     */
    private function _seriesTotal0Or1(GameStat $gameStat)
    {
        return $gameStat->getTotal() < 2;
    }

    /**
     * Сумма мячей 2-3 подряд
     */
    private function _seriesTotal2Or3(GameStat $gameStat)
    {
        return $gameStat->getTotal() == 2 || $gameStat->getTotal() == 3;
    }

    /**
     * Сумма мячей 4 и больше
     */
    private function _seriesTotal4OrMore(GameStat $gameStat)
    {
        return $gameStat->getTotal() >= 4;
    }

    /**
     * Разница мячей +1 подряд
     */
    private function _seriesDiff1(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == 1;
    }

    /**
     * Разница мячей -1 подряд
     */
    private function _seriesDiffMinus1(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == -1;
    }

    /**
     * Разница мячей модуль 1 подряд
     */
    private function _seriesDiff1Abs(GameStat $gameStat)
    {
        return $gameStat->getScoreDiffAbs() == 1;
    }

    /**
     * Разница мячей +2 подряд
     */
    private function _seriesDiff2(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == 2;
    }

    /**
     * Разница мячей -2 подряд
     */
    private function _seriesDiffMinus2(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == -2;
    }

    /**
     * Разница мячей модуль 2 подряд
     */
    private function _seriesDiff2Abs(GameStat $gameStat)
    {
        return $gameStat->getScoreDiffAbs() == 2;
    }

    /**
     * Разница мячей +3 подряд
     */
    private function _seriesDiff3(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == 3;
    }

    /**
     * Разница мячей -3 подряд
     */
    private function _seriesDiffMinus3(GameStat $gameStat)
    {
        return $gameStat->getScoreDiff() == -3;
    }

    /**
     * Разница мячей модуль 3 подряд
     */
    private function _seriesDiff3Abs(GameStat $gameStat)
    {
        return $gameStat->getScoreDiffAbs() == 3;
    }

    /**
     * Чередование победа/поражение
     */
    private function _rotationWinLost(GameStat $gameStat)
    {
        return $gameStat->isWin() ? 1 : ($gameStat->isLost() ? 2 : null);
    }
}