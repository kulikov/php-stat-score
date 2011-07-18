<?php

interface ProviderInterface
{
    /**
     * ���������� ������ ����� � �������� �� ���������� �������
     *
     * @param $requestUrl string
     * @return array
     */
    public function getTeamTableLinks($requestUrl);

    /**
     * ���������� ��� ������ �� ���� ����� ��������� �������
     *
     * @param $requestUrl string
     * @return array
     */
    public function getTeamGames($requestUrl);
}