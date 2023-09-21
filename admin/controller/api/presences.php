<?php
use ApiCore\Api;
use System\ToolBox;
use Snake\Adherent;
use Snake\Presence;

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'coach'])) {
	$app->get('/presences_list/{id_section}', function($args) {
		$presence = Presence::getByDay(new DateTime(), (int)$args['id_section']);
		$adherents = Adherent::getListBySection((int)$args['id_section']);
		$adherentsList = [];

		foreach ($adherents as $adherent) {
			$adherentData = [
				'id_adherent' => $adherent->getId(),
				'firstname' => $adherent->getFirstname(),
				'lastname' => $adherent->getLastname(),
				'status' => ''
			];

			if ($presence !== false) {
				foreach ($presence->getListMembers() as $item) {
					if ((int)$item['id'] === $adherent->getId()) {
						$adherentData['status'] = $item['state'];
						break;
					}
				}
			}

			$adherentsList[] = $adherentData;
		}

		API::sendJSON([
			'adherents' => $adherentsList
		]);
	});

	$app->post('/validate_presences', function($args) {
		$presence = Presence::getByDay(new DateTime(), (int)$args['id_section']);

		if ($presence === false) {
			$presence = new Presence();
			$presence->setIdSection((int)$args['id_section']);
			$presence->setDay(new DateTime());
		}

		if (!is_array($args['status'])) {
			API::sendJSON(false);
		}

		$presence->setListMembers($args['status']);
		
		API::sendJSON($presence->saveToDatabase());
	});

	$app->post('/presences_stats', function($args) {
		$presences = Presence::getListBySection((int)$args['id_section']);
		$adherents = Adherent::getListBySection((int)$args['id_section']);
		$adherentsList = [];

		foreach($adherents as $adherent) {
			$adherentsList[$adherent->getId()] = [
				'firstname' => $adherent->getFirstname(),
				'lastname' => $adherent->getLastname(),
				'status' => [
					'present' => 0,
					'justify' => 0,
					'late' => 0,
					'absent' => 0
				]
			];
		}

		foreach ($presences as $presence) {
			$list = $presence->getListMembers();

			foreach ($list as $item) {
				if (isset($adherentsList[$item['id']])) {
					$adherentsList[$item['id']]['status'][$item['state']] ++;
				}
			}
		}

		API::sendJSON([
			'adherents' => array_values($adherentsList)
		]);
	});

}
?>