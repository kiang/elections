<?php

class ImportShell extends AppShell {

    public $areas = array(
        'code2id' => array(),
    );
    public $uses = array('Election');

    public function main() {
        $this->areas();
        $electionParent = $this->Election->field('id', array('name' => '2014'));
        if (empty($electionParent)) {
            $this->Election->create();
            $this->Election->save(array('Election' => array(
                    'name' => '2014',
            )));
            $electionParent = $this->Election->getInsertID();
        }
    }

    public function areas() {
        $areaParent = $this->Election->Area->find('first', array(
            'conditions' => array('name' => '2014'),
        ));
        if (empty($areaParent)) {
            $this->Election->Area->create();
            $this->Election->Area->save(array('Area' => array(
                    'name' => '2014',
                    'is_area' => '0',
            )));
            $areaParent = $this->Election->Area->read();
        }

        $this->areas['code2id'] = $this->Election->Area->find('list', array(
            'fields' => array('code', 'id'),
            'conditions' => array(
                'lft >' => $areaParent['Area']['lft'],
                'rght <' => $areaParent['Area']['rght'],
            ),
        ));

        $fh = fopen('/home/kiang/public_html/cunli/villages.csv', 'r');
        fgetcsv($fh, 2048);
        while ($line = fgetcsv($fh, 2048)) {
            $countyCode = substr($line[1], 0, -7);
            $townCode = substr($line[1], 0, -4);
            $cunliCode = $line[1];
            if (!isset($this->areas['code2id'][$countyCode])) {
                $this->Election->Area->create();
                $this->Election->Area->save(array('Area' => array(
                        'parent_id' => $areaParent['Area']['id'],
                        'name' => $line[4],
                        'is_area' => '1',
                        'ivid' => $line[6],
                        'code' => $countyCode,
                )));
                $this->areas['code2id'][$countyCode] = $this->Election->Area->getInsertID();
            }
            if (!isset($this->areas['code2id'][$townCode])) {
                $this->Election->Area->create();
                $this->Election->Area->save(array('Area' => array(
                        'parent_id' => $this->areas['code2id'][$countyCode],
                        'name' => $line[3],
                        'is_area' => '1',
                        'ivid' => $line[7],
                        'code' => $townCode,
                )));
                $this->areas['code2id'][$townCode] = $this->Election->Area->getInsertID();
            }
            if (!isset($this->areas['code2id'][$cunliCode])) {
                $this->Election->Area->create();
                $this->Election->Area->save(array('Area' => array(
                        'parent_id' => $this->areas['code2id'][$townCode],
                        'name' => $line[2],
                        'is_area' => '1',
                        'ivid' => $line[0],
                        'code' => $cunliCode,
                )));
                $this->areas['code2id'][$cunliCode] = $this->Election->Area->getInsertID();
            }
        }
    }

}
