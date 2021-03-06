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
 * @subpackage      clipit_api
 */

/**
 * Class ClipitEvent
 *
 */
class ClipitEvent extends UBEvent{

    static function get_recommended_events($user_id, $offset = 0, $limit = 10){
        $user_groups = ClipitUser::get_groups($user_id);
        $user_activities = array();
        foreach($user_groups as $group){
            if($activity_id = ClipitGroup::get_activity($group)){
                $user_activities[] = $activity_id;
            }
        }
        $object_array = array_merge($user_groups, $user_activities);
        return ClipitEvent::get_by_object($object_array, $offset, $limit);
    }
} 