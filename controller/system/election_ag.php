<?php
WebSite::Redirect("accueil", true);

// ================================
// ==== Controleur Election AG ====
// ================================

include_once(ABSPATH . "model/system/Database.php");

$database = new Database();
$rech = $database->Query("SELECT * FROM ag_candidats");

$html = "<tbody>";

if($rech != null)
{
	while($donnees = $rech->fetch())
	{
		$html .= "
			<tr>
				<td>
					<label for='inputMessage'>" . $donnees['firstname'] . " " . $donnees['lastname'] . "</label>
				</td>
				<td class='choice'>
					<div class='custom-control custom-radio custom-control-inline'>
						<input id='candidate" . $donnees['id_candidat'] . "yes' class='custom-control-input' type='radio' name='candidat" . $donnees['id_candidat'] . "' value='yes'>
						<label class='custom-control-label' for='candidate" . $donnees['id_candidat'] . "yes'>Oui</label>
					</div>
					<div class='custom-control custom-radio custom-control-inline'>
						<input id='candidate" . $donnees['id_candidat'] . "no' class='custom-control-input' type='radio' name='candidat" . $donnees['id_candidat'] . "' value='no'>
						<label class='custom-control-label' for='candidate" . $donnees['id_candidat'] . "no'>Non</label>
					</div>
				</td>
			</tr>
		";
	}
}

$html .= "</tbody>";
?>