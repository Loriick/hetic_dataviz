<?php

namespace App\model;

use App\model\DAO;
use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\FilterList;
use App\dataviz\Entities\AssocDataPeriode;

class AssocDataPeriodeDAO extends DAO
{
    const TABLE_NAME = 'assoc_data_periode';

    public function __construct( $db ) {
        parent::__construct($db);
    }

    public function getOne($eleve) {
        
    }

    public function getAll( FilterList $filters = null ) {
        //build request
        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if($filters) {
            $sql .= " WHERE ";
            $count = 0;
            foreach($filters->getList() as $filter) {

                $sql .= $filter->name() . $filter->operator() ."'". $filter->value() ."'";
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
            $assocs = array();
            while ($row = $stmt->fetch()) {
                $assocs[] = new AssocDataPeriode( $row );
                continue;
            }
        }
        catch (\PDOException $e) {
            $assocs = array();
        }
        
        return $assocs;
    }

    public function save(Entite &$assocDataPeriode) {
        if( $assocDataPeriode->id() === self::UNKNOWN_ID ) {
            //INSERT
            $sql = "INSERT INTO " . self::TABLE_NAME . " (".
                    "idEleve,
                     idPeriode,
                     idGroupe,
                     idContrat,
                     idFonction,
                     idSecteur,
                     idFourchette".
                    ") VALUES (".
                    ":idEleve,
                     :idPeriode,
                     :idGroupe,
                     :idContrat,
                     :idFonction,
                     :idSecteur,
                     :idFourchette )";
            $sth = $this->db->prepare($sql);
            $sth->execute( array(
                    ':idEleve' => $assocDataPeriode->idELeve(),
                    ':idPeriode' => $assocDataPeriode->idPeriode(),
                    ':idGroupe' => $assocDataPeriode->idGroupe(),
                    ':idContrat' => $assocDataPeriode->idContrat(),
                    ':idFonction' => $assocDataPeriode->idFonction(),
                    ':idSecteur' => $assocDataPeriode->idSecteur(),
                    ':idFourchette' => $assocDataPeriode->idFourchette()) );
            //set l'id de l'élève inseré
            $assocDataPeriode->setId($this->db->lastInsertId());
        }
        else {
            //UPDATE
        }
    }

    public function delete(Entite $obj) { }
}