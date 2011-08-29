<?php

require_once 'Interface.php';
require_once 'table/Cell.php';
require_once 'table/StatisticsRowBuilder.php';
require_once  dirname(__FILE__) . '/../statistics/GameStat.php';
require_once  dirname(__FILE__) . '/../statistics/GameSeriesCalculator.php';

class DisplayXlsTableStrategy implements ProcessorInterface
{
    private $_params = '';

    public function __construct($params)
    {
    	$this->_params = (string) $params;
    }

    public function process(Championship $championship)
    {

        /* суммы по строкам. достаем из номера интересующих нас команд из реквеста */
        $summaryPosNumbers = array();
        $summaryPosString  = '';
        $summaryTeams      = array();
        $matches           = array();
        if (preg_match('/n=([^|]+)/', urldecode($this->_params), $matches)) {
            $summaryPosString = $matches[1];
            if (strstr($matches[1], '-')) {
                @list($min, $max) = explode('-', trim($matches[1]));
                $min = min(intval(trim($min)), intval(trim($max)));
                $max = max(intval(trim($min)), intval(trim($max)));
                $summaryPosNumbers = array_keys(array_fill($min, $max-$min+1, 1));
            } else {
                $summaryPosNumbers = array_map(create_function('$v', 'return (int) trim($v);'), explode(',', $matches[1]));
            }
        }

        if (preg_match('/t=([^|]+)/', urldecode($this->_params), $matches)) {
            $printedTable = preg_split('/[^\d]/', $matches[1]);
        }

        if (empty($printedTable)) {
            $printedTable = array(1, 2, 3);
        }


        foreach ($championship->getTeamSeasons() as $teamSeason) {
            $teamStat[] = $seriesStat[] = $teamStatHome[] = $seriesStatHome[] = $teamStatGuest[] = $seriesStatGuest[] = array();

            $teamStat        = array_merge($teamStat, $this->_prepareTeamSeasonTable($teamSeason));
            $teamStatHome    = array_merge($teamStatHome, $this->_prepareTeamSeasonTable($teamSeason, Game::HOME));
            $teamStatGuest   = array_merge($teamStatGuest, $this->_prepareTeamSeasonTable($teamSeason, Game::GUEST));
            $seriesStat      = array_merge($seriesStat, $this->_prepareTeamSeriesStats($teamSeason));

            if (!$summaryPosNumbers || in_array($teamSeason->getPositionNumber(), $summaryPosNumbers)) {
                $summaryTeams[] = $teamSeason;
            }
        }

        header('Content-type: text/html; charset=windows-1251');

        print '
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
        <style>
            table { border-collapse: collapse; font: 12px Arial; }
            table td { padding: 2px 5px 2px 3px; border: #f5f5f5 1px solid; }
        </style>
        ';

        print '<table>';

        if (in_array(1, $printedTable)) {
            $this->_printTable($teamStat);
            print '<tr><td>newSheet</td><td colspan="10">Визуальщина общая</td></tr>';

            $this->_printTable($teamStatHome);
            print '<tr><td>newSheet</td><td colspan="10">Визуальщина дома</td></tr>';

            $this->_printTable($teamStatGuest);
            print '<tr><td>newSheet</td><td colspan="10">Визуальщина гости</td></tr>';
        }

        if (in_array(3, $printedTable)) {
            $this->_printTable($seriesStat);
            print '<tr><td>newSheet</td><td colspan="10">Все серии</td></tr>';

            print '<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>';
            $this->_printTable($this->_reversStat($seriesStat), false);
            print '<tr><td>newSheet</td><td colspan="10">Все серии в развернутом виде</td></tr>';
        }

        if (in_array(2, $printedTable)) {
            $summaryTeamsStats = $this->_prepareSummarySeriesStats($summaryTeams, $summaryPosString);
            array_unshift($summaryTeamsStats, null);
            $this->_printTable($summaryTeamsStats);
            print '<tr><td>newSheet</td><td colspan="10">Суммарная статистика</td></tr>';
        }

        print '</table>';
    }

    private function _reversStat($seriesStat)
    {
    	$output = array();
        $number = 0;
        foreach ($seriesStat as $i => $row) {
            if (!$row) {
                $number++;
                continue;
            }
            foreach ($row as $key => $cell) {
                if (stristr($key, 'space:')) continue;
                $output[$number . '_' . $key][$i] = $cell;
            }
        }
        return $output;
    }

    private function _printTable($table, $showHeader = true)
    {
        $out           = array();
        $printedHeader = false;

        foreach ($table as $row) {
            if (!$row && $showHeader) {
                $printedHeader = false;
                $out[] = '<tr><td colspan="10">&nbsp;</td></tr>';
                $out[] = '<tr><td colspan="10">&nbsp;</td></tr>';
                continue;
            }
            if (!$printedHeader && $showHeader) {
                $out[] = '<tr>';
                foreach ($row as $cell) {
                    $out[] = "<td style='font-size: 10px; font-weight: bold;'>". ($cell ? $cell->getTitle() : '&nbsp;') ."</td>";
                }
                $printedHeader = true;
                $out[] = '</tr>';
            }
            $out[] = '<tr>';
            foreach ($row as $cell) {
                if ($cell) {
                    $out[] = "<td style=\"{$cell->getStyle()}\">{$cell->getValue()}</td>";
                } else {
                    $out[] = '<td>&nbsp;</td>';
                }
            }
            $out[] = '</tr>';
        }
        echo join('', $out);
    }



    /* PRIVATE */

    private function _prepareTeamSeasonTable(TeamSeason $teamSeason, $gameType = null)
    {
        $rowBuilder  = StatisticsRowBuilder::getInstance();
        $output = array();
        foreach ($teamSeason->getGames() as $game) {
            if ($gameType == Game::HOME && !$teamSeason->isHomeGame($game)) continue;
            if ($gameType == Game::GUEST && $teamSeason->isHomeGame($game)) continue;

            $output[$game->getNumber()] = $rowBuilder->createRow($game->getGameStat($teamSeason));
        }
        return $output;
    }


    private function _prepareSummarySeriesStats(array $tesmSeasons, $summaryPosString = null)
    {
        $seriesCalculator = GameSeriesCalculator::getInstance();

        $summarySeries = array();
        $years         = array();
        $posNumbers    = array();
        foreach ($tesmSeasons as $season) {
            $series = $seriesCalculator->calcAllStatistics($season)
                + $seriesCalculator->calcAllStatistics($season, Game::HOME)
                + $seriesCalculator->calcAllStatistics($season, Game::GUEST);

            foreach ($series as $title => $values) {
                if (!isset($summarySeries[$title])) {
                    $summarySeries[$title] = array();
                }
                foreach ((array) $values as $num => $value) {
                    @$summarySeries[$title][$num] += $value;
                }
            }
            $years       = array_merge($season->getYears(), $years);
            $posNumbers[] = $season->getPositionNumber();
        }

        $years      = new TableCell(join('/', array_unique($years)), 'Сезон');
        $posNumbers = new TableCell(str_replace('-', '–', $summaryPosString ? $summaryPosString : (min($posNumbers) .'-'. max($posNumbers))), 'Место');
        $main       = new TableCell('общий', 'Разрез', 1);
        $home       = new TableCell('дома', 'Разрез', 2);
        $guest      = new TableCell('гости', 'Разрез', 3);

        $output = array();
        foreach ($summarySeries as $title => $values) {
            list($_type, $_title) = explode('::', $title, 2);

            $output[$title][-4] = $years;
            $output[$title][-3] = $posNumbers;
            $output[$title][-2] = $_type == Game::HOME ? $home : ($_type == Game::GUEST ? $guest : $main);
            $output[$title][-1] = new TableCell($seriesCalculator->getDescription($_title), 'Вид аналитики');
            $output[$title][0]  = new TableCell(array_sum($values), 'Сумма по строке', 'background: #99CCFF;');

            foreach ($values as $num => $value) {
                $output[$title][$num] = new TableCell($value, $num);
            }
        }

        return $output;
    }

    private function _prepareTeamSeriesStats(TeamSeason $teamSeason)
    {
    	$seriesCalculator = GameSeriesCalculator::getInstance();

    	$series = array('space::main' => array())
    	    + $seriesCalculator->calcAllStatistics($teamSeason)
            + array('space::home' => array())
            + $seriesCalculator->calcAllStatistics($teamSeason, Game::HOME)
            + array('space::guest' => array())
            + $seriesCalculator->calcAllStatistics($teamSeason, Game::GUEST);


        $defaultRow = array_combine(array_keys($series), array_fill(0, count($series), null));
        $refloat    = array(); //array_fill(1, count($teamSeason->getGames()), array());

        $maxRows = 0;
        foreach ($series as $title => $values) {
            foreach ($values as $i => $value) {
                if (empty($refloat[$i])) {
                    $refloat[$i] = $defaultRow;
                }
                $refloat[$i][$title] = new TableCell($value, $title);
                $maxRows = max($maxRows, $i);
            }
        }

        $_style = 'text-align: left;';
        foreach ((array) @$refloat[1] as $title => $cell) {
            list($_type, $_title) = explode('::', $title, 2);
            if ($_type != 'space') {
                $refloat[-4][$title] = new TableCell($teamSeason->getTeam()->getName(), '&nbsp;', $_style);
                $refloat[-3][$title] = new TableCell(join('/', $teamSeason->getYears()), null, $_style);
                $refloat[-2][$title] = new TableCell($teamSeason->getPositionNumber(), null, $_style);
                $refloat[-1][$title] = new TableCell($_type == Game::HOME ? 'дома' : ($_type == Game::GUEST ? 'гости' : 'общий'), null, $_style);
                $refloat[0][$title]  = new TableCell($seriesCalculator->getDescription($_title), null, 'background: #FFFFBB;');
            } else {
                for ($i = -4; $i <= $maxRows; $i++) {
                    $refloat[$i][$title] = $i > 0 && $_title == 'main' ? new TableCell($i .'.') : null;
                }
            }
        }

        ksort($refloat);
        return $refloat;
    }
}


















