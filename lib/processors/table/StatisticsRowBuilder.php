<?php

require_once 'Cell.php';

class StatisticsRowBuilder
{
    const DATA_CELL_STYLE = 'border: #b9b9b9 2px solid;';

    private static
        $_instance = null;

    /**
     * @return StatisticsRowBuilder
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    public function createRow(GameStat $gameStat)
    {
        $cellCreateMethods = array_filter(get_class_methods($this), create_function('$name', 'return strpos($name, "_create") === 0;'));

        $output = array();
        foreach ($cellCreateMethods as $createMethod) {

            $cellParams = $this->{$createMethod}($gameStat->getGame(), $gameStat);

            $cell = new TableCell();

            if (isset($cellParams['value'])) {
                $cell->setValue($cellParams['value']);
            }
            if (isset($cellParams['title'])) {
                $cell->setTitle($cellParams['title']);
            }
            if (isset($cellParams['style'])) {
                $cell->setStyle($cellParams['style']);
            }

            $output[substr($createMethod, 7)] = $cell;
        }

        return $output;
    }


    /* ------------------------------------------------------------------------------ */


    private function _createNumber(Game $game, GameStat $gameStat)
    {
        return array('value' => $game->getNumber(), 'title' => '№ Тура', 'style' => self::DATA_CELL_STYLE);
    }

    private function _createDate(Game $game, GameStat $gameStat)
    {
        return array('value' => date('d.m.Y', $game->getDate()), 'title' => 'Дата', 'style' => self::DATA_CELL_STYLE);
    }

    private function _createHomeTeam(Game $game, GameStat $gameStat)
    {
        return array('value' => $game->getHomeTeam()->getName(), 'title' => 'Хозяева', 'style' => self::DATA_CELL_STYLE . ($gameStat->getTeamSeason()->getTeam() === $game->getHomeTeam() ? 'font-weight: bold;' : ''));
    }

    private function _createGuestTeam(Game $game, GameStat $gameStat)
    {
    	return array('value' => $game->getGuestTeam()->getName(), 'title' => 'Гости', 'style' => self::DATA_CELL_STYLE . ($gameStat->getTeamSeason()->getTeam() === $game->getGuestTeam() ? 'font-weight: bold;' : ''));
    }

    private function _createScore(Game $game, GameStat $gameStat)
    {
    	return array('value' => $game->getHomeTeamScore() .' &times; '. $game->getGuestTeamScore(), 'title' => 'Счет', 'style' => self::DATA_CELL_STYLE .'color:'. ($gameStat->isWin() ? 'green' : ($gameStat->isLost() ? 'red' : 'blue')) .';');
    }

    private function _createPositionNumber(Game $game, GameStat $gameStat)
    {
    	return array('value' => $gameStat->getTeamSeason()->getPositionNumber(), 'title' => 'Позиция');
    }

    private function _createYears(Game $game, GameStat $gameStat)
    {
    	return array('value' => join('/', $gameStat->getTeamSeason()->getYears()), 'title' => 'Года');
    }

    private function _createHomeTeamScore(Game $game, GameStat $gameStat)
    {
    	return array('value' => $game->getHomeTeamScore(), 'title' => 'Голы хозяев', 'style' => 'width: 45px;');
    }

    private function _createGuestTeamScore(Game $game, GameStat $gameStat)
    {
    	return array('value' => $game->getGuestTeamScore(), 'title' => 'Голы гостей', 'style' => 'width: 45px;');
    }

    private function _createScoreDiff(Game $game, GameStat $gameStat)
    {
    	return array('value' => $gameStat->getScoreDiff(), 'title' => "Просто разница мячей", 'style' => 'background: '. $this->_getColorByValue($gameStat->getScoreDiff()) .'; width: 60px;');
    }

    private function _createScoreDiffAbs(Game $game, GameStat $gameStat)
    {
    	return array('value' => $gameStat->getScoreDiffAbs(), 'title' => "Разница мячей по модулю", 'style' => 'background: '. $this->_getColorByValue($gameStat->getScoreDiffAbs()) .'; width: 60px;');
    }

    private function _createResult(Game $game, GameStat $gameStat)
    {
    	return array('value' => ($gameStat->isWin() ? 'выиграли' : ($gameStat->isLost() ? 'проиграли' : 'ничья')), 'style' => 'background: '. ($gameStat->isWin() ? 'lime' : ($gameStat->isLost() ? 'red' : '#3377FF')) .';');
    }

    private function _createTotalMore15(Game $game, GameStat $gameStat)
    {
    	return $this->_getTotal($gameStat, 1.5);
    }

    private function _createTotalMore25(Game $game, GameStat $gameStat)
    {
    	return $this->_getTotal($gameStat, 2.5);
    }

    private function _createTotalMore35(Game $game, GameStat $gameStat)
    {
    	return $this->_getTotal($gameStat, 3.5);
    }

    private function _createTotalMore45(Game $game, GameStat $gameStat)
    {
    	return $this->_getTotal($gameStat, 4.5);
    }

    private function _createTotalMore55(Game $game, GameStat $gameStat)
    {
    	return $this->_getTotal($gameStat, 5.5);
    }

    private function _createTotalMore65(Game $game, GameStat $gameStat)
    {
    	return $this->_getTotal($gameStat, 6.5);
    }

    private function _createEvenOdd(Game $game, GameStat $gameStat)
    {
    	$even = $gameStat->isEvenTotal();
        return array('value' => ($even ? 'чет' : 'нечет'), 'title' => 'Чет/нечет', 'style' => 'background: '. ($even ? 'magenta' : 'aqua') .';');
    }

    private function _createTotal25AndEvenOdd(Game $game, GameStat $gameStat)
    {
        $totalMore = $gameStat->totalMore(2.5);
    	$even      = $gameStat->isEvenTotal();
        return array('value' => ($totalMore ? 'ТБ' : 'ТМ').'+'.($even ? 'чет' : 'нечет'), 'title' => 'Тотал и чет/нечет', 'style' => 'background: '. ($totalMore && $even ? 'lime' : (!$totalMore && $even ? 'magenta' : ($totalMore && !$even ? 'aqua' : 'red'))) .';');
    }

    private function _createTotal(Game $game, GameStat $gameStat)
    {
        $total = $gameStat->getTotal();
        return array('value' => ($total < 2 ? '0&ndash;1' : ($total < 4 ? '2&ndash;3' : '4 и более')), 'title' => 'Сумма мячей', 'style' => 'background: '. ($total < 2 ? 'aqua' : ($total < 4 ? 'lime' : 'red')) .';');
    }

    private function _createEvenOddAndResult(Game $game, GameStat $gameStat)
    {
        $win  = $gameStat->isWin();
        $lost = $gameStat->isLost();
        $even = $gameStat->isEvenTotal();
        return array('value' => ($even ? 'Чет' : 'Нечет').'+'.($win ? 'победа' : ($lost ? 'поражение' : 'ничья')), 'title' => 'Чет/нечет и исход', 'style' => 'background: '. ($even && $win ? 'magenta' : (!$even && $win ? 'lime' : (!$even && $lost ? 'aqua' : ($even && $lost ? 'red' : '#3377FF')))) .';');
    }

    private function _createAllTeamScored(Game $game, GameStat $gameStat)
    {
        $v1 = $game->getGuestTeamScore();
        $v2 = $game->getHomeTeamScore();
        return array('value' => ($v1 && $v2 ? 'Обе команды забили' : ($v1 || $v2 ? 'Забила только одна' : 'Никто не забил')), 'title' => 'Обе команды забили', 'style' => 'background: '. ($v1 && $v2 ? 'lime' : ($v1 || $v2 ? 'red' : '#3377FF')) .';');
    }

    private function _createTeamScoredAndTotal25(Game $game, GameStat $gameStat)
    {
        $v1    = $game->getGuestTeamScore();
        $v2    = $game->getHomeTeamScore();
        $total = $gameStat->totalMore(2.5);
        return array('value' => ($v1 && $v2 ? 'Обе команды забили' : ($v1 || $v2 ? 'Забила только одна' : 'Никто не забил')) . ($v1 || $v2 ? ' + ' . ($total ? 'ТБ' : 'ТМ') : ''), 'title' => 'Команды забили + тотал 2,5', 'style' => 'background: '. ($v1 && $v2 ? 'lime' : ($v1 || $v2 ? 'red' : '#3377FF')) .';');
    }

    private function _createTeamScoredAndTotal35(Game $game, GameStat $gameStat)
    {
        $v1    = $game->getGuestTeamScore();
        $v2    = $game->getHomeTeamScore();
        $total = $gameStat->totalMore(3.5);
        return array('value' => ($v1 && $v2 ? 'Обе команды забили' : ($v1 || $v2 ? 'Забила только одна' : 'Никто не забил')) . ($v1 || $v2 ? ' + ' . ($total ? 'ТБ' : 'ТМ') : ''), 'title' => 'Команды забили + тотал 3,5', 'style' => 'background: '. ($v1 && $v2 ? 'lime' : ($v1 || $v2 ? 'red' : '#3377FF')) .';');
    }

    private function _createTotal1(Game $game, GameStat $gameStat)
    {
        return $this->_getTotalEqual($gameStat, 1);
    }

    private function _createTotal2(Game $game, GameStat $gameStat)
    {
        return $this->_getTotalEqual($gameStat, 2);
    }

    private function _createTotal3(Game $game, GameStat $gameStat)
    {
        return $this->_getTotalEqual($gameStat, 3);
    }

    private function _createTotal4(Game $game, GameStat $gameStat)
    {
        return $this->_getTotalEqual($gameStat, 4);
    }

    private function _createTotal5(Game $game, GameStat $gameStat)
    {
        return $this->_getTotalEqual($gameStat, 5);
    }

    private function _createTotal6(Game $game, GameStat $gameStat)
    {
        return $this->_getTotalEqual($gameStat, 6);
    }

    private function _createTotal7(Game $game, GameStat $gameStat)
    {
        return $this->_getTotalEqual($gameStat, 7);
    }



    /* ------------------------------------------------------------------------------ */



    private function _getTotal(GameStat $gameStat, $index)
    {
    	$v = $gameStat->totalMore($index);
        return array('value' => ($v ? 'тотал больше' : 'тотал меньше'), 'title' => 'Тотал '.$index, 'style' => 'background: '. ($v ? 'aqua' : 'magenta') .';');
    }

    private function _getTotalEqual(GameStat $gameStat, $value)
    {
        $v = $gameStat->getTotal() == $value;
        return array('value' => $v ? 'Да' : 'Нет', 'title' => 'Тотал = ' . $value, 'style' => 'background: '. ($v ? 'lime' : 'red') .';');
    }


    private function _getColorByValue($value)
    {
        if ($value >= 5) return 'gray';
        if ($value <= -5) return '#ddd; border: black 2px dotted';
    	$colors = array(0 => '#3377FF', 1 => 'lime', 2 => 'magenta', 3 => 'aqua', 4 => 'yellow',
    	               -1 => "#CCFFCC; border: black 2px dotted", -2 => '#FF99CC; border: black 2px dotted', -3 => '#CCFFFF; border: black 2px dotted', -4 => '#FFFF99; border: black 2px dotted');
        return $colors[intval($value)];
    }




    private function __construct()
    {
    }


}