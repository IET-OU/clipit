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
$entity = elgg_extract('entity', $vars);
$files_id = elgg_extract('files', $vars);
$href = elgg_extract("href", $vars);
?>

<div class="block" style="margin-bottom: 10px;">
    <?php echo elgg_view_form('multimedia/files/upload', array('data-validate'=> "true", 'enctype' => 'multipart/form-data'), array('entity'  => $entity)); ?>
</div>
<script>
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }
        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }
        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }
        return (bytes / 1000).toFixed(2) + ' KB';
    }
    $(function () {
        'use strict';
        // Change this to the location of your server-side upload handler:
        var url = window.location.hostname === 'blueimp.github.io' ?
                '//jquery-file-upload.appspot.com/' : 'server/php/',
            uploadButton = $('<button/>')
                .addClass('btn btn-primary')
                .prop('disabled', true)
                .text('Processing...')
                .on('click', function () {
                    var $this = $(this),
                        data = $this.data();
                    $this
                        .off('click')
                        .text('Abort')
                        .on('click', function () {
                            $this.remove();
                            data.abort();
                        });
                    data.submit().always(function () {
                        $this.remove();
                    });
                });
        $('#uploadfiles').fileupload({
            url: url,
            dataType: 'json',
            autoUpload: false,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|wmv|mp4)$/i,
            maxFileSize: 500000000, // 500 MB
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            previewMaxWidth: 100,
            previewMaxHeight: 100,
            previewCrop: true
        }).on('fileuploadadd', function (e, data) {
            data.context = $('<div class="file"/>').appendTo('.upload-files-list');
            $.each(data.files, function (index, file) {
                var node = $('<div class="details"/>')
                    .append($('<small class="size pull-right"/>').text(formatFileSize(file.size)))
                    .append($('<div class="name"/>').text(file.name))
                    .append('<div id="progress" class="progress"><div class="progress-bar progress-bar-success"></div></div>');
//                if (!index) {
//                    node
//                        .append('<br>')
//                        .append(uploadButton.clone(true).data(data));
//                }
                node.appendTo(data.context);
            });
        }).on('fileuploadprocessalways', function (e, data) {
            var index = data.index,
                file = data.files[index],
                node = $(data.context.children()[index]);
//                node.find(".size").text(formatFileSize(file.size));
            console.log(formatFileSize(file.size))
            if (file.preview) {
                node
                    .before(file.preview)
                    .append("<div></div>");
            }
            if (file.error) {
                node
                    .append('<br>')
                    .append($('<span class="text-danger"/>').text(file.error));
            }
            if (index + 1 === data.files.length) {
                data.context.find('button')
                    .text('Upload')
                    .prop('disabled', !!data.files.error);
            }
        }).on('fileuploadprogressall', function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }).on('fileuploaddone', function (e, data) {
            $.each(data.result.files, function (index, file) {
                if (file.url) {
                    var link = $('<a>')
                        .attr('target', '_blank')
                        .prop('href', file.url);
                    $(data.context.children()[index])
                        .wrap(link);
                } else if (file.error) {
                    var error = $('<span class="text-danger"/>').text(file.error);
                    $(data.context.children()[index])
                        .append('<br>')
                        .append(error);
                }
            });
        }).on('fileuploadfail', function (e, data) {
            $.each(data.files, function (index, file) {
                var error = $('<span class="text-danger"/>').text('File upload failed.');
                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            });
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    });
</script>
<style>
    .table td{
        border-bottom: 1px solid #bae6f6;
        border-top: 0 !important;
    }
    input[type=file]#uploadfiles {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        -ms-filter: 'alpha(opacity=0)';
        direction: ltr;
        cursor: pointer;
    }
</style>
<?php
// MODAL SIMULATE
for($i=0; $i<3; $i++){
$body .='
<div class="col-md-3">
    <div class="no-file" style="display:table;text-align:center;">
        <div style="display:table;vertical-align:center">
           <h2>File</h2>
        </div>
    </div>
</div>
<div class="col-md-8">
    <div class="form-group">
        <label for="file-name">'.elgg_echo("multimedia:links:add").'</label>
    '.elgg_view("input/text", array(
            'name' => 'file-name[]',
            'id' => 'file-name',
            'style' => 'padding-left: 25px;',
            'class' => 'form-control blue',
            'required' => true
        )).'
    </div>
    <div class="form-group">
        <label for="file-text">'.elgg_echo("discussion:text_topic").'</label>
        '.elgg_view("input/plaintext", array(
            'name' => 'file-text[]',
            'class' => 'form-control mceEditor',
            'required' => true,
            'rows'  => 6,
        )).'
    </div>
</div>';
}
echo elgg_view("page/components/modal",
    array(
        "dialog_class"     => "modal-lg",
        "target"    => "add-file",
        "title"     => elgg_echo("multimedia:files:add"),
        "form"      => true,
        "body"      => $body,
        "cancel_button" => true,
        "ok_button" => elgg_view('input/submit',
            array(
                'value' => elgg_echo('add'),
                'class' => "btn btn-primary"
            ))
    ));
// MODAL SIMULATE
?>
<button type="button" data-toggle="modal" data-target="#add-file" class="btn btn-default">MODAL</button>

<div style="margin-bottom: 30px;color: #999;margin-left: 10px;">
    <div class="checkbox" style=" display: inline-block;margin: 0;">
        <label>
            <input type="checkbox" class="select-all"> Select all
        </label>
    </div>
    <div style=" display: inline-block; margin-left: 10px; ">
        <select name="set-option" class="form-control message-options" style="height: 20px;padding: 0;" disabled="">
            <option>[Options]</option>
            <option value="read">Download</option>
            <option value="unread">Publish</option>
        </select>
    </div>
    <div class="pull-right search-box">
        <input type="text" placeholder="Search">
        <div class="input-group-btn">
            <span></span>
            <button type="submit">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</div>
<table class="table files-table">
<tbody>
<?php
foreach($files_id as $file_id):
    $file = array_pop(ClipitFile::get_by_id(array($file_id)));
    $owner = array_pop(ClipitUser::get_by_id(array($file->owner_id)));
?>
<tr>
    <td>
        <input type="checkbox">
    </td>
    <td>
        <i class="fa fa-file-o file-icon"></i>
    </td>
    <td class="col-md-9 file-info">
        <h4>
            <?php echo elgg_view('output/url', array(
                'href'  => "{$href}/view/".$file->id,
                'title' => $file->name,
                'text'  => $file->name));
            ?>
        </h4>
        <small class="show">
            <strong>
                PDF document
            </strong>
        </small>
        <p>
            <?php echo $file->description; ?>
        </p>
        <small class="show file-user-info">
            <i>Uploaded by
                <?php echo elgg_view('output/url', array(
                    'href'  => "profile/".$owner->login,
                    'title' => $owner->name,
                    'text'  => $owner->name));
                ?>
                <?php echo elgg_view('output/friendlytime', array('time' => $file->time_created));?>
            </i>
        </small>
    </td>
    <td style=" vertical-align: middle; text-align: center; " class="col-md-3">
        <div>
            <div style="width: 35px;display: inline-block;float: right;">
                <?php echo elgg_view('output/url', array(
                    'href'  => "{$href}/download/".$file->id,
                    'title' => $owner->name,
                    'class' => 'btn btn-default',
                    'style' => 'padding: 5px 10px;',
                    'text'  => '<i class="fa fa-download"></i>'));
                ?>
                <small class="show text-truncate" style="margin-top: 3px;">
                    <?php echo formatFileSize($file->size);?>
                </small>
            </div>
        </div>
    </td>
</tr>
<?php endforeach; ?>

</tbody>

</table>