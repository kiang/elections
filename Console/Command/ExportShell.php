<?php

class ExportShell extends AppShell {

    public $uses = array('Election');

    public function main() {
        $this->lawsuitCandidates();
    }

    public function lawsuitCandidates() {
        $tags = $this->Election->Candidate->Tag->find('all', array(
            'conditions' => array('Tag.name LIKE' => '當選無效之訴%'),
            'contain' => array(
                'Candidate' => array(
                    'fields' => array('id', 'name', 'no', 'party', 'election_id'),
                ),
            ),
            'order' => array(
                'Tag.name' => 'ASC',
            ),
        ));
        $elections = array();
        $fh = fopen(__DIR__ . '/data/2014_lawsuit_candidates.csv', 'w');
        fputcsv($fh, array(
            '標籤',
            '政黨',
            '編號',
            '候選人',
            '選區',
        ));
        foreach ($tags AS $tag) {
            $lines = array();
            foreach ($tag['Candidate'] AS $candidate) {
                $electionId = $candidate['Candidate']['election_id'];
                if (!isset($elections[$electionId])) {
                    $path = $this->Election->getPath($electionId, array('name'));
                    unset($path[0]);
                    unset($path[2]);

                    $elections[$electionId] = implode(' > ', Set::extract('{n}.Election.name', $path));
                    if (false !== strpos($elections[$electionId], '> 第')) {
                        $areas = $this->Election->Area->find('list', array(
                            'joins' => array(
                                array(
                                    'table' => 'areas_elections',
                                    'alias' => 'AreasElection',
                                    'type' => 'inner',
                                    'conditions' => array(
                                        'AreasElection.Area_id = Area.id',
                                    ),
                                ),
                            ),
                            'conditions' => array(
                                'AreasElection.Election_id' => $electionId,
                            ),
                        ));
                        $elections[$electionId] .= '(' . implode('/', $areas) . ')';
                    }
                }
                switch (mb_substr($elections[$electionId], 0, 4, 'utf8')) {
                    case '直轄市議':
                        $key = 1;
                        break;
                    case '縣市議員':
                        $key = 2;
                        break;
                    case '直轄市山':
                        $key = 3;
                        break;
                    case '鄉鎮市長':
                        $key = 4;
                        break;
                    case '鄉鎮市民':
                        $key = 5;
                        break;
                    case '村里長 ':
                        $key = 6;
                        break;
                }
                if (!isset($lines[$key])) {
                    $lines[$key] = array();
                }
                $lines[$key][] = array(
                    $tag['Tag']['name'],
                    $candidate['party'],
                    $candidate['no'],
                    $candidate['name'],
                    $elections[$electionId],
                );
            }
            ksort($lines);
            foreach ($lines AS $c) {
                foreach ($c AS $line) {
                    fputcsv($fh, $line);
                }
            }
        }
        fclose($fh);
    }

    public function facebook() {
        $candidates = $this->Election->Candidate->find('all', array(
            'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.links'),
            'conditions' => array(
                'Candidate.links LIKE' => '%facebook.com%',
                'Candidate.active_id IS NULL',
            ),
            'contain' => array('Election'),
        ));
        $fh = fopen(__DIR__ . '/data/facebook_candidates.csv', 'w');
        fputcsv($fh, array('選舉類型', '選區', '姓名', '連結'));
        foreach ($candidates AS $candidate) {
            if (!empty($candidate['Election']['id'])) {
                $parents = $this->Election->getPath($candidate['Election']['id'], array('id', 'name'));
                $links = array();
                $candidate['Candidate']['links'] = str_replace(array('\\n', '&amp;'), array("\n", '&'), $candidate['Candidate']['links']);
                $lines = explode("\n", $candidate['Candidate']['links']);
                foreach ($lines AS $line) {
                    $pos = strpos($line, 'facebook.com');
                    if (false !== $pos) {
                        $links[] = urldecode(trim('http://www.' . substr($line, $pos)));
                    }
                }
                $electionType = $parents[1];
                unset($parents[0]);
                unset($parents[1]);
                fputcsv($fh, array(
                    $electionType['Election']['name'],
                    implode(' > ', Set::extract($parents, '{n}.Election.name')),
                    $candidate['Candidate']['name'],
                    implode(' | ', $links)));
            }
        }
    }

    public function candidates() {
        $rootNode = $this->Election->find('first', array(
            'conditions' => array('Election.name' => '2014'),
        ));
        $eTypes = $this->Election->find('all', array(
            'fields' => array('id', 'name', 'lft', 'rght'),
            'conditions' => array('Election.parent_id' => $rootNode['Election']['id']),
        ));
        $fh = fopen(__DIR__ . '/data/db_dump.csv', 'w');
        foreach ($eTypes AS $eType) {
            $eNodes = $this->Election->find('all', array(
                'fields' => array('id', 'name'),
                'conditions' => array(
                    'Election.rght - Election.lft = 1',
                    'Election.rght <' => $eType['Election']['rght'],
                    'Election.lft >' => $eType['Election']['lft'],
                ),
            ));
            foreach ($eNodes AS $eNode) {
                $parents = $this->Election->getPath($eNode['Election']['id'], array('id', 'name'));
                unset($parents[0]);
                unset($parents[1]);
                $eNode['Election']['name'] = implode(' > ', Set::extract($parents, '{n}.Election.name'));
                $candidates = $this->Election->Candidate->find('all', array(
                    'conditions' => array(
                        'Candidate.active_id IS NULL',
                        'Candidate.election_id' => $eNode['Election']['id'],
                    ),
                    'fields' => array('Candidate.id', 'Candidate.name'),
                ));
                foreach ($candidates AS $candidate) {
                    fputcsv($fh, array(
                        $eType['Election']['name'],
                        $eNode['Election']['name'],
                        $candidate['Candidate']['name'],
                        'http://k.olc.tw/elections/candidates/view/' . $candidate['Candidate']['id'],
                    ));
                }
            }
        }
        fclose($fh);
    }

}
