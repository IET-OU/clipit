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

class ClipitChat extends UBMessage{

    const SUBTYPE = "clipit_chat";

    public $archived_array = array();

    protected function load_from_elgg($elgg_object){
        parent::load_from_elgg($elgg_object);
        $this->archived_array = (array)$elgg_object->archived_array;
    }

    /**
     * @param ElggObject $elgg_object Elgg object instance to save Item to
     */
    protected function copy_to_elgg($elgg_object){
        parent::copy_to_elgg($elgg_object);
        $elgg_object->archived_array = (array)$this->archived_array;
    }

    static function get_inbox($user_id){
        $inbox_array = array();
        $incoming_messages = static::get_by_destination(array($user_id));
        $incoming_messages = array_pop($incoming_messages);
        if($incoming_messages !== null){
            $sender_array = array();
            foreach($incoming_messages as $message){
                $archived_status = ClipitChat::get_archived_status($message->id, array($user_id));
                $archived_status = (bool)array_pop($archived_status);
                if($archived_status === false){
                    if(array_search($message->owner_id, $sender_array) === false){
                        $sender_array[] = $message->owner_id;
                        $inbox_array[$message->owner_id] = array($message);
                    } else{
                        array_push($inbox_array[$message->owner_id], $message);
                    }
                }
            }
        }
        return $inbox_array;
    }

    static function get_inbox_count($user_id){
        $count = 0;
        $inbox_array = static::get_inbox($user_id);
        foreach($inbox_array as $owner_messages){
            $count += count($owner_messages);
        }
        return $count;
    }

    static function get_inbox_unread($user_id){
        $count = 0;
        $inbox_array = static::get_inbox($user_id);
        foreach($inbox_array as $owner_messages){
            foreach($owner_messages as $message){
                $read_status = static::get_read_status($message->id, array($user_id));
                $read_status = (bool)array_pop($read_status);
                if(!$read_status){
                    $count++;
                }
            }
        }
        return $count;
    }

    static function get_sent($user_id){
        $sent_messages = static::get_by_sender(array($user_id));
        $sent_messages = array_pop($sent_messages);
        $sent_array = array();
        foreach($sent_messages as $message){
            $archived_status = ClipitChat::get_archived_status($message->id, array($user_id));
            $archived_status = (bool)array_pop($archived_status);
            if($archived_status === false){
                $sent_array[] = $message;
            }
        }
        return $sent_array;
    }

    static function get_sent_count($user_id){
        $count = 0;
        $sent_messages = static::get_by_sender(array($user_id));
        $sent_messages = array_pop($sent_messages);
        foreach($sent_messages as $message){
            $archived_status = ClipitChat::get_archived_status($message->id, array($user_id));
            $archived_status = (bool)array_pop($archived_status);
            if($archived_status === false){
                $count++;
            }
        }
        return $count;
    }

    static function get_archived($user_id){
        $incoming_messages = static::get_by_destination(array($user_id));
        $incoming_messages = array_pop($incoming_messages);
        $sent_messages = static::get_by_sender(array($user_id));
        $sent_messages = array_pop($sent_messages);
        $archived_array = array();
        foreach($incoming_messages as $message){
            $archived_status = ClipitChat::get_archived_status($message->id, array($user_id));
            $archived_status = (bool)array_pop($archived_status);
            if($archived_status === true){
                array_push($archived_array, $message);
            }
        }
        foreach($sent_messages as $message){
            $archived_status = ClipitChat::get_archived_status($message->id, array($user_id));
            $archived_status = (bool)array_pop($archived_status);
            if($archived_status === true){
                array_push($archived_array, $message);
            }
        }
        return $archived_array;
    }

    static function get_archived_count($user_id){
        $count = 0;
        $archived_array = static::get_archived($user_id);
        foreach($archived_array as $owner_messages){
            $count += count($owner_messages);
        }
        return $count;
    }

    static function get_conversation($user1_id, $user2_id){
        // user1 --> user2
        $sender_messages = static::get_by_sender(array($user1_id));
        $sender_messages = $sender_messages[$user1_id];
        $conversation = array();
        foreach($sender_messages as $message){
            if($message->destination == (int)$user2_id){
                $archived = static::get_archived_status($message->id, array($user1_id));
                if(!$archived[$user1_id]){
                    $conversation[$message->id] = $message;
                }
            }
        }
        // user2 --> user1
        $sender_messages = static::get_by_sender(array($user2_id));
        $sender_messages = $sender_messages[$user2_id];
        foreach($sender_messages as $message){
            if($message->destination == (int)$user1_id){
                $archived = static::get_archived_status($message->id, array($user1_id));
                if(!$archived[$user1_id]){
                    $conversation[$message->id] = $message;
                }
            }
        }
        usort($conversation, 'UBItem::sort_by_date');
        return $conversation;
    }

    static function get_conversation_count($user1_id, $user2_id){
        $count = 0;
        // user1 --> user2
        $sender_messages = static::get_by_sender(array($user1_id));
        $sender_messages = $sender_messages[$user1_id];
        foreach($sender_messages as $message){
            if($message->destination == (int)$user2_id){
                $archived = static::get_archived_status($message->id, array($user1_id));
                if(!$archived[$user1_id]){
                    $count++;
                }
            }
        }
        // user2 --> user1
        $sender_messages = static::get_by_sender(array($user2_id));
        $sender_messages = $sender_messages[$user2_id];
        foreach($sender_messages as $message){
            if($message->destination == (int)$user1_id){
                $archived = static::get_archived_status($message->id, array($user1_id));
                if(!$archived[$user1_id]){
                    $count++;
                }
            }
        }
        return $count;
    }
    static function get_conversation_unread($user1_id, $user2_id){
        $count = 0;
        // user1 --> user2
        $sender_messages = static::get_by_sender(array($user1_id));
        $sender_messages = $sender_messages[$user1_id];
        foreach($sender_messages as $message){
            if($message->destination == (int)$user2_id){
                $archived = static::get_archived_status($message->id, array($user1_id));
                $read = static::get_read_status($message->id, array($user1_id));
                if(!$archived[$user1_id] && !$read[$user1_id]){
                    $count++;
                }
            }
        }
        // user2 --> user1
        $sender_messages = static::get_by_sender(array($user2_id));
        $sender_messages = $sender_messages[$user2_id];
        foreach($sender_messages as $message){
            if($message->destination == (int)$user1_id){
                $archived = static::get_archived_status($message->id, array($user1_id));
                $read = static::get_read_status($message->id, array($user1_id));
                if(!$archived[$user1_id] && !$read[$user1_id]){
                    $count++;
                }
            }
        }
        return $count;
    }
    static function get_archived_status($id, $user_array = null){
        $archived_array = ClipitChat::get_properties($id, array("archived_array"));
        $archived_array = array_pop($archived_array);
        if(!$user_array){
            return $archived_array;
        } else{
            $return_array = array();
            foreach($user_array as $user_id){
                if(in_array($user_id, $archived_array)){
                    $return_array[$user_id] = true;
                } else{
                    $return_array[$user_id] = false;
                }
            }
            return $return_array;
        }
    }

    static function set_archived_status($id, $archived_value, $user_array){
        $archived_array = ClipitChat::get_properties($id, array("archived_array"));
        $archived_array = array_pop($archived_array);
        foreach($user_array as $user_id){
            if($archived_value == true){
                if(!in_array($user_id, $archived_array)){
                    array_push($archived_array, $user_id);
                }
            } else if($archived_value == false){
                $index = array_search((int) $user_id, $archived_array);
                if(isset($index) && $index !== false){
                    array_splice($archived_array, $index, 1);
                }
            }
        }
        $prop_value_array["archived_array"] = $archived_array;
        return ClipitChat::set_properties($id, $prop_value_array);
    }
}