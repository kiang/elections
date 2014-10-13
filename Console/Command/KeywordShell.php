<?php

class KeywordShell extends AppShell {

    public $uses = array('Keyword');

    public function main() {
        $this->dumpKeywords();
    }

    public function dumpKeywords() {
        $keywords = $this->Keyword->find('list', array(
            'fields' => array('Keyword.keyword', 'Keyword.id'),
        ));
        $targetFile = TMP . 'keywords.csv';
        $fh = fopen($targetFile, 'w');
        foreach($keywords AS $keyword => $keywordId) {
            fputcsv($fh, array($keywordId, $keyword));
        }
    }

    public function generateKeywords() {
        $keywords = $this->Keyword->find('list', array(
            'fields' => array('Keyword.keyword', 'Keyword.id'),
        ));
        $candidates = $this->Keyword->Candidate->find('list', array(
            'conditions' => array('Candidate.active_id IS NULL'),
            'fields' => array('Candidate.id', 'Candidate.name'),
        ));
        foreach ($candidates AS $candidateId => $candidateName) {
            if (!isset($keywords[$candidateName])) {
                $this->Keyword->create();
                if ($this->Keyword->save(array('Keyword' => array(
                                'keyword' => $candidateName,
                    )))) {
                    $keywords[$candidateName] = $this->Keyword->getInsertID();
                }
            }
            if ($this->Keyword->CandidatesKeyword->find('count', array(
                        'conditions' => array(
                            'Candidate_id' => $candidateId,
                            'Keyword_id' => $keywords[$candidateName],
                        ),
                    )) === 0) {
                $this->Keyword->CandidatesKeyword->create();
                $this->Keyword->CandidatesKeyword->save(array('CandidatesKeyword' => array(
                        'Candidate_id' => $candidateId,
                        'Keyword_id' => $keywords[$candidateName],
                )));
            }
        }
    }

}
