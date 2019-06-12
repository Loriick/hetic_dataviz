<?php

namespace App\dataviz\datavizs;

use App\dataviz\Entities\Entite;
use App\model\DAOFactory;

class Dataviz
{
    protected $db;

    protected $filters;

    protected $dataJson;

    public function __construct( $db = null ) {
        $this->db = $db;
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