<?php

class KeywordShell extends AppShell {

    public $uses = array('Keyword');

    public function main() {
        $this->importKeywords();
    }

    public function importKeywords() {
        $db = ConnectionManager::getDataSource('default');
        $mysqli = new mysqli($db->config['host'], $db->config['login'], $db->config['password'], $db->config['database']);
        $sql = array(
            'links',
            'links_keywords',
        );
        foreach (glob('/home/kiang/public_html/news/cache/output/*.json') AS $jsonFile) {
            $json = json_decode(file_get_contents($jsonFile), true);
            $newLinkId = String::uuid();
            $json['title'] = $mysqli->real_escape_string(trim($json['title']));
            $json['url'] = $mysqli->real_escape_string($json['url']);
            $json['created'] = date('Y-m-d H:i:s', $json['created_at']);
            $sql['links'][] = "('{$newLinkId}', '{$json['title']}', '{$json['url']}', '{$json['created']}')";
            foreach ($json['keywords'] AS $keywordId => $summary) {
                $lkId = String::uuid();
                $summary = $mysqli->real_escape_string(trim($summary));
                $sql['links_keywords'][] = "('{$lkId}', '{$newLinkId}', '{$keywordId}', '{$summary}')";
            }
            unlink($jsonFile);
        }
        if (!empty($sql['links'])) {
            $linksSql = 'INSERT INTO links VALUES ' . implode(',', $sql['links']) . ";\n";
            $lkSql = 'INSERT INTO links_keywords VALUES ' . implode(',', $sql['links_keywords']) . ';';
            file_put_contents(TMP . 'keywords.sql', $linksSql . $lkSql);
        }
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
