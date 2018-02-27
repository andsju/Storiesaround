<?php
if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}

require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if (!get_role_CMS('author') == 1) {
    die;
}

$pages = new Pages();

$id = array_key_exists('id', $_GET) ? $_GET['id'] : 0;
$id = filter_var($id, FILTER_VALIDATE_INT) ? $id : 0;
?>

<div class="admin-area-outer ui-widget ui-widget-content">
    <div class="admin-area-inner">

        <table style="width:100%;">
            <tr>
                <td style="width:50%;vertical-align:top;">

                    <?php
                    $r = $pages->getPagesPublished();
                    if ($r) {
                        echo '<div class="container box_pages ui-black-white">';
                        echo '<h4 class="admin-heading">In brief</h4>';
                        echo '<p>' . count($r) . ' pages published</p>';
                        echo '</div>';
                    }

                    if (get_role_CMS('editor') == 1) {
                        echo '<div class="box_pages ui-black-white">';
                        echo '<h4 class="admin-heading">Pending pages</h4>';
                        $pending_pages = $pages->getPagesPending();
                        if ($pending_pages) {
                            echo '<ul>';
                            foreach ($pending_pages as $pending_page) {
                                $dt = get_utc_dtz($pending_page['utc_modified'], $dtz, 'Y-m-d H:i:s');
                                echo '<li>' . $pending_page['title'] . ', last modified: <abbr class="timeago" title="' . $dt . '">' . $dt . '</abbr>';
                                echo '&nbsp; <a href="pages_edit.php?id=' . $pending_page['pages_id'] . '" class="colorbox_edit"><span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a></li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '[none]';
                        }
                        echo '</div>';
                    }

                    $recent_pages_draft = $pages->getPagesRecent($status = 1);
                    echo '<div class="box_pages ui-black-white">';
                    echo '<h4 class="admin-heading">Recent draft pages</h4>';
                    if ($recent_pages_draft) {
                        echo '<ul>';
                        foreach ($recent_pages_draft as $recent_page) {
                            $dt = get_utc_dtz($recent_page['utc_modified'], $dtz, 'Y-m-d H:i:s');
                            echo '<li>' . $recent_page['title'] . ', last modified: <abbr class="timeago" title="' . $dt . '">' . $dt . '</abbr>';
                            echo '&nbsp; <a href="pages_edit.php?id=' . $recent_page['pages_id'] . '" class="colorbox_edit"><span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a></li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '[none]';
                    }
                    echo '</div>';

                    $recent_pages_published = $pages->getPagesRecent($status = 2);
                    echo '<div class="box_pages ui-black-white">';
                    echo '<h4 class="admin-heading">Recent published pages</h4>';
                    if ($recent_pages_published) {
                        echo '<ul>';
                        foreach ($recent_pages_published as $recent_page) {
                            $dt = get_utc_dtz($recent_page['utc_modified'], $dtz, 'Y-m-d H:i:s');
                            echo '<li><a href="pages.php?id=' . $recent_page['pages_id'] . '" target="_blank">' . $recent_page['title'] . '</a>, last modified: <abbr class="timeago" title="' . $dt . '">' . $dt . '</abbr>';
                            echo '&nbsp; <a href="pages_edit.php?id=' . $recent_page['pages_id'] . '" class="colorbox_edit"><span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a></li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '[none]';
                    }
                    echo '</div>';

                    $recent_pages_edited = $pages->getPagesRecentModified();
                    echo '<div class="box_pages ui-black-white">';
                    echo '<h4 class="admin-heading">Recent edited pages</h4>';
                    if ($recent_pages_edited) {
                        echo '<ul>';
                        foreach ($recent_pages_edited as $recent_page_edited) {
                            $dt = get_utc_dtz($recent_page_edited['utc_modified'], $dtz, 'Y-m-d H:i:s');
                            echo '<li><a href="pages.php?id=' . $recent_page_edited['pages_id'] . '" target="_blank">' . $recent_page_edited['title'] . '</a>, last modified: <abbr class="timeago" title="' . $dt . '">' . $dt . '</abbr>';
                            echo '&nbsp; <a href="pages_edit.php?id=' . $recent_page_edited['pages_id'] . '" class="colorbox_edit"><span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a></li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '[none]';
                    }
                    echo '</div>';
                    ?>

                </td>
                <td>

                    <div id="stories_news" style="border:2px dotted grey;" class="ui-black-white"></div>

                    <div class="container box_pages ui-black-white">
                        <div style="float:right;text-align:right;"><span class="ui-icon ui-icon-person"></span></div>
                        <h4 class="admin-heading">Online users (logged in)</h4>
                        <p>
                    <span class="toolbar_update"><button id="btn_whois_online"
                                                         title="show users online">Show users</button></span>
                        </p>
                        <div id="whois_online">
                        </div>
                    </div>

                    <div class="container box_pages ui-black-white">
                        <p>Storiesaround version: <?php echo CMS_VERSION; ?></p>
                        <ul class="cms-version">
                            <li>jQuery 3.1.1</li>
                            <li>jQuery UI 1.12.1</li>
                            <li>jQuery Cycle2 2.1.6</li>
                            <li>tinyMCE 4.5.0</li>
                            <li></li>

                        </ul>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>

    setInterval("whois_online()", 30000);

    function whois_online() {
        var action = "whois_online";
        var token = $("#token").val();
        var users_id = $("#users_id").val();
        $.ajax({
            type: 'POST',
            url: 'online_ajax.php',
            data: "action=" + action + "&token=" + token + "&users_id=" + users_id,
            success: function (list) {
                $("#whois_online").html(list);
            },
        });
    }

    $(document).ready(function () {

        jQuery("abbr.timeago").timeago();

        $('#btn_whois_online').click(function (event) {
            event.preventDefault();
            whois_online();
        });

        $(".toolbar_update button").button({
            icons: {
                secondary: "ui-icon-refresh"
            },
            text: true
        });
    });


</script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/yui/3.18.0/yui/yui-min.js"></script>
    <script>
        /*
        YUI().use('yql', function(Y){
            var query = 'select * from rss(0,2) where url = "http://www.storiesaround.com/content/rss/news"';
            var q = Y.YQL(query, function(r){
                //r now contains the result of the YQL Query as a JSON
                var date;
                var feedmarkup = '<p>'
                var feed = r.query.results.item // get feed as array of entries
                for (var i=0; i<feed.length; i++){
                    feedmarkup += '<div><a href="' + feed[i].link + '" target="">' + feed[i].title + '</a></div>';
                    date = new Date(feed[i].pubDate);
                    date = date.toISOString().substring(0, 10);
                    feedmarkup += '<div><abbr class="timeago" datetime="'+ date +'">'+ date +'</abbr></div>'
                    feedmarkup += '<p>' + feed[i].description + '</p>'
                }
                feedmarkup += '</p>';
                document.getElementById('#stories_news').innerHTML = feedmarkup
            })
        })
        */
    </script>


