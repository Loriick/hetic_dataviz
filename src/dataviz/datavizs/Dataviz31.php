<?php

namespace App\dataviz\datavizs;

use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\FilterList;
use App\dataviz\datavizs\Filter;
use App\dataviz\datavizs\Dataviz;
use App\model\DAOFactory;

class Dataviz31 extends Dataviz
{
    /* AVAILABLE FILTERS FOR THIS DATAVIZ */
    const AVAILABLE_FILTERS = array("cursus","promo","civilite","date_sortie_hetic");

    /* CATEGORIES NAME A EVALUER */
    const FONC_DEVELOP_NAME = "Développeurs";
    const FONC_DESIGNR_NAME = "Designer";
    const FONC_MANEGMT_NAME = "Management";
    const FONC_AUTRE_NAME   = "Autre";
    const FONC_POURETU_NAME = "Poursuite d’études";

    public function __construct() {
        parent::__construct();
    }

    protected function build() {
        //get eleves for stat
        $this->filters->add(new Filter('cursus','web'));
        $eleves = DAOFactory::get('eleve')->getAll( $this->filters );        

        //parcours des assoc_data_periode filtrées pour creer la stat
        $develp = 0;
        $design = 0;
        $managm = 0;
        $autre = 0;
        $pouetu = 0;
        $idPerio6moi = DAOFactory::get('periode')->get6moisId();
        $idPerioActu = DAOFactory::get('periode')->getActuelleId();
        $elevesIgnoredCount = 0;
        foreach($eleves as $eleveObj) {

            $filters = new Filterlist();
            $filters->add(new Filter('idEleve', $eleveObj->id()));
            $assoc_data_periode = DAOFactory::get('assoc_data_periode')->getAll($filters);

            //get la fonction determinant pour le placement de l'élève
            $fonctionEleve6moi = null;
            $fonctionEleveActu = null;
            foreach($assoc_data_periode as $assocObj) {
                if($assocObj->idPeriode() == $idPerioActu) {
                    if($assocObj->idFonction()){
                        $fonctionEleveActu = DAOFactory::get('fonction')->getOne($assocObj->idFonction());
                    }
                }
                elseif($assocObj->idPeriode() == $idPerio6moi) {
                    if($assocObj->idFonction()){
                        $fonctionEleve6moi = DAOFactory::get('fonction')->getOne($assocObj->idFonction());
                    }
                }
            }
            
            $fonctionObj = null;
            $fonctionObj = $fonctionEleveActu ? $fonctionEleveActu : $fonctionEleve6moi;

            //placememnt de l'etudiant
            if(!$fonctionObj){
                $elevesIgnoredCount++;
                continue;
            }
            if($fonctionObj){
                //MANAGEMENT
                if(strpos(($fonctionObj->nom()), 'DA' ) !== false) {
                    $managm++;
                }
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'manager' ) !== false) {
                    $managm++; continue;
                }
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'dire' ) !== false) {
                    $managm++; continue;
                }
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'entrep' ) !== false) {
                    $managm++; continue;
                }
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'lead' ) !== false) {
                    $managm++; continue;
                }
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'chef' ) !== false) {
                    $managm++; continue;
                }
                //DEV
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'dév' ) !== false) {
                    $develp++; continue;
                }
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'dev' ) !== false) {
                    $develp++; continue;
                }
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'intégra' ) !== false) {
                    $develp++; continue;
                }
                //DESIGN
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'design' ) !== false) {
                    $design++; continue;
                }
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'désign' ) !== false) {
                    $design++; continue;
                }
                //POURSUITE ETUDE
                elseif(strpos( mb_strtolower($fonctionObj->nom(), 'UTF-8'), 'poursui' ) !== false) {
                    $pouetu++; continue;
                }
                //NON COMPTE
                elseif(strpos( $fonctionObj->nom(), Entite::WORD_NC ) !== false) {
                    $elevesIgnoredCount++; continue;
                }
                //AUTRE
                else{
                    $autre++; continue;
                }
            } 
        }
        
        $pop = $managm + $develp + $design + $pouetu + $autre;
        $managm = round($managm * 100 / $pop) . "%";
        $develp = round($develp * 100 / $pop) . "%";
        $design = round($design * 100 / $pop) . "%";
        $pouetu = round($pouetu * 100 / $pop) . "%";
        $autre =  round($autre * 100 / $pop) . "%";

        $result = array( 
            self::FONC_DEVELOP_NAME => $develp,
            self::FONC_DESIGNR_NAME => $design,
            self::FONC_MANEGMT_NAME => $managm,
            self::FONC_POURETU_NAME => $pouetu,
            self::FONC_AUTRE_NAME   => $autre
        );
        
        $this->dataJson = json_encode($result);
    }
}