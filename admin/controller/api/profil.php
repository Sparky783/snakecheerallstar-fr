<?php
use ApiCore\Api;
use System\Session;
use System\Admin;

// Met a jour les informations de l'utilisateur connecté.
$app->post('/profil_update_infos', function($args) {
	$args['name'] = trim($args['name']);
	
	if ($args['name'] !== '') {
		$session = Session::getInstance();
		$user = Admin::getById($session->admin_id);
		$user->setName($args['name']);
		
		if ($user->saveToDatabase()) {
			$session->admin_name = $user->getName();
		
			$response = [
				'type' => "success",
				'message' => "Vos informations ont bien été mises à jour."
			];
		} else {
			$response = [
				'type' => "error",
				'message' => "Une erreur s'est produit lors de la mise à jour de vos informations."
			];
		}
	} else {
		$response = [
			'type' => "error",
			'message' => "Vous devez entrer un nom."
		];
	}
	
	API::sendJSON($response);
});

$app->post('/profil_update_password', function($args) {
	$session = Session::getInstance();
	$old_password = sha1(sha1(AUTH_SALT) . sha1($args['old_password']));
	$password = '';
	
	if ($args['old_password'] !== '' &&
		$args['new_password'] !== '' &&
		$args['confirm_password'] !== '' &&
		$old_password === $session->admin_password &&
		$args['new_password'] === $args['confirm_password']
	) {
		$password = sha1(sha1(AUTH_SALT) . sha1($args['new_password']));
	}
	
	if ($password !== '') {
		$user = Admin::getById($session->admin_id);
		$user->setPassword($password);
		
		if ($user->saveToDatabase()) {
			$session->admin_password = $args['new_password'];
		
			$response = [
				'type' => "success",
				'message' => "Votre mot de passe à été mises à jour."
			];
		} else {
			$response = [
				'type' => "error",
				'message' => "Une erreur s'est produit lors de la mise à jour de votre mot de passe."
			];
		}
	} else {
		$response = [
			'type' => "error",
			'message' => "Les informations que vous avez fournis ne sont pas correcte ou ne correspondent pas."
		];
	}
	
	API::sendJSON($response);
});
?>