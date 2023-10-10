<?php
// ================================
// ==== Controleur Inscription ====
// ================================

use Snake\Section;
use Snake\SnakeTools;

$sections = Section::getList(SnakeTools::getCurrentSaison());
$nbSections = count($sections);

$htmlHoraires = '';
$htmlPricesHeader = '';
$htmlPricesBody = '';

if ($nbSections === 0) {
	$htmlHoraires = <<<HTML
	<div class="col-sm-12">
		<p class="text-center">Aucune section n'est disponible pour le moment, celà sera disponible très rapidement.</p>
	</div>
	HTML;
} else {
	for ($i = 0; $i < $nbSections; $i++) {
		$section = $sections[$i];

		if ($i === $nbSections - 1) {
			// Pour la dernière section
			$ages = $section->getMaxYear() . ' ans et +';
		} else {
			// Pour les autres sections
			$ages = $section->getMaxYear() . ' à ' . ($sections[$i + 1]->getMaxYear() - 1);
		}

		$horaires = '';

		foreach ($section->getHoraires() as $horaire) {
			$horaires .= <<<HTML
				<span class="plage">{$horaire->getDay()->value} de {$horaire->getStartTime()->format('H\hi')} à {$horaire->getEndTime()->format('H\hi')} ({$horaire->getPlace()})</span>
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
			<td class="text-center">{$section->getCotisationPrice()} €</td>
			HTML;
	}
}
?>