<?php
if(ToolBox::SearchInArray($session->admin_roles, array("admin", "member")))
{
	$app->Post("/change_saison", function($args) {
		include_once(ABSPATH . "model/snake/SnakeTools.php");

		if(preg_match('/^\d{4}-\d{4}$/i', $args['saison']))
		{
			$session = Session::getInstance();
			$session->selectedSaison = $args['saison'];
	
			API::SendJSON(true);
		}
		
		API::SendJSON(false);
	});
}
?>