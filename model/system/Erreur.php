<?php
class Erreur
{
	// == ATTRIBUTS ==
	private $error;

	// == METHODES PRIMAIRES ==
	public function __construct($error = null)
	{
		if($error != null){
			$this->SetError($error);
			//$this->SendEmail();
			//$this->SaveError();
		}
	}
	
	// == METHODES GETTERS ==
	public function GetError()
	{
		return $this->error;
	}
	
	// == METHODES SETTERS ==
	public function SetError($erreur)
	{
		$this->error = htmlspecialchars($erreur);
	}
	
	// == AUTRES METHODES ==
	// Envoi l'email avec les données contenu dans l'objet
	/*public function SendEmail()
	{
		if($this->erreur != "" and $this->erreur != null){
			$emeteur = array("Serveur flpmovie", $this->serveur_email);
			$destinataire = array($this->webmaster_email);
			$sujet = "Erreur sur le serveur flpmovie.";
			
			$message = "
				<html>
					<head>
						<title>Erreur sur le projet - ".ProjectManager::getProjectName()."</title>
					</head>
					<body style='margin:0px; padding:0px; -webkit-text-size-adjust:none;'>
						<table width='100%' cellpadding='0' cellspacing='0' border='0' bgcolor='#ffffff' style='background-color:#ffffff;'>
							<tbody>
								<tr>
									<td align='center'>
										<h1>Erreur sur le projet - ".ProjectManager::getProjectName()."</h1>
										<p>
											<br /><br />
											".$this->erreur."
										</p>
									</td>
								</tr>
							</tbody>
						</table>
					</body>
				</html>
			";
			$email = new Email($emeteur, $destinataire, $sujet, $message);
			if($email->envoi())
				return true;
		}
		return false;
	}
	
	// Sauvegarde du fichier erreur
	public function SaveError()
	{
		if($this->erreur != "" and $this->erreur != null){
			$dossier = "/home/flpmovie/errors_reports/".ProjectManager::getProjectName()."/".date("Y");
			if(ToolBox::IsDirectoryOrCreateIt($dossier)){
				$fichier = $dossier."/".date("M")."-liste_des_erreurs.txt";
				$code = "";
				if(file_exists($fichier)){
					$code .= file_get_contents($fichier);
					$code .= "\n\n";
				}
				$code .= "==== Erreur du ".date("d/m/Y à H:i:s")." ====\n".$this->erreur;
				file_put_contents($fichier, $code);
				return true;
			}
		}
		return false;
	}*/
}