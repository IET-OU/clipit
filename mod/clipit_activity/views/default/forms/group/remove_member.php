<?php
/**
 * ClipIt - JuxtaLearn Web Space
 * PHP version:     >= 5.2
 * Creation date:   24/03/14
 * Last update:     24/03/14
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
$user = elgg_extract('entity', $vars);
$group = elgg_extract('group', $vars);
echo elgg_view("input/hidden", array(
    'name' => 'user-id',
    'value' => $user->id,
));
echo elgg_view("input/hidden", array(
    'name' => 'group-id',
    'value' => $group->id,
));

echo '<button class="pull-right btn btn-xs btn-danger" title="'.elgg_echo("group:member:remove").'"><i class="fa fa-times"></i></button>'
?>
