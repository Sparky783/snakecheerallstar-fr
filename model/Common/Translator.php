<?php
namespace Common;

/*
 * Permet de gérer les traductions.
 */

class Translator
{
    private $_folder = ABSPATH . "view/trads";
    private $_lang = "fr";
    private $_acceptedLang = array("fr", "en");
    private $_trads = array(); // Liste des traductions.


    /**
     * Constructor.
     */
    public function __construct()
    {
        global $gmm;

        if($lang = $gmm->GetValue("lang"))
        {
            $this->_lang = $lang;
        }
        else
        {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $this->_lang = in_array($lang, $this->_acceptedLang) ? $lang : "fr";

            $gmm->setValue("lang", $this->_lang);
        }
    }

    public function LoadTrad($page)
    {
        require_once $this->_folder . "/" . $page . "/trad_fr.php";
        require_once $this->_folder . "/" . $page . "/trad_" . $this->_lang . ".php"; 
    }

    /**
     * Ajoute une traduction à la liste.
     */
    public function AddTrad($textId, $textContent)
    {
        $this->_trads[$textId] = $textContent;  
    }

    /**
     * Retourne la traduction souhaité.
     */
    public function Trad($textId)
    {
        echo $this->_trads[$textId];
    }
}
?>