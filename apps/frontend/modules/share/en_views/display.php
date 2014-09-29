<script type="text/javascript" src="/js/bootstrap-popover.js"></script>

<div class="container">
<div id="top">
<h2 class="title"><?php echo $pseudo; ?>'s ShareBox</h2>

<!--<div class="alert alert-info advice-file">
    Ecoutez une musique, regardez vos photos ou une vidéo d'un <strong>simple clic sur l'icone</strong> correspondante !
</div>-->

<?php

if($has_parent) {
    echo '<div class="fd">';
    echo '<a href="#" onclick="window.location.replace(document.referrer)"><img src="/images/folder_home.png" alt="home"><br> Back</a>';
    echo '</div>';
}
$count = 1;
foreach ($folders as $folder) {
    $name = htmlentities($folder->name(), ENT_QUOTES, 'UTF-8');
    $shortname = \lib\ToolBox::cut($folder->name(), 7, '...', true);

    $mode = $folder->permissions()->mode();
    if ($mode == \lib\Permission::P_Private) {
        $permission = "Private";
    } else if ($mode == \lib\Permission::P_Public) {
        $permission = "Everybody";
    } else {
        $permission = "Friends only";
    }
    ?>
<script>
    $(document).ready(function () {
        $(function () {
            $("#name<?php echo $count; ?>").popover({
                html:true })
        })
    });
</script>

<div class="fd">

    <a href="/users/<?php echo $pseudo;?>/files/<?php echo $folder->id(); ?>" id="name<?php echo $count; ?>"
       style="text-decoration:none" rel="popover"
       data-content="Name : <b><?php echo $name; ?></b><br>Permission : <b><?php echo $permission; ?></b><br />Uploaded the : <b><?php echo $folder->date(); ?></b>"
       title="Folder properties">
        <?php if (strtolower($folder->name()) == "musique") { ?>
        <img src="/images/music.png" style="border:none" alt="music">
        <?php } else if (strtolower($folder->name()) == "photos") { ?>
        <img src="/images/pictures.png" style="border:none" alt="pictures">
        <?php } else if (strtolower($folder->name()) == "vidéos") { ?>
        <img src="/images/videos.png" style="border:none" alt="videos">
        <?php } else { ?>
        <img src="/images/folder.png" style="border:none" alt="folder"> <?php }?>
        <br/>
        <span style="margin-left:3px"><?php echo $shortname ?></span></a><br/>
</div>
    <?php
    $count++;
}
?>

<?php
foreach ($files as $file) {
    $type = explode('/', $file->mimetype());
    if ($type[0] == "image") {
        $p_files[] = $file;
    }
}
$pcount = 0;
// AFFICHAGE DES FICHIERS
foreach ($files as $file) {
    $name = $file->name();
    $type = explode('/', $file->mimetype());
    if ($type[0] == "video" || ($type[0] == "application" && $type[1] == "ogg")) {
        require $modal_videoFile;
    } else if ($type[0] == "image") {
        $pcount++;
    }
    ?>
<div class="fd">
    <?php
    if ($type[0] == "audio") {
        echo "<a href=\"#\" onclick=\"lire(" . $file->id() . ")\" data-backdrop=\"static\" ";
    } else {
        echo "<a href=\"/download/" . $file->id() . "\" ";
    }
    ?>
    id="name<?php echo $count; ?>" data-rel="popover" data-content="Name :
    <b><?php echo htmlentities($name, ENT_QUOTES, 'UTF-8'); ?></b><br/>Permission : <b>
    <?php
    $mode = $file->permissions()->mode();
    if ($mode == \lib\Permission::P_Private) {
        echo "Private";
    } else if ($mode == \lib\Permission::P_Public) {
        echo "Everybody";
    } else {
        echo "Friends only";
    }?>
</b><br/>Size : <b><?php echo number_format($file->size() / 1024 / 1024, 2); ?> Mo</b><br/>Type :
    <b><?php echo $file->mimetype(); ?></b><br/>Uploaded the : <b><?php echo $file->date(); ?></b>" title="File properties">


    <script>
        $(document).ready(function () {
            $(function () {
                $("#name<?php echo $count; ?>").popover({
                    html:true
                })

            })
        });
    </script>

    <?php

    if (substr($name, strlen($name) - 3) == "odp") {
        echo "<img src=\"/images/mimetypes/diapo.png\" alt=\"diapo\">";
    } // si image
    else if ($type[0] == "image") {
        if ($type[1] == "jpg" || $type[1] == "jpeg" || $type[1] == "png" || $type[1] == "gif") {
            echo "<img src=\"/download/" . $file->id() . "/min." . $type[1] . "\" height=\"48\" class=\"icone-img\" alt=\"image\">";
        } else {
            echo "<img src=\"/images/mimetypes/image.png\" alt=\"image\">";
        }
    } // si audio
    else if ($type[0] == "audio") {
        echo "<img src=\"/images/mimetypes/audio.png\" alt=\"audio\">";
    } // si video
    else if ($type[0] == "video" || ($type[0] == "application" && $type[1] == "ogg")) {
        echo "<img src=\"/images/mimetypes/video.png\" alt=\"video\">";
    } // si texte
    else if ($type[0] == "text") {
        if ($type[1] == "html" or $type[1] == "webviewhtml") {
            echo "<img src=\"/images/mimetypes/www.png\" alt=\"www\">";
        } else {
            echo "<img src=\"/images/mimetypes/brut.png\" alt=\"brut\">";
        }
    } // si application
    else if ($type[0] == "application") {
        if ($type[1] == "msword" or $type[1] == "vnd.ms-office") {
            echo "<img src=\"/images/mimetypes/word.png\" alt=\"word\">";
        } else if ($type[1] == "zip" || $type[1] == "x-gzip") {
            echo "<img src=\"/images/mimetypes/zip.png\" alt=\"zip\">";
        } else if ($type[1] == "octet-stream") {
            echo "<img src=\"/images/mimetypes/executable.png\" alt=\"exe\">";
        } else if ($type[1] == "pdf") {
            echo "<img src=\"/images/mimetypes/word.png\" alt=\"pdf\">";
        } else if ($type[1] == "vnd.ms-excel") {
            echo "<img src=\"/images/mimetypes/tableur.png\" alt=\"tableur\">";
        } else {
            echo "<img src=\"/images/mimetypes/brut.png\" alt=\"brut\">";
        }
    } else {
        echo "<img src=\"/images/mimetypes/brut.png\" alt=\"brut\">";
    }


    $shortname = \lib\ToolBox::cut($file->name(), 7, '...', true)
    ?>
    </a>
    <?php
    // Cadenas si fichier privé
    if ($mode == \lib\Permission::P_Private) {
        echo '<i class="icon-lock cadenas"></i>';
    }
    ?>

    <br/>
    <li class="dropdown" data-dropdown="dropdown">
        <a href="#" data-toggle="dropdown" class="dropdown-toggle options"><?php echo $shortname; ?></a><br/>
        <ul class="dropdown-menu">
            <li><a href="/download/<? echo $file->id();?>" data-backdrop="static"><i class="icon-download"></i>
                Download</a></li>
            <li><a href="#" onclick="importFile(<?php echo $file->id(); ?>)"><i class="icon-plus-sign" style="opacity:0.8"></i>
                Copy in my documents</a></li>
            <li><a href="#" onclick="qrcode('<?php echo $file->id(); ?>', '<?php echo $file->name(); ?>')"
                   data-controls-modal="qrcodefichier" data-backdrop="static"><i class="icon-qrcode"></i>
                Download link</a></li>
        </ul>
    </li>
</div>
    <?php $count++;
}
require $modal_pictureFile;
require $modal_qrFile;
?>
</div>
</div>

<script type="text/javascript" src="/js/bootstrap-modal.js"></script>
<script>

    function qrcode(id, nom) {
        document.getElementById("qrname").innerHTML = nom;
        document.getElementById("imageqrcode").src = '/qrcode/' + id;
        document.getElementById("lien").value = 'http://sharea.net/download/' + id;
    }

    function importFile(id) {
        jQuery.ajax({
            type:'POST',
            url:'/ajax/file/import',
            data:{
                file:id,
            },
            success:function (data, textStatus, jqXHR) {
                if (check(data, true, false) !== false) {
                    alert("File imported successfully!");
                }
            },
            error:function (jqXHR, textStatus, errorThrown) {
                alert("Error");
            }
        });
    }
</script>
