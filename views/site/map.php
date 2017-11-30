<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Map';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <div id="map"></div>
    <?php foreach ($markers as $marker) { ?>

        <?php echo $marker->lat ?>&nbsp;<?php echo $marker->lng ?><br/>

    <?php } ?>

    <script>

        function initMap() {
            var uluru = {lat: 49.991, lng: 36.231};

            var element = document.getElementById('map');
            var options = {
                zoom: 12,
                center: uluru
            };

            var myMap = new google.maps.Map(element, options);

            var markers = [
                <?php foreach ($markers as $marker){?>
                {
                    coordinates: {lat: <?php echo $marker->lat?>, lng: <?php echo $marker->lng?>},
                    info: '<div id="contentmarker">' +
                    '<div id="box">' +
                    '<div id="imagemarker"> <?php echo Html::img(Yii::getAlias('@web') . '/images/50/' . $marker->image);?></div>' +
                    '<div id="infokont">' +
                    '<b><p id="namekont"><?php echo $marker->name ?> <br/>' +
                    '<?php echo $marker->address ?></p></b>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                },
                <?php } ?>
            ];

            for (var i = 0; i < markers.length; i++) {
                addMarker(markers[i]);
            }

            function addMarker(properties) {

                var marker = new google.maps.Marker({
                    position: properties.coordinates,
                    map: myMap
                });
                if (properties.info) {
                    var InfoWindow = new google.maps.InfoWindow({
                        content: properties.info
                    });
                    marker.addListener('click', function () {
                        InfoWindow.open(myMap, marker);
                    })
                }
            }


        }
    </script>

    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRYQPKd03secUEZBQf32bmGkfNwIP1NJE&callback=initMap">
    </script>

    <code><?= __FILE__ ?></code>
</div>
