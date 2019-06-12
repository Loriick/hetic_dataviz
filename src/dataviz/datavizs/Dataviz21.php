<?php

namespace App\dataviz\datavizs;

use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\FilterList;
use App\dataviz\datavizs\Filter;
use App\dataviz\datavizs\Dataviz;
use App\model\DAOFactory;

class Dataviz21 extends Dataviz
{
    /* AVAILABLE FILTERS FOR THIS DATAVIZ */
    const AVAILABLE_FILTERS = array("cursus","annee_promo","civilite","date_sortie_hetic");

    /* CATEGORIES NAME A EVALUER */
    const DATA_CON_CDD_NAME = "CDD";
    const DATA_CON_CDI_NAME = "CDI";
    const DATA_POU_ETU_NAME = "Poursuite d’études";
    const DATA_ENTREPR_NAME = "Entreprenariat";

    public function __construct( $db ) {
        parent::__construct($db);
    }

    protected function build() {
        //get les eleves si un filtre est parametré pour filtrer les resultat de la dataviz 
        //en fonction de ce paramètre eleve
        $eleves = null;
        if($this->filters) {
            $eleves = DAOFactory::get('eleve')->getAll( $this->filters );
        }
        //get les assocs de la periode 6 mois après sortie HETIC
        $assocFilters = new FilterList();
        $assocFilters->add(new Filter('idPeriode', DAOFactory::get('periode')->get6moisId()));
        $assoc_data_periode = DAOFactory::get('assoc_data_periode')->getAll( $assocFilters );
        //sil y a des eleves, donc si un filtre eleve a été paramétré ds la requette
        //on réduit le tableau $assoc_data_periode en fonction des id d'eleves ds la liste
        if($eleves) {
            $idsElevAvailable = array();
            foreach($eleves as $eleve) {
                $idsElevAvailable[] = $eleve->id();
            }
            $assoc_data_periode = array_filter($assoc_data_periode, function($assocObj) use($idsElevAvailable) {
                return in_array($assocObj->idEleve(), $idsElevAvailable) !== false;
            });
        }
        //parcours des assoc_data_periode filtrées pour creer la stat
        $concdd = 0;
        $concdi = 0;
        $pouetu = 0;
        $entrep = 0;
        $group_soc_proDAO =     DAOFactory::get('groupe_socio_pro');
        $idIndependant =        $group_soc_proDAO->getGrIndeId();
        $idPatron =             $group_soc_proDAO->getGrDirId();
        $elevesIgnoredCount =   0;
        foreach( $assoc_data_periode as $assocObj ) {
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