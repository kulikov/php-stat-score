<?php

require_once 'Abstract.php';

class ParimatchProvider extends AbstractProvider
{
    const TEAM_URL = 'http://www.parimatch.com/stats.html';


    /**
     * @param $requestUrl
     * @return array
     */
    public function getTeamTableLinks($requestUrl)
    {
        $html = $this->_requestUrl($requestUrl);

    	$matches = array();
    	preg_match_all('/onclick="WO\(\'([^\']+)/', $html, $matches);

    	$output = array();
    	foreach ($matches[1] as $key => $url) {
    	    $output[$key+1] = $url;
    	}

    	return $output;
    }

    public function getTeamGames($requestUrl)
    {
        $html = $this->_requestUrl(self::TEAM_URL . $requestUrl);

        $tables = preg_split('/<table/i', $html);
        $rows   = preg_split('/<tr/i', $tables[2]);
        $output = array(
            'team'  => preg_replace('/^.+>Матчи команды ([^<]+).*$/i', '$1', $rows[1]),
            'games' => array(),
        );

        foreach ($rows as $key => $row) {
            if ($key < 3) continue;

            $cells  = preg_split('/<td/i', $row);
            $score  = explode(':', preg_replace('/[^\d\:]/', '', $cells[5]));
            $number = preg_replace('/[^\d]/', '', $cells[1]);

            $output['games'][$number] = array(
                'date'       => preg_replace('/[^\d\.]/', '', $cells[2]),
                'homeTeam'   => preg_replace('/^.+>([^<]+).*$/', '$1', $cells[3]),
                'homeScore'  => $score[0],
                'guestTeam'  => preg_replace('/^.+>([^<]+).*$/', '$1', $cells[4]),
                'guestScore' => $score[1],
            );
        }

        ksort($output);

        return $output;
    }
}