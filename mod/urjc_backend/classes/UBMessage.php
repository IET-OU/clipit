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
 * @package         Clipit
 */


class UBMessage extends UBItem{

    const SUBTYPE = "message";

    const REL_MESSAGE_DESTINATION = "message-destination";
    const REL_MESSAGE_FILE = "message-file";

    public $read_array = array();
    public $destination = -1;
    public $file_array = array();

    protected function _load($elgg_object){
        parent::_load($elgg_object);
        $this->read_array = (array)$elgg_object->read_array;
        $this->destination = static::get_destination($this->id);
        $this->file_array = static::get_files($this->id);
    }

    /**
     * Saves this instance to the system.
     *
     * @return bool|int Returns id of saved instance, or false if error.
     */
    function save(){
        if($this->id == -1){
            $elgg_object = new ElggObject();
            $elgg_object->subtype = (string)static::SUBTYPE;
        } elseif(!$elgg_object = new ElggObject((int)$this->id)){
            return false;
        }
        $elgg_object->name = (string)$this->name;
        $elgg_object->description = (string)$this->description;
        $elgg_object->read_array = (array)$this->read_array;
        $elgg_object->access_id = ACCESS_PUBLIC;
        $elgg_object->save();
        $this->id = (int)$elgg_object->guid;
        $this->owner_id = (int)$elgg_object->owner_guid;
        $this->time_created = (int)$elgg_object->time_created;
        static::set_destination($this->id, $this->destination);
        static::add_files($this->id, $this->file_array);
        return $this->id;
    }

    function delete(){
        if($rel_array = get_entity_relationships($this->id, true)){
            foreach($rel_array as $rel){
                switch($rel->relationship){
                    case ClipitMessage::REL_MESSAGE_DESTINATION:
                        $reply_array[] = $rel->guid_one;
                        break;
                }
            }
            if(isset($reply_array)){
                ClipitMessage::delete_by_id($reply_array);
            }
        }
        return parent::delete();
    }

    /* STATIC FUNCTIONS */

    static function get_by_destination($destination_array){
        $called_class = get_called_class();
        $message_array = array();
        foreach($destination_array as $destination_id){
            $item_array = UBCollection::get_items($destination_id, static::REL_MESSAGE_DESTINATION, true);
            $temp_array = array();
            foreach($item_array as $item_id){
                $temp_array[$item_id] = new $called_class((int)$item_id);
            }
            if(empty($temp_array)){
                $message_array[$destination_id] = null;
            } else{
                $message_array[$destination_id] = $temp_array;
            }
            usort($message_array[$destination_id], 'UBItem::sort_by_date');
        }

        return $message_array;
    }

    static function get_by_sender($sender_array){
        return static::get_by_owner($sender_array);
    }

    static function get_destination($id){
        $item_array = UBCollection::get_items($id, static::REL_MESSAGE_DESTINATION);
        if(empty($item_array)){
            return -1;
        }
        return array_pop($item_array);
    }

    static function set_destination($id, $destination_id){
        if($destination_id != -1){
            UBCollection::remove_all_items($id, static::REL_MESSAGE_DESTINATION);
            return UBCollection::add_items($id, array($destination_id), static::REL_MESSAGE_DESTINATION);
        }
        return -1;
    }

    static function get_sender($id){
        $called_class = get_called_class();
        $message = new $called_class($id);
        return $message->owner_id;
    }

    static function get_files($id){
        return UBCollection::get_items($id, static::REL_MESSAGE_FILE);
    }

    static function add_files($id, $file_array){
        return UBCollection::add_items($id, $file_array, static::REL_MESSAGE_FILE, true);
    }


    static function get_read_status($id, $user_array = null){
        $called_class = get_called_class();
        $read_array = $called_class::get_properties($id, array("read_array"));
        $read_array = array_pop($read_array);
        if(!$user_array){
            return $read_array;
        } else{
            $return_array = array();
            foreach($user_array as $user_id){
                if(in_array($user_id, $read_array)){
                    $return_array[$user_id] = true;
                } else{
                    $return_array[$user_id] = false;
                }
            }
            return $return_array;
        }
    }

    static function set_read_status($id, $read_value, $user_array){
        $called_class = get_called_class();
        $read_array = $called_class::get_properties($id, array("read_array"));
        $read_array = array_pop($read_array);
        foreach($user_array as $user_id){
            if($read_value == true){
                if(!in_array($user_id, $read_array)){
                    array_push($read_array, $user_id);
                }
            } else if($read_value == false){
                $index = array_search((int) $user_id, $read_array);
                if(isset($index) && $index !== false){
                    array_splice($read_array, $index, 1);
                }
            }
        }
        $prop_value_array["read_array"] = $read_array;
        return $called_class::set_properties($id, $prop_value_array);
    }

    static function get_count_by_destination($destination_array, $user_id = -1){
        $called_class = get_called_class();
        foreach($destination_array as $destination_id){
            $count = 0;
            //$direct_messages = static::get_by_destination()

        }
    }
}