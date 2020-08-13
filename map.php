<!doctype html>
<html lang="de">

<head>
    <title>Karte | Seen in Oldenburg</title>
    <meta charset="utf-8">
    <meta name="description" content="Die schönsten Seen in Oldenburg zum genießen! Mit reiner Wasserqualität. " />
    <meta name="keywords"
        content="Seen, Oldenburg, Sommer, Sonnenbaden, Badesee, Schwimmen, Urlaub, Niedersachen, Deutschland, FKK" />
    <meta name="author" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="copyright" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <noscript><style> .jsRequired { display: none!important; } </style></noscript>

    <?php include 'header.php'; ?>

    

    <?php include 'banner.php'; ?>

    <script>
        var preset;
        function setSearch(str){
            preset = str;
        };
    </script>
    
    <?php 
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            echo '<script type="text/javascript">',
            'setSearch("',$search,'");',
            '</script>';
        }
    }
    ?>

    <div class="container my-3 jsRequired">
        <form class="row form-group" onsubmit="return false">
            <div class="input-group col-md-4 col-xs-12 text-center my-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
                </div>
                <input type="text" class="form-control mr-2" placeholder="Seename..."
                    aria-label="Seename..." aria-describedby="basic-addon1" onkeyup="filter_markers_name()" id="search">
            </div>

            <div class="input-group col-md-4 col-xs-12 text-center my-3">
                <select class="selectpicker" onchange="filterChanged.call(this, event)" id="filter" multiple>
                    <option value="bathinglake">Badesee</option>
                    <option value="fishinglake">Angelsee</option>
                    <option value="dogbeach">Hundestrand</option>
                    <option value="shower">WC / Duschen</option>
                    <option value="bbq">Grillen erlaubt</option>
                    <option value="wifi">WLAN</option>
                </select>
            </div>


        </form>

        <div id="map" style="width: 100%; height: 400px;"></div>

        <script>
        function initMap() {

            xmlhttp=new XMLHttpRequest();

            var myLatLng = {
             lat         :   53.14118,
             lng         :   8.21467
            };
            var mapOptions      =   {
                zoom            :   10,
                mapTypeControl  :   false,
                center          :   myLatLng,
            };
          
         map = new google.maps.Map(document.getElementById('map'), mapOptions);

            xmlhttp.onreadystatechange=function() {
                if (this.readyState==4 && this.status==200) {
                    var result = JSON.parse(this.responseText);
                    var locations = new Array();   
                    
                    result.forEach(function(element) {
                        var split = element["coordinates"].split(",");
                        var lat_ = split[0];
                        var lng_ = split[1];
                        var name_ = element["lakename"]
                        var keywords_ = {bathinglake:element["filter"].includes("Badesee"), fishinglake:element["filter"].includes("Angelsee"), dogbeach:element["filter"].includes("Hundestrand"), shower:element["filter"].includes("WC / Duschen"), bbq:element["filter"].includes("Grillen"), wifi:element["filter"].includes("WLAN")}
                        var content_ = "<a href=\"blog_detail.php?id="+element["id"]+"\" target=\"_blank\"><h5>"+element["lakename"]+"</h5></a>Bewertung: "+element["rating"]+"/5"+"<br>Filter: "+element["filter"];
                        var obj = {lat : lat_, lng : lng_, content : content_, marker : 'img/icons/lake.png', keywords : keywords_, name : name_};
                        locations.push(obj);
                    });
                                            
                    googleMaps(map, locations);
                }
            }
            xmlhttp.open("GET","blog_coordinates.php", true);
            xmlhttp.send();
         }

     var filters = []
     var markers = []
     var visible_markers = []
     
     function filterChanged(event){
        map_filter($('#filter').val());
        filter_markers();
     }

    var map_filter = function(selected_filter) {
        filters = selected_filter
    }

     var convert_filter = function(marker) {  
         var result = []
            if(marker.properties.bathinglake == true)
                result.push("bathinglake")
            if(marker.properties.fishinglake == true)
                result.push("fishinglake")
            if(marker.properties.dogbeach == true)
                result.push("dogbeach")
            if(marker.properties.shower == true)
                result.push("shower")
            if(marker.properties.bbq == true)
                result.push("bbq")
            if(marker.properties.wifi == true)
                result.push("wifi")
            return result
    }

    var count = 0

    function filter_markers(){
        for (i = 0; i < visible_markers.length; i++) {
            marker = visible_markers[i]
            if(filters.length > 0){
                if(containsAny(visible_markers[i].properties, filters)){
                    marker.setVisible(true);
                }else{
                marker.setVisible(false);
                }
            }else{
                marker.setVisible(true);
            }
        }
    }


    function filter_markers_name(){
        var input = document.getElementById('search');
        for (i = 0; i < markers.length; i++) {
            marker = markers[i]
            if(input.value.length > 0){
                document.getElementById("filter").value = "";
                $('#filter').selectpicker('refresh');
                if(!marker.name.includes(input.value.toLowerCase())){
                    marker.setVisible(false);
                    if(visible_markers.includes(marker))
                        visible_markers = arrayRemove(visible_markers,marker);
                }else{
                    if(!visible_markers.includes(marker))
                        visible_markers.push(marker);
                marker.setVisible(true);
                }
            }else{
                if(!visible_markers.includes(marker))
                        visible_markers.push(marker);
                marker.setVisible(true);
            }       
        }
        filter_markers();
    }

    function arrayRemove(arr, value) {
        return arr.filter(function(ele){
            return ele != value;
        });
    }

    function containsAny(haystack, arr){
        return arr.every(function (v) {
            return haystack.indexOf(v) >= 0;
        });
    };

    function setPresetSearch(){
        if(!(preset == null)){
            document.getElementById('search').value = preset;
            filter_markers_name();
        }
    };

     function googleMaps(map, locations) {
          var infowindow      =   new google.maps.InfoWindow();
          
          for (i = 0; i < locations.length; i++) { 
              marker = new google.maps.Marker({
                  position    :   new google.maps.LatLng(locations[i]['lat'], locations[i]['lng']),
                  map         :   map,
                  animation   :   google.maps.Animation.DROP,
                  icon        :   new google.maps.MarkerImage( locations[i]['marker'], null, null, null, new google.maps.Size(24, 24) ),
                  properties  :   locations[i]['keywords'],
                  name        :   locations[i]['name'].toLowerCase(),
              });
              marker.properties = convert_filter(marker)
              markers.push(marker);
              visible_markers.push(marker);

               
              google.maps.event.addListener(marker, 'click', (function(marker, i) {
                  return function() {
                      var content = locations[i]['content'];
                      infowindow.setContent(content);
                      infowindow.open(map, marker);
                  }
              })(marker, i));
          }
          setPresetSearch()
     }
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBunvE1R3oNYctjl80HnZVHu6PeZTC4eYE&callback=initMap"
        type="text/javascript"></script>

    </div>

    <noscript>
        <div class="container my-3">
            <p class="red-text center margin-top-bottom-05rem border-box-red">
                Bitte aktivieren Sie JavaScript, sonst können Sie GoogleMaps nicht nutzen!
            </p>
        </div>
    </noscript>
    

    <?php include 'footer.php'; ?>

    </body>

</html>