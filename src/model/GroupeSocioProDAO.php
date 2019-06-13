<?php

namespace App\model;

use App\model\DAO;
use App\dataviz\Entities\Entite;
use App\dataviz\Entities\EntityFactory;
use App\dataviz\datavizs\FilterList;
use App\dataviz\Entities\GroupeSocioPro;

class GroupeSocioProDAO extends DAO 
{
    const TABLE_NAME = 'groupe_socio_pro';
    const ID_NAME = 'idGroupe';
    const NAME_WORD = "nom";

    public function __construct( $db ) {
        parent::__construct($db);
    }

    public function getOne($id) {
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ". self::ID_NAME ." = $id";
        $sth = $this->db->query($sql);
        $props = $sth->fetch();
        return EntityFactory::get('groupe_socio_pro', $props);
    }

    public function getAll(FilterList $filters = null) { }

    public function getGrEmployeId() {
        $sql =  "SELECT ". self::ID_NAME .
                " FROM " . self::TABLE_NAME . 
                " WHERE ". self::NAME_WORD ." = '". GroupeSocioPro::WORD_GR_EMPLOYE . "'";
        $sth = $this->db->query($sql);
        return (int) $sth->fetch()['idGroupe'];                                
    }

    public function getGrIndeId() {
        $sql =  "SELECT ". self::ID_NAME .
                " FROM " . self::TABLE_NAME . 
                " WHERE ". self::NAME_WORD ." = '". GroupeSocioPro::WORD_GR_INDEPENDANT . "'";
        $sth = $this->db->query($sql);
        return (int) $sth->fetch()['idGroupe'];                                                
    }

    public function getGrCadreId() {
        $sql =  "SELECT ". self::ID_NAME .
                " FROM " . self::TABLE_NAME . 
                " WHERE ". self::NAME_WORD ." = '". GroupeSocioPro::WORD_GR_CADRE . "'";
        $sth = $this->db->query($sql);
        return (int) $sth->fetch()['idGroupe'];                  
    }

    public function getGrDirId() {
        $sql =  "SELECT ". self::ID_NAME .
                " FROM " . self::TABLE_NAME . 
                " WHERE ". self::NAME_WORD ." = '". GroupeSocioPro::WORD_GR_PATRON . "'";
        $sth = $this->db->query($sql);
        return (int) $sth->fetch()['idGroupe'];                     
    }

    public function save(Entite &$groupSocPro){
        //ici vu que les differents type de contrat sont des valeurs enums, 
        //on rempli juste l'id du contrat correspondant a celui de l'objet
        switch($groupSocPro->nom()) {
            case GroupeSocioPro::WORD_GR_EMPLOYE :
                $groupSocPro->setId($this->getGrEmployeId());
                break;
            case GroupeSocioPro::WORD_GR_INDEPENDANT :
                $groupSocPro->setId($this->getGrIndeId());
                break;
            case GroupeSocioPro::WORD_GR_CADRE :
                $groupSocPro->setId($this->getGrCadreId());
                break;
            case GroupeSocioPro::WORD_GR_PATRON :
                $groupSocPro->setId($this->getGrDirId());
                break;
            case GroupeSocioPro::WORD_NC :
                $groupSocPro->setId(null);
                break;
            default:
                throw new Exception('L\'entite"GroupSocPro" porte une valeur "nom" invalide');
        }
    }

    public function delete(Entite $obj) {}
}