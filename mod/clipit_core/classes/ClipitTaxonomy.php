<?php

/**
 * [Short description/title for module]
 *
 * [Long description for module]
 *
 * PHP version:      >= 5.2
 *
 * Creation date:    [YYYY-MM-DD]
 * Last update:      $Date$
 *
 * @category         [name]
 * @package          [name]
 * @subpackage       [name]
 * @author           Pablo Llinás Arnaiz <pebs74@gmail.com>
 * @version          $Version$
 * @link             [URL description]
 *
 * @license          GNU Affero General Public License v3
 * http://www.gnu.org/licenses/agpl-3.0.txt
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, version 3. *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details. *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see
 * http://www.gnu.org/licenses/agpl-3.0.txt.
 */
class ClipitTaxonomy{

    // Class properties
    public $description = string;
    public $id = int;
    public $name = string;
    public $taxonomy_tc_list = array(ClipitTaxonomyTC);
    public $creation_date = DateTime;

    static function getProperty($id, $prop){
        return "TO-DO";
    }

    static function setProperty($id, $prop, $value){
        return "TO-DO";
    }

    static function exposeFunctions(){
        expose_function("clipit.taxonomy.sb.getProperty", "ClipitTaxonomySB::getProperty",
            array(
                "id" => array(
                    "type" => "integer",
                    "required" => true),
                "prop" => array(
                    "type" => "string",
                    "required" => true)),
            "TO-DO:description",
            'GET',
            true,
            false);

        expose_function("clipit.taxonomy.sb.setPropertysetProperty", "ClipitTaxonomySB::setProperty",
            array(
                "id" => array(
                    "type" => "integer",
                    "required" => true),
                "prop" => array(
                    "type" => "string",
                    "required" => true),
                "value" => array(
                    "type" => "string",
                    "required" => true)),
            "TO-DO:description",
            'GET',
            true,
            false);
    }

}