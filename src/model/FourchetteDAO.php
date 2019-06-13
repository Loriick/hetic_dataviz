<?php

namespace App\model;

use App\model\DAO;
use App\dataviz\Entities\Entite;
use App\dataviz\datavizs\FilterList;
use App\dataviz\Entities\EntityFactory;
use App\dataviz\Entities\Fourchette;

class FourchetteDAO extends DAO{

    const TABLE_NAME = 'fourchette_salaire';
    const ID_NAME = 'idFourchette';

    public function __construct( $db ) {
        parent::__construct($db);
    }

    public function getOne($id) {
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ". self::ID_NAME ." = $id";
        $sth = $this->db->query($sql);
        $props = $sth->fetch();
        return EntityFactory::get('fourchette', $props);
    }

    public function getAll(FilterList $filters = null) {
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
            $fourchettes = array();
            while ($row = $stmt->fetch()) {
                $fourchettes[] = EntityFactory::get('fourchette', $row);
                continue;
            }
        }
        catch (\PDOException $e) {
            $fourchettes = array();
        }
        
        return $fourchette;
    }

    public function save(Entite &$fourchette) {
        if( $fourchette->id() === self::UNKNOWN_ID ) {
            if(!$fourchette->isEmpty()) {
                //INSERT
                $sql = "INSERT INTO ". self::TABLE_NAME." (". 
                        "fourchette".
                        ") VALUES (".
                        ":fourchette )";
                $sth = $this->db->prepare($sql);
                $sth->execute( array(':fourchette' => $fourchette->fourchette()) );
                $fourchette->setId($this->db->lastInsertId());
            }
            else{
                $fourchette->setId(Entite::WORD_NC); 
            }
        }
        else {
            //UPDATE
        }
    }

    public function delete(Entite $obj) { }
}