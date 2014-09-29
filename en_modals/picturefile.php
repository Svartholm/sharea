<div id="picture" class="modal hide">
    <div class="modal-header">
        <a data-dismiss="modal" class="close" href="#">×</a>
        <h4>Diaporama</h4>
    </div>

    <div class="modal-body">
        <div id="carousel" class="carousel">
            <!-- Carousel items -->
            <div id="carousel-inner" class="carousel-inner">
            </div>

            <!-- Carousel nav -->
            <a class="carousel-control left" href="#left" onclick="diapoPrev()">&lsaquo;</a>
            <a class="carousel-control right" href="#right" onclick="diapoNext()">&rsaquo;</a>
        </div>
        <script type="text/javascript" src="/js/bootstrap-carousel.js"></script>
        <script type="text/javascript">

            var imgCourante;

            // On lance le carousel
            $(function () {
                $('.carousel').carousel({
                    interval:false
                });
                $('.carousel').carousel('next'); //Obligé pour init (ne pas demander pourquoi...)
            });

            // Si les flèches ou la touche Echap sont appuyées
            function actionEvent10(e) {
                if (e.keyCode == 37) {
                    diapoPrev();
                }
                else if (e.keyCode == 39) {
                    diapoNext();
                }
                else if (e.keyCode == 27) {
                    $('#picture').modal('hide')
                }
            }

            function initSlide() {
                for (i = 0; i < Images.length; i++) {
                    var div = document.createElement("div");
                    div.setAttribute("class", "item");
                    div.innerHTML = "<img class=\"imgDiapo\" id=\"item" + i + "\" src=\"\"><div class=\"carousel-caption\"><h4>" + Images[i][1] + "</h4></div>";
                    document.getElementById("carousel-inner").appendChild(div);
                }
            }
            // Place la diapo à un indice donnée, et charge l'image
            function diapo(nbre) {
                document.onkeydown = actionEvent10;
                document.getElementById("item0").src = "/download/" + Images[0][0];
                imgCourante = nbre;
                document.getElementById("item" + nbre).src = "/download/" + Images[nbre][0];
                $('.carousel').carousel(nbre);
            }

            // Si on veut la diapo précédente
            function diapoPrev() {
                // Si on était à la première image, on passe à la dernière
                if (imgCourante == 0) {
                    diapo(Images.length - 1);
                }
                else {
                    diapo(imgCourante - 1);
                }
            }

            // Si on veut la diapo suivante
            function diapoNext() {
                // Si on était à la dernière image, on repasse à la première
                if (imgCourante == (Images.length - 1)) {
                    diapo(0);
                }
                else {
                    diapo(imgCourante + 1);
                }
            }
        </script>
    </div>
</div>
