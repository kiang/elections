<?php

class ExportShell extends AppShell {

    public $uses = array('Election');

    public function main() {
        $this->facebook();
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
            if (!empty($candidate['Election'][0]['id'])) {
                $parents = $this->Election->getPath($candidate['Election'][0]['id'], array('id', 'name'));
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
                        'CandidatesElection.Election_id' => $eNode['Election']['id'],
                    ),
                    'fields' => array('Candidate.id', 'Candidate.name'),
                    'joins' => array(
                        array(
                            'table' => 'candidates_elections',
                            'alias' => 'CandidatesElection',
                            'type' => 'inner',
                            'conditions' => array(
                                'CandidatesElection.Candidate_id = Candidate.id',
                            ),
                        ),
                    )
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
