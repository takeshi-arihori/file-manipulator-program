<?php

class Game
{
    protected $min;
    protected $max;
    protected $randomNumber;
    protected $message;

    public function __construct($min = null, $max = null)
    {
        $this->min = $min;
        $this->max = $max;
        $this->message = '';
    }

    public function initialize()
    {
        if ($this->min !== null && $this->max !== null) {
            $this->randomNumber = rand($this->min, $this->max);
            return true;
        }
        return false;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getMin()
    {
        return $this->min;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function isInitialized()
    {
        return isset($this->randomNumber);
    }
}
