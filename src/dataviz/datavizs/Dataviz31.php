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
    const FONC_RECHEMP_NAME = "Recherche d’emploi";
    const FONC_POURETU_NAME = "Poursuite d’études";

    public function __construct() {
        parent::__construct();
    }

    protected function build() {
        /*
            Cat1 : Développeurs
            SS Cat1.1 : Front | SS Cat1.2 : Back | SS Cat1.3 : Fullstack | SS Cat1.4 : Poursuites d’études dans le dev
            Cat2 : Designer
            SS Cat2.1 : UX | SS Cat2.2 : UI | SS Cat2.3:  DA | SS Cat2.4:  Poursuites d’études dans le design
            Cat3 : Management
            SS Cat3.1 : Lead dev & consor | SS Cat3.2 : Lead design & consor
            Cat4 : Recherche d’emploi
            Cat5 : Poursuite d’études
        */
        
        //
        /*
            //récuperation des eleves
            //recup des assocs data avec id eleve
            //recup l'id fonction de 6 mois
            //recup l'id fonction de Actuelle
            //si id == null pour les deux on ignore l'eleve -> OK
            //si actuelle on prend actuelle
            //sinon on prend 6 mois
            //placer l'etudiant ds la catégorie

        */
        //get eleves for stat
        $this->filters->add(new Filter('cursur','web'));
        $eleves = DAOFactory::get('eleve')->getAll( $this->filters );
        //get les assocs de tt les periodes
        $assoc_data_periode = DAOFactory::get('assoc_data_periode')->getAll();
        //on réduit le tableau $assoc_data_periode en fonction des id d'eleves ds la liste
        $idsElevAvailable = array();
        foreach($eleves as $eleve) {
            $idsElevAvailable[] = $eleve->id();
        }
        $assoc_data_periode = array_filter($assoc_data_periode, function($assocObj) use($idsElevAvailable) {
            return in_array($assocObj->idEleve(), $idsElevAvailable) !== false;
        });
        //parcours des assoc_data_periode filtrées pour creer la stat
        $develp = 0;
        $design = 0;
        $managm = 0;
        $recemp = 0;
        $pouetu = 0;
        $idPerio6moi = DAOFactory::get('periode')->get6moisId();
        $idPerioActu = DAOFactory::get('periode')->getActuelleId();
        $elevesIgnoredCount = 0;

        $tmp_assoc_data_ignored = array();
        //check pour la periode actuelle
        $periodeAEvaluer = 'actuelle';
        for( $i = 0; $i < count($assoc_data_periode); $i++ ) {
            $assocObj = $assoc_data_periode[$i];
            //get la fonction determinant pour le placement de l'élève
            $fonction = null;

            //d'abord pour la peridoe actuelle
            if($periodeAEvaluer === 'actuelle') {
                //evaluation de la periode actuelle
                if($assocObj->idPeriode() == $idPerioActu) {
                    if($idFonction){
                        $fonction = DAOFactory::get('fonction')->getOne($assocObj->idFonction());
                        //retirer l'eleve
                        $assoc_data_periode = array_filter($assoc_data_periode, function($tmpassocObj) use($idEleve) {
                            return $tmpassocObj->idEleve() === $assocObj->idEleve();
                        });
                    }
                }
                elseif($i != (count($assoc_data_periode) - 1)) {
                    $periodeAEvaluer = '6mois';
                }
                else{
                    continue;
                }
            }
            //puis la periode 6mois
            if($periodeAEvaluer === '6mois') {
                //evaluation de la periode 6mois
                if($assocObj->idPeriode() == $idPerioActu) {
                    if($idFonction){
                        $fonction = DAOFactory::get('fonction')->getOne($assocObj->idFonction());
                        //retirer l'eleve
                        $assoc_data_periode = array_filter($assoc_data_periode, function($tmpassocObj) use($idEleve) {
                            return $tmpassocObj->idEleve() === $assocObj->idEleve();
                        });
                    }
                }
            }

            //qd la fonction est definie on place l'élève
            if(!$fonction){
                continue;
            }
            if($fonction){
                //on retire l'assoc
                //MANAGEMENT
                if(strpos(($fonction), 'DA' ) !== false) {
                    $managm++;
                }
                elseif(strpos( strtolower($fonction), 'manager' ) !== false) {
                    $managm++;
                }
                elseif(strpos( strtolower($fonction), 'dire' ) !== false) {
                    $managm++;
                }
                elseif(strpos( strtolower($fonction), 'entrep' ) !== false) {
                    $managm++;
                }
                elseif(strpos( strtolower($fonction), 'lead' ) !== false) {
                    $managm++;
                }
                //DEV
                elseif(strpos( strtolower($fonction), 'dév' ) !== false) {
                    $develp++;
                }
                elseif(strpos( strtolower($fonction), 'dev' ) !== false) {
                    $develp++;
                }
                elseif(strpos( strtolower($fonction), 'intégra' ) !== false) {
                    $develp++;
                }
                //DESIGN
                elseif(strpos( strtolower($fonction), 'design' ) !== false) {
                    $design++;
                }
                elseif(strpos( strtolower($fonction), 'désign' ) !== false) {
                    $design++;
                }
                //POURSUITE ETUDE
                elseif(strpos( strtolower($fonction), 'poursui' ) !== false) {
                    $pouetu++;
                }
                else{
                    $recemp++;
                }
            }
            $i = 0;
        }
        //check pour la periode 6 mois si la periode actuelle ne renseignait rien
        if(count($tmp_assoc_data_ignored) > 0) {
            foreach($tmp_assoc_data_ignored as $assocObj) {
                $idFonction = $assocObj->idFonction();
                if($idFonction) {
                    $fonction = DAOFactory::get('fonction')->getOne($idFonction);
                }
                else{
                    $elevesIgnoredCount++;
                    continue;
                }
            }
        }
        



            //get les nom de contrat et de fonction qui vont aider a repartir 
            //l'etudiant ds les bonnes categories
            $contratName = Entite::WORD_NC;
            if($assocObj->idContrat()){
                $contratName = DAOFactory::get('contrat')->getOne($assocObj->idContrat())->nom();
            }
            $fonctionName = Entite::WORD_NC;
            if($assocObj->idFonction()){
                $fonctionName = DAOFactory::get('fonction')->getOne($assocObj->idFonction())->nom();
            }              

            //if hierarchique
            if( $fonctionName === Entite::WORD_FONCTION_POURSUITE_ETUDE ) {
                $pouetu++;
                continue;
            }
            if( $assocObj->idGroupe() == $idIndependant || $assocObj->idGroupe() == $idPatron ) {
                $entrep++;
                continue;
            }
            if( $contratName === Entite::WORD_CONTRAT_CDD ) {
                $concdd++;
                continue;
            }
            if( $contratName === Entite::WORD_CONTRAT_CDI ) {               
                $concdi++;
                continue;
            }
            if( $contratName === Entite::WORD_CONTRAT_CONTRAT_PRO ) {
                $pouetu++;
                continue;
            }
            $elevesIgnoredCount++;
        }
        
        $pop = $concdd + $concdi + $pouetu + $entrep;
        $concdd = round($concdd * 100 / $pop) . "%";
        $concdi = round($concdi * 100 / $pop) . "%";
        $pouetu = round($pouetu * 100 / $pop) . "%";
        $entrep = round($entrep * 100 / $pop) . "%";

        $result = array( 
            self::DATA_CON_CDD_NAME => $concdd,
            self::DATA_CON_CDI_NAME => $concdi,
            self::DATA_POU_ETU_NAME => $pouetu,
            self::DATA_ENTREPR_NAME => $entrep
        );
        
        $this->dataJson = json_encode($result);
    }
}