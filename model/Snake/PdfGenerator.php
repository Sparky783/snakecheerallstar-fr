<?php
namespace Snake;

use FPDF;

require_once(ABSPATH . 'model/FPDF/FPDF.php');

/**
 * Représente une section (équipe) du club.
 */
class PdfGenerator
{
	/**
	 * Generate a FFFA licence document with member information.
	 * 
	 * @param Adherent $adherent Adherent associated to this licence.
	 * @param Tuteur $tuteur Tuteur associated to the Adherent. Adherent himself if possible.
	 * @param string $outputPath Path of the generated PDF file.
	 * @return void
	 */
	public static function generateLicenceFffa(Adherent $adherent, Tuteur $tuteur, string $outputPath): void
	{
		$pdf = new FPDF();	
		$pdf->AddPage();
		$pdf->Image("content/pdf_documents/licence_FFFA/licence_FFFA.jpg", 0, 0, 210, 297);
		
        $pdf->SetFont('Courier', '', 8);
        $pdf->SetTextColor(0, 0, 0);

        // Write club name
		$pdf->SetXY(12.5, 47.4);
        $pdf->Cell(0, 10, 'Snake Cheer All Star');
		
        // Write Lastname
		$pdf->SetXY(27, 57);
        $pdf->Cell(0, 10, utf8_decode($adherent->getLastname()));

        // Write Firstname
		$pdf->SetXY(16, 65.4);
        $pdf->Cell(0, 10, utf8_decode($adherent->getFirstname()));

		// Check Sex
		if ($adherent->getSex()) {
			$pdf->SetXY(47.3, 69.1);
		} else {
			$pdf->SetXY(64.8, 69,1);
		}
		
        $pdf->Cell(0, 10, 'X');

        // Write Adddress
		$pdf->SetXY(16.5, 73.6);
        $pdf->Cell(0, 10, utf8_decode($adherent->getAddressStreet()));

		$pdf->SetXY(20.1, 82.1);
        $pdf->Cell(0, 10, utf8_decode($adherent->getAddressCode()));

		$pdf->SetXY(81, 82.1);
        $pdf->Cell(0, 10, utf8_decode($adherent->getAddressCity()));

        // Write Phone
		$pdf->SetXY(19, 86.4);
        $pdf->Cell(0, 10, $tuteur->getPhone());

        // Write E-mail
		$pdf->SetXY(16, 90.3);
        $pdf->Cell(0, 10, $tuteur->getEmail());

        // Write Birthday
		$pdf->SetXY(27.2, 94.8);
        $pdf->Cell(0, 10, $adherent->getBirthday()->format('d/m/Y'));

		// Check Cheerleading
		$pdf->SetXY(59, 225.1);
        $pdf->Cell(0, 10, 'X');

		// Save in file
		$pdf->Output($outputPath, 'F');
	}

	/**
	 * Generate a Sportmut document with member information.
	 * 
	 * @param Adherent $adherent Adherent associated to this licence.
	 * @param Tuteur $tuteur Tuteur associated to the Adherent. Adherent himself if possible.
	 * @param string $outputPath Path of the generated PDF file.
	 * @return void
	 */
	public static function generateSportmut(Adherent $adherent, Tuteur $tuteur, string $outputPath): void
	{
		$pdf = new FPDF();	
		$pdf->AddPage();
		$pdf->Image("content/pdf_documents/sportmut/sportmut-1.jpg", 0, 0, 210, 297);
		
        $pdf->SetFont('Courier', '', 10);
        $pdf->SetTextColor(0, 0, 0);

		// Check Sex
		if ($adherent->getSex()) {
			$pdf->SetXY(17.4, 56.2);
		} else {
			$pdf->SetXY(46.5, 56.2);
		}
		
        $pdf->Cell(0, 10, 'X');
		
        // Write Lastname
		$pdf->SetXY(62, 55.4);
        $pdf->Cell(0, 10, utf8_decode($adherent->getLastname()));

        // Write Firstname
		$pdf->SetXY(134, 55.4);
        $pdf->Cell(0, 10, utf8_decode($adherent->getFirstname()));

        // Write Birthday
		$pdf->SetXY(140, 61.1);
        $pdf->Cell(0, 10, $adherent->getBirthday()->format('d/m/Y'));

        // Write Adddress
		$pdf->SetXY(26, 67.6);
        $pdf->Cell(0, 10, utf8_decode($adherent->getAddressStreet()));

		$pdf->SetXY(31, 73.3);
        $pdf->Cell(0, 10, utf8_decode($adherent->getAddressCode()));

		$pdf->SetXY(93.2, 73.3);
        $pdf->Cell(0, 10, utf8_decode($adherent->getAddressCity()));

        // Write Phone
		$pdf->SetXY(92, 85.6);
        $pdf->Cell(0, 10, $tuteur->getPhone());

        // Write E-mail
		$pdf->SetXY(22, 91.5);
        $pdf->Cell(0, 10, $tuteur->getEmail());

        // Write club name
		$pdf->SetXY(140, 97.8);
        $pdf->Cell(0, 10, 'Snake Cheer All Star');

		// Add page 2
		$pdf->AddPage();
		$pdf->Image("content/pdf_documents/sportmut/sportmut-2.jpg", 0, 0, 210, 297);

		// Save in file
		$pdf->Output($outputPath, 'F');
	}
	
	/**
	 * Generate a Afld document with member information.
	 * 
	 * @param Adherent $adherent Adherent associated to this licence.
	 * @param Tuteur $tuteur Tuteur associated to the Adherent. Adherent himself if possible.
	 * @param string $outputPath Path of the generated PDF file.
	 * @return void
	 */
	public static function generateAfld(Adherent $adherent, Tuteur $tuteur, string $outputPath): void
	{
		$pdf = new FPDF();	
		$pdf->AddPage();
		$pdf->Image("content/pdf_documents/afld/afld-1.jpg", 0, 0, 210, 297);
		
        $pdf->SetFont('Courier', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        // Write Lastname
		$pdf->SetXY(36.5, 57.5);
        $pdf->Cell(0, 10, utf8_decode($adherent->getLastname()));

        // Write Firstname
		$pdf->SetXY(114, 57.5);
        $pdf->Cell(0, 10, utf8_decode($adherent->getFirstname()));

		// Check Sex
		if ($adherent->getSex()) {
			$pdf->SetXY(37.8, 80.5);
		} else {
			$pdf->SetXY(64, 80.5);
		}
		
        $pdf->Cell(0, 10, 'X');

        // Write Adddress
		$pdf->SetXY(43, 89);
        $pdf->Cell(0, 10, utf8_decode($adherent->getAddressStreet()));

		$pdf->SetXY(48.3, 97.1);
        $pdf->Cell(0, 10, utf8_decode($adherent->getAddressCode()));

		$pdf->SetXY(86, 97.1);
        $pdf->Cell(0, 10, utf8_decode($adherent->getAddressCity()));

		$pdf->SetXY(154, 97.1);
        $pdf->Cell(0, 10, 'France');

        // Write Phone
		$pdf->SetXY(34.5, 105.2);
        $pdf->Cell(0, 10, $tuteur->getPhone());

        // Write E-mail
		$pdf->SetXY(89.5, 105.2);
        $pdf->Cell(0, 10, $tuteur->getEmail());

        // Write Birthday
		$pdf->SetXY(154, 80.1);
        $pdf->Cell(0, 10, $adherent->getBirthday()->format('d m Y'));
		
        // Write Sport information
		$pdf->SetXY(47, 111.7);
        $pdf->Cell(0, 10, utf8_decode('Fédération Française de'));

		
		$pdf->SetXY(47, 115.1);
        $pdf->Cell(0, 10, utf8_decode('Football Américain'));

		$pdf->SetXY(38, 121.5);
        $pdf->Cell(0, 10, 'Cheerleading');

		// Add pages
		$pdf->AddPage();
		$pdf->Image("content/pdf_documents/afld/afld-2.jpg", 0, 0, 210, 297);

		$pdf->AddPage();
		$pdf->Image("content/pdf_documents/afld/afld-3.jpg", 0, 0, 210, 297);
		
		$pdf->AddPage();
		$pdf->Image("content/pdf_documents/afld/afld-4.jpg", 0, 0, 210, 297);

		$pdf->AddPage();
		$pdf->Image("content/pdf_documents/afld/afld-5.jpg", 0, 0, 210, 297);

		// Save in file
		$pdf->Output($outputPath, 'F');
	}
	
	/**
	 * Generate a parental autorization document with member information.
	 * 
	 * @param Adherent $adherent Adherent associated to this licence.
	 * @param Tuteur $tuteur Tuteur associated to the Adherent. Adherent himself if possible.
	 * @param string $outputPath Path of the generated PDF file.
	 * @return void
	 */
	public static function generateParentalAutorization(Adherent $adherent, Tuteur $tuteur, string $outputPath): void
	{
		$pdf = new FPDF();	
		$pdf->AddPage();
		$pdf->Image("content/pdf_documents/autorisation_parentale/autorisation_parentale.jpg", 0, 0, 210, 297);
		
        $pdf->SetFont('Courier', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        // Write Lastname Firstname
		$pdf->SetXY(90, 49);
        $pdf->Cell(0, 10, utf8_decode("{$adherent->getFirstname()} {$adherent->getLastname()}"));

        // Write Birthday
		$pdf->SetXY(38, 57.8);
        $pdf->Cell(0, 10, $adherent->getBirthday()->format('d   m   Y'));

        // Write sport
		$pdf->SetXY(65, 66.5);
        $pdf->Cell(0, 10, 'Cheerleading');
		
        // Write club name
		$pdf->SetXY(36, 84.5);
        $pdf->Cell(0, 10, 'Snake Cheer All Star');


		// == Duplicata ==
		$offset = 93.5;

		// Write Lastname Firstname
		$pdf->SetXY(90, $offset + 49);
        $pdf->Cell(0, 10, utf8_decode("{$adherent->getFirstname()} {$adherent->getLastname()}"));

        // Write Birthday
		$pdf->SetXY(38, $offset + 57.8);
        $pdf->Cell(0, 10, $adherent->getBirthday()->format('d   m   Y'));

        // Write sport
		$pdf->SetXY(65, $offset + 66.5);
        $pdf->Cell(0, 10, 'Cheerleading');
		
        // Write club name
		$pdf->SetXY(140, $offset + 84.5);
        $pdf->Cell(0, 10, 'Snake Cheer All Star');

		// Save in file
		$pdf->Output($outputPath, 'F');
	}
}