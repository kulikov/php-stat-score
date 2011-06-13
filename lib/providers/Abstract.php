<?php

require_once 'Interface.php';

abstract class AbstractProvider implements ProviderInterface
{
    protected function _requestUrl($requestUrl, array $options = array())
    {
    	$curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $requestUrl,
            CURLOPT_TIMEOUT        => 3000,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)',
        ) + $options);

        return curl_exec($curl);
    }
}