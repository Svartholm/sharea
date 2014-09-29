/************************************************
 *
 *          LISTES
 *
 ************************************************/
function displayFoldersList(data) {
    /* Affichage du home */
    var space = document.getElementById("files");
    var a = document.createElement("tr");
    a.setAttribute('id', 'racine');
    a.setAttribute('data-rel', 'popover');
    a.setAttribute('data-content', 'Name: <b>Home</b>');
    a.setAttribute('title', 'Propriétés du dossier');

    a.setAttribute("onclick", "backFolderList()");
    a.innerHTML = '<td><img src="/images/folder_home.png" alt="racine"></td><td>Back</td><td>--</td><td>Private</td><td class="options" style="visibility:hidden"><li class="dropdown" data-dropdown="dropdown"><a data-toggle="dropdown" class="btn btn-small btn-primary dropdown-toggle options"><span class="caret"></span></a></td></tr>';
    $('#racine').popover({html:true}); // On initialise la popover
    space.appendChild(a);

    if (data.folders != null) {
        /* Parcourt de tous les autres dossiers */
        for (var i = 0; i < data.folders.length; i++) {
            var nom = data.folders[i].name;

            var a = document.createElement("tr");

            /* On set les attributs */
            a.setAttribute("id", 'd' + i);
            a.setAttribute('onmouseover', 'this.lastChild.style.display="inline-block"');
            a.setAttribute('onmouseout', 'this.lastChild.style.display="none"');

            /* Si dossier musique */
            if (data.folders[i].name.toLowerCase() == "musique" || data.folders[i].name.toLowerCase() == "musiques") {
                hash = '<td><img src="/images/music.png" alt="Musiques">';
            }

            /* Si dossier vidéo */
            else if (data.folders[i].name.toLowerCase() == "videos" || data.folders[i].name.toLowerCase() == "video" || data.folders[i].name.toLowerCase() == "vidéos" || data.folders[i].name.toLowerCase() == "vidéo") {
                hash = '<td><img src="/images/videos.png" alt="Vidéos">';
            }

            /* Si images/photos */
            else if (data.folders[i].name.toLowerCase() == "images" || data.folders[i].name.toLowerCase() == "photos" || data.folders[i].name.toLowerCase() == "image" || data.folders[i].name.toLowerCase() == "photo") {
                hash = '<td><img src="/images/pictures.png" alt="Photos">';
            }

            /* Si dossier normal */
            else {
                hash = '<td><img src="/images/folder.png" alt="Dossier">';
            }


            /* PERMISSIONS */
            if(data.folders[i].permissions == 1) {
                permission = "Private";
                hash += '<i class="icon-lock" style="vertical-align: top; margin-left: 2px;"></i>';
            }
            else if(data.folders[i].permissions == 4) {
                permission = "Friends";
                hash += '<i class="icon-user" style="vertical-align: top; margin-left: 2px"></i>';
            }
            else {
                permission = "Public";
                hash += '<i class="icon-globe" style="vertical-align: top; margin-left: 2px"></i>';
            }

            hash += '</td><td onclick=loadFoldersList(' + data.folders[i].id + ')>' + nom + '</td><td>' + data.folders[i].date + '</td><td>'+permission+'</td>';


            /* Dropdown options */
            hash += '<td class="options" style="display:none"><li class="dropdown" data-dropdown="dropdown"><a data-toggle="dropdown" class="btn btn-small btn-primary dropdown-toggle options"><span class="caret"></span></a><ul class="dropdown-menu"><li><a href="#" onclick="renommerdossier(\'' + data.folders[i].id + '\', \'' + data.folders[i].name + '\')" data-controls-modal="renommerdossier" data-backdrop="static"><i class="icon-pencil"></i> Rename</a></li><li><a href="#" onclick="partagerdossier(\'' + data.folders[i].id + '\', \'' + data.folders[i].name + '\', \'' + data.folders[i].permission + '\')" data-controls-modal="partagerdossier" data-backdrop="static"><i class="icon-share"></i> Share</a></li><li class="divider"></li><li><a href="#" onclick="supprimerdossier(\'' + data.folders[i].id + '\', \'' + data.folders[i].name + '\')" data-controls-modal="supprimerdossier" data-backdrop="static"><i class="icon-remove"></i> Remove</a></li></ul></li></td>';
            a.innerHTML = hash;
            space.appendChild(a);
        }
    }
}

function displayFilesList(data) {
    var space = document.getElementById("files");
    var nbImages = 0;
    if (data.files != null) {
        for (var i = 0; i < data.files.length; i++) {
            var nom = data.files[i].name;
            /*if(nom.length > 18) {
             nom = nom.substring(0, 18);
             nom += "...";
             }*/
            var a = document.createElement("tr");
            var td = document.createElement("td");

            /* ICONES */
            // si diapo
            if (data.files[i].mimetype.indexOf("odp") != -1) {
                td.innerHTML = '<img src="/images/mimetypes/diapo.png" alt="diapo">';
            }

            // si image
            else if (data.files[i].mimetype.indexOf("image") != -1) {
                if (data.files[i].mimetype.indexOf("jpg") != -1 || data.files[i].mimetype.indexOf("jpeg") != -1 || data.files[i].mimetype.indexOf("png") != -1 || data.files[i].mimetype.indexOf("gif") != -1) {
                    td.innerHTML = '<img src="/download/' + data.files[i].id + '/min.' + data.files[i].mimetype.substring(6, data.files[i].mimetype.length) + '" height="48" class="icone-img" alt="image">';
                }
                else {
                    td.innerHTML = '<img src="/images/mimetypes/image.png" alt="image">';
                }
                Images.push(new Array(data.files[i].id, data.files[i].name));
            }

            // si audio
            else if (data.files[i].mimetype.indexOf("audio") != -1) {
                td.innerHTML = '<img src="/images/mimetypes/audio.png" alt="audio">';
            }

            // si video
            else if (data.files[i].mimetype.indexOf("video") != -1 || data.files[i].mimetype.indexOf("application/ogg") != -1) {
                td.innerHTML = '<img src="/images/mimetypes/video.png" alt="video">';
            }

            // si texte
            else if (data.files[i].mimetype.indexOf("text") != -1) {
                if (data.files[i].mimetype.indexOf("html") != -1 || data.files[i].mimetype.indexOf("webviewhtml") != -1) {
                    td.innerHTML = '<img src="/images/mimetypes/www.png" alt="www">';
                }
                else {
                    td.innerHTML = '<img src="/images/mimetypes/brut.png" alt="brut">';
                }
            }

            // si application
            else if (data.files[i].mimetype.indexOf("application") != -1) {
                if (data.files[i].mimetype.indexOf("msword") != -1 || data.files[i].mimetype.indexOf("vnd.ms-office") != -1) {
                    td.innerHTML = '<img src="/images/mimetypes/word.png" alt="word">';
                }
                else if (data.files[i].mimetype.indexOf("zip") != -1 || data.files[i].mimetype.indexOf("x-gzip") != -1) {
                    td.innerHTML = '<img src="/images/mimetypes/zip.png" alt="zip">';
                }
                else if (data.files[i].mimetype.indexOf("octet-stream") != -1) {
                    td.innerHTML = '<img src="/images/mimetypes/executable.png" alt="exe">';
                }
                else if (data.files[i].mimetype.indexOf("pdf") != -1) {
                    td.innerHTML = '<img src="/images/mimetypes/word.png" alt="pdf">';
                }
                else if (data.files[i].mimetype.indexOf("vnd.ms-excel") != -1) {
                    td.innerHTML = '<img src="/images/mimetypes/tableur.png" alt="tableur">';
                }
                else {
                    td.innerHTML = '<img src="/images/mimetypes/brut.png" alt="brut">';
                }
            }
            else {
                td.innerHTML = '<img src="/images/mimetypes/brut.png" alt="brut">';
            }

            /* PERMISSIONS */
            if(data.files[i].permissions == 1) {
                permission = "Private";
                icone = '<i class="icon-lock" style="position: absolute; vertical-align: top; margin-left: 2px;"></i>';
            }
            else if(data.files[i].permissions == 4) {
                permission = "Friends";
                icone = '<i class="icon-user" style="position: absolute; vertical-align: top; margin-left: 2px"></i>';
            }
            else {
                permission = "Public";
                icone = '<i class="icon-globe" style="position: absolute; vertical-align: top; margin-left: 2px"></i>';
            }

            td.innerHTML += icone;
            var td2 = document.createElement("td");
            td2.style.maxWidth = "30%";
            td2.style.overflow = "hidden";
            td2.innerHTML = nom;
            var td3 = document.createElement("td");
            td3.innerHTML = data.files[i].date;
            var td4 = document.createElement("td");

            td4.innerHTML = permission;

            var td5 = document.createElement("td");
            td5.setAttribute("class", "options");
            td5.style.display = "none";
            /* Dropdown options */
            hash = '<li class="dropdown" data-dropdown="dropdown">' +
                '<a data-toggle="dropdown" class="btn btn-small btn-info dropdown-toggle options">' +
                '<span class="caret"></span></a>' +
                '<ul class="dropdown-menu">' +
                '<li><a href="/download/' + data.files[i].id + '" data-backdrop="static"><i class="icon-download"></i> Download</a></li>' +
                '<li><a href="#" onclick="partagerfichier(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="partagerfichier" data-backdrop="static"><i class="icon-share-alt"></i> Share</a></li>' +
                '<li><a href="#" onclick="qrcode(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="qrcodefichier" data-backdrop="static"><i class="icon-qrcode"></i> Download link</a></li>' +
                '<li><a href="#" onclick="renommer(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="renommerfichier" data-backdrop="static"><i class="icon-pencil"></i> Rename</a></li>' +
                '<li><a href="#" onclick="deplacer(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="deplacerfichier" data-backdrop="static"><i class="icon-move"></i> Move to...</a></li>' +
                '<li class="divider"></li><li><a href="#" onclick="supprimer(\'' + data.files[i].id + '\', \'' + data.files[i].name + '\')" data-controls-modal="supprimerfichier" data-backdrop="static"><i class="icon-remove"></i> Remove</a></li>' +
                '</ul>' +
                '</li>';

            td5.innerHTML = hash;

            /* Prévisualisation */
            if (data.files[i].mimetype.indexOf("audio") != -1) {
                td.setAttribute('onclick', 'lire(' + data.files[i].id + ')');
                td.setAttribute('data-backdrop', 'static');
                td2.setAttribute('onclick', 'lire(' + data.files[i].id + ')');
                td2.setAttribute('data-backdrop', 'static');
                td3.setAttribute('onclick', 'lire(' + data.files[i].id + ')');
                td3.setAttribute('data-backdrop', 'static');
                td4.setAttribute('onclick', 'lire(' + data.files[i].id + ')');
                td4.setAttribute('data-backdrop', 'static');
            }
            else if (data.files[i].mimetype.indexOf("video") != -1 || data.files[i].mimetype.indexOf("application/ogg") != -1) {
                td.setAttribute('onclick', 'video(' + data.files[i].id + ', "' + data.files[i].name + '")');
                td.setAttribute('data-backdrop', 'static');
                td.setAttribute('data-controls-modal', 'videofile');
                td2.setAttribute('onclick', 'video(' + data.files[i].id + ', "' + data.files[i].name + '")');
                td2.setAttribute('data-backdrop', 'static');
                td2.setAttribute('data-controls-modal', 'videofile');
                td3.setAttribute('onclick', 'video(' + data.files[i].id + ', "' + data.files[i].name + '")');
                td3.setAttribute('data-backdrop', 'static');
                td3.setAttribute('data-controls-modal', 'videofile');
                td4.setAttribute('onclick', 'video(' + data.files[i].id + ', "' + data.files[i].name + '")');
                td4.setAttribute('data-backdrop', 'static');
                td4.setAttribute('data-controls-modal', 'videofile');
            }
            else if (data.files[i].mimetype.indexOf("image") != -1) {
                td.setAttribute('data-backdrop', 'static');
                td.setAttribute('data-controls-modal', 'picture');
                td.setAttribute('onclick', 'diapo(' + nbImages + ')');
                td2.setAttribute('data-backdrop', 'static');
                td2.setAttribute('data-controls-modal', 'picture');
                td2.setAttribute('onclick', 'diapo(' + nbImages + ')');
                td3.setAttribute('data-backdrop', 'static');
                td3.setAttribute('data-controls-modal', 'picture');
                td3.setAttribute('onclick', 'diapo(' + nbImages + ')');
                td4.setAttribute('data-backdrop', 'static');
                td4.setAttribute('data-controls-modal', 'picture');
                td4.setAttribute('onclick', 'diapo(' + nbImages + ')');
                nbImages++;
            }
            else {
                td.setAttribute('onclick', 'document.location = "/download/' + data.files[i].id + '"');
                td2.setAttribute('onclick', 'document.location = "/download/' + data.files[i].id + '"');
                td3.setAttribute('onclick', 'document.location = "/download/' + data.files[i].id + '"');
                td4.setAttribute('onclick', 'document.location = "/download/' + data.files[i].id + '"');
            }

            a.appendChild(td);
            a.appendChild(td2);
            a.appendChild(td3);
            a.appendChild(td4);
            a.appendChild(td5);
            a.setAttribute('onmouseover', 'this.lastChild.style.display="inline-block"');
            a.setAttribute('onmouseout', 'this.lastChild.style.display="none"');

            space.appendChild(a);
        }
    }
    initSlide();
}

function loadFoldersList(id) {
    document.getElementById("parent").value = document.getElementById("parent").value + ',' + id;
    document.getElementById("folder").value = id;
    document.getElementById("files").innerHTML = "";
    displayList(id);
}

function backFolderList() {
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
    document.getElementById("files").innerHTML = "";
    displayList(parent);
}

function displayList(id) {
    document.getElementById("btnIcone").disabled = false;
    document.getElementById("btnListe").disabled = true;

    document.getElementById("top").innerHTML = '<table class="table table-stripped"><thead><tr><td id="icone"></td>' +
        '<td id="nom">Name</td>' +
        '<td>Modification date</td>' +
        '<td>Permission</td>' +
        '<td id="opt"></td>' +
        '</tr>' +
        '</thead>' +
        '<tbody id="files" style="font-size:13px">' +
        '</tbody></table>';

    document.cookie = 'display=0; expires=Fri, 01 Jan 2100 00:0:00 UTC; path=/'

    jQuery.post("/ajax/folder/getfolders", { folder:document.getElementById("folder").value },
        function (data) {
            displayFoldersList(data);
            jQuery.post("/ajax/file/getfiles", { folder:document.getElementById("folder").value },
                function (data) {
                    displayFilesList(data);
                }
                , "json"
            );
        }
        , "json"
    );
}