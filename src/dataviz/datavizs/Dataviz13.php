<?php

namespace App\dataviz\datavizs;

use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\FilterList;
use App\dataviz\datavizs\Dataviz;
use App\model\DAOFactory;

class Dataviz13 extends Dataviz
{
    /* AVAILABLE FILTERS FOR THIS DATAVIZ */
    const AVAILABLE_FILTERS = array("cursus","annee_promo","civilite","date_sortie_hetic");

    const DATA_CIV_HOM_NAME = "Hommes";
    const DATA_CIV_FEM_NAME = "Femme";

    public function __construct( $db ) {
        parent::__construct($db);
    }

    protected function build() {
        $eleves = DAOFactory::get('eleve')->getAll( $this->filters );
        
        $result = array();
        if(count($eleves) > 0) {
            //traitement
            $homme = 0;
            $femme = 0;
            foreach ($eleves as $eleve) {
                if($eleve->civilite() === Entite::HOMME_ACRONYME) {
                    $homme++;
                    continue;
                }
                elseif($eleve->civilite() === Entite::FEMME_ACRONYME) {
                    $femme++;
                    continue;
                }
            }

            $pop = $homme + $femme;
            $homme = round($homme * 100 / $pop) . "%";
            $femme = round($femme * 100 / $pop) . "%";

            $result = array( 
                self::DATA_CIV_HOM_NAME => $homme,
                self::DATA_CIV_FEM_NAME => $femme
            );
        }
        
        $this->dataJson = json_encode($result);
    }
}