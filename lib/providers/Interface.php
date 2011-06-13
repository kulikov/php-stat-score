<?php

interface ProviderInterface
{
    /**
     * Возвращает массив урлов с табилцей по конкретной команде
     *
     * @param $requestUrl string
     * @return array
     */
    public function getTeamTableLinks($requestUrl);

    /**
     * Возвращает все данные по всем играм указанной команды
     *
     * @param $requestUrl string
     * @return array
     */
    public function getTeamGames($requestUrl);
}