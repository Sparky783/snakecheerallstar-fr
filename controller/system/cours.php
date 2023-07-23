<?php
// ================================
// ==== Controleur Inscription ====
// ================================

use System\Session;
use Snake\Section;

$sections = Section::getList("2021-2022");
$nbSections = count($sections);

$htmlHoraires = '';
$htmlPricesHeader = '';
$htmlPricesBody = '';

if($nbSections === 0) {
	$htmlHoraires = <<<HTML
	<div class="col-sm-12">
		<p class="text-center">Aucune section n'est disponible pour le moment, celà sera disponible très rapidement.</p>
	</div>
	HTML;
} else {
	for($i = 0; $i < $nbSections; $i++) {
		$section = $sections[$i];

		if ($i === $nbSections - 1) {
			// Pour la dernière section
			$ages = $section->getMinAge() . ' ans et +';
		} else {
			// Pour les autres sections
			$ages = $section->getMinAge() . ' à ' . ($sections[$i + 1]->getMinAge() - 1) . ' ans';
		}

		$horaires = '';

		foreach ($section->getHoraires() as $horaire) {
			$horaires .= <<<HTML
				<span class="plage">{$horaire->getDay()} de {$horaire->getStartTime()->format('Hhi')} à {$horaire->getEndTime()->format('Hhi')} ({$horaire->getPlace()})</span>
				HTML;
		}

		$htmlHoraires .= <<<HTML
			<div class="col-sm-4">
				<div class="card border-snake">
					<div class="card-header bg-snake text-white text-center">
						{$section->getName()} ({$ages})
					</div>
					<div class="card-body" action="#" method="post">
						{$horaires}
					</div>
				</div>
			</div>
			HTML;

		$htmlPricesHeader .= <<<HTML
			<th class="text-center">{$section->getName()}<br />({$ages})</th>
			HTML;

		$htmlPricesBody .= <<<HTML
			<td class="text-center">{$section->getPriceCotisation()} €</td>
			HTML;
	}
}
?>