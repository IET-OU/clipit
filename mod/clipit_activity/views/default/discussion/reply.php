<?php
/**
 * ClipIt - JuxtaLearn Web Space
 * PHP version:     >= 5.2
 * Creation date:   22/04/14
 * Last update:     22/04/14
 * @author          Miguel Ángel Gutiérrez <magutierrezmoreno@gmail.com>, URJC JuxtaLearn Project
 * @version         $Version$
 * @link            http://www.juxtalearn.eu
 * @license         GNU Affero General Public License v3
 * @package         ClipIt
 */
$message = elgg_extract('entity', $vars);
$auto_id = elgg_extract('auto_id', $vars);
$user_loggedin_id = elgg_get_logged_in_user_guid();
$user_reply = array_pop(ClipitUser::get_by_id(array($message->owner_id)));
$user_reply_elgg = new ElggUser($message->owner_id);

// activity discussion, get group data
if($vars['activity']){
    $group_id = ClipitGroup::get_from_user_activity($user_reply->id, 74);
}
// set read status
if($message->owner_id != $user_loggedin_id){
    ClipitPost::set_read_status($message->id, true, array($user_loggedin_id));
}
?>
<a name="reply_<?php echo $message->id; ?>"></a>
<div class="discussion discussion-reply-msg">
    <div class="header-post">
        <a class="show btn pull-right msg-quote" style="
    background: #fff;
    padding: 2px 5px;
    border-radius: 4px;
    border: 1px solid #bae6f6;
">#<?php echo $auto_id;?></a>
        <div class="user-reply">
            <img class="user-avatar" src="<?php echo $user_reply_elgg->getIconURL('small'); ?>" />
        </div>
        <div class="block">
            <strong>
                <?php echo elgg_view('output/url', array(
                    'href'  => "profile/".$user_reply->login,
                    'title' => $user_reply->name,
                    'text'  => $user_reply->name));
                ?>
            </strong>
            <small class="show">
                <?php if($vars['show_group'] && $group = array_pop(ClipitGroup::get_by_id(array($group_id)))):?>
                    <span class="label label-primary" style="display: inline-block;background: #32b4e5;color: #fff;">
                        <?php echo $group->name?>
                    </span>
                <?php endif; ?>

                <?php echo elgg_view('output/friendlytime', array('time' => $message->time_created));?>
            </small>
        </div>
    </div>
    <div class="body-post"><?php echo text_reference($message->description); ?></div>
</div>