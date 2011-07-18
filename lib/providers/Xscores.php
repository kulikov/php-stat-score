<?php

require_once 'Abstract.php';

class XscoresProvider extends AbstractProvider
{
    const TEAM_URL = 'http://www.xscores.com/%s/Results.jsp?sports=%s&result=%s&teamName=%s || %s';

    private $_cells = array(
        'default' => array(
            'homeTeam'  => 5,
            'guestTeam' => 6,
            'score'     => 10,
        ),
        'soccer' => array(
            'homeTeam'  => 5,
            'guestTeam' => 9,
            'score'     => 14,
        ),
    );


    public function getTeamTableLinks($requestUrl)
    {
    	$matches = array();
    	if (preg_match('#/(\w+)/Results\.jsp.*result\=(\d+)#', $requestUrl, $matches)) {
    	    $gameType = $matches[1];
    	    $resultId = $matches[2];
    	} else {
    	    throw new Exception('Xscores: Ошибка при определении типа игры. Возможно указан неверный урл');
    	}

    	$html = $this->_requestUrl($requestUrl, array(
    	    CURLOPT_HEADER => 1,
    	));

    	$cookie = array();
    	preg_match('/JSESSIONID=([^;]+)\;/', $html, $cookie);

        $matches = array();
        if (!preg_match_all('/teamData\(\'([^\']+)\'\)/', $html, $matches)) {
            throw new Exception('Xscores: Ошабка получения списка команд');
        }

        $output = array();
        foreach ($matches[1] as $key => $teamName) {
            $output[$key+1] = sprintf(self::TEAM_URL, $gameType, $gameType, $resultId, $teamName, $cookie[0]);
        }

        return $output;
    }

    // http://www.xscores.com/hockey/Results.jsp?sports=hockey&result=5&teamName=WAS CAPITALS || JSESSIONID=58D57DB44C3A63334577CB93909D09D8;
    public function getTeamGames($requestUrl)
    {
        if (preg_match('#/(\w+)/Results\.jsp#', $requestUrl, $matches)) {
            $gameType = strtolower($matches[1]);
        } else {
            throw new Exception('Xscores: Ошибка при определении типа игры. Возможно указан неверный урл');
        }

        $cellsKey = isset($this->_cells[$gameType]) ? $this->_cells[$gameType] : $this->_cells['default'];


        list($requestUrl, $cookie) = explode(' || ', $requestUrl);

        $html = $this->_requestUrl(str_replace(' ', '+', $requestUrl), array(
            CURLOPT_COOKIE => $cookie,
        ));

        $tables = preg_split('/<table/i', $html);
        $rows   = preg_split('/<tr[^>]*>/i', $tables[6]);

        $output = array(
            'team'  => null,
            'games' => array(),
        );

        $number = 1;
        $date   = '';
        foreach ($rows as $key => $row) {
            if ($key < 2) continue;
            $cells  = preg_split('/<td[^>]*>/i', $row);

            if (count($cells) == 2) {
                $date = strip_tags($cells[1]);
                continue;
            }

            if (empty($output['team'])) {
                if (preg_match('/<b>(.+)<\/b>/', $cells[$cellsKey['homeTeam']])) {
                    $output['team'] = trim(strip_tags($cells[$cellsKey['homeTeam']]));
                } else {
                    $output['team'] = trim(strip_tags($cells[$cellsKey['guestTeam']]));
                }
            }

            list($homeScore, $guestScore) = explode('-', trim(strip_tags($cells[$cellsKey['score']])));

            $output['games'][$number] = array(
                'date'       => trim($date),
                'homeTeam'   => trim(strip_tags($cells[$cellsKey['homeTeam']])),
                'homeScore'  => $homeScore,
                'guestTeam'  => trim(strip_tags($cells[$cellsKey['guestTeam']])),
                'guestScore' => $guestScore,
            );

            $number++;
        }

        return $output;
    }
}