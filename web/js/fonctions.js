function renommerdossier(id, nom) {
    document.getElementById("folderid").value = id;
    document.getElementById("foldernewname").value = nom;
    document.onkeydown = actionEvent1;
}

function partagerdossier(id, nom, permission) {
    document.getElementById("folderid2").value = id;
    document.getElementById("foldershared").innerHTML = nom;
    document.onkeydown = actionEvent2;
}

function supprimerdossier(id, nom) {
    document.getElementById("foldertoremove").value = id;
    document.getElementById("foldername").innerHTML = nom;
    document.onkeydown = actionEvent3;
}

function partagerfichier(id, nom) {
    document.getElementById("file").value = id;
    document.getElementById("fileshared").innerHTML = nom;
    document.onkeydown = actionEvent5;
}

function renommer(id, nom) {
    document.getElementById("filetorename").value = id;
    document.getElementById("newname").value = nom;
    document.onkeydown = actionEvent6;
}

function supprimer(id, nom) {
    document.getElementById("filetoremove").value = id;
    document.getElementById("rname").innerHTML = nom;
    document.onkeydown = actionEvent9;
}

function qrcode(id, nom) {
    document.getElementById("qrname").innerHTML = nom;
    document.getElementById("imageqrcode").src = '/qrcode/' + id;
    document.getElementById("lien").value = 'http://sharea.net/download/' + id;
    document.onkeydown = actionEvent4;
}

function deplacer(id, nom) {
    document.getElementById("filetomove").value = id;
    document.getElementById("mfile").innerHTML = nom;
    document.onkeydown = actionEvent7;
}

function video(id, nom) {
    document.getElementById("videolink").src = '/download/' + id;
    document.getElementById("videoname").innerHTML = nom;
    document.onkeydown = actionEvent8;
}

function initPlayer() {
    var player = document.getElementById('player');
    player.setAttribute("data", "/dewplayer-rect.swf?mp3=/download/0&autoplay=1");
}

function lire(id) {
    $("#dropdown-music").show();
    var player = document.getElementById('player');
    player.setAttribute("data", "/dewplayer-rect.swf?mp3=/download/" + id + "&autoplay=1&showtime=true");
}

function createFolder()
{
    jQuery.ajax({
        type: 'POST',
        url: '/ajax/folder/create',
        data:
        {
            folder_name: document.getElementById("folder_name").value,
            parent: document.getElementById("folder").value

        },
        success: function(data, textStatus, jqXHR)
        {
            if(check(data, true, false) !== false)
            {
                if (document.cookie == null || readCookie("display") == 0) {
                    displayList();
                }
                else {
                    displayIcon();
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            alert("Error");
        }
    });
}