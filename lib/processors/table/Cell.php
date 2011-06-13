<?php

class TableCell
{
    private
        $_title = null,
        $_value = null,
        $_style = null;

    public function __construct($value = null, $title = null, $style = null)
    {
        $this->_value = $value;
        $this->_title = $title;
        $this->_style = $style;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    public function getStyle()
    {
        return $this->_style;
    }

    public function setStyle($style)
    {
        $this->_style = $style;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->getValue();
    }
}