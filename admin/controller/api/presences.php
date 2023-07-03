<?php
use ApiCore\Api;
use System\ToolBox;
use System\Database;
use Snake\Adherent;

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "coach")))
{
	$app->Get("/presences_get_list/{id_section}", function($args) {
		global $router;

		$nbr = 0;
		$html = "";
		$database = new Database();

		$rech = $database->query("SELECT COUNT(*) FROM presences WHERE id_section=:id_section AND jour=:jour", array(
			"id_section" => intval($args['id_section']),
			"jour" => date("Y-m-d")
		));
		
		if($rech != null)
		{
			$data = $rech->fetch();
			
			if(intval($data['COUNT(*)']) > 0)
			{
				$html = "
					<p class='text-center'>
						Les présences ont déjà été validé pour aujourd'hui.
						<br /><br />
						<a class='btn btn-snake' href=" . $router->GetUrl("home") . " title=''>Retour</a>
					</p>
				";
			}
			else
			{
				$adherents = Adherent::GetListBySection($args['id_section']);
				$htmlAdh = "";

				foreach($adherents as $adherent)
				{
					$htmlAdh .= "
						<tr data-id='" . $adherent->GetId() . "'>
							<td>" . $adherent->GetFirstname() . " " . $adherent->GetLastname() . "</td>
							<td class='text-right'>
								<div class='presence-button btn-group'>
									<button class='btn snake' data-type='present'><i class='far fa-thumbs-up'></i></button>
									<button class='btn justify' data-type='justify'><i class='far fa-file-alt'></i></button>
									<button class='btn warning' data-type='late'><i class='far fa-clock'></i></button>
									<button class='btn danger' data-type='absent'><i class='fas fa-ban'></i></button>
								</div>
							</td>
						</tr>
					";
					
					$nbr ++;
				}

				$html = "
					<div class='card-header clearfix'>
						<span class='float-left'>Il y a " . $nbr . " élèves</span>
					</div>
					<table id='tableAdherents' class='card-body table table-hover'>
						<tbody>
						" . $htmlAdh . "
						</tbody>
					</table>
					<div class='card-footer clearfix text-right'>
						<button id='validatePresences' class='btn btn-snake' type='button'>Valider les présences</button> 
					</div>
				";
			}
		}

		API::SendJSON(array(
			"html" => $html
		));
	});

	$app->Post("/validate_presences", function($args) {
		$database = new Database();

		$result = $database->Insert("presences", array(
			"id_section" => intval($args['section']),
			"jour" => date("Y-m-d"),
			"list" => serialize($args['status'])
		));
		
		API::SendJSON($result);
	});
}
?>