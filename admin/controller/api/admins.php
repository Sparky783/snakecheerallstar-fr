<?php
use ApiCore\Api;
use System\ToolBox;
use System\Admin;
use Snake\SnakeMailer;

if (ToolBox::searchInArray($session->admin_roles, ['admin'])) {
	$app->post('/admins_list', function($args) {
		$admins = Admin::getList();
		$list = [];
		
		foreach ($admins as $admin) {
			$list[] = $admin->toArray();
		}

		Api::sendJSON(['admins' => $list]);
	});

	$app->post('/admin_add', function($args) {
		$password = ToolBox::generatePassword();

		$admin = new Admin();
		$admin->setEmail($args['email']);
		$admin->setPassword($password);
		$admin->setName($args['name']);
		$admin->setRoles($args['roles']);
		
		if ($admin->saveToDatabase() && SnakeMailer::sendNewAdminAccount($admin, $password)) {
			Api::sendJSON($admin->getId());
		}

		Api::sendJSON(false);
	});

	$app->post('/admin_edit', function($args) {
		$admin = Admin::getById($args['id_admin']);

		if ($admin === false) {
			Api::sendJSON(false);
		}

		$admin->setEmail($args['email']);
		$admin->setName($args['name']);
		$admin->setRoles($args['roles']);

		Api::sendJSON($admin->saveToDatabase());
	});

	$app->post('/admin_remove', function($args) {
		Api::sendJSON(Admin::removeFromDatabase($args['id_admin']));
	});

	$app->post('/reinit_admin_password', function($args) {
		$password = ToolBox::generatePassword();

		$admin = Admin::getById($args['id_admin']);
		$admin->setPassword($password);

		if ($admin->saveToDatabase() && SnakeMailer::sendNewAdminPassword($admin, $password)) {
			API::sendJSON($admin->getId());
		}
		
		API::sendJSON(false);
	});
}
?>