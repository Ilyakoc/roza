<?php

CmsHtml::jquery();
CmsHtml::js($assets. '/js/tiny_mce/tiny_mce_popup.js');
CmsHtml::js('http://maps.api.2gis.ru/1.0');

?>
<style type="text/css">
    div.form .row {
        /*margin-bottom: 5px;*/
    }
    div.form .row textarea {
        height: 50px;
        display: block;
    }
    .removeMarker {
        margin-left: 20px;
    }
    #my2GisMap {
        margin-bottom: 10px;
    }
</style>
<div id="my2GisMap" style="width:100%; height:400px" class="2gismap"></div>

<script type="text/javascript">

    var updateContent = function() {
        var ed = tinyMCEPopup.editor, f = document.forms[0], nl = f.elements, args = {}, el;

        tinyMCEPopup.restoreSelection();

        el = ed.selection.getNode();

        var call_block = "{2gismap}";
        var content = ed.getContent();
        if (content.indexOf(call_block) > 0) {
            tinyMCEPopup.close();
        } else {
            ed.execCommand('mceInsertContent', false, tinyMCEPopup.editor.dom.createHTML('p', {}, call_block));
            tinyMCEPopup.close();
        }
    };

    DG.autoload(function() {
        var my2GisMap = GisMap.map = new DG.Map('my2GisMap');

        var isset_markers = <?php echo CJSON::encode($markers); ?>;
        var map_params = <?php echo CJSON::encode($params); ?>;

        var center = GisMap.calculateCenter(isset_markers);


        my2GisMap.setCenter(new DG.GeoPoint(center.lon > 0 ? center.lon : 82.927810142519, center.lat > 0 ? center.lat : 55.028936234826));
        my2GisMap.setZoom(map_params != null && map_params.zoom != undefined ? map_params.zoom : 14);
        my2GisMap.controls.add(new DG.Controls.Zoom());

        var observers = [];

        $(isset_markers).each(function(index, item) {
            var coors = item.coors.split(',');
            if (coors.length<=1) {
                return;
            }
            if (!coors[0].length || !coors[1].length) {
                return;
            }

            var geoPoint = new DG.GeoPoint(coors[0], coors[1]);
            GisMap.addMarker(geoPoint, item.desc, {isText: true, showBalloon: false});
        });

        var callback = function(evt){
            var geoPoint = evt.getGeoPoint();
            var content  = $('#markerHtml').clone();

            GisMap.addMarker(geoPoint, content);
        };
        observers[0] = my2GisMap.addEventListener(my2GisMap.getContainerId(), 'DgClick', callback);
    });

    var GisMap = {
        map : null,
        addMarker: function(geoPoint, content, params) {
            var options = {
                isText: false,
                showBalloon: true
            };

            if (params == undefined) params = {};

            if (params.isText != undefined)
                options.isText = params.isText;
            if (params.showBalloon != undefined)
                options.showBalloon = params.showBalloon;

            var balloon = new DG.Balloons.Common({
                geoPoint: geoPoint,
                contentHtml: options.isText ? content : $(content).html()
            });

            var marker = new DG.Markers.Common({
                geoPoint: geoPoint,
                clickCallback: function() {
                    var bid = balloon.getId();
                    balloon.show();
                }
            });

            this.map.markers.add(marker);
            this.map.balloons.add(balloon);

            if (!options.isText) {
                $('.balloon_id', $(content)).val(balloon.getId());
                $('.marker_id' , $(content)).val(marker.getId());
                $('.marker_coors', $(content)).val(geoPoint.lon+','+geoPoint.lat);
            }

            balloon.setContent(options.isText ? this.viewForm(content, marker.getId(), balloon.getId()) : $(content).html());

            if (!options.showBalloon) {
                balloon.hide();
            }
        },
        addBalloon: function() {

        },
        getMarker: function(id) {
            return this.map.markers.get(id);
        },
        getBalloon: function(id) {
            return this.map.balloons.get(id);
        },
        removeMarker: function(id, full) {
            if (full == undefined){
                full = false;
            }

            var self = this;
            var marker = this.getMarker(id);

            if (full) {
                $.post('<?php echo $this->createUrl('GisMapRemoveMarker'); ?>',
                    {coors: marker.lonlat.getLon()+','+marker.lonlat.getLat()},
                    function(data) {
                        console.log(data);
                    }
                );
            }
            self.map.markers.remove(marker);
        },
        removeBalloon: function(id) {
            //console.log('bid:'+id);
            //var balloon = this.getBalloon(id);
            this.map.balloons.remove(id);
        },
        removeAllMarkers: function() {
            this.map.markers.removeAll();
            this.map.balloons.removeAll();
        },
        viewForm: function(desc, mid, bid) {
            var params = 'mid="'+ mid +'" bid="'+ bid +'"';

            return desc
                + '<p>'
                + '<a href="#" class="editMarker" '+ params +'>Редактировать</a>'
                + '<a href="#" class="removeMarker" '+ params +'>Удалить</a>'
                + '</p>';
        },
        editForm: function(mid, bid, desc, saved) {
            var geoPoint = this.getMarker(mid).getPosition();

            var content = $('#markerHtml').clone();
            var text = $(desc).find('.editMarker').parent().html();
            desc = desc.replace('<p>'+text+'</p>', '');

            $('.balloon_id', $(content)).val(bid);
            $('.marker_id' , $(content)).val(mid);
            $('.marker_description', $(content)).text(desc);
            $('.marker_coors', $(content)).val(geoPoint.getLon()+','+geoPoint.getLat());

            if (saved)
                $('.saved', $(content)).val(true);

            return $(content).html();
        },
        checkAll: function() {
            console.log(this.map.balloons.getAll());
            console.log(this.map.markers.getAll());
        },
        calculateCenter: function (markers) {
            var result = {lon: 0, lat: 0};

            if (markers.length) {
                $(markers).each(function(index, item) {
                    var coors = item.coors.split(',');
                    result.lon += parseFloat(coors[0]);
                    result.lat += parseFloat(coors[1]);
                });

                result.lon = result.lon / markers.length;
                result.lat = result.lat / markers.length;
            }

            return result;
        },
        zoom: function() {
            return this.map.getZoom();
        }
    };

    $(function() {
        $('body .markerForm').live('submit', function(e) {
            var t = this;
            e.preventDefault();

            $.post($(t).attr('action'), $(t).serialize(), function(data) {
                if (data.result == 'ok') {
                    var balloon = GisMap.getBalloon(data.balloon_id);
                    var marker  = GisMap.getMarker(data.marker_id);

                    balloon.setContent(GisMap.viewForm(data.text, data.marker_id,data.balloon_id));
                }
            }, 'json');
        });

        $('body .removeMarker').live('click', function() {
            var t = this, mid = false, bid = false, form = false;
            if ($(t).attr('mid') == undefined) {
                mid = $(t).parents('form').find('.marker_id').val();
                bid = $(t).parents('form').find('.balloon_id').val();
                //var saved =
                form = $(t).parents('form').find('.saved').val() != 'true';
            } else {
                mid = $(t).attr('mid');
                bid = $(t).attr('bid');
            }

            if (!mid || !bid)
                return;

            GisMap.removeMarker(mid, !form);
            GisMap.removeBalloon(bid);

            //window.location.reload();
        });

        $('body .editMarker').live('click', function(){
            var t = this;

            var mid = $(t).attr('mid');
            var bid = $(t).attr('bid');

            var balloon = GisMap.getBalloon(bid);
            var content = GisMap.editForm(mid, bid, balloon.getContent(), true);
            balloon.setContent(content);

            var width = $('#markerHtml').width();
            var height = $('#markerHtml').height();
            balloon.setSize(new DG.Size(width, height+20));
        });

        $('#mapForm').submit(function(e) {
            e.preventDefault();

            var post = {
                mapParams: {
                    zoom: GisMap.zoom()
                }
            };

            $.post('<?php echo $this->createUrl('saveMapParams') ?>', post, function() {
                updateContent();
            });
        });
    });
</script>

<form id="mapForm" action="#">
    <div class="mapControlPanel">
        <!--label for="zoom">Увеличение</label>
        <select name="zoom" id="zoom" onchange="GisMap.zoom(this.value);">
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="15">16</option>
        </select-->
        <!--a href="#" onclick="GisMap.removeAllMarkers(); return false;">Удалить все</a-->
        <!--a href="#" onclick="GisMap.checkAll(); return false;">Проверить</a-->
    </div>

    <div class="mceActionPanel">
        <input type="submit" value="Вставить" name="insert" id="insert" />
        <input type="button" onclick="tinyMCEPopup.close();" value="Отменить" name="cancel" id="cancel" />
    </div>
</form>

<div style="display: none;" id="markerHtml">
    <div class="form">
        <form action="" method="post" class="markerForm">
            <div class="row">
                <textarea name="description" cols="30" placeholder="Описание" class="marker_description"></textarea>
            </div>
            <div class="row">
                <input type="submit" value="Сохранить">
                <a href="#" class="removeMarker">Удалить</a>
            </div>
            <input class="marker_coors" type="hidden" name="coors" value="" />
            <input class="marker_id" type="hidden" name="marker_id" value="" />
            <input class="balloon_id" type="hidden" name="balloon_id" value="" />
            <input class="saved" type="hidden" value="false" >
        </form>
    </div>
</div>
