<?php

require_once 'providers/Interface.php';
require_once 'util/ObserverInterface.php';
require_once 'util/CacherInterface.php';

class DataLoader
{
    /**
     * @var ProviderInterface
     */
    private $_dataProvider = null;

    private
        $_observers  = array(),
        $_cacher     = null,
        $_result     = null,
        $_request    = null,
        $_countTeams = 0;


    /**
     * @return DataLoader
     */
    public static function factory(ProviderInterface $provider)
    {
    	$instance = new self();
    	$instance->setDataProvider($provider);
    	return $instance;
    }

    public function setDataProvider(ProviderInterface $provider)
    {
        $this->_dataProvider = $provider;
        return $this;
    }

    public function addCompleteHandler(Observer $observer)
    {
    	$this->_observers[] = $observer;
    	return $this;
    }

    public function setCacher(CacherInterface $cacher)
    {
    	$this->_cacher = $cacher;
    	return $this;
    }

    public function loadChampionship($request, ThreadManager $threadManager)
    {
        if (!$request) {
            throw new Exception('�� ������ ��� ��� �������� �������� ������');
        }

        $this->_result  = null;
        $this->_request = $request;

        /**
         * ���� ���� �������������� ���������� ���������� � ���
         */
        if ($result = $this->_getFromCache()) {
            $this->_notifyObservers($result);
            return;
        }

        // ������� ��������� ������ �� ��� ������� � ���������� ������
        $teamLinks = $this->_dataProvider->getTeamTableLinks($request);

        if (!$teamLinks) {
            throw new Exception('�� ������� ��������� ������ ������ �� ' . $request);
        }

        // ���������� ����� ���-�� �����䠗 ������ ����� �������, ������� � ��� ������
        $this->_countTeams = count($teamLinks);

        // ������ ������������ ���� ����� �������� ���������� ����
        $threadManager->setCompliteCallback(array($this, '_onWorkerResponse'));


        // �������� ��� �� ��������� ������ �� ���������� ������� ���� ��� ����� ������
        $providerReflection = new ReflectionClass($this->_dataProvider);
        $providerFile       = $providerReflection->getFileName();
        $providerClass      = get_class($this->_dataProvider);


        // ������� ������ ������ �� ���� ����������� ��� ������� �� ��� �����������
        foreach ($teamLinks as $key => $link) {
            $threadManager->addThread(array(
                'provider' => array(
                    'file'  => $providerFile,
                    'class' => $providerClass,
                ),
                'number' => $key,
                'url'    => $link,
            ));
        }

        // ��������� ����������
        $threadManager->run();
    }



    /**
     * ��� ������� ���������� � ������ �� ������
     * ��� ������� ������ ��� ������ ���������� �������
     * � ���������� � ���� �������
     *
     * @return array
     */
    public function loadTeamGames($requestUrl)
    {
        return $this->_dataProvider->getTeamGames($requestUrl);
    }



    /* PRIVATE */

    private function __construct() { }


    public function _onWorkerResponse($response)
    {
        $this->_countTeams--;

        if ($response = @unserialize($response)) {
    	    $this->_result[$response['number']] = $response['gamesData'];
    	}

    	/**
    	 * ���� ��� ������� ��������� ������������ ������������ � ��������� ������
    	 */
    	if ($this->_countTeams <= 0) {
    	    ksort($this->_result);

    	    $this->_saveCache($this->_result);

    	    $this->_notifyObservers($this->_result);
    	}
    }

    private function _notifyObservers($result)
    {
        foreach ($this->_observers as $observer) {
            $observer->update($result);
        }
    }

    private function _getFromCache()
    {
        if ($this->_cacher) {
            return $this->_cacher->get($this->_makeCacheKey());
        }
        return null;
    }

    private function _saveCache($result)
    {
        if ($this->_cacher) {
            $this->_cacher->save($this->_makeCacheKey(), $result);
        }
    }

    private function _makeCacheKey()
    {
    	return get_class($this->_dataProvider) . serialize($this->_request);
    }
}