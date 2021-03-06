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
$files_id = $message->get_files($message->id);
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
    <div class="body-post">
        <?php echo text_reference($message->description); ?>
        <!-- files -->
        <?php if($files_id): ?>
            <div class="attachment-files row">
                <span class="total-files"><i class="fa fa-paperclip"></i> <?php echo count($files_id);?> attachments</span>
                <?php
                foreach($files_id as $file_id):
                    $file = array_pop(ClipitFile::get_by_id(array($file_id)));

                    $isViewer = elgg_view("multimedia/file/view", array(
                        'file'  => $file,
                        'size'  => 'original' ));
                    $href_viewer = false;
                    if($isViewer){
                        echo elgg_view("page/components/modal_remote", array('id'=> "viewer-id-{$file->id}" ));
                        $href_viewer = "ajax/view/multimedia/file/viewer?id=".$file->id;
                    }
                ?>
                    <div class="file col-md-3">
                        <div class="preview">
                            <div class="file-preview">
                                <?php echo elgg_view('output/url', array(
                                    'href'  => $href_viewer,
                                    'title' => $file->name,
                                    'data-target' => '#viewer-id-'.$file->id,
                                    'data-toggle' => 'modal',
                                    'text'  => elgg_view("multimedia/file/preview", array('file'  => $file))));
                                ?>
                            </div>
                        </div>
                        <div class="details">
                            <strong>
                            <?php if ($isViewer): ?>
                                <?php echo elgg_view('output/url', array(
                                    'href'  => $href_viewer,
                                    'title' => $file->name,
                                    'class' => 'text-truncate',
                                    'data-target' => '#viewer-id-'.$file->id,
                                    'data-toggle' => 'modal',
                                    'text'  => $file->name));
                                ?>
                            <?php else: ?>
                                <div class="name text-truncate" title="<?php echo $file->name; ?>">
                                    <?php echo $file->name; ?>
                                </div>
                            <?php endif; ?>
                            </strong>
                            <?php echo elgg_view('output/url', array(
                                'class' => 'btn btn-default btn-xs',
                                'style' => 'margin-right: 5px;font-family: inherit;',
                                'href'  => "file/download/".$file->id,
                                'title' => $file->name,
                                'text'  => '<i class="fa fa-download""></i>',
                                'target' => 'blank_'
                            ));
                            ?>

                            <small><?php echo formatFileSize($file->size); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <!-- files end-->
    </div>
</div>