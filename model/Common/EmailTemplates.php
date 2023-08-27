<?php
namespace Common;

class EmailTemplates
{
	// Afficahge stendard des Email.
	static public function StandardHTML($sujet = "", $message = "") {
		return "
			<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='fr'>
				<head>
					<title>Elodie Esthetique 64 - " . $sujet . "</title>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
					<meta content='width=device-width,initial-scale=1' />
					<style type='text/css'>
						/* Fonts and Content */
						body, td { font-family: 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif; font-size:14px; color:#333; }
						body { background-color:#fff; margin:0; padding:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; }
					</style>
				</head>
				<body style='width:650px; margin:0px auto; padding:0px; -webkit-text-size-adjust:none;'>
					<table width='650px' cellpadding='0' cellspacing='0' border='0'>
						<tbody>
							<tr>
								<td width='650px' bgcolor='#fff' valign='top'>
									<p width='650px' height='59px' align='center' valign='middle'style='margin:0 5px 5px; font-size:25px;'>
										" . $sujet . "
									</p>
								</td>
							</tr>
							<tr>
								<td width='500px' bgcolor='#fff'>
									" . $message . "
								</td>
							</tr>
						</tbody>
					</table>
				</body>
			</html>
		";
	}

	// Afficahge stendard des Email.
	static public function StandardText($sujet = "", $message = "") {
		$message = str_replace(array("<br />", "<br>"), "\n", $message);
		return "Sujet: " . $sujet . "\n\nMessage:\n" . $message;
	}
}
?>