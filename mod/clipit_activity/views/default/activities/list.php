<?php
/**
 * Created by JetBrains PhpStorm.
 * User: equipo
 * Date: 14/02/14
 * Time: 10:29
 * To change this template use File | Settings | File Templates.
 */
$items = $vars['items'];
$offset = elgg_extract('offset', $vars);
$limit = elgg_extract('limit', $vars);
$count = elgg_extract('count', $vars);
$base_url = elgg_extract('base_url', $vars, '');
$pagination = elgg_extract('pagination', $vars, true);
$progress_html = elgg_extract('progress_bar', $vars);
$user_loggedin_id = elgg_get_logged_in_user_guid();

$list_class = 'row';
if (isset($vars['list_class'])) {
    $list_class = "$list_class {$vars['list_class']}";
}

if (is_array($items) && count($items) > 0):
    echo "<ul class=\"$list_class\">";
    foreach ($items as $item):
        if($item):
            $group_id = ClipitGroup::get_from_user_activity($user_loggedin_id, $item->id);
            $group_array = ClipitGroup::get_by_id(array($group_id));
            $group =  array_pop($group_array); // ClipitGroup object
            $users = ClipitGroup::get_users($group_id);
            $users = array_slice($users, 0, 10);

            $title = elgg_get_friendly_title($item->name);
            $activity_link = elgg_view('output/url', array(
                'href' => "clipit_activity/{$item->id}",
                'text' => $item->name,
                'is_trusted' => true,
                'style' => 'color: #'.$item->color
            ));
            if($progress_html){
                $progress_html = "<div style='margin-bottom: 5px;'>{$progress_html}</div>";
            }
            ?>
            <li class='list-item col-md-12'>
                <div class="row">

                    <div class="col-md-6">
                        <div class="pull-right">
                            <span class="label label-primary">Active</span>
                        </div>
                        <h3 style='margin: 0' class="text-truncate">
                            <a style='display: none; color: #<?php echo $item->color; ?>;opacity: 0.7;'><i class='fa fa-bars' title='Activity menu'></i></a>
                            <?php echo $activity_link; ?>
                        </h3>
                        <div style='color: #999;'>
                            <i class='fa fa-calendar'></i> 20/03/2014 - 01/10/2014
                        </div>
                        <div style='max-height: 45px; overflow: hidden; color: #666666; '>
                            <?php echo $item->description; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-xs-6" style="border-right: 1px solid #ccc; padding-right: 15px;">
                                <?php if($group):?>
                                <div>
                                    <?php echo $progress_html; ?>
                                    <strong>
                                        <?php echo elgg_view('output/url', array(
                                            'href'  => "clipit_activity/{$item->id}/group/activity_log",
                                            'title' => $group->name,
                                            'text'  => $group->name));
                                        ?>
                                    </strong>
                                </div>
                                <div style='margin-top: 5px;'>
                                    <?php
                                    foreach ($users as $user_id):
                                        $user = array_pop(ClipitUser::get_by_id(array($user_id)));
                                        $user_elgg = new ElggUser($user_id);
                                    ?>
                                        <?php echo elgg_view('output/url', array(
                                            'href'  => "profile/".$user->login,
                                            'title' => $user->name,
                                            'text'  => '<img style="margin: 1px;" src="'.$user_elgg->getIconURL('tiny').'" />'));
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-xs-6">
                                <small class='show'>Teacher</small>
                                <ul style="max-height: 100px; overflow: auto;">
                                    <li style="margin-bottom: 5px;">
                                        <img class='pull-left' style='margin-right: 10px;' src='http://juxtalearn.org/sandbox/clipit02/mod/profile/icondirect.php?lastcache=1378461434&amp;joindate=1378461161&amp;guid=133&amp;size=small'>
                                        <div class='text-truncate'>
                                            <a href='http://juxtalearn.org/sandbox/clipit_befe/profile/student_1' title='Jose Antonio Martín' rel='nofollow'>Manuel Gertrudix</a>
                                            <div class='show'>
                                                <a href='http://juxtalearn.org/sandbox/clipit_befe/ajax/view/modal/messages/send?id=60' title='Jose Antonio Martín' data-target='#send-message-60' data-toggle='modal' rel='nofollow'><i class='fa fa-envelope'></i></a>
                                                <small>@gertrudix</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li style="margin-bottom: 5px;">
                                        <img class='pull-left' style='margin-right: 10px;' src='http://juxtalearn.org/sandbox/clipit02/mod/profile/icondirect.php?lastcache=1378934833&joindate=1378934545&guid=442&size=small'>
                                        <div class='text-truncate'>
                                            <a href='http://juxtalearn.org/sandbox/clipit_befe/profile/student_1' title='Jose Antonio Martín' rel='nofollow'>Jaime Urquiza Sanchez</a>
                                            <div class='show'>
                                                <a href='http://juxtalearn.org/sandbox/clipit_befe/ajax/view/modal/messages/send?id=60' title='Jose Antonio Martín' data-target='#send-message-60' data-toggle='modal' rel='nofollow'><i class='fa fa-envelope'></i></a>
                                                <small>@jaime.urquiza</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
