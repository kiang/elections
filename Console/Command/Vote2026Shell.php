<?php

class Vote2026Shell extends AppShell {

    public $uses = array('Election', 'Area');

    private $villageCandidateElectionId = '8e52f4d0-f3cc-4c0d-bbaf-0ee754e4c8ed';

    private function sanitizeUtf8mb3($str) {
        return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '?', $str);
    }

    public function main() {
        $this->importCunliCandidates();
    }

    public function importCunliCandidates() {
        $csvFile = __DIR__ . '/data/vote2026_cunli_candidates.csv';
        if (!file_exists($csvFile)) {
            $this->out('CSV file not found: ' . $csvFile);
            return;
        }

        $rootElection = $this->Election->find('first', array(
            'conditions' => array('Election.id' => $this->villageCandidateElectionId),
        ));
        if (empty($rootElection)) {
            $this->out('Root election not found');
            return;
        }

        $this->out('Building election tree index...');
        $cityElections = $this->Election->find('all', array(
            'conditions' => array('Election.parent_id' => $this->villageCandidateElectionId),
            'fields' => array('id', 'name'),
        ));
        $cityMap = array();
        foreach ($cityElections as $city) {
            $cityMap[$city['Election']['name']] = $city['Election']['id'];
        }

        $districtMap = array();
        foreach ($cityMap as $cityName => $cityId) {
            $districts = $this->Election->find('all', array(
                'conditions' => array('Election.parent_id' => $cityId),
                'fields' => array('id', 'name'),
            ));
            foreach ($districts as $d) {
                $districtMap[$cityName . $d['Election']['name']] = $d['Election']['id'];
            }
        }

        $cunliMap = array();
        foreach ($districtMap as $key => $districtId) {
            $cunlis = $this->Election->find('all', array(
                'conditions' => array('Election.parent_id' => $districtId),
                'fields' => array('id', 'name'),
            ));
            foreach ($cunlis as $c) {
                $cunliMap[$key . $c['Election']['name']] = $c['Election']['id'];
            }
        }
        $this->out('Found ' . count($cunliMap) . ' existing cunli elections');

        $areaIndex = $this->buildAreaIndex();

        $existingCandidates = array();

        $fh = fopen($csvFile, 'r');
        $header = fgetcsv($fh, 2048);
        $counter = 0;
        $created = 0;
        $skipped = 0;
        $newElections = 0;
        $newAreas = 0;

        while ($line = fgetcsv($fh, 2048)) {
            if (empty($line[0])) {
                continue;
            }
            $counter++;
            if ($counter % 500 === 0) {
                $this->out("Processing {$counter}...");
            }

            $parsed = $this->parseArea($line[0]);
            if (empty($parsed)) {
                $this->out('Cannot parse area: ' . $line[0]);
                continue;
            }

            $cityName = $parsed['city'];
            $districtName = $this->sanitizeUtf8mb3($parsed['district']);
            $cunliName = $this->sanitizeUtf8mb3($parsed['cunli']);
            $fullKey = $cityName . $districtName . $cunliName;

            if (!isset($cityMap[$cityName])) {
                $this->out('City not found in election tree: ' . $cityName);
                continue;
            }

            $districtKey = $cityName . $districtName;
            if (!isset($districtMap[$districtKey])) {
                $this->out('District not found: ' . $districtKey . ', creating...');
                $this->Election->create();
                $this->Election->save(array('Election' => array(
                    'parent_id' => $cityMap[$cityName],
                    'name' => $districtName,
                    'population' => 0,
                    'population_electors' => 0,
                    'quota' => 0,
                    'quota_women' => 0,
                )));
                $districtMap[$districtKey] = $this->Election->getInsertID();
                $newElections++;
            }

            if (!isset($cunliMap[$fullKey])) {
                $this->Election->create();
                $this->Election->save(array('Election' => array(
                    'parent_id' => $districtMap[$districtKey],
                    'name' => $cunliName,
                    'population' => 0,
                    'population_electors' => 0,
                    'quota' => 1,
                    'quota_women' => 0,
                )));
                $cunliMap[$fullKey] = $this->Election->getInsertID();
                $newElections++;
            }

            $electionId = $cunliMap[$fullKey];

            $this->ensureAreaLink($electionId, $cityName, $districtName, $cunliName, $areaIndex, $newAreas);

            if (!isset($existingCandidates[$electionId])) {
                $existingCandidates[$electionId] = $this->Election->Candidate->find('list', array(
                    'conditions' => array(
                        'Candidate.active_id IS NULL',
                        'Candidate.election_id' => $electionId,
                    ),
                    'fields' => array('Candidate.name', 'Candidate.id'),
                ));
            }

            $candidateName = $this->sanitizeUtf8mb3(trim($line[2]));
            $party = trim($line[3]);
            if ($party === '無') {
                $party = '';
            }

            if (isset($existingCandidates[$electionId][$candidateName])) {
                $this->Election->Candidate->id = $existingCandidates[$electionId][$candidateName];
                $this->Election->Candidate->save(array('Candidate' => array(
                    'party' => $party,
                    'stage' => '1',
                    'is_reviewed' => '1',
                )));
                $skipped++;
            } else {
                $this->Election->Candidate->create();
                $this->Election->Candidate->save(array('Candidate' => array(
                    'election_id' => $electionId,
                    'name' => $candidateName,
                    'party' => $party,
                    'stage' => '1',
                    'is_reviewed' => '1',
                )));
                $existingCandidates[$electionId][$candidateName] = $this->Election->Candidate->getInsertID();
                $created++;
            }
        }
        fclose($fh);

        $this->out('');
        $this->out('Import complete:');
        $this->out("  Total rows: {$counter}");
        $this->out("  Candidates created: {$created}");
        $this->out("  Candidates updated: {$skipped}");
        $this->out("  New elections created: {$newElections}");
        $this->out("  New areas created: {$newAreas}");

        if ($newElections > 0) {
            $this->out('Recovering election tree...');
            $this->Election->recover();
            $this->out('Election tree recovered.');
        }
    }

    private function parseArea($raw) {
        $raw = trim($raw);
        $cityPatterns = array(
            '臺北市', '新北市', '桃園市', '臺中市', '臺南市', '高雄市',
            '基隆市', '新竹市', '嘉義市',
            '新竹縣', '苗栗縣', '彰化縣', '南投縣', '雲林縣', '嘉義縣',
            '屏東縣', '宜蘭縣', '花蓮縣', '臺東縣', '澎湖縣', '金門縣', '連江縣',
        );

        foreach ($cityPatterns as $city) {
            $cityLen = mb_strlen($city, 'UTF-8');
            if (mb_substr($raw, 0, $cityLen, 'UTF-8') === $city) {
                $rest = mb_substr($raw, $cityLen, null, 'UTF-8');
                if (preg_match('/^(.+?[區鄉鎮市])(.+[里村])$/u', $rest, $m)) {
                    return array(
                        'city' => $city,
                        'district' => $m[1],
                        'cunli' => $m[2],
                    );
                }
                return null;
            }
        }
        return null;
    }

    private function buildAreaIndex() {
        $this->out('Building area index...');
        $index = array();

        $topAreas = $this->Area->find('all', array(
            'conditions' => array('Area.parent_id IS NULL'),
            'fields' => array('id', 'name'),
        ));

        foreach ($topAreas as $top) {
            $cityAreas = $this->Area->find('all', array(
                'conditions' => array('Area.parent_id' => $top['Area']['id']),
                'fields' => array('id', 'name', 'parent_id'),
            ));
            foreach ($cityAreas as $city) {
                $districtAreas = $this->Area->find('all', array(
                    'conditions' => array('Area.parent_id' => $city['Area']['id']),
                    'fields' => array('id', 'name', 'parent_id'),
                ));
                foreach ($districtAreas as $district) {
                    $distKey = $city['Area']['name'] . $district['Area']['name'];
                    $index[$distKey] = array(
                        'city_id' => $city['Area']['id'],
                        'district_id' => $district['Area']['id'],
                        'cunlis' => array(),
                    );
                    $cunliAreas = $this->Area->find('all', array(
                        'conditions' => array('Area.parent_id' => $district['Area']['id']),
                        'fields' => array('id', 'name'),
                    ));
                    foreach ($cunliAreas as $cunli) {
                        $index[$distKey]['cunlis'][$cunli['Area']['name']] = $cunli['Area']['id'];
                    }
                }
            }
        }
        $this->out('Area index built with ' . count($index) . ' districts');
        return $index;
    }

    private function ensureAreaLink($electionId, $cityName, $districtName, $cunliName, &$areaIndex, &$newAreas) {
        $distKey = $cityName . $districtName;

        $existing = $this->Election->AreasElection->find('first', array(
            'conditions' => array('AreasElection.Election_id' => $electionId),
        ));
        if (!empty($existing)) {
            return;
        }

        if (!isset($areaIndex[$distKey])) {
            return;
        }

        $districtInfo = $areaIndex[$distKey];

        if (isset($districtInfo['cunlis'][$cunliName])) {
            $areaId = $districtInfo['cunlis'][$cunliName];
        } else {
            $this->Area->create();
            $this->Area->save(array('Area' => array(
                'parent_id' => $districtInfo['district_id'],
                'name' => $cunliName,
                'population' => 0,
                'population_electors' => 0,
                'keywords' => '',
            )));
            $areaId = $this->Area->getInsertID();
            $areaIndex[$distKey]['cunlis'][$cunliName] = $areaId;
            $newAreas++;
        }

        $this->Election->AreasElection->create();
        $this->Election->AreasElection->save(array('AreasElection' => array(
            'Election_id' => $electionId,
            'Area_id' => $areaId,
        )));
    }

}
