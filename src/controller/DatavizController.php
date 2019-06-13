<?php

namespace App\controller;

use App\dataviz\datavizs\DatavizFactory;
use App\dataviz\datavizs\FilterList;
use App\dataviz\datavizs\Filter;

class DatavizController 
{
    public function __construct() {
        //header('Content-Type: application/json; charset=utf-8');
    }

    private function parseFilter( $args = array() ) {
        //les filtres sont simpelment tout les paramètres en dehor du 'to'
        unset($args['to']);
        //après le paramtre 'to' retiré, chaque argument est considéré comme un filtre
        $filterListObj = new FilterList();
        if( count($args) > 0 ) {
            foreach($args as $key => $value) {
                $filterListObj->add(new Filter($key, $value));
            }
        }
        return $filterListObj;
    }

    public function dataviz11( $args = null) {        
        echo DatavizFactory::get('1.1')->filter( $this->parseFilter($args) )->get();
    }

    public function dataviz12( $args = null) {        
        echo DatavizFactory::get('1.2')->filter( $this->parseFilter($args) )->get();
    }

    public function dataviz13( $args = null) {        
        echo DatavizFactory::get('1.3')->filter( $this->parseFilter($args) )->get();
    }

    public function dataviz21( $args = null) {        
        echo DatavizFactory::get('2.1')->filter( $this->parseFilter($args) )->get();
    }

    public function dataviz31( $args = null) {        
        echo DatavizFactory::get('3.1')->filter( $this->parseFilter($args) )->get();
    }

    public function dataviz34( $args = null) {        
        echo DatavizFactory::get('3.4')->filter( $this->parseFilter($args) )->get();
    }
}