<script type="text/javascript">
    var leaflet_init = function(uid, tile_layer, attribution, $) {
        // only render the map if an api-key is present
        var api_key = <?php echo '"'.$field['api_key'].'"'; ?>;

        render_leaflet_map(uid);

        function render_leaflet_map(uid) {
            // Get the hidden input-field
            var field = $('#field_' + uid);

            window.map_settings[uid] = null;

            // check if we have a saved value
            if( field.val().length > 0 ) {
                window.map_settings[uid] = JSON.parse(field.val());
            }
            else {
                window.map_settings[uid] = {
                    zoom_level:null,
                    center:{
                        lat:null,
                        lng:null
                    },
                    markers:{}
                };
            }

            if( window.map_settings[uid].center.lat == null ) {
                window.map_settings[uid].center.lat = field.attr('data-lat');
            }

            if( window.map_settings[uid].center.lng == null ) {
                window.map_settings[uid].center.lng = field.attr('data-lng');
            }

            // check if the zoom level is set and within 1-18
            if( window.map_settings[uid].zoom_level == null || window.map_settings[uid].zoom_level > 18 || window.map_settings[uid].zoom_level < 1 ) {
                if( field.attr('data-zoom-level') > 0 && field.attr('data-zoom-level') < 19 ) {
                    window.map_settings[uid].zoom_level = field.attr('data-zoom-level');
                }
                else {
                    window.map_settings[uid].zoom_level = 13;
                }
            }

            window.maps[uid] = L.map( "map_" + uid, {
                center: new L.LatLng( window.map_settings[uid].center.lat, window.map_settings[uid].center.lng ),
                zoom: window.map_settings[uid].zoom_level,
                doubleClickZoom: false
            });

            L.tileLayer( tile_layer, {
                attribution: attribution,
                maxZoom: 18
            }).addTo(window.maps[uid]);

            var controlClick = function(e) {
                var uid = $(this).parents('.leaflet-map').attr('data-uid');

                if( $(this).hasClass('tool-reset') ) {
                    // TODO: Clear map and the field-value
                }
                else if( $(this).hasClass('tool-compass') ) {
                    // try to locate the user
                    window.maps[uid].locate();
                }
                else {
                    $('#leaflet_field-wrapper_' + uid).removeClass();
                    $('#leaflet_field-wrapper_' + uid).addClass($(this).attr('class').match(/\btool-(\w+)\b/)[0] + '-active');

                    $('#leaflet_field-wrapper_' + uid + ' .leaflet-map .tool.active').removeClass('active');
                    $(this).addClass('active');
                }
            };

            var addControl = function(toolName, icon, active) {
                active = (active) ? ' active' : '';
                var newControl = L.control({position: 'topleft'});

                newControl.onAdd = function(){
                    this._div = L.DomUtil.create('div', 'tool tool-' + toolName + ' icon-' + icon + active);

                    var stop = L.DomEvent.stopPropagation;

                    L.DomEvent
                        .on(this._div, 'click', stop)
                        .on(this._div, 'mousedown', stop)
                        .on(this._div, 'dblclick', stop)
                        .on(this._div, 'click', L.DomEvent.preventDefault)
                        .on(this._div, 'click', controlClick);

                    return this._div;
                };

                newControl.addTo(window.maps[uid]);
            }

            var controls = {
                locateControl: addControl('compass', 'compass'),
                pinControl: addControl('marker', 'location', true),
                tagControl: addControl('tag', 'comment-alt2-fill'),
                drawToolsControl: addControl('drawtools', 'share'),
                removeControl: addControl('remove', 'cancel-circle red'),
                //resetControl: addControl('reset', 'relaod'),
            };

            var editableLayer = new L.FeatureGroup();
            window.maps[uid].addLayer(editableLayer);

            var drawControl = new L.Control.Draw({
                position: 'topleft',
                draw: {
                    polyline: {
                        shapeOptions: {
                            color: '#000000'
                        }
                    },
                    polygon: {
                        shapeOptions: {
                            color: '#000000'
                        }
                    },
                    circle: false,
                    rectangle: {
                    shapeOptions: {
                            color: '#000000'
                        }
                    },
                    marker: false
                },
                edit: {
                    featureGroup: editableLayer
                }
            });



            window.maps[uid].addControl(drawControl);

            // render existing markers if we have any
            console.log(window.map_settings[uid]);
            if( Object.keys(window.map_settings[uid].markers).length > 0 || (window.map_settings[uid].drawnItems && window.map_settings[uid].drawnItems.features.length > 0) ) {
                var newMarkers = {};
                $.each(window.map_settings[uid].markers, function(index, marker) {
                    //var newMarker = L.marker(marker.geometry.coordinates, {draggable: true});
                    index = add_marker(marker);
                    marker.id = index;
                    newMarkers['m_' + index] = marker;
                });

                var geoJsonLayer = L.geoJson(window.map_settings[uid].drawnItems, {
                    onEachFeature: function(feature, layer) {
                        layer.options.color = "#000000";
                        editableLayer.addLayer(layer);
                    }
                }).addTo(window.maps[uid]);

                window.map_settings[uid].markers = newMarkers;
                update_field(uid);
            }

            window.maps[uid].on('click', function(e) {
                var active_tool = $('#leaflet_field-wrapper_' + uid + ' .tool.active');

                if( active_tool.hasClass('tool-marker') ) {
                    // the marker-tool is currently being used
                    //var marker = L.marker(e.latlng, {draggable: true});
                    var marker = {
                        "type": "Feature",
                        "properties": {
                            "popupContent": ""
                        },
                        "geometry": {
                            "type": "Point",
                            "coordinates": [e.latlng.lng, e.latlng.lat]
                        }
                    };

                    index = add_marker( marker );
                    window.map_settings[uid].markers['m_' + index] = marker;
                    window.map_settings[uid].markers['m_' + index].id = index;
                }

                update_field(uid);
            }).on('zoomend', function(e) {
                // the map was zoomed, update field
                update_field(uid);
            }).on('dragend', function(e) {
                // the map was dragged, update field
                update_field(uid);
            }).on('locationfound', function(e) {
                // users location was found, pan to the location and update field
                window.maps[uid].panTo(e.latlng);
                window.maps[uid].stopLocate();
                update_field(uid);
            }).on('locationerror', function(e) {
                // users location could not be found
                window.maps[uid].stopLocate();
            }).on('draw:created', function(e) {
                var type = e.layerType;
                var layer = e.layer;
                editableLayer.addLayer(layer);
                update_field(uid);
            }).on('draw:deleted', function(e){
                update_field(uid);
            }).on('draw:edited', function(e){
                update_field(uid);
            });

            function add_marker( marker ) {

                var geoJsonLayer = L.geoJson(marker, {
                    onEachFeature:function( feature, layer ) {
                        layer.options.draggable = true;
                        layer.options.riseOnHover = true;

                        layer.on('click', function(e) {
                            var active_tool = $('#leaflet_field-wrapper_' + uid + ' .tool.active');

                            if( active_tool.hasClass('tool-remove') ) {
                                delete window.map_settings[uid].markers['m_' + layer._leaflet_id];
                                window.maps[uid].removeLayer(layer);
                            }
                            else if( active_tool.hasClass('tool-tag') ) {
                                if( typeof window.map_settings[uid].markers['m_' + layer._leaflet_id].properties.popupContent == 'undefined' ) {
                                    content = '';
                                }
                                else {
                                    content = window.map_settings[uid].markers['m_' + layer._leaflet_id].properties.popupContent;
                                }

                                popup_html = '<textarea class="acf-leaflet-field-popup-textarea" data-marker-id="' + layer._leaflet_id + '" style="width:200px;height:125px;min-height:0;">' + content + '</textarea>';

                                if( typeof layer._popup == 'undefined' ) {
                                    // bind a popup to the marker
                                    layer.bindPopup(popup_html, {maxWidth: 300, maxHeight: 200}).openPopup();
                                }
                                else {
                                    layer._popup.setContent(popup_html);
                                }
                            }

                            update_field(uid);
                        }).on('dragend', function(e) {
                            newLatLng = e.target.getLatLng();
                            window.map_settings[uid].markers['m_' + e.target._leaflet_id].geometry.coordinates = [newLatLng.lng, newLatLng.lat];
                            //window.map_settings[uid].markers['m_' + e.target._leaflet_id].coords.lat = newLatLng.lat;
                            //window.map_settings[uid].markers['m_' + e.target._leaflet_id].coords.lng = newLatLng.lng;
                            update_field(uid);
                        });
                    }
                }).addTo(window.maps[uid]);

                return geoJsonLayer._layers[geoJsonLayer._leaflet_id-1]._leaflet_id;
            }

            function update_field(uid) {
                // update center and zoom-level
                var center = window.maps[uid].getCenter();
                window.map_settings[uid].center.lat = center.lat;
                window.map_settings[uid].center.lng = center.lng;
                window.map_settings[uid].zoom_level = window.maps[uid].getZoom();
                window.map_settings[uid].drawnItems = editableLayer.toGeoJSON();
                var field = $('#field_' + uid);
                field.val(JSON.stringify(window.map_settings[uid]));
            }

            /* Handle input inside popups */
            $(document).on('keyup', '.leaflet-map .acf-leaflet-field-popup-textarea', function(e){

                var uid = $(this).parents('.leaflet-map').attr('data-uid');
                var textarea = $(this);
                var marker_id = 'm_' + textarea.data('marker-id');
                window.map_settings[uid].markers[marker_id].properties.popupContent = textarea.val();

                if( textarea.val().length == 0 ) {
                    delete window.map_settings[uid].markers[marker_id].properties.popupContent;
                }

                update_field(uid);
            });
        }
    };

    if( typeof window.maps == 'undefined' ) {
        window.maps = {};
    }

    if( typeof window.map_settings == 'undefined' ) {
        window.map_settings = {};
    }

    function initialize_field( map ) {
        uid = map.attr('data-uid');

        if( typeof uid == 'undefined') {
            map = map.find('.leaflet-map');
            uid = map.attr('data-uid');
        }

        if( typeof window.maps[uid] == 'undefined' ) {
            window.maps[uid] = null;
            leaflet_init(uid, map.attr('data-tile-layer'), map.attr('data-attribution'), jQuery);
        }
    }

    if( typeof acf.add_action !== 'undefined' ) {

        acf.add_action('ready append', function( $el ) {

            // search $el for fields of type 'FIELD_NAME'
            acf.get_fields({ type : 'leaflet_field'}, $el).each(function() {

                initialize_field( jQuery(this) );

            });

        });

    }
    else {
        jQuery(document).live('acf/setup_fields', function(e, postbox){
            jQuery(postbox).find('.leaflet-map').each(function(){
                initialize_field( jQuery(this) );
            });
        });
    }
</script>