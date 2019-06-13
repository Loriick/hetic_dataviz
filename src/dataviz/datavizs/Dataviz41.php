<?php

namespace App\dataviz\datavizs;

use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\FilterList;
use App\dataviz\datavizs\Filter;
use App\dataviz\datavizs\Dataviz;
use App\model\DAOFactory;

class Dataviz41 extends Dataviz
{
    /* AVAILABLE FILTERS FOR THIS DATAVIZ */
    const AVAILABLE_FILTERS = array("cursus","promo","civilite","date_sortie_hetic");

    /* CATEGORIES NAME A EVALUER */
    const FONC_DEVELOP_NAME = "Développeurs";
    const FONC_DESIGNR_NAME = "Designer";
    const FONC_MANEGMT_NAME = "Management";

    const FUNCTION_NOT_IN_CATEGORIES = -1;

    public function __construct() {
        parent::__construct();
    }

    protected function build() {

        /*

        //get les eleves
        //get eleve fonction
        //si fonction rentre ds une categorie
        //calcul fourchette salaire pour l'eleve
        //calcul fourchette salaire fonction catégorie

        //si eleve a renseigné fourchette pour cette periode sinon on ignore
                            //get la fourchette
                            //separer min/max
                            //si min < min fouchette categorie : on maj la fouchette basse
                            //si max > max fourchette categorie : on maj la fouchette max

        */


        //get eleves for stat
        $this->filters->add(new Filter('cursus','web'));
        $eleves = DAOFactory::get('eleve')->getAll( $this->filters );        

        //parcours des assoc_data_periode filtrées pour creer la stat
        $fourdevMin = 0; $fourdevMax = 0;
        $fourdesMin = 0; $fourdesMax = 0;
        $fourmanMin = 0; $fourmanMax = 0;
        $idPerio6moi = DAOFactory::get('periode')->get6moisId();
        $idPerioActu = DAOFactory::get('periode')->getActuelleId();
        $elevesIgnoredCount = 0;
        foreach($eleves as $eleveObj) {

            $filters = new Filterlist();
            $filters->add(new Filter('idEleve', $eleveObj->id()));
            $assoc_data_periode = DAOFactory::get('assoc_data_periode')->getAll($filters);

            //get la/les fonctions pour le placement de l'élève
            $fonctionEleve6moi = null;
            $fonctionEleveActu = null;
            foreach($assoc_data_periode as $assocObj) {

                //periode actuelle
                if($assocObj->idPeriode() == $idPerioActu) {

                    //si la fonction est renseignée
                    if($assocObj->idFonction()){
                        $fonctionEleveActuObj = DAOFactory::get('fonction')->getOne($assocObj->idFonction());

                        //si la fonction appartien a une categorie calcul de la fouchette
                        $functionCategory = $this->getFunctionCat($fonctionEleveActuObj);
                        if($functionCategory !== self::FUNCTION_NOT_IN_CATEGORIES) {

                            //calcul furchette salaire
                            if($assocObj->idFourchette()) {
                                $fourchetteObj = DAOFactory::get('fourchette')->getOne($assocObj->idFourchette());

                                //si la fourchette est renseignée
                                if($fourchetteObj->fourchette() != Entite::WORD_NC) {
                                    $fourMinMax = explode(',', $fourchetteObj->fourchette());
                                    //cas particulier fourchette = "X, +"
                                    if(strpos($fourchetteObj->fourchette(), '+' ) !== false) {
                                        $fourMin = (int) $fourMinMax[0];
                                        $fourMax = (int) $fourMinMax[0];
                                    }
                                    else{
                                        $fourMin = (int) $fourMinMax[0];
                                        $fourMax = (int) $fourMinMax[1];
                                        if($fourMin == 0) $fourMin = $fourMax;
                                    }                                    
                                    //si min < min fouchette categorie : on maj la fouchette basse
                                    //si max > max fourchette categorie : on maj la fouchette max
                                    switch($functionCategory) {
                                        case self::FONC_DEVELOP_NAME :
                                            if($fourdevMin > $fourMin || $fourdevMin === 0) {
                                                $fourdevMin = $fourMin;
                                            }
                                            if($fourdevMax < $fourMax || $fourdevMax === 0) {
                                                $fourdevMax = $fourMax;
                                            }
                                            break;
                                        case self::FONC_DESIGNR_NAME :
                                            if($fourdesMin > $fourMin || $fourdesMin === 0) {
                                                $fourdesMin = $fourMin;
                                            }
                                            if($fourdesMax < $fourMax || $fourdesMax === 0) {
                                                $fourdesMax = $fourMax;
                                            }
                                            break;
                                        case self::FONC_MANEGMT_NAME :
                                            if($fourmanMin > $fourMin || $fourmanMin === 0) {
                                                $fourmanMin = $fourMin;
                                            }
                                            if($fourmanMax < $fourMax || $fourmanMax === 0) {
                                                $fourmanMax = $fourMax;
                                            }
                                            break;
                                    }                                    
                                }
                                else{
                                    $elevesIgnoredCount++;
                                continue;
                                }
                            }
                            else{
                                $elevesIgnoredCount++;
                                continue;
                            }
                        }
                    }
                }
                elseif($assocObj->idPeriode() == $idPerio6moi) {

                    //si la fonction est renseignée
                    if($assocObj->idFonction()){
                        $fonctionEleve6moiObj = DAOFactory::get('fonction')->getOne($assocObj->idFonction());

                        //si la fonction appartien a une categorie calcul de la fouchette
                        $functionCategory = $this->getFunctionCat($fonctionEleve6moiObj);
                        if($functionCategory !== self::FUNCTION_NOT_IN_CATEGORIES) {

                            //calcul furchette salaire
                            if($assocObj->idFourchette()) {
                                $fourchetteObj = DAOFactory::get('fourchette')->getOne($assocObj->idFourchette());

                                //si la fourchette est renseignée
                                if($fourchetteObj->fourchette() != Entite::WORD_NC) {
                                    $fourMinMax = explode(',', $fourchetteObj->fourchette());
                                    //cas particulier fourchette = "X, +"
                                    if(strpos($fourchetteObj->fourchette(), '+' ) !== false) {
                                        $fourMin = (int) $fourMinMax[0];
                                        $fourMax = (int) $fourMinMax[0];
                                    }
                                    else{
                                        $fourMin = (int) $fourMinMax[0];
                                        $fourMax = (int) $fourMinMax[1];
                                        if($fourMin == 0) $fourMin = $fourMax;
                                    }       
                                    
                                    //si min < min fouchette categorie : on maj la fouchette basse
                                    //si max > max fourchette categorie : on maj la fouchette max
                                    switch($functionCategory) {
                                        case self::FONC_DEVELOP_NAME :
                                            if($fourdevMin > $fourMin || $fourdevMin === 0) {
                                                $fourdevMin = $fourMin;
                                            }
                                            if($fourdevMax < $fourMax || $fourdevMax === 0) {
                                                $fourdevMax = $fourMax;
                                            }
                                            break;
                                        case self::FONC_DESIGNR_NAME :
                                            if($fourdesMin > $fourMin || $fourdesMin === 0) {
                                                $fourdesMin = $fourMin;
                                            }
                                            if($fourdesMax < $fourMax || $fourdesMax === 0) {
                                                $fourdesMax = $fourMax;
                                            }
                                            break;
                                        case self::FONC_MANEGMT_NAME :
                                            if($fourmanMin > $fourMin || $fourmanMin === 0) {
                                                $fourmanMin = $fourMin;
                                            }
                                            if($fourmanMax < $fourMax || $fourmanMax === 0) {
                                                $fourmanMax = $fourMax;
                                            }
                                            break;
                                    }                                    
                                }
                                else{
                                    $elevesIgnoredCount++;
                                continue;
                                }
                            }
                            else{
                                $elevesIgnoredCount++;
                                continue;
                            }
                        }
                    }
                }
            }
        }
        $result = array( 
            self::FONC_DEVELOP_NAME => implode(',', array($fourdevMin, $fourdevMax)),
            self::FONC_DESIGNR_NAME => implode(',', array($fourdesMin, $fourdesMax)),
            self::FONC_MANEGMT_NAME => implode(',', array($fourmanMin, $fourmanMax))
        );
        
        $this->dataJson = json_encode($result);
    }

    private function getFunctionCat(Entite $fonctionObj) {
        $fonction = $fonctionObj->nom();

        //MANAGEMENT
        if(strpos(($fonction), 'DA' ) !== false) {
            return self::FONC_MANEGMT_NAME;
        }
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'manager' ) !== false) {
            return self::FONC_MANEGMT_NAME;
        }
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'dire' ) !== false) {
            return self::FONC_MANEGMT_NAME;
        }
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'entrep' ) !== false) {
            return self::FONC_MANEGMT_NAME;
        }
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'lead' ) !== false) {
            return self::FONC_MANEGMT_NAME;
        }
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'chef' ) !== false) {
            return self::FONC_MANEGMT_NAME;
        }
        //DEV
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'dév' ) !== false) {
            return self::FONC_DEVELOP_NAME;
        }
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'dev' ) !== false) {
            return self::FONC_DEVELOP_NAME;
        }
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'intégra' ) !== false) {
            return self::FONC_DEVELOP_NAME;
        }
        //DESIGN
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'design' ) !== false) {
            return self::FONC_DESIGNR_NAME;
        }
        elseif(strpos( mb_strtolower($fonction, 'UTF-8'), 'désign' ) !== false) {
            return self::FONC_DESIGNR_NAME;
        }
        //AUTRE
        else{
            return self::FUNCTION_NOT_IN_CATEGORIES;
        }
    }
}