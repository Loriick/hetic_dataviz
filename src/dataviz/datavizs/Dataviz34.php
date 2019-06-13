<?php

namespace App\dataviz\datavizs;

use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\FilterList;
use App\dataviz\datavizs\Filter;
use App\dataviz\datavizs\Dataviz;
use App\model\DAOFactory;

class Dataviz34 extends Dataviz
{
    /* AVAILABLE FILTERS FOR THIS DATAVIZ */
    const AVAILABLE_FILTERS = array("cursus","promo","civilite","date_sortie_hetic");

    /* CATEGORIES NAME A EVALUER */
    const GR_TECSU_NAME = "Technicien supérieur";
    const GR_CADRE_NAME = "Cadre";
    const GR_INDEP_NAME = "Indépendant";

    public function __construct() {
        parent::__construct();
    }

    protected function build() {
        //get eleves for stat
        $eleves = DAOFactory::get('eleve')->getAll( $this->filters );

        $idPerio6moi = DAOFactory::get('periode')->get6moisId();
        $idPerioActu = DAOFactory::get('periode')->getActuelleId();

        $techsup = 0;
        $cadre = 0;
        $inde = 0;
        $countElevesIgnored = 0;
        foreach($eleves as $eleveObj) {
            $filters = new Filterlist();
            $filters->add(new Filter('idEleve', $eleveObj->id()));
            $assocs = DAOFactory::get('assoc_data_periode')->getAll($filters);

            //get groupe socio pro
            $groupeEleve6moi = null;
            $groupeEleveActu = null;
            foreach($assocs as $assoc_data_periodeObj) {
                if($assoc_data_periodeObj->idGroupe()) {
                    if($assoc_data_periodeObj->idPeriode() == $idPerio6moi) {
                        $groupeEleve6moi = DAOFactory::get('groupe_socio_pro')->getOne($assoc_data_periodeObj->idGroupe());
                    }
                    elseif($assoc_data_periodeObj->idPeriode() == $idPerioActu) {
                        if(!$groupeEleve6moi) {
                            $groupeEleveActu = DAOFactory::get('groupe_socio_pro')->getOne($assoc_data_periodeObj->idGroupe());    
                        }
                    }
                }
            }

            $groupeEleveFinal = null;
            $groupeEleveFinal = $groupeEleve6moi ? $groupeEleve6moi : $groupeEleveActu;

            //le grpupe 6 mois prime alors il est evalue en premier
            if($groupeEleveFinal) {
                if($groupeEleveFinal->nom() === Entite::WORD_GR_EMPLOYE) { 
                    $techsup++; continue;
                }
                elseif($groupeEleveFinal->nom() === Entite::WORD_GR_INDEPENDANT) {
                    $inde++; continue;
                }
                elseif($groupeEleveFinal->nom() === Entite::WORD_GR_PATRON) {
                    $cadre++; continue;
                }
                elseif($groupeEleveFinal->nom() === Entite::WORD_GR_CADRE) {
                    $cadre++; continue;
                }
                else{
                    $countElevesIgnored++;
                    continue;
                }
            }
            else{
                $fonction = DAOFactory::get('fonction')->getOne($assoc_data_periodeObj->idFonction());
                $techsup++; //#TODO ici discriminer correctement en fonctionde la fonction 
                continue;
            }
        }
        
        $pop = $techsup + $inde + $cadre;
        $techsup = round($techsup * 100 / $pop) . "%";
        $inde = round($inde * 100 / $pop) . "%";
        $cadre = round($cadre * 100 / $pop) . "%";

        $result = array( 
            self::GR_TECSU_NAME => $techsup,
            self::GR_CADRE_NAME => $inde,
            self::GR_INDEP_NAME => $cadre
        );
        
        $this->dataJson = json_encode($result);
    }
}