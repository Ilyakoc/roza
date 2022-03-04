<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 28.12.11
 * Time: 15:20
 * To change this template use File | Settings | File Templates.
 */
class GismapPluginDecorator extends PluginDecorator
{
    public $point = '{2gismap}';

    public function processModel($model, $attribute = 'text')
    {
        $result = $this->checkPoint($model->$attribute);

        if (!$result)
            return;

        $coors = $this->getCoors();

        $html = $this->mapHtml($coors);
        $model->$attribute = $this->replace($model->$attribute, $html);
    }

    private function mapHtml()
    {
        CmsHtml::js('http://maps.api.2gis.ru/1.0');

        ob_start();
        ?>
        <div id="my2GisMap" style="width:100%; height:400px" class="2gismap"></div>
        <script type="text/javascript">
            function addMarker(geoPoint, desc) {
                var balloon = new DG.Balloons.Common({
                    geoPoint: geoPoint,
                    contentHtml: desc
                });

                var marker = new DG.Markers.Common({
                    geoPoint: geoPoint,
                    clickCallback: function() {
                        balloon.show();
                    }
                });

                my2GisMap.markers.add(marker);
                my2GisMap.balloons.add(balloon);
            }

            function calculateCenter(markers) {
                var result = {lon: 0, lat: 0};
                $(markers).each(function(index, item) {
                    var coors = item.coors.split(',');
                    markers[index].lon = parseFloat(coors[0]);
                    markers[index].lat = parseFloat(coors[1]);
                    result.lon += markers[index].lon;
                    result.lat += markers[index].lat;
                });

                if (markers.length) {
                    result.lon = result.lon / markers.length;
                    result.lat = result.lat / markers.length;
                }

                return result;
            }

            var my2GisMap;
            var markers = [];

            DG.autoload(function() {
                var observers = [];
                markers = <?php echo CJavaScript::encode($this->getCoors()); ?>;
                var center  = calculateCenter(markers);

                my2GisMap = new DG.Map('my2GisMap');
                my2GisMap.setCenter(new DG.GeoPoint(center.lon ? center.lon : 82.927810142519, center.lat ? center.lat : 55.028936234826));
                my2GisMap.setZoom(<?php echo $this->getZoom(); ?>);
                my2GisMap.controls.add(new DG.Controls.Zoom());

                $(markers).each(function(index, item) {
                    var geoPoint = new DG.GeoPoint(item.lon, item.lat);
                    addMarker(geoPoint, item.desc);
                });
            });
        </script>
        <?php
        $content = ob_get_contents();
        ob_clean();

        return $content;
    }

    private function getCoors()
    {
        $markers = Yii::app()->settings->get('markers');
        $result  = array();

        if ($markers) {
            foreach($markers as $marker) {
                $result[] = json_decode($marker);
            }
        }

        return $result;
    }

    private function getZoom()
    {
        return Yii::app()->settings->get('mapParams', 'zoom');
    }
}
