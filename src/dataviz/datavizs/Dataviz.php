<?php

namespace App\dataviz\datavizs;

use App\dataviz\Entities\Entite;
use App\model\DAOFactory;

class Dataviz
{
    protected $filters;

    protected $dataJson;

    public function __construct() {
        $this->dataJson = json_encode(array());
    }

    protected function build() { }

    public function get() {
        static::build();
        return $this->dataJson;
    }

    public function filter(FilterList $filters) {
        $this->filters = $filters;
        $this->filters->reduce(static::AVAILABLE_FILTERS);
        return $this;
    }
}