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
$event = elgg_extract("event", $vars);
$author = $event['author'];
$activity = $event['activity'];
$item = $event['item'];
$object = $event['object'];
$url_item = $vars['item']['url'];

if($event):
    ?>
    <li class="list-item">
        <div class="pull-left" style="margin-right: 10px;margin-top: 5px; position: relative;">
            <?php echo elgg_view('output/url', array(
                'href'  => "clipit_activity/".$activity->id,
                'title' => $activity->name,
                'text'  => "",
                'style' => 'background: #'.$activity->color.';width: 22px; height: 22px; position: absolute; border-radius: 15px; border: 2px solid #fff; left: -5px; top: -5px;'
            ));
            ?>
            <img src="<?php echo $author['icon']; ?>">
        </div>
        <div style="overflow: hidden">
            <div class="text-truncate">
                <small class="pull-right"><?php echo elgg_view('output/friendlytime', array('time' => $event['time']));?></small>
                <strong><a><?php echo $author['name'];?></a></strong>
            </div>
            <div>
                <small class="show">
                    <i class="fa fa-<?php echo $event['icon'];?>" style=" color: #C9C9C9; "></i>
                    <?php echo $event['message'];?>
                    <?php if(!$item):?>
                        <?php echo elgg_view('output/url', array(
                            'href'  => $object['url'],
                            'title' => $object['name'],
                            'text'  => $object['name'],
                        ));
                        ?>
                    <?php endif; ?>
                    <?php if(!$item['icon']):?>
                        <?php echo elgg_view('output/url', array(
                            'href'  => $item['url'],
                            'title' => $item['name'],
                            'text'  => $item['name'],
                        ));
                        ?>
                    <?php endif; ?>
                </small>
            </div>
            <?php
            if($item['description']):
                $description = trim(elgg_strip_tags($item['description']));
                // text truncate max length 70
                if(mb_strlen($description)>70){
                    $description = substr($description, 0, 70)."<b>...</b>";
                }
                if(!$item['icon']):
                    ?>
                    <p style=" color: #666666; padding-left: 5px; border-left: 3px solid #eee; overflow: hidden; margin-top: 5px; ">
                        <?php echo $description;?>
                    </p>
                <?php else: ?>
                    <div style=" margin-top: 5px;">
                        <i class="fa fa-file-o file-icon" style=" font-size: 35px; color: #C9C9C9; float: left; margin-right: 5px; "></i>
                        <div style="overflow: hidden">
                            <small>
                                <?php echo elgg_view('output/url', array(
                                    'href'  => $item['url'],
                                    'title' => $item['name'],
                                    'text'  => $item['name'],
                                    'class' => 'show'
                                ));
                                ?>
                                <strong><?php echo $description;?></strong>
                            </small>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </li>
<?php endif; ?>
