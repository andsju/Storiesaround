<?php
if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}

// include core
//--------------------------------------------------
require_once 'inc.core.php';

if (!get_role_CMS('editor') == 1) {
    die;
}
?>

<script src="libraries/fileuploader/fileuploader.js"></script>

<script>
    $(document).ready(function () {
        var token = $("#token").val();
        var uploader = new qq.FileUploader({
            element: document.getElementById('file-uploader'),
            multiple: false,
            allowedExtensions: ['jpeg', 'jpg', 'gif', 'png', 'swf'],
            sizeLimit: 5000000,
            uploadButtonText: 'Upload image or flash file',
            action: 'includes/inc.banners_upload.php',
            debug: true,
            params: {token: '' + token + '', folder: '../content/uploads/ads'},
            onComplete: function (id, fileName, responseJSON) {
                if (responseJSON.success) {
                    var height = responseJSON['height'];
                    var width = responseJSON['width'];
                    var banners_id = responseJSON['banners_id'];
                    var name = fileName.substr(0, fileName.lastIndexOf('.'));
                    $("#banners_upload").empty().val(fileName);
                    $("#banners_name").empty().val(name);
                    $("#banners_id").empty().val(banners_id);
                    $("#banners_width").empty().val(width);
                    $("#banners_height").empty().val(height);
                    $("#banners_edit").empty().append('&raquo;<a href=banners_edit.php?id=' + banners_id + '&token=' + token + '>edit banner settings</a>').hide().fadeIn('slow');
                }
                $('#file-uploader .qq-upload-button').hide();
                $('#file-uploader .qq-upload-file').addClass('qq-upload-success');
                $('#file-uploader .qq-upload-drop-area').hide();
            }
        });


        $('#btn_banners_create').click(function (event) {
            event.preventDefault();
            var action = "banners_create";
            var banners_new = $('input:radio[name=banners_new]:checked').val();
            var banners_active = 0;
            var banners_name = $("#banners_name").val();
            var token = $("#token").val();

            $.ajax({
                beforeSend: function () {
                    loading = $('#ajax_spinner_banners').show()
                },
                complete: function () {
                    loading = setTimeout("$('#ajax_spinner_banners').hide()", 700)
                },
                type: 'POST',
                url: 'admin_edit_ajax.php',
                data: "action=" + action + "&token=" + token + "&banners_new=" + banners_new + "&banners_name=" + banners_name + "&banners_active=" + banners_active,
                success: function (message) {
                    ajaxReply(message, '#ajax_status_banners');
                }
            });
        });

    });
</script>


<h4 class="admin-heading">Add banner</h4>

<p>
    Create from existing image or flash (gif, jpg, png, swf)
</p>


<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token']; ?>"/>
<input type="hidden" name="banners_id" id="banners_id" value="0"/>
<br/>

<table>
    <tr>
        <td>
            <div id="file-uploader"></div>
            <input type="hidden" id="banners_upload"/>
        </td>
    </tr>
</table>

<p>
    <input type="text" id="banners_name" style="display:none;"/>
    <input type="text" id="banners_width" style="display:none;"/>
    <input type="text" id="banners_height" style="display:none;"/>
</p>

<span id="banners_edit" style="display:none;"></span>
<span id="ajax_spinner_banners" style="display:none;"></span>
<span id="ajax_status_banners" style="display:none;"></span>

