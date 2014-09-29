/*****************************************************
 *
 *          ICONES
 *
 ******************************************************/

function displayFoldersIcon(data) {
    /* Affichage du home */
    var space = document.getElementById("top");

    var divRetour = document.createElement("div");
    divRetour.setAttribute("class", "fd");
    var retour = document.createElement("a");
    retour.innerHTML = '<img src="/images/folder_home.png" alt="home"><br><span class="grey">Retour</span>';
    retour.setAttribute("onclick", "backFolder()");
    divRetour.appendChild(retour);
    space.appendChild(divRetour);

    if (data.folders != null) {
        for (var i = 0; i < data.folders.length; i++) {
            var nom = data.folders[i].name;
            if(nom.length > 11) {
             nom = nom.substring(0, 11);
             nom += "...";
             }

            /* PERMISSIONS */
            if(data.folders[i].permissions == 1) {
                permission = "Privé";
                icone = '<i class="icon-lock" style="position: absolute; vertical-align: top; margin-left: 2px;"></i>';
            }
            else if(data.folders[i].permissions == 4) {
                permission = "Amis";
                icone = '<i class="icon-user" style="position: absolute; vertical-align: top; margin-left: 2px"></i>';
            }
            else {
                permission = "Public";
                icone = '<i class="icon-globe" style="position: absolute; vertical-align: top; margin-left: 2px"></i>';
            }

            var div = document.createElement("div");
            div.setAttribute("class", "fd");
            div.setAttribute("data-original-title", "Propriétés du dossier")
            div.setAttribute("id", "dossier"+i);
            div.setAttribute("rel", "popover");
            div.setAttribute("data-content", "Nom : <b>"+data.folders[i].name+"</b><br>Visible par : <b>"+permission+"</b><br />Date de création : <b>"+data.folders[i].date+"</b>");

            var div2 = document.createElement("div");
            div2.setAttribute("onclick", "loadFoldersIcon(" + data.folders[i].id + ")");
            /* Si dossier musique */
            if (data.folders[i].name.toLowerCase() == "musique" || data.folders[i].name.toLowerCase() == "musiques") {
                div2.innerHTML = '<img src="/images/music.png" alt="Musiques" />';
            }

            /* Si dossier vidéo */
            else if (data.folders[i].name.toLowerCase() == "videos" || data.folders[i].name.toLowerCase() == "video" || data.folders[i].name.toLowerCase() == "vidéos" || data.folders[i].name.toLowerCase() == "vidéo") {
                div2.innerHTML = '<img src="/images/videos.png" alt="Vidéos" />';
            }

            /* Si images/photos */
            else if (data.folders[i].name.toLowerCase() == "images" || data.folders[i].name.toLowerCase() == "photos" || data.folders[i].name.toLowerCase() == "image" || data.folders[i].name.toLowerCase() == "photo") {
                div2.innerHTML = '<img src="/images/pictures.png" alt="Photos" />';
            }

            /* Si dossier normal */
            else {
                div2.innerHTML = '<img src="/images/folder.png" alt="Dossier" />';
            }
            div2.innerHTML += icone;

            div.appendChild(div2);
            var divBtn = document.createElement("div");
            divBtn.setAttribute("class", "options");
            divBtn.innerHTML = '<li class="dropdown" data-dropdown="dropdown">' +
                '<a href="#" data-toggle="dropdown" class="dropdown-toggle options"><span>'+nom+'</span> <i class="caret"></i></a>' +
                '<ul class="dropdown-menu">' +
                '<li><a href="#" onclick="renommerdossier(\'' + data.folders[i].id + '\', \'' + data.folders[i].name + '\')" data-controls-modal="renommerdossier" data-backdrop="static"><i class="icon-pencil"></i> Renommer</a></li>' +
                '<li><a href="#" onclick="partagerdossier(\'' + data.folders[i].id + '\', \'' + data.folders[i].name + '\', \'' + data.folders[i].permission + '\')" data-controls-modal="partagerdossier" data-backdrop="static"><i class="icon-share"></i> Partager</a></li>' +
                '<li class="divider"></li><li><a href="#" onclick="supprimerdossier(\'' + data.folders[i].id + '\', \'' + data.folders[i].name + '\')" data-controls-modal="supprimerdossier" data-backdrop="static"><i class="icon-remove"></i> Supprimer</a></li>' +
                '</ul></li>';

            div.appendChild(divBtn);
            //div.setAttribute('onmouseover', 'this.lastChild.style.display="inline-block"');
            //div.setAttribute('onmouseout', 'this.lastChild.style.display="none"');
            space.appendChild(div);

            /*$("#dossier"+i).popover({
                html: true, delay: 800
            });*/
        }
    }
}

function displayFilesIcon(data) {
    Images = new Array();
    var space = document.getElementById("top");
    var nbImages = 0;
    // Si le tableau de fichiers n'est pas nul, on l'affiche
    if (data.files != null) {
        for (var i = 0; i < data.files.length; i++) {
            var nom = data.files[i].name;
            if(nom.length > 12) {
             nom = nom.substring(0, 12);
             nom += "...";
             }

            var div = document.createElement("div");
            div.setAttribute("class", "fd");
            div.setAttribute("data-original-title", "Propriétés du fichier")
            div.setAttribute("id", "fichier"+i);
            div.setAttribute("rel", "popover");

            /* PERMISSIONS */
            if(data.files[i].permissions == 1) {
                permission = "Privé";
                icone = '<i class="icon-lock" style="position: absolute; vertical-align: top; margin-left: 2px;"></i>';
            }
            else if(data.files[i].permissions == 4) {
                permission = "Amis";
                icone = '<i class="icon-user" style="position: absolute; vertical-align: top; margin-left: 2px"></i>';
            }
            else {
                permission = "Public";
                icone = '<i class="icon-globe" style="position: absolute; vertical-align: top; margin-left: 2px"></i>';
            }

            div.setAttribute("data-content", "Nom : <b>"+data.files[i].name+"</b><br>Visible par : <b>"+permission+"</b><br />Date de mise en ligne : <b>"+data.files[i].date+"</b>");

            var div2 = document.createElement("div");

            if (data.files[i].name.indexOf("odp") != -1) {
                div2.innerHTML = '<img src="/images/mimetypes/diapo.png" alt="diapo">';
            }

            // si image
            else if (data.files[i].mimetype.indexOf("image") != -1) {
                if (data.files[i].mimetype.indexOf("jpg") != -1 || data.files[i].mimetype.indexOf("jpeg") != -1 || data.files[i].mimetype.indexOf("png") != -1 || data.files[i].mimetype.indexOf("gif") != -1) {
                    div2.innerHTML = '<img src="/download/' + data.files[i].id + '/min.' + data.files[i].mimetype.substring(6, data.files[i].mimetype.length) + '" height="48" class="icone-img" alt="image">';
                }
                else {
                    div2.innerHTML = '<img src="/images/mimetypes/image.png" alt="image">';
                }
                Images.push(new Array(data.files[i].id, data.files[i].name));
            }

            // si audio
            else if (data.files[i].mimetype.indexOf("audio") != -1) {
                div2.innerHTML = '<img src="/images/mimetypes/audio.png" alt="audio">';
            }

            // si video
            else if (data.files[i].mimetype.indexOf("video") != -1 || data.files[i].mimetype.indexOf("application/ogg") != -1) {
                div2.innerHTML = '<img src="/images/mimetypes/video.png" alt="video">';
            }

            // si texte
            else if (data.files[i].mimetype.indexOf("text") != -1) {
                if (data.files[i].mimetype.indexOf("html") != -1 || data.files[i].mimetype.indexOf("webviewhtml") != -1) {
                    div2.innerHTML = '<img src="/images/mimetypes/www.png" alt="www">';
                }
                else {
                    div2.innerHTML = '<img src="/images/mimetypes/brut.png" alt="brut">';
                }
            }

            // si application
            else if (data.files[i].mimetype.indexOf("application") != -1) {
                if (data.files[i].mimetype.indexOf("msword") != -1 || data.files[i].mimetype.indexOf("vnd.ms-office") != -1) {
                    div2.innerHTML = '<img src="/images/mimetypes/word.png" alt="word">';
                }
                else if (data.files[i].mimetype.indexOf("zip") != -1 || data.files[i].mimetype.indexOf("x-gzip") != -1) {
                    div2.innerHTML = '<img src="/images/mimetypes/zip.png" alt="zip">';
                }
                else if (data.files[i].mimetype.indexOf("octet-stream") != -1) {
			if (data.files[i].name.split('.')[data.files[i].name.split('.').length-1] == "ods") {
				div2.innerHTML = '<img src="/images/mimetypes/tableur.png" alt="tableur">';
			}
			else {
                    		div2.innerHTML = '<img src="/images/mimetypes/executable.png" alt="exe">';
			}
                }
                else if (data.files[i].mimetype.indexOf("pdf") != -1) {
                    div2.innerHTML = '<img src="/images/mimetypes/word.png" alt="pdf">';
                }
                else if (data.files[i].mimetype.indexOf("vnd.ms-excel") != -1) {
                    div2.innerHTML = '<img src="/images/mimetypes/tableur.png" alt="tableur">';
                }
                else {
                    div2.innerHTML = '<img src="/images/mimetypes/brut.png" alt="brut">';
                }
            }
            else {
                div2.innerHTML = '<img src="/images/mimetypes/brut.png" alt="brut">';
            }

            div2.innerHTML += icone;

            /* Prévisualisation */
            if (data.files[i].mimetype.indexOf("audio") != -1) {
                div2.setAttribute('onclick', 'lire(' + data.files[i].id + ')');
                div2.setAttribute('data-backdrop', 'static');
            }
            else if (data.files[i].mimetype.indexOf("video") != -1 || data.files[i].mimetype.indexOf("application/ogg") != -1) {
                div2.setAttribute('onclick', 'video(' + data.files[i].id + ', "' + data.files[i].name + '")');
                div2.setAttribute('data-backdrop', 'static');
                div2.setAttribute('data-controls-modal', 'videofile');
            }
            else if (data.files[i].mimetype.indexOf("image") != -1) {
                div2.setAttribute('data-backdrop', 'static');
                div2.setAttribute('data-controls-modal', 'picture');
                div2.setAttribute('onclick', 'diapo(' + nbImages + ')');
                nbImages++;
            }
            else {
                div2.setAttribute('onclick', 'document.location = "/download/' + data.files[i].id + '"');
            }

            div.appendChild(div2);
            var divBtn = document.createElement("div");
            divBtn.setAttribute("class", "options");
            divBtn.innerHTML = '<li class="dropdown" data-dropdown="dropdown">' +
                '<a href="#" data-toggle="dropdown" class="dropdown-toggle options"><span>'+nom+'</span></a>' +
                '<ul class="dropdown-menu">' +
                '<li><a href="/download/' + data.files[i].id + '" data-backdrop="static"><i class="icon-download"></i> Télécharger</a></li>' +
                '<li><a href="#" onclick="partagerfichier(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="partagerfichier" data-backdrop="static"><i class="icon-share-alt"></i> Partager</a></li>' +
                '<li><a href="#" onclick="qrcode(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="qrcodefichier" data-backdrop="static"><i class="icon-qrcode"></i> Lien de téléchargement</a></li>' +
                '<li><a href="#" onclick="renommer(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="renommerfichier" data-backdrop="static"><i class="icon-pencil"></i> Renommer</a></li>' +
                '<li><a href="#" onclick="deplacer(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="deplacerfichier" data-backdrop="static"><i class="icon-move"></i> Déplacer ...</a></li>' +
                '<li class="divider"></li>' +
                '<li><a href="#" onclick="supprimer(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="supprimerfichier" data-backdrop="static"><i class="icon-remove"></i> Supprimer</a></li>'
            '</ul></li>';

            div.appendChild(divBtn);
            //div.setAttribute('onmouseover', 'this.lastChild.style.display="inline-block"');
            //div.setAttribute('onmouseout', 'this.lastChild.style.display="none"');
            space.appendChild(div);


            $("#fichier"+i).popover({
                html: true, delay: 0
            });
        }
        initSlide();
    }

}

function loadFoldersIcon(id) {
    document.getElementById("parent").value = document.getElementById("parent").value + ',' + id;
    document.getElementById("folder").value = id;
    document.getElementById("top").innerHTML = "";
    displayIcon();
}

function backFolder() {
    arbre = document.getElementById("parent").value.split(',');
    if(arbre.length < 2) {
        document.getElementById("folder").value = arbre[0];
    }
    else {
        document.getElementById("folder").value = arbre[arbre.length-2];
    }
    document.getElementById("parent").value = arbre[0];
    parent = arbre[arbre.length-2];
    /* Reconstruction de l'arbre */
    for(var z=1; z<arbre.length-1; z++) {
        document.getElementById("parent").value = document.getElementById("parent").value + ',' + arbre[z];
    }
    document.getElementById("top").innerHTML = "";
    displayIcon();
}

function displayIcon() {
    document.getElementById("btnIcone").disabled = true;
    document.getElementById("btnListe").disabled = false;

    document.getElementById("top").innerHTML = "";

    document.cookie = 'display=1; expires=Fri, 01 Jan 2100 00:0:00 UTC; path=/'

    jQuery.post("/ajax/folder/getfolders", { folder:document.getElementById("folder").value },
        function (data) {
            displayFoldersIcon(data);
            jQuery.post("/ajax/file/getfiles", { folder:document.getElementById("folder").value },
                function (data) {
                    displayFilesIcon(data);
                }
                , "json"
            );
        }
        , "json"
    );
}


/*************************************
 *   Fonction de recherche de fichier
 **************************************/

function searchList() {
    jQuery.post("/ajax/folder/search", { folder:document.getElementById("search").value },
        function (data) {
            purge();
            if (document.cookie == null || document.cookie[document.cookie.length - 1] == 0) {
                displayFoldersList(data);
            }
            else {
                displayFoldersIcon(data);
            }
        }
        , "json"
    );

    jQuery.post("/ajax/file/search", { file:document.getElementById("search").value },
        function (data) {
            if (document.cookie == null || document.cookie[document.cookie.length - 1] == 0) {
                displayFilesList(data);
            }
            else {
                displayFilesIcon(data);
            }
        }
        , "json"
    );

}

/********************************************
 * Vide le tableau
 */
function purge() {
    if (document.cookie == null || document.cookie[document.cookie.length - 1] == 0) {
        document.getElementById("files").innerHTML = "";
    }
    else {
        document.getElementById("top").innerHTML = "";
    }
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while(c.charAt(0) == ' ') c = c.substring(1, c.length);
        if(c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function getFiles() {
    if (document.cookie == null || readCookie("display") == 0) {
        displayList();
        document.getElementById("btnListe").disabled = true;
    }
    else {
        displayIcon();
        document.getElementById("btnIcone").disabled = true;
    }
}

function sleep(milliSeconds){
    var startTime = new Date().getTime(); // get the current time
    while (new Date().getTime() < startTime + milliSeconds); // hog cpu
}

getFiles();
