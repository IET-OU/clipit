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

/**
 * Register the init method
 */
elgg_register_event_handler('init', 'system', 'urjc_backend_init');

/**
 * Initialization method which loads objects, libraries, exposes the REST API, and registers test classes.
 */
function urjc_backend_init(){
    /*echo elgg_get_entities(
        array(
            'type' => "object",
            'subtype' => "clipit_activity",
            'limit' => 10));*/
    register_pam_handler('clipit_auth_usertoken');
    loadFiles(elgg_get_plugins_path() . "urjc_backend/libraries/");
}

/**
 * Loads PHP files.
 *
 * @throws InstallationException
 */
function loadFiles($path){
    if(!$path){
        return false;
    }
    $obj_files = elgg_get_file_list($path, array(), array(), array(".php"));
    foreach($obj_files as $obj){
        elgg_log("Loading $obj...");
        if(!include_once($obj)){
            $msg = "Could not load $obj";
            throw new InstallationException($msg);
        }
    }
}

/**
 * Check the user token
 * This examines whether an authentication token is present and returns true if
 * it is present and is valid. The user gets logged in so with the current
 * session code of Elgg, that user will be logged out of all other sessions.
 *
 * @return bool
 * @access private
 */
function clipit_auth_usertoken(){
    global $CONFIG;
    if(isset($_SERVER["HTTP_AUTH_TOKEN"])){
        $token = $_SERVER["HTTP_AUTH_TOKEN"];
    }
    if(!isset($token) || empty($token)){
        return false;
    }

    $validated_userid = validate_user_token($token, $CONFIG->site_id);

    if($validated_userid){
        $u = get_entity($validated_userid);

        // Could we get the user?
        if(!$u){
            return false;
        }

        // Not an elgg user
        if((!$u instanceof ElggUser)){
            return false;
        }

        // User is banned
        if($u->isBanned()){
            return false;
        }

        // Fail if we couldn't log the user in
        if(!login($u)){
            return false;
        }

        return true;
    }

    return false;
}
