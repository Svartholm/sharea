<div id="partagerdossier" class="modal hide fade">
    <div class="modal-header">
        <a data-dismiss="modal" class="close" href="#">×</a>

        <h3>Share the folder</h3>
    </div>

    <div class="modal-body">
        <label class="control-label">Who can see the folder « <span id="foldershared"></span> »
            ?</label><br/>

        <div class="controls">
            <input id="folderid2" type="hidden" name="folder" value=""/>
            <ul style="display:table; text-align:center; margin-left:auto; margin-right:auto">
                <li onclick="chgPermFolder(document.getElementById('folderid2').value, <?php echo \lib\Permission::P_Public; ?>);"
                    class="permission main"><img src="/images/permissions/all.png" alt="public"/><br/><br/>Everybody
                </li>
                <li onclick="chgPermFolder(document.getElementById('folderid2').value, <?php echo \lib\Permission::P_Friends; ?>);"
                    class="permission main"><img src="/images/permissions/friends.png" alt="friends"/><br/><br/>Friends only
                    seulement
                </li>
                <li onclick="chgPermFolder(document.getElementById('folderid2').value, <?php echo \lib\Permission::P_Private; ?>);"
                    class="permission main"><img src="/images/permissions/private.png" alt="private"/><br/><br/>Me only
                </li>
            </ul>
        </div>
        <br/><br/>
        <input id="allfile" type="checkbox" name="allfile" value="true"> Apply this permission to all files in the folder
    </div>

    <script type="text/javascript">
        function actionEvent2(e) {
            if (e.keyCode == 27) {
                $('#partagerdossier').modal('hide');
            }
        }
        function chgPermFolder(id, perm) {
            jQuery.ajax({
                type:'POST',
                url:'/ajax/folder/permissions/edit',
                data:{
                    folder:id,
                    perm:perm,
                    allfile:document.getElementById('allfile').checked
                },
                success:function (data, textStatus, jqXHR) {
                    if (check(data, true, false) !== false) {
                    }
                }
            });
            $('#partagerdossier').modal('hide');
            if (document.cookie == null || readCookie("display") == 0) {
                displayList();
            }
            else {
                displayIcon();
            }
        }
    </script>
    <div class="modal-footer">
        <a data-dismiss="modal" class="close" href="#">Close</a>
    </div>
</div>
