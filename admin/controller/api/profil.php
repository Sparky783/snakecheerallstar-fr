<?php
if(ToolBox::SearchInArray($session->admin_roles, array("admin", "member")))
{
	// Met a jour les informations de l'utilisateur connecté.
	$app->Post("/profil_update_infos", function($args) {
		include_once(ABSPATH . "model/system/Admin.php");
		
		$args['name'] = trim($args['name']);
		
		if($args['name'] != "")
		{
			$session = Session::GetInstance();
			$user = Admin::GetById($session->admin_id);
			$user->SetName($args['name']);
			
			if($user->SaveToDatabase())
			{
				$session->admin_name = $user->GetName();
			
				$response = array(
					"type" => "success",
					"message" => "Vos informations ont bien été mises à jour."
				);
			}
			else
			{
				$response = array(
					"type" => "error",
					"message" => "Une erreur s'est produit lors de la mise à jour de vos informations."
				);
			}
		}
		else
		{
			$response = array(
				"type" => "error",
				"message" => "Vous devez entrer un nom."
			);
		}
		
		API::SendJSON($response);
	});

	$app->Post("/profil_update_password", function($args) {
		include_once(ABSPATH . "model/system/Admin.php");
		
		$session = Session::GetInstance();
		$old_password = sha1(sha1(AUTH_SALT) . sha1($args['old_password']));
		$password = "";
		
		if ($args['old_password'] != "" &&
			$args['new_password'] != "" &&
			$args['confirm_password'] != "" &&
			$old_password == $session->admin_password &&
			$args['new_password'] == $args['confirm_password']
		)
			$password = sha1(sha1(AUTH_SALT) . sha1($args['new_password']));
		
		if($password != "")
		{
			$user = Admin::GetById($session->admin_id);
			$user->SetPassword($password);
			
			if($user->SaveToDatabase())
			{
				$session->admin_password = $args['new_password'];
			
				$response = array(
					"type" => "success",
					"message" => "Votre mot de passe à été mises à jour."
				);
			}
			else
			{
				$response = array(
					"type" => "error",
					"message" => "Une erreur s'est produit lors de la mise à jour de votre mot de passe."
				);
			}
		}
		else
		{
			$response = array(
				"type" => "error",
				"message" => "Les informations que vous avez fournis ne sont pas correcte ou ne correspondent pas."
			);
		}
		
		API::SendJSON($response);
	});
}
?>