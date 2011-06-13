<?php

interface CacherInterface
{
    public function get($key);

    public function save($key, $data, $lifetime = null);

    public function remove($key);
}