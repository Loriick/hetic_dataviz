<?php
namespace App\dataviz\datavizs;

class Filter
{
    private $name;
    private $value;
    private $operator;

    public function __construct($name, $value, $operator = null) {
        $this->name = $name;
        $this->value = $value;
        $this->operator = $operator ? $operator : "=";
    }

    public function name() {
        return $this->name;
    }

    public function value() {
        return $this->value;
    }

    public function operator() {
        return $this->operator;
    }
}
