<?php

$dir = dirname($argv[1]);
$input = fopen($argv[1], 'r');
$output = fopen($dir . '/dois.csv', 'w');

while (($file = fgets($input)) !== false) {
	$data = json_decode(file_get_contents(trim($file)), true);

	$works = $data['orcid-profile']['orcid-activities']['orcid-works'];
	if (!$works) continue;

	$orcid = $data['orcid-profile']['orcid'];

	foreach ($works as $work) {
		$identifiers = $work['work-external-identifiers']['work-external-identifier'];
		if (!$identifiers) continue;

		foreach ($identifiers as $identifier) {
			if ($identifier['work-external-identifier-type'] === 'doi') {
				$doi = $identifier['work-external-identifier-id'];
				fputcsv($output, [$orcid, $doi]);
			}
		}
	}
}
