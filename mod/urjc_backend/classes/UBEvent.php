<?php
/**
 * ClipIt - JuxtaLearn Web Space
 * PHP version:     >= 5.2
 * Creation date:   2013-10-10
 * Last update:     $Date$
 * @author          Pablo Llinás Arnaiz <pebs74@gmail.com>, URJC JuxtaLearn Team
 * @version         $Version$
 * @link            http://www.juxtalearn.eu
 * @license         GNU Affero General Public License v3
 * @package         ClipIt
 * @subpackage      urjc_backend
 */


class UBEvent{

    static function get_latest($offset = 0, $limit = 10){
        return get_system_log(
            null, // $by_user
            null, // $event
            null, // $class
            null, // $type
            null, // $subtype
            $limit, // $limit
            $offset, // $offset
            null, // $count
            null, // $timebefore
            null, // $timeafter
            null, // $object_id
            null, // $ip_address
            null // $owner
        );
    }

    static function get_by_user($user_array, $offset = 0, $limit = 10){
        global $CONFIG;
        $query = "SELECT * FROM {$CONFIG->dbprefix}system_log where ";
        $query .= "performed_by_guid in (" . implode(",", $user_array) . ")";
        $query .= " OR ";
        $query .= "owner_guid in (" . implode(",", $user_array) . ")";
        foreach($user_array as $user_id){
            $relationship_array = array_merge(
                get_entity_relationships($user_id, false),
                get_entity_relationships($user_id, true));
            if($relationship_array){
                foreach($relationship_array as $relationship){
                    $relationship_ids[] = $relationship->id;
                }
                if(isset($relationship_ids) and !empty($relationship_ids)){
                    $query .= " OR ";
                    $query .= "object_id";
                    $query .= " IN (";
                    $query .= implode(",", $relationship_ids) . ")";
                }
            }
        }
        $query .= " ORDER BY ";
        $query .= "time_created desc";
        $query .= " LIMIT $offset, $limit"; // Add order and limit
        return get_data($query);
    }

    static function get_by_object($object_array, $offset = 0, $limit = 10){
        if(empty($object_array)){
            return array();
        }
        global $CONFIG;
        $query = "SELECT * FROM {$CONFIG->dbprefix}system_log where ";
        $query .= "object_id IN (" . implode(",", $object_array) . ")";
        $query .= " AND object_type != \"relationship\" AND object_type != \"metadata\"";
        $query .= " UNION";
        $query .= " SELECT * FROM {$CONFIG->dbprefix}system_log";
        $relationship_array = array();
        foreach($object_array as $object_id){
            $relationship_array = array_merge(
                $relationship_array,
                get_entity_relationships($object_id, false),
                get_entity_relationships($object_id, true));
        }
        if(!empty($relationship_array)){
            foreach($relationship_array as $relationship){
                $relationship_ids[] = $relationship->id;
            }
            if(isset($relationship_ids) and !empty($relationship_ids)){
                $query .= " where object_id IN (";
                $query .= implode(",", $relationship_ids) . ") ";
                $query .= " AND object_type = \"relationship\"";
            }
        }
        $query .= " ORDER BY ";
        $query .= "time_created desc";
        $query .= " LIMIT $offset, $limit"; // Add order and limit
        return get_data($query);
    }
}