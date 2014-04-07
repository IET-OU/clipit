<?php
/**
 * ClipIt - JuxtaLearn Web Space
 * PHP version:     >= 5.2
 * Creation date:   3/04/14
 * Last update:     3/04/14
 *
 * @author          Miguel Ángel Gutiérrez <magutierrezmoreno@gmail.com>, JuxtaLearn Project
 * @version         $Version$
 * @link            http://juxtalearn.org
 * @license         GNU Affero General Public License v3
 *                  (http://www.gnu.org/licenses/agpl-3.0.txt)
 *                  This program is free software: you can redistribute it and/or modify
 *                  it under the terms of the GNU Affero General Public License as
 *                  published by the Free Software Foundation, version 3.
 *                  This program is distributed in the hope that it will be useful,
 *                  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *                  GNU Affero General Public License for more details.
 *                  You should have received a copy of the GNU Affero General Public License
 *                  along with this program. If not, see
 *                  http://www.gnu.org/licenses/agpl-3.0.txt.
 */
$message_id = (int)get_input('message-id');
$message = array_pop(ClipitPost::get_by_id(array($message_id)));
$group = array_pop(ClipitGroup::get_by_id(array($message->destination)));
$message_reply = get_input('message-reply');


if(count($message)==0 || count($group)==0 || trim($message_reply) == ""){
    register_error(elgg_echo("reply:cantcreate"));
} else{
    ClipitPost::create(array(
        'name' => '',
        'description' => $message_reply,
        'destination' => $group->id,
        'parent'      => $message->id,
    ));
    system_message(elgg_echo('reply:created'));
}

forward(REFERER);