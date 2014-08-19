<?php

/**
 * GeoTopoJSON GeoJSON <==> TopoJSON library
 * Ref: https://github.com/topojson/topojson-specification/blob/master/README.md
 *
 * @license http://opensource.org/licenses/BSD-3-Clause
 * @author Ronny Wang <ronnywang@gmail.com>
 * @url https://github.com/ronnywang/php-geotopojson
 */
class GeoTopoJSON {

    /**
     * toGeoJSONs tranform TopoJSON to GeoJson
     *
     * @param string $topojson 
     * @access public
     * @return array GeoJSON array, key is object id
     */
    public static function toGeoJSONs($topojson) {
        if (is_scalar($topojson)) {
            $json = json_decode($topojson);
        } else {
            $json = $topojson;
        }

        if ($json->type != 'Topology') {
            throw new Exception('Type must be Topology');
        }

        if (property_exists($json, 'transform')) {
            $scale = $json->transform->scale;
            $translate = $json->transform->translate;
        } else {
            $scale = array(1, 1);
            $translate = array(0, 0);
        }

        $arcs = $json->arcs;
        $ret = array();
        foreach ($json->objects as $key => $object) {
            $ret[$key] = self::topoObjectToGeoObject($object, $scale, $translate, $arcs);
        }
        return $ret;
    }

    /**
     * transformPoint from https://github.com/topojson/topojson-specification/blob/master/README.md#212-transforms
     * 
     * @param array $point 
     * @param array $scale 
     * @param array $translate 
     * @static
     * @access protected
     * @return array
     */
    protected static function transformPoint($point, $scale, $translate) {
        $point[0] = $point[0] * $scale[0] + $translate[0];
        $point[1] = $point[1] * $scale[1] + $translate[1];
        return $point;
    }

    /**
     * decodeArc from https://github.com/topojson/topojson-specification/blob/master/README.md#213-arcs
     * 
     * @param array $arcs 
     * @param int $id 
     * @param array $scale 
     * @param array $translate 
     * @static
     * @access protected
     * @return array
     */
    protected static function decodeArc($arcs, $id, $scale, $translate) {
        $x = 0;
        $y = 0;

        if ($id >= 0) {
            $arc = $arcs[$id];
        } else {
            $arc = $arcs[0 - $id - 1];
        }

        $points = array();
        foreach ($arc as $point) {
            $point[0] = ($x += $point[0]) * $scale[0] + $translate[0];
            $point[1] = ($y += $point[1]) * $scale[1] + $translate[1];
            $points[] = $point;
        }
        if ($id < 0) {
            return array_reverse($points);
        }
        return $points;
    }

    /**
     * topoObjectToGeoObject 
     * 
     * @param object $topo_obj 
     * @param array $scale 
     * @param array $translate 
     * @param array $arcs 
     * @static
     * @access protected
     * @return object
     */
    protected static function topoObjectToGeoObject($topo_obj, $scale, $translate, $arcs) {
        switch ($topo_obj->type) {
            case 'Point':
                $obj = new StdClass;
                $obj->type = 'Point';
                $obj->coordinates = self::transformPoint($topo_obj->coordinates, $scale, $translate);
                break;

            case 'MultiPoint':
                $obj = new StdClass;
                $obj->type = $topo_obj->type;
                $obj->coordinates = array_map(function($point) use ($scale, $translate) {
                    return self::transformPoint($point, $scale, $translate);
                }, $topo_obj->coordinates);
                break;

            case 'Polygon':
                $linearrings = array();
                foreach ($topo_obj->arcs as $linestring_arc_ids) {
                    $linestrings = array();
                    foreach ($linestring_arc_ids as $arc_id) {
                        foreach (self::decodeArc($arcs, $arc_id, $scale, $translate) as $point) {
                            if ($linestrings and $point == $linestrings[count($linestrings) - 1]) {
                                continue;
                            }
                            $linestrings[] = $point;
                        }
                    }
                    if (count($linestrings) < 4) {
                        continue;
                    }
                    $linearrings[] = $linestrings;
                }
                $obj = new StdClass;
                $obj->type = $topo_obj->type;
                $obj->coordinates = $linearrings;
                break;

            case 'GeometryCollection':
                $obj = new StdClass;
                $obj->type = 'FeatureCollection';
                $obj->features = array();
                foreach ($topo_obj->geometries as $geometry) {
                    $geometry_obj = self::topoObjectToGeoObject($geometry, $scale, $translate, $arcs);
                    if(!is_object($geometry_obj)) {
                        continue;
                    }
                    if ($geometry_obj->type != 'Feature') {
                        $geometry_feature_obj = new StdClass;
                        $geometry_feature_obj->type = 'Feature';
                        $geometry_feature_obj->properties = new StdClass;
                        $geometry_feature_obj->geometry = $geometry_obj;
                        $geometry_obj = $geometry_feature_obj;
                    }
                    $obj->features[] = $geometry_obj;
                }
                break;

            case 'MultiPolygon':
                $polygons = array();
                foreach ($topo_obj->arcs as $polygon_arc_id) {
                    $linearrings = array();
                    foreach ($polygon_arc_id as $i => $linestring_arc_ids) {
                        $linestrings = array();
                        foreach ($linestring_arc_ids as $arc_id) {
                            foreach (self::decodeArc($arcs, $arc_id, $scale, $translate) as $point) {
                                if ($linestrings and $point == $linestrings[count($linestrings) - 1]) {
                                    continue;
                                }
                                $linestrings[] = $point;
                            }
                        }
                        // linestrings muse have at least 4 point
                        if (count($linestrings) < 4) {
                            continue;
                        }
                        $linearrings[$i] = $linestrings;
                    }
                    if (count($linearrings) == 0) {
                        continue;
                    }
                    $polygons[] = $linearrings;
                }
                $obj = new StdClass;
                $obj->type = $topo_obj->type;
                $obj->coordinates = $polygons;
                break;

            case 'LineString':
                $linestring = array();
                foreach ($topo_obj->arcs as $arc_id) {
                    foreach (self::decodeArc($arcs, $arc_id, $scale, $translate) as $point) {
                        if ($linestring and $point == $linestring[count($linestring) - 1]) {
                            continue;
                        }
                        $linestring[] = $point;
                    }
                }
                $obj = new StdClass;
                $obj->type = $topo_obj->type;
                $obj->coordinates = $linestring;
                break;

            case 'MultiLineString':
                $linestrings = array();
                foreach ($topo_obj->arcs as $linestring_arc_ids) {
                    $linestring = array();
                    foreach ($linestring_arc_ids as $arc_id) {
                        foreach (self::decodeArc($arcs, $arc_id, $scale, $translate) as $point) {
                            if ($linestring and $point == $linestring[count($linestring) - 1]) {
                                continue;
                            }
                            $linestring[] = $point;
                        }
                    }
                    $linestrins[] = $linestring;
                }
                $obj = new StdClass;
                $obj->type = $topo_obj->type;
                $obj->coordinates = $linestrings;
                break;

            default:
                continue;
                throw new Exception("Unsupported Topology type {$topo_obj->type}");
        }

        if (isset($obj)) {
            if (!property_exists($topo_obj, 'properties')) {
                return $obj;
            }

            $feature_obj = new StdClass;
            $feature_obj->type = 'Feature';
            $feature_obj->properties = $topo_obj->properties;
            $feature_obj->geometry = $obj;
            return $feature_obj;
        }
        return false;
    }

    /**
     * toTopoJSON transform GeoJSON to TopoJSON
     * 
     * @param string $geojson 
     * @access public
     * @return string
     */
    public static function toTopoJSON($geojson) {
        // TODO
    }

}
