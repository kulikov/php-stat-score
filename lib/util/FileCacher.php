<?php

require_once 'CacherInterface.php';

class FileCacher implements CacherInterface
{
    private $_options = array(
        'lifetime' => 3600,
        'cacheDir' => '',
    );

    /**
     * @return FileCacher
     */
    public static function factory(array $options)
    {
        $instance = new self();
        $instance->_options = array_merge($instance->_options, $options);
        return $instance;
    }

	public function get($key)
    {
        $content = @file_get_contents($this->_makeFileName($key));
        if (!$content) return null;

        $lines = explode("\n", $content, 2);

        if (!preg_match('/^create:(\d+), lifetime:(\d+)/', $lines[0], $matches)) {
            return null;
        }

        if ($matches[1] + $matches[2] < time()) {
            $this->remove($key);
            return null;
        }

        return unserialize(trim($lines[1]));
    }

	public function remove($key)
    {
        @unlink($this->_makeFileName($key));
        return $this;
    }

	public function save($key, $data, $lifetime = null)
    {
        if ($lifetime === null) {
            $lifetime = $this->_options['lifetime'];
        }
        file_put_contents($this->_makeFileName($key), 'create:' . time() . ', lifetime:' . $lifetime . "\n\n" . serialize($data));
        return $this;
    }

    private function _makeFileName($key)
    {
    	return rtrim(realpath($this->_options['cacheDir']), '\/') . '/' . md5($key) .'.cache';
    }

    private function __construct()
    {
    }
}