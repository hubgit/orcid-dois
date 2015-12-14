<?php

$dir = dirname($argv[1]);
$input = fopen($argv[1], 'r');
$output = fopen($dir . '/dois.json', 'w');

while (($file = fgets($input)) !== false) {
    $data = json_decode(file_get_contents(trim($file)), true);

    if (!isset($data['orcid-profile']['orcid-bio'])) continue;
    if (!isset($data['orcid-profile']['orcid-bio']['personal-details'])) continue;
    if (!isset($data['orcid-profile']['orcid-activities']['orcid-works']['orcid-work'])) continue;

    $profile = [
        'orcid' => $data['orcid-profile']['orcid-identifier']['path'],
        'name' => [
            $data['orcid-profile']['orcid-bio']['personal-details']['given-names']['value'],
            $data['orcid-profile']['orcid-bio']['personal-details']['family-name']['value']
        ],
        'dois' => [],
    ];

	$works = $data['orcid-profile']['orcid-activities']['orcid-works']['orcid-work'];
    foreach ($works as $work) {
        if (!is_array($work)) continue;
        if (!isset($work['work-external-identifiers'])) continue;

        foreach ($work['work-external-identifiers']['work-external-identifier'] as $identifier) {
            if ($identifier['work-external-identifier-type'] === 'DOI') {
                $profile['dois'][] = $identifier['work-external-identifier-id']['value'];
            }
        }
    }

    if ($profile['dois']) {
        fwrite($output, json_encode($profile) . "\n");
    }
}
