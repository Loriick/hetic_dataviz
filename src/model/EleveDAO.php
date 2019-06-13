<?php

namespace App\model;

use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\FilterList;
use App\dataviz\Entities\Eleve;

class EleveDAO extends DAO 
{

    const TABLE_NAME = 'eleve';

    public function __construct( $db ) {
        parent::__construct($db);
    }

    public function getOne($id) {
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE idEleve = $id";
        $sth = $this->db->query($sql);
        $props = $sth->fetch();
        return new Eleve($props);
    }

    public function getAll( FilterList $filters = null ) {
        //build request
        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if(!$filters->isEmpty()) {
            $sql .= " WHERE ";
            $count = 0;
            foreach($filters->getList() as $filter) {

                $sql .= $filter->name() ." = '". $filter->value() ."'";
                $count++;

                if( $count < count($filters->getList()) ) {
                    $sql .= " AND ";
                }
            }                    
        }
        //get result
        try {
            $stmt = $this->db->query($sql);
            //create Eleves array
            $eleves = array();
            while ($row = $stmt->fetch()) {
                $eleves[] = new Eleve( $row );
                continue;
            }
        }
        catch (\PDOException $e) {
            $eleves = array();
        }
        
        return $eleves;
    }

    public function save(Entite &$eleve) {
        if( $eleve->id() === self::UNKNOWN_ID ) {
            //insert
            $sql = "INSERT INTO " . self::TABLE_NAME . " (".
                    "cursus,
                     civilite,
                     date_sortie_hetic,
                     ville,
                     code_postal_residence,
                     pays,
                     annee_promo,
                     etudes_avant_hetic,
                     situation_pro_sortie_hetic,
                     jobs_notables_exerces".
                    ") VALUES (".
                    ":cursus,
                     :civilite,
                     :date_sortie_hetic,
                     :ville,
                     :code_postal_residence,
                     :pays,
                     :annee_pro,
                     :etudes_avant_hetic,
                     :situation_pro_sortie_hetic,
                     :jobs_notables_exerces )";
            $sth = $this->db->prepare($sql);
            $sth->execute( array( 
                    ':cursus' =>$eleve->cursus(),
                    ':civilite' => $eleve->civilite(),
                    ':date_sortie_hetic' => $eleve->date_sortie_hetic(),
                    ':ville' => $eleve->ville(),
                    ':code_postal_residence' => $eleve->code_postal_residence(),
                    ':pays' => $eleve->pays(),
                    ':annee_pro' => $eleve->annee_promo(),
                    ':etudes_avant_hetic' => $eleve->etudes_avant_hetic(),
                    ':situation_pro_sortie_hetic' => $eleve->situation_pro_sortie_hetic(),
                    ':jobs_notables_exerces' => $eleve->jobs_notables_exerces() ));
            //set l'id de l'élève inseré
            $eleve->setId($this->db->lastInsertId());
        }
        else {
            //UPDATE
        }
    }

    public function delete(Entite $obj) { }
}