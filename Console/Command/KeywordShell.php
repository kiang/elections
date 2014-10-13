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
        $lineCount = 0;
        $fileNumber = 1;
        $fh = fopen(TMP . 'keywords_' . str_pad($fileNumber, 4, '0', STR_PAD_LEFT) . '.csv', 'w');
        foreach ($keywords AS $keyword => $keywordId) {
            fputcsv($fh, array($keywordId, $keyword));
            if (++$lineCount > 100) {
                fclose($fh);
                ++$fileNumber;
                $fh = fopen(TMP . 'keywords_' . str_pad($fileNumber, 4, '0', STR_PAD_LEFT) . '.csv', 'w');
                $lineCount = 0;
            }
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
