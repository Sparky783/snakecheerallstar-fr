<?php
use ApiCore\Api;
use System\ToolBox;
use System\Database;
use Snake\Adherent;

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'coach'])) {
	$app->get('/presences_list/{id_section}', function($args) {
		global $router;

		$nbr = 0;
		$html = '';
		//TODO: Move this SQL query in an object.
		$database = new Database();

		$rech = $database->query("SELECT COUNT(*) FROM presences WHERE id_section=:id_section AND jour=:jour", array(
			'id_section' => (int)$args['id_section'],
			'jour' => date("Y-m-d")
		));
		
		if ($rech != null) {
			$data = $rech->fetch();
			
			if ((int)$data['COUNT(*)'] > 0) {
				$url = $router->getUrl("home");
				$html = <<<HTML
					<p class='text-center'>
						Les présences ont déjà été validé pour aujourd'hui.
						<br /><br />
						<a class='btn btn-snake' href="{$url}" title=''>Retour</a>
					</p>
					HTML;
			} else {
				$adherents = Adherent::getListBySection($args['id_section']);
				$htmlAdh = '';

				foreach ($adherents as $adherent) {
					$htmlAdh .= <<<HTML
						<tr data-id="{$adherent->getId()}">
							<td>{$adherent->GetFirstname()} {$adherent->GetLastname()}</td>
							<td class='text-right'>
								<div class='presence-button btn-group'>
									<button class='btn snake' data-type='present' title='Présent'><i class='far fa-thumbs-up'></i></button>
									<button class='btn justify' data-type='justify' title='Absence justifié'><i class='far fa-file-alt'></i></button>
									<button class='btn warning' data-type='late' title='En retard'><i class='far fa-clock'></i></button>
									<button class='btn danger' data-type='absent' title='Absent'><i class='fas fa-ban'></i></button>
								</div>
							</td>
						</tr>
						HTML;
					
					$nbr ++;
				}

				$html = <<<HTML
					<div class='card-header clearfix'>
						<span class='float-left'>Il y a {$nbr} élèves</span>
					</div>
					<table id='tableAdherents' class='card-body table table-hover'>
						<tbody>
							{$htmlAdh}
						</tbody>
					</table>
					<div class='card-footer clearfix text-right'>
						<button id='validatePresences' class='btn btn-snake' type='button'>Valider les présences</button> 
					</div>
				HTML;
			}
		}

		API::sendJSON([
			'html' => $html
		]);
	});

	$app->post('/validate_presences', function($args) {
		//TOTO: move SQL to an object
		$database = new Database();

		$result = $database->insert('presences', [
			'id_section' => (int)$args['section'],
			'jour' => date('Y-m-d'),
			'list' => serialize($args['status'])
		]);
		
		API::sendJSON($result);
	});
}
?>