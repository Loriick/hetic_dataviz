<?php

namespace App\dataviz\Entities;

class Fourchette extends Entite
{
    private $id;
    private $fourchette;

    public function __construct( array $props ) {
        $this->id =   isset( $props['idFourchette'] ) ? $props['idFourchette'] : self::UNKNOW_ID;
        $this->fourchette = $props['fourchette'];

        //on test pour clean sinon bug dans la dataviz 4.1 : xxxx,xxxx => xxxx,, due aux fonctions de cleaning
        if($this->id == self::UNKNOW_ID) {
            $this->clean();
        }
    }

    /**
     * GETTERS
     */
    public function id() {
        return $this->id;
    }
    public function fourchette() {
        return $this->fourchette;
    }
    /**
     * SETTERS
     */
    public function setId($id) {
        $this->id = $id;
    }
    
    protected function clean() {
        if(strpos( $this->fourchette, "NC" ) !== false) {
            $this->fourchette = self::WORD_NC;
        }
        elseif(strpos( strtolower($this->fourchette), "poursuite" ) !== false) {
            $this->fourchette = self::WORD_NC;
        }
        elseif(strpos( strtolower($this->fourchette), "emploi" ) !== false) {
            $this->fourchette = self::WORD_NC;
        }

        //cas de : "moins de X €"
        elseif(strpos( strtolower($this->fourchette), "moins de" ) !== false)
        {
            $tmp = trim(str_replace("moins de", "", $this->fourchette)); // recup "X €"
            $tmp_chiffre_salaire = substr($tmp, 0, 6); //on extrait les 6 premiers caractères qui représente la fourchette
            $chiffre_salaire = str_replace(" ", "", $tmp_chiffre_salaire);
            $this->fourchette = "0," . $chiffre_salaire;
        }

        //cas de : "plus de X €"
        elseif(strpos( strtolower($this->fourchette), "plus de" ) !== false)
        {
            $tmp = trim(str_replace("plus de", "", $this->fourchette)); // recup "X €"
            $tmp_chiffre_salaire = substr($tmp, 0, 6); //on extrait les 6 premiers caractères qui représente la fourchette
            $chiffre_salaire = str_replace(" ", "", $tmp_chiffre_salaire);
            $this->fourchette = $chiffre_salaire . ',+';
        }

        //cas de : 'de 32 000 € à 34 999 €'
        elseif($this->fourchette !== null)
        {
            $tmp = trim(str_replace("de", "", $this->fourchette)); // recup "32 000 € à 34 999 €"
            $chiffre_bas = trim(str_replace(" ", "",substr($tmp, 0, 6))); //on extrait les 6 premiers caractères qui représente la fourchette basse
            $chiffre_haut = trim(str_replace(" ", "",substr($tmp, 13, 7))); //on extrait les caractères 13 à 20 qui représente la fourchette haute
            $this->fourchette = $chiffre_bas . "," . $chiffre_haut;
        }
    }

    public function isEmpty() {
        return (bool) !$this->fourchette;
    }
}