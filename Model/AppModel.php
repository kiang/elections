<?php

class AppModel extends Model
{
    public $actsAs = array('Containable');
    public $recursive = -1;

    public function checkUnique($data)
    {
        foreach ($data AS $key => $value) {
            if (empty($value)) {
                return false;
            }
            if ($this->id) {
                return!$this->hasAny(array(
                    'id !=' => $this->id, $key => $value,
                ));
            } else {
                return!$this->hasAny(array($key => $value));
            }
        }
    }

}
