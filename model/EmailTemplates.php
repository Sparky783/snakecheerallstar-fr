<?php
require_once(ABSPATH . "model/system/ToolBox.php");
require_once(ABSPATH . "model/snake/Payment.php");
require_once(ABSPATH . "model/snake/Tuteur.php");

class EmailTemplates
{
	// E-mail text standard.
	static public function TextFormat($subject) {
		return "Sujet:" . $subject . "\n\nVeuillez autoriser l'affichage des E-mails au format HTML pour consulter le contenu de cet E-mail.";
	}

	// Affichage standard des E-mail.
	static public function StandardHTML($subject = "", $message = "") {
		return "
			<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='fr'>
				<head>
					<title>" . TITLE . " - " . $subject . "</title>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
					<meta content='width=device-width,initial-scale=1' />
					<style type='text/css'>
						/* Fonts and Content */
						body, td { font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; font-size:14px; color:#333; }
						body { background-color:#e0e0e3; margin:0; padding:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; }
					</style>
				</head>
				<body style='width:100%; padding:0px; -webkit-text-size-adjust:none; background-color:#e0e0e3;'>
					<table width='700px' cellpadding='0' cellspacing='0' border='0' style='margin:0px auto;'><tbody>
						<tr><td width='700px' height='50px' bgcolor='#e0e0e3' valign='top'>
						</td></tr>
						<tr><td width='700px' height='1px' bgcolor='#ababab' valign='top'>
						</td></tr>
						<tr><td width='700px' valign='top'>
							<table width='700px' cellpadding='0' cellspacing='0' border='0'><tbody>
								<tr>
									<td width='1px' bgcolor='#ababab' valign='top'></td>
									<td width='698px' bgcolor='#fff' valign='top'>
										<table width='698px' cellpadding='0' cellspacing='0' border='0'><tbody>
											<tr><td width='698px' height='20px' bgcolor='#fff' valign='top'>
											</td></tr>
											<tr><td width='698px' bgcolor='#fff' valign='top'>
												<table width='698px' cellpadding='0' cellspacing='0' border='0'><tbody>
													<tr>
														<td width='20px' bgcolor='#fff' valign='top'></td>
														<td width='120px' bgcolor='#fff' valign='top'>
															<img src='" . URL . "/view/img/logoEmail.jpg' alt='logo officiel des Snake Cheer All Star' />
														</td>
														<td width='20px' bgcolor='#fff' valign='top'></td>
														<td width='518px' bgcolor='#fff' valign='middle'>
															<b style='font-size:16px'>Snake Cheer All Star</b><br />
															<b>" . $subject . "</b>
														</td>
														<td width='20px' bgcolor='#fff' valign='top'></td>
													</tr>
												</tbody></table>
											</td></tr>
											<tr><td width='698px' height='20px' bgcolor='#fff' valign='top'>
											</td></tr>
											<tr><td width='698px' bgcolor='#fff' valign='top'>
												<table width='698px' cellpadding='0' cellspacing='0' border='0'><tbody>
													<tr>
														<td width='20px' bgcolor='#fff' valign='top'></td>
														<td width='658px' bgcolor='#fff' valign='top'>
															" . $message . "
														</td>
														<td width='20px' bgcolor='#fff' valign='top'></td>
													</tr>
												</tbody></table>
											</td></tr>
											<tr><td width='698px' height='20px' bgcolor='#fff' valign='top'>
											</td></tr>
										</tbody></table>
									</td>
									<td width='1px' bgcolor='#ababab' valign='top'></td>
								</tr>
							</tbody></table>
						</td></tr>
						<tr><td width='700px' height='1px' bgcolor='#ababab' valign='top'>
						</td></tr>
						<tr><td width='700px' height='50px' bgcolor='#e0e0e3' valign='top'>
						</td></tr>
					</tbody></table>
				</body>
			</html>
		";
	}

	// E-mail de facture.
	static public function BillHTML($number, Payment $payment, Tuteur $tuteur)
	{
		include_once(ABSPATH . "model/snake/SnakeTools.php");

		$adherents = $payment->GetAdherents();

		$html = "
			<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='fr'>
				<head>
					<title>" . TITLE . " - Facture d'inscription</title>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
					<meta content='width=device-width,initial-scale=1' />
					<style type='text/css'>
						/* Fonts and Content */
						body, td { font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; font-size:14px; color:#333; }
						body { background-color:#fff; margin:0; padding:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; }
					</style>
				</head>
				<body style='width:700px; margin:0px auto; padding:0px; -webkit-text-size-adjust:none;'>
					<table width='700px' cellpadding='0' cellspacing='0' border='0'><tbody>
						<tr><td width='700px' height='30px' bgcolor='#fff' valign='top'>
						</td></tr>
						<tr><td width='700px' bgcolor='#fff' valign='top'>
							<table width='700px' cellpadding='0' cellspacing='0' border='0'><tbody>
								<tr>
									<td width='120px' bgcolor='#fff' valign='top'>
										<img src='" . URL . "/view/img/logoEmail.jpg' alt='logo officiel des Snake Cheer All Star' />
									</td>
									<td width='10px' bgcolor='#fff' valign='top'>
									</td>
									<td width='370px' bgcolor='#fff' valign='top'>
										<b style='font-size:16px'>Snake Cheer All Star</b><br />
										Siret: 819 491 226 00026<br />
										19 bis Avenue de Buros<br />
										64000 Pau<br />
										" . EMAIL_CONTACT . "<br />
										" . URL . "
									</td>
									<td width='200px' bgcolor='#fff' valign='top' align='right'>
										<b style='font-size:16px'>Facturé à :</b><br />
										" . mb_strtoupper($tuteur->GetLastname()) . " " . $tuteur->GetFirstname() . "<br />
										" . $tuteur->GetEmail() . "<br />
										" . $tuteur->GetPhone() . "
									</td>
								</tr>
							</tbody></table>
						</td></tr>
						<tr><td width='700px' bgcolor='#fff'>
							<table width='700px' cellpadding='0' cellspacing='0' border='0'><tbody>
								<tr><td width='700px' bgcolor='#fff'>
									<p style='font-size:30px; border: 1px solid #000;' align='center'>
										<font color='#38761d'><b>FACTURE - " . $number . "</b></font>
									</p>
								</td></tr>
							</tbody></table>
						</td></tr>
						<tr><td width='700px' bgcolor='#fff'>
							<table width='700px' cellpadding='0' cellspacing='0' border='0'><tbody>
								<tr><td width='700px' bgcolor='#fff'>
									<p>
										<b>Date: </b> <i>" . ToolBox::FormatDate(null, false) . "</i>
									</p>
								</td></tr>
							</tbody></table>
						</td></tr>
						<tr><td width='700px' bgcolor='#fff'>
							<table width='700px' cellpadding='0' cellspacing='0' border='0' style='border: 1px solid #000;'>
								<thead style='border-bottom: 1px solid #000;'>
									<tr>
										<td width='100px' bgcolor='#b6d7a8' align='center'><b>Quantité</b></td>
										<td width='400px' bgcolor='#b6d7a8'><b>Description</b></td>
										<td width='100px' bgcolor='#b6d7a8' align='center'><b>P.U</b></td>
										<td width='100px' bgcolor='#b6d7a8' align='center'><b>Tot TTC</b></td>
									</tr>
								</thead>
								<tbody>";

									foreach($adherents as $adherent)
									{
										$html .= "<tr>
											<td width='100px' bgcolor='#fff' align='center'>1</td>
											<td width='400px' bgcolor='#fff' style='padding-top:5px; padding-bottom:5px;'>Inscription annnuelle pour la saison " . $adherent->GetSection()->GetSaison() . " (Section " . $adherent->GetSection()->GetName() . ") - " . $adherent->GetFirstname() . " " . $adherent->GetLastname() . "</td>
											<td width='100px' bgcolor='#fff' align='right'>" . $adherent->GetSection()->GetPriceCotisation() . " €</td>
											<td width='100px' bgcolor='#fff' align='right' style='padding:5px'>" . $adherent->GetSection()->GetPriceCotisation() . " €</td>
										</tr>";
									}
									
								$html .= "</tbody>
							</table>
						</td></tr>
						<tr><td width='700px' bgcolor='#fff'>
							<table width='700px' cellpadding='0' cellspacing='0' border='0'><tbody>
								<tr>
									<td width='700px' bgcolor='#fff'>
										<table width='700px' cellpadding='0' cellspacing='0' border='0'><tbody>
											<tr>
												<td width='600px' bgcolor='#fff' align='right'>SOUS-TOTAL</td>
												<td width='100px' bgcolor='#fff' align='right' style='padding:5px'>" . $payment->GetBasePrice() . " €</td>
											</tr>";

											$has_reduction = false;
											foreach($payment->GetReductions() as $reduction)
											{
												$has_reduction = true;

												if($reduction->GetType() == Reduction::$TYPE['Percentage'])
												{
													$html .= "<tr>
														<td width='600px' bgcolor='#fff' align='right'>" . $reduction->GetSujet() . "</td>
														<td width='100px' bgcolor='#fff' align='right' style='padding:5px'>- " . $reduction->GetValue() . " %</td>
													</tr>";
												}
												
												if($reduction->GetType() == Reduction::$TYPE['Amount'])
												{
													$html .= "<tr>
														<td width='600px' bgcolor='#fff' align='right'>" . $reduction->GetSujet() . "</td>
														<td width='100px' bgcolor='#fff' align='right' style='padding:5px'>- " . $reduction->GetValue() . " €</td>
													</tr>";
												}
											}
									
											if($has_reduction)
											{
												$html .= "<tr>
													<td width='600px' bgcolor='#fff' align='right'>SOUS-TOTAL</td>
													<td width='100px' bgcolor='#fff' align='right' style='padding:5px'>" . $payment->GetBasePriceWithReductions() . " €</td>
												</tr>";
											}
										
										$html .= "</tbody></table>
									</td>
								</tr>
							</tbody></table>
						</td></tr>";

						$has_uniforms = false;
						foreach($adherents as $adherent)
						{
							if(!$adherent->GetTenue())
							{
								$has_uniforms = true;
								break;
							}
						}

						if($has_uniforms)
						{
							$html .= "<tr><td width='700px' bgcolor='#fff'>
								<table width='700px' cellpadding='0' cellspacing='0' border='0' style='border: 1px solid #000;'>
									<thead style='border-bottom: 1px solid #000;'>
										<tr>
											<td width='100px' bgcolor='#b6d7a8' align='center'><b>Quantité</b></td>
											<td width='400px' bgcolor='#b6d7a8'><b>Description</b></td>
											<td width='100px' bgcolor='#b6d7a8' align='center'><b>P.U</b></td>
											<td width='100px' bgcolor='#b6d7a8' align='center'><b>Tot TTC</b></td>
										</tr>
									</thead>
									<tbody>";

										foreach($adherents as $adherent)
										{
											if(!$adherent->GetTenue())
											{
												$html .= "<tr>
													<td width='100px' bgcolor='#fff' align='center'>1</td>
													<td width='400px' bgcolor='#fff' style='padding-top:5px; padding-bottom:5px;'>Uniforme de cheerleading (Section " . $adherent->GetSection()->GetName() . ") - " . $adherent->GetFirstname() . " " . $adherent->GetLastname() . "</td>
													<td width='100px' bgcolor='#fff' align='right'>" . $adherent->GetSection()->GetPriceUniform() . " €</td>
													<td width='100px' bgcolor='#fff' align='right' style='padding:5px'>" . $adherent->GetSection()->GetPriceUniform() . " €</td>
												</tr>";
											}
										}
										
									$html .= "</tbody>
								</table>
							</td></tr>";
						}

						$html .= "<tr><td width='700px' bgcolor='#fff'>
							<table width='700px' cellpadding='0' cellspacing='0' border='0'><tbody>
								<tr>
									<td width='300px' bgcolor='#fff'>
										<table width='300px' cellpadding='0' cellspacing='0' border='0'><tbody>
											<tr><td width='300px' bgcolor='#fff'>";

												if($payment->IsDone())
													$html .= "<p style='font-size:20px' align='center'><font color='green'><b>Payée</b></font></p>";
												else
													$html .= "<p style='font-size:20px' align='center'><font color='red'><b>En attente de paiement</b></font></p>";
												
											$html .= "</td></tr>
										</tbody></table>
									</td>
									<td width='400px' bgcolor='#fff'>
										<table width='400px' cellpadding='0' cellspacing='0' border='0'><tbody>
											<tr>
												<td width='300px' bgcolor='#fff' align='right'><b>TOTAL</b></td>
												<td width='100px' bgcolor='#fff' align='right' style='padding:5px'><b>" . $payment->GetFinalAmount() . " €</b></td>
											</tr>
										</tbody></table>
									</td>
								</tr>
							</tbody></table>
						</td></tr>
						<tr><td width='700px' height='70px' bgcolor='#fff'>
						</td></tr>
						<tr><td width='700px' bgcolor='#fff'>
							<table width='700px' cellpadding='0' cellspacing='0' border='0'><tbody>
								<tr>
									<td width='350px' bgcolor='#fff' valign='top'>
										Chèques à l'ordre des <b>Snake Cheer All Star</b><br />
										En votre aimable réglement à réception
									</td>
									<td width='350px' bgcolor='#fff' valign='top' align='right'>
										Association exonérée des impôts commerciaux
									</td>
								</tr>
							</tbody></table>
						</td></tr>
					</tbody></table>
				</body>
			</html>
		";

		return $html;
	}
}
?>