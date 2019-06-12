<?php

namespace App\dataviz\datavizs;

use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\FilterList;
use App\dataviz\datavizs\Dataviz;
use App\model\DAOFactory;

class Dataviz12 extends Dataviz
{
    /* AVAILABLE FILTERS FOR THIS DATAVIZ */
    const AVAILABLE_FILTERS = array("cursus","civilite","date_sortie_hetic");

    /* CATEGORIES NAME A EVALUER */
    const BASE_ANNEE = 2015;
    const ANNEES_PROMO = array(2016, 2017);

    const DATA_BAC_PRO_NAME = "Evolution du % de Bac pro par rapport à " . self::BASE_ANNEE ;
    const DATA_BAC_GEN_NAME = "Evolution du % de Bac général & techno par rapport à " . self::BASE_ANNEE;
    const DATA_ETU_SUP_NAME = "Evolution du % d'Etudes supérieures par rapport à " . self::BASE_ANNEE;
    const DATA_SAN_DIP_NAME = "Evolution du % de Sans diplômes par rapport à " . self::BASE_ANNEE;
    const DATA_SAL_CHO_NAME = "Evolution du % d'Anciens salarié / chômeur par rapport à ". self::BASE_ANNEE;

    public function __construct( $db ) {
        parent::__construct($db);
    }

    protected function build() {
        $this->filters->add(new Filter('date_sortie_hetic', self::BASE_ANNEE));
        $dataBaseAnnee = $this->getData();
        $result = array();
        foreach(self::ANNEES_PROMO as $annee) {
            $this->filters->add(new Filter('date_sortie_hetic', $annee));
            $dataAnnee = $this->getData();
            if( count($dataAnnee) > 0 ) {
                $evoBacpro = $dataAnnee['bac pro'] - $dataBaseAnnee['bac pro'] . '%'; 
                $evoBacgen = $dataAnnee['bac gen'] - $dataBaseAnnee['bac gen'] . '%';
                $evoEtusup = $dataAnnee['etu sup'] - $dataBaseAnnee['etu sup'] . '%';
                $evoSandip = $dataAnnee['san dip'] - $dataBaseAnnee['san dip'] . '%';
                $evoSalcho = $dataAnnee['sal cho'] - $dataBaseAnnee['sal cho'] . '%';

                $result[self::DATA_BAC_PRO_NAME][$annee] = $evoBacpro;
                $result[self::DATA_BAC_GEN_NAME][$annee] = $evoBacgen;
                $result[self::DATA_ETU_SUP_NAME][$annee] = $evoEtusup;
                $result[self::DATA_SAN_DIP_NAME][$annee] = $evoSandip;
                $result[self::DATA_SAL_CHO_NAME][$annee] = $evoSalcho;
            }
        }
        
        $this->dataJson = json_encode($result);
    }

    private function getData() {
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
            $bacpro = round($bacpro * 100 / $pop);
            $bacgen = round($bacgen * 100 / $pop);
            $etusup = round($etusup * 100 / $pop);
            $sandip = round($sandip * 100 / $pop);
            $salcho = round($salcho * 100 / $pop);

            $result = array( 
                'bac pro' => $bacpro,
                'bac gen' => $bacgen,
                'etu sup' => $etusup,
                'san dip' => $sandip,
                'sal cho' => $salcho
            );
        }

        return $result;
    }
}