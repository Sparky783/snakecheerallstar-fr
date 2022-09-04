<?php
echo "==== Patch de mise à jour de la base de données et des dépendances ====";
echo "<br /><br />";
/*
include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.
include_once(ABSPATH . "model/system/Database.php");

$database = new Database();

$payments = $database->Query("SELECT * FROM payments");

if($payments != null)
{
    $result = true;

    while($payment = $payments->fetch())
    {
        $result = $result & $database->Update(
            "payments", "id_payment", intval($payment['id_payment']),
            array(
                "deadlines" => count(unserialize($payment['deadlines']))
            )
        );
    }

    if($result)
    {
        $database->Query("ALTER TABLE `payments` CHANGE `deadlines` `nb_deadlines` INT NULL DEFAULT NULL COMMENT 'Echeances';");

        if($database->GetError() == false)
            echo "Patch installed !";
        else
            echo "Error : Impossible de modifier la colonne dans la table payments";
    }
}
else
{
    echo "Error : Impossible récupérer la liste des paiements.";
}
*/