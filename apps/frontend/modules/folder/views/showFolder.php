<script type="text/javascript" src="/js/bootstrap-popover.js"></script>
<script type="text/javascript" src="/js/bootstrap-modal.js"></script>
<script type="text/javascript" src="/js/bootstrap-transition.js"></script>

<div id="showfiles" class="container">


<?php
if (isset($notification)) {
    echo "<div class=\"alert alert-success\" id=\"notifok\">
			   <a class=\"close\" href=\"" . $_SERVER['REDIRECT_URL'] . "\">&times;</a>
        		" . $notification . "
     			</div>";
}
?>

<br/>

<div class="btn-group">
    <button id="btnListe" class="btn btn-orange" onclick="displayList()"><i class="icon-list"></i> Liste</button>
    <button id="btnIcone" class="btn btn-orange" onclick="displayIcon()"><i class="icon-th"></i> Icônes</button>
    <input id="search" name="search" type="text" placeholder="Rechercher un fichier..." value=""
           onkeyup="searchList()"/>
    <button class="btn btn-orange" data-toggle="collapse" data-target="#upload"><i class="icon-share-alt"></i> Ajouter
        des fichiers
    </button>
    <button class="btn btn-orange" data-toggle="collapse" data-target="#createFolder"><i class="icon-folder-open"></i>
        Créer un dossier
    </button>
</div>

<div id="createFolder" class="collapse">
   <input type="text" name="folder_name" id="folder_name" placeholder="Nom du nouveau dossier"/>
   <button class="btn btn-primary" onclick="createFolder()">Créer le dossier</button>
</div>

<!-- PARTIE UPLOAD !-->
<div id="upload" class="collapse container">
    <form id="formfile" action="#">
        <div class="control-group">
            <div class="controls">
                <input type="file" id="fileselect[]" name="file" multiple="multiple">
                <input type="hidden" value="<?php echo $current_folder_id; ?>" name="parent" id="parent"/>
                <input type="hidden" id="folder" name="folder" value="<?php echo $current_folder_id; ?>" />
                <br/>

                <div id="filedrag">Glissez vos fichiers ici !</div>
            </div>
        </div>
    </form>
    <div id="pre_upload">
        <table class="table table-striped">
            <thead id="head_table">
            <tr>
                <th>#</th>
                <th>Nom du fichier</th>
                <th>Taille</th>
            </tr>
            </thead>
            <tbody id="body_table">
            </tbody>
        </table>
        <!-- Bouton d'envoi et de suppression -->
        <input id="submit_ie" type="submit" class="btn btn-primary" value="Envoyer le fichier"/>
        <button id="submitbutton" class="btn btn-primary" name="submit"/>Envoyer les fichiers</button>
        <button id="remove_list" class="btn btn-danger" name="submit"/>Vider la liste</button>
    </div>
    <!-- Barre de progression avec infos du fichier -->
    <strong>Nom du fichier</strong> : <span id="fileName"><em>Aucun fichier chargé</em></span> <span
        id="nbr"></span><br/>

    <div id="bardiv" class="progress progress-striped active">
        <div id="progress" class="bar" style="width: 0%;"></div>
    </div>
    <span id="percent_upload"></span><span id="current"></span><span id="total"></span>
</div>

<div id="top" class="center">

</div>

<div class="center">
    <script type="text/javascript">google_ad_client="ca-pub-4983973769928411";google_ad_slot="4830354866";google_ad_width=234;google_ad_height=60;</script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</div>

<script type="text/javascript">var Images = new Array();</script>
<script type="text/javascript" src="/js/affichageListe.js"></script>
<script type="text/javascript" src="/js/affichageIcone.js"></script>

<?php
    require $modal_renameFolder;
    require $modal_removeFolder;
    require $modal_shareFolder;
    require $modal_pictureFile;
    require $modal_shareFile;
    require $modal_renameFile;
    require $modal_removeFile;
    require $modal_qrFile;
    require $modal_moveFile;
    require $modal_videoFile;
?>


<script type="text/javascript" src="/js/fonctions.js"></script>
<script type="text/javascript" src="/js/drag.js"></script>
</div>


