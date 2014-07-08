<?php
/**
 * This file is a part of UploadPack - a set of classes that makes file uploads in CakePHP as easy as possible.
 *
 * UploadBehavior
 *
 * UploadBehavior does all the job of saving files to disk while saving records to database. For more info read UploadPack documentation.
 *
 * @author Michał Szajbe (michal.szajbe@gmail.com) and netguru.pl
 * @link http://github.com/netguru/uploadpack
 * @version 0.1
 */
App::import('Core', 'String');
class UploadBehavior extends ModelBehavior
{
    public static $__settings = array();

    public $toWrite = array();

    public $toDelete = array();

    public function setup(&$model, $settings = array())
    {
        $defaults = array(
            'path' => ':webroot/upload/:model/:id/:basename_:style.:extension',
            'styles' => array()
        );

        foreach ($settings as $field => $array) {
            self::$__settings[$model->name][$field] = array_merge($defaults, $array);
        }
    }

    public function beforeSave(&$model)
    {
        $this->_reset();
        foreach (self::$__settings[$model->name] as $field => $settings) {
            if (!empty($model->data[$model->name][$field]) && is_array($model->data[$model->name][$field]) && is_uploaded_file($model->data[$model->name][$field]['tmp_name'])) {
                $pathInfo = pathinfo($model->data[$model->name][$field]['name']);
                $model->data[$model->name][$field]['name'] = String::uuid() . '.' . $pathInfo['extension'];
                if (!empty($model->id)) {
                    $this->_prepareToDeleteFiles($model, $field, true);
                }
                $this->_prepareToWriteFiles($model, $field);
                unset($model->data[$model->name][$field]);
                $model->data[$model->name][$field] = $this->toWrite[$field]['name'];
            } else {
                unset($model->data[$model->name][$field]);
            }
        }

        return true;
    }

    public function afterSave(&$model, $create)
    {
        if (!$create) {
            $this->_deleteFiles($model);
        }
        $this->_writeFiles($model);
    }

    public function beforeDelete(&$model)
    {
        $this->_reset();
        $this->_prepareToDeleteFiles($model);

        return true;
    }

    public function afterDelete(&$model)
    {
        $this->_deleteFiles($model);
    }

    public function _reset()
    {
        $this->_toWrite = null;
        $this->_toDelete = null;
    }

    public function _prepareToWriteFiles(&$model, $field)
    {
        $this->toWrite[$field] = $model->data[$model->name][$field];
    }

    public function _writeFiles(&$model)
    {
        if (!empty($this->toWrite)) {
            foreach ($this->toWrite as $field => $toWrite) {
                $settings = $this->_interpolate($model, $field, $toWrite['name'], 'original');
                $destDir = dirname($settings['path']);
                if (!file_exists($destDir)) {
                    @mkdir($destDir, 0777, true);
                    @chmod($destDir, 0777);
                }
                if (is_dir($destDir) && is_writable($destDir)) {
                    if (@move_uploaded_file($toWrite['tmp_name'], $settings['path'])) {
                        foreach ($settings['styles'] as $style => $geometry) {
                            $newSettings = $this->_interpolate($model, $field, $toWrite['name'], $style);
                            $this->_resize($settings['path'], $newSettings['path'], $geometry);
                        }
                    }
                }
            }
        }
    }

    public function _prepareToDeleteFiles(&$model, $field = null, $forceRead = false)
    {
        $needToRead = true;
        if ($field === null) {
            $fields = array_keys(self::$__settings[$model->name]);
        } else {
            $fields = array($field);
        }

        if (!$forceRead && !empty($model->data[$model->alias])) {
            $needToRead = false;
            foreach ($fields as $field) {
                if (!array_key_exists($field, $model->data[$model->alias])) {
                    $needToRead = true;
                    break;
                }
            }
        }
        if ($needToRead) {
            $data = $model->find('first', array('conditions' => array($model->alias.'.'.$model->primaryKey => $model->id), 'fields' => $fields));
        } else {
            $data = $model->data;
        }
        if (is_array($this->toDelete)) {
            $this->toDelete = array_merge($this->toDelete, $data[$model->alias]);
        } else {
            $this->toDelete = $data[$model->alias];
        }
        $this->toDelete['id'] = $model->id;
    }

    public function _deleteFiles(&$model)
    {
        foreach (self::$__settings[$model->name] as $field => $settings) {
            if (!empty($this->toDelete[$field])) {
                $styles = array_keys($settings['styles']);
                $styles[] = 'original';
                foreach ($styles as $style) {
                    $settings = $this->_interpolate($model, $field, $this->toDelete[$field], $style);
                    if (file_exists($settings['path'])) {
                        @unlink($settings['path']);
                    }
                }
            }
        }
    }

    public function _interpolate(&$model, $field, $filename, $style)
    {
        return self::interpolate($model->name, $model->id, $field, $filename, $style);
    }

    public static function interpolate($modelName, $modelId, $field, $filename, $style, $defaults = array())
    {
        $pathinfo = pathinfo($filename);
        $interpolations = array_merge(array(
            'webroot' => preg_replace('/\/$/', '', WWW_ROOT),
            'model' => Inflector::tableize($modelName),
            'basename' => !empty($filename) ? $pathinfo['filename'] : null,
            'extension' => !empty($filename) ? $pathinfo['extension'] : null,
            'id' => $modelId,
            'style' => $style
        ), $defaults);
        $settings = self::$__settings[$modelName][$field];
        $keys = array('path', 'url', 'default_url');
        foreach ($interpolations as $k => $v) {
            foreach ($keys as $key) {
                if (isset($settings[$key])) {
                    $settings[$key] = preg_replace('/\/{2,}/', '/', str_replace(":$k", $v, $settings[$key]));
                }
            }
        }

        return $settings;
    }

    public function _resize($srcFile, $destFile, $geometry)
    {
        copy($srcFile, $destFile);
        $pathinfo = pathinfo($srcFile);
        $src = null;
        $createHandler = null;
        $outputHandler = null;
        switch (low($pathinfo['extension'])) {
            case 'gif':
                $createHandler = 'imagecreatefromgif';
                $outputHandler = 'imagegif';
                break;
            case 'jpg':
            case 'jpeg':
                $createHandler = 'imagecreatefromjpeg';
                $outputHandler = 'imagejpeg';
                break;
            case 'png':
                $createHandler = 'imagecreatefrompng';
                $outputHandler = 'imagepng';
                break;
            default:
                return false;
        }
        if ($src = $createHandler($destFile)) {
            $srcW = imagesx($src);
            $srcH = imagesy($src);
            list($width, $height) = explode('x', $geometry);
            $destW = $width;
            $destH = $height;
            if ($srcW > $width || $srcH > $height) {
                $ratio = $width / $srcW;
                if ($srcH * $ratio <= $height) {
                    $destH = $srcH * $ratio;
                } else {
                    $ratio = $height / $srcH;
                    $destW = $srcW * $ratio;
                }
            }
            $destX = 0;
            $destY = 0;
            if ($destW < $width) {
                $destX = floor(($width - $destW) / 2);
            }
            if ($destY < $height) {
                $destY = floor(($height - $destH) / 2);
            }
            $img = imagecreatetruecolor($width, $height);
            imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
            imagecopyresampled($img, $src, $destX, $destY, 0, 0, $destW, $destH, $srcW, $srcH);
            $outputHandler($img, $destFile);

            return true;
        }

        return false;
    }

}
