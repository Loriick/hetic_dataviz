<?php

namespace App\controller;

use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\dataviz\datafetcher\ExcellDataFetcher;
use App\model\DAOFactory;

class InsertionController 
{
    /**
     * INSERTION DES DONNEES
     */
    public function index() {
        $isDataAlreadyInserted = (bool) DAOFactory::get('eleve')->getOne(1);
        if( !$isDataAlreadyInserted ) {

            $insertor = new ExcellDataFetcher( new Xlsx() );
            
            $isInsertionOk = $insertor->insertDatas();

            if( !$isInsertionOk ) {
                throw new Exception("L'insertion a échoué"); 
            }
            echo("l'insertion s'est bien deroulée");
        }
        else{
            echo("Données deja inserées");
        }
    }
}