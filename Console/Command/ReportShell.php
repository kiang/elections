<?php

class ReportShell extends AppShell {

    public $uses = array();

    public function main() {
        $this->getReports();
    }

    public function getReports() {
        $base = json_decode(file_get_contents('http://catding.twbbs.org/report/api/search.php?page=1'), true);
        $fh = fopen(__DIR__ . '/data/reports.csv', 'w');
        fputcsv($fh, array_keys($base['data'][0]));
        foreach ($base['data'] AS $item) {
            fputcsv($fh, $item);
        }
        for ($i = 1; $i <= $base['summary']['max_page']; $i++) {
            $page = json_decode(file_get_contents('http://catding.twbbs.org/report/api/search.php?page=' . $i), true);
            foreach ($page['data'] AS $item) {
                fputcsv($fh, $item);
            }
        }
    }

}
