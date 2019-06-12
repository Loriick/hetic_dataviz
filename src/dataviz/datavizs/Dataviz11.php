<?php

namespace App\dataviz\datavizs;

use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\Dataviz;
use App\model\DAOFactory;

class Dataviz11 extends Dataviz
{
    /* AVAILABLE FILTERS FOR THIS DATAVIZ */
    const AVAILABLE_FILTERS = array("cursus","annee_promo","civilite","date_sortie_hetic");

    /* CATEGORIES NAME A EVALUER */
    const DATA_BAC_PRO_NAME = "Bac pro";
    const DATA_BAC_GEN_NAME = "Bac général & techno";
    const DATA_ETU_SUP_NAME = "Etudes supérieures";
    const DATA_SAN_DIP_NAME = "Sans diplômes";
    const DATA_SAL_CHO_NAME = "Anciens salarié / chômeur";

    public function __construct( $db ) {
        parent::__construct($db);
    }

    protected function build() {
        $eleves = DAOFactory::get('eleve')->getAll( $this->filters );
        
        $result = array();
        if(count($eleves) > 0) {
            //traitement
            $bacpro = 0;
            $bacgen = 0;
            $etusup = 0;
            $sandip = 0;
            $salcho = 0;
            foreach ($eleves as $eleve) {
                if($eleve->etudes_avant_hetic() !== Entite::WORD_NC) {
                    $value = $eleve->etudes_avant_hetic();
                    if(strpos( strtolower($value), 'bac ' ) !== false) {
                        if(strpos( strtolower($value), 'bac+' ) !== false) {
                            $etusup++;
                            continue;
                        }
                        if(strpos( strtolower($value), 'bac +' ) !== false) {
                            $etusup++;
                            continue;
                        }
                        if(strpos( strtolower($value), 'bac pro' ) !== false) {
                            $bacpro++;
                            continue;
                        }
                        else{
                            $bacgen++;
                            continue;
                        }
                    }
                    elseif(
                        strpos( strtolower($value), 'bts' ) !== false ||
                        strpos( strtolower($value), 'iut' ) !== false ||
                        strpos( strtolower($value), 'dut' ) !== false ||
                        preg_match("/l[0-9]/i", $value) === 1 || //L1 L2 L3
                        preg_match("/m[0-9]/i", $value) === 1 || //M1, M2
                        strpos( strtolower($value), 'master' ) !== false ||
                        strpos( strtolower($value), 'prépa' ) !== false ||
                        strpos( strtolower($value), 'licence' ) !== false ||
                        strpos( strtolower($value), 'llce' ) !== false ||
                        strpos( strtolower($value), 'études' ) !== false ||
                        strpos( strtolower($value), 'iej' ) !== false ||
                        strpos( strtolower($value), 'dees' ) !== false ||
                        strpos( strtolower($value), 'bachelor' ) !== false 
                        ) {
                        $etusup++;
                        continue;
                    }
                }
                elseif($eleve->jobs_notables_exerces() !== Entite::WORD_NC) {
                    $value = $eleve->jobs_notables_exerces();
                    $salcho++;
                    continue;
                }
                $sandip++;
                continue;
            }

            $pop = $bacpro + $bacgen + $etusup + $sandip + $salcho;
            $bacpro = round($bacpro * 100 / $pop) . "%";
            $bacgen = round($bacgen * 100 / $pop) . "%";
            $etusup = round($etusup * 100 / $pop) . "%";
            $sandip = round($sandip * 100 / $pop) . "%";
            $salcho = round($salcho * 100 / $pop) . "%";

            $result = array( 
                self::DATA_BAC_PRO_NAME => $bacpro,
                self::DATA_BAC_GEN_NAME => $bacgen,
                self::DATA_ETU_SUP_NAME => $etusup,
                self::DATA_SAN_DIP_NAME => $sandip,
                self::DATA_SAL_CHO_NAME => $salcho
            );
        }

        $this->dataJson = json_encode($result);
    }
}