/**
 * Extend CartoDB Tooltip
 * Get Layer position
 *
 * @returns {number} Layer Position
 */
cdb.geo.ui.Tooltip.prototype.getLayerIndex = function () {
    return this.options.layer._position;
};

$(function(){

    /**
     * window.gw.map = {
     *  centerLatitude: Number
     *  centerLongitude: Number
     *  zoom: Number
     *  username: String
     * }
     */
    window.gw.map = JSON.parse(window.gw.map);

    //Create the leaflet map
    var map = L.map('map', {
        zoomControl: true,
        center: [window.gw.map.centerLatitude, window.gw.map.centerLongitude],
        zoom: window.gw.map.zoom
    });

    var basemap = L.tileLayer('http://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}.png', {
        attribution: 'GovWiki'
    }).addTo(map);

    // Empty layer
    cartodb.createLayer(map,{
        user_name: window.gw.map.username,
        type: 'cartodb',
        sublayers: []
    })
    .addTo(map)
    .done(function(layer){

        var subLayers = [];

        /**
         * Available layers
         */
        var countySubLayer, citySubLayer, schoolSubLayer, specialSubLayer;
        var tooltips, countyTooltip, cityTooltip, schoolTooltip, specialTooltip;

        /**
         * Create new SQL request
         */
        var sql = new cartodb.SQL({ user: window.gw.map.username });

        /**
         * SubLayers & tooltips initialization
         * Get unique altTypes and render new subLayers by them
         */
        sql.execute("SELECT alt_type_slug FROM " + window.gw.environment + " GROUP BY alt_type_slug")
            .done(function(altTypes) {

                initCountySubLayer();

                initSubLayers(altTypes);

                initLegendHandlers(altTypes);

                initTooltips();

                initSublayerHandlers();

                fixCartodbConstrain();

                loadFinished();

            })
            .error(function(errors) {
                return cartodbError(errors);
            });

        function cartodbError(errors)
        {
            $('.loader').hide();
            var $mapProcessing = $('.mapOnProcessing');
            $mapProcessing.find('h5').eq(0).text('Something went wrong, please contact with us (contact@californiapolicycenter.org) ');
            $mapProcessing.css({"opacity":1});
            $mapProcessing.show();

            console.log(errors);
        }

        /**
         * Create additional subLayers by altType
         *
         * @param altTypes Unique altTypes from MySQL
         */
        function initSubLayers(altTypes) {

            altTypes.rows.forEach(function(altType){

                switch (altType.alt_type_slug) {

                    case 'City':
                        initCitySubLayer(altType.alt_type_slug);
                        break;

                    case 'School_District':
                        initSchoolSubLayer(altType.alt_type_slug);
                        break;

                    case 'Special_District':
                        initSpecialSubLayer(altType.alt_type_slug);
                        break;

                }

            });

        }

        /**
         * Initialization County SubLayer
         *
         * Tooltip window
         * Tooltip work with 3.11-13 version, 3.15 is buggy
         */
        function initCountySubLayer() {
            // todo change california dataset at staging and change back
            var cLayer = {
                'cartocss': '#layer{polygon-fill:  #FF6600 ;polygon-opacity: 0.7;line-color: #FFF; line-width: 0.5; line-opacity: 1;}',
                'sql': 'SELECT *, ST_AsGeoJSON(ST_Simplify(the_geom,.01)) AS geometry FROM ' + window.gw.environment,
                'interactivity': ['cartodb_id', 'slug', 'geometry', 'data']
            };

            countySubLayer = layer.createSubLayer(cLayer);

            subLayers.push(countySubLayer);

            countyTooltip = new cdb.geo.ui.Tooltip({
                layer: countySubLayer,
                template: '<div class="cartodb-tooltip-content-wrapper"> <div class="cartodb-tooltip-content"><p>{{slug}}</p></div></div>',
                width: 200,
                position: 'bottom|right'
            });
        }

        /**
         * Initialization City SubLayer
         *
         * Tooltip window
         * Tooltip work with 3.11-13 version, 3.15 is buggy
         */
        function initCitySubLayer(altType) {

            citySubLayer = layer.createSubLayer({
                sql: "SELECT * FROM " + window.gw.environment + " WHERE alt_type_slug = '" + altType +"'",
                cartocss: "#layer { marker-fill: #f00000; }", // TODO: Hardcoded
                interactivity: 'cartodb_id, slug, alt_type_slug'
            });

            subLayers.push(citySubLayer);

            cityTooltip = new cdb.geo.ui.Tooltip({
                layer: citySubLayer,
                template: '<div class="cartodb-tooltip-content-wrapper"> <div class="cartodb-tooltip-content"><p>{{slug}}</p></div></div>',
                width: 200,
                position: 'bottom|right'
            });
        }

        /**
         * Initialization School SubLayer
         *
         * Tooltip window
         * Tooltip work with 3.11-13 version, 3.15 is buggy
         */
        function initSchoolSubLayer(altType) {

            schoolSubLayer = layer.createSubLayer({
                sql: "SELECT * FROM " + window.gw.environment + " WHERE alt_type_slug = '" + altType +"'",
                cartocss: "#layer { marker-fill: #add8e6; }", // TODO: Hardcoded
                interactivity: 'cartodb_id, slug, alt_type_slug'
            });

            subLayers.push(schoolSubLayer);

            schoolTooltip = new cdb.geo.ui.Tooltip({
                layer: schoolSubLayer,
                template: '<div class="cartodb-tooltip-content-wrapper"> <div class="cartodb-tooltip-content"><p>{{slug}}</p></div></div>',
                width: 200,
                position: 'bottom|right'
            });
        }

        /**
         * Initialization Special SubLayer
         *
         * Tooltip window
         * Tooltip work with 3.11-13 version, 3.15 is buggy
         */
        function initSpecialSubLayer(altType) {

            specialSubLayer = layer.createSubLayer({
                sql: "SELECT * FROM " + window.gw.environment + " WHERE alt_type_slug = '" + altType +"'",
                cartocss: "#layer { marker-fill: #800080; }", // TODO: Hardcoded
                interactivity: 'cartodb_id, slug, alt_type_slug'
            });

            subLayers.push(specialSubLayer);

            specialTooltip = new cdb.geo.ui.Tooltip({
                layer: specialSubLayer,
                template: '<div class="cartodb-tooltip-content-wrapper"> <div class="cartodb-tooltip-content"><p>{{slug}}</p></div></div>',
                width: 200,
                position: 'bottom|right'
            });
        }

        /**
         * Add tooltips on page
         * @type {*[]}
         */
        function initTooltips() {
            tooltips = [countyTooltip, cityTooltip, schoolTooltip, specialTooltip];

            tooltips.forEach(function (tooltip) {
                if (tooltip != null){
                    $('#map_wrap').append(tooltip.render().el);
                }
            });
        }

        /**
         * Move objectsPane above tilePane
         * It's necessary, otherwise county hover will not work
         */
        function fixCartodbConstrain() {

            var $objectsPane = $('.leaflet-objects-pane');
            var $tilePane = $('.leaflet-tile-pane');

            $objectsPane.appendTo($tilePane);
            $objectsPane.css({"z-index":"100"});
        }

        /**
         * Set handlers on SubLayers
         */
        function initSublayerHandlers() {

            var hovers = [];

            subLayers.forEach(function (layer) {

                // Allow events on layer
                layer.setInteraction(true);

                /**
                 * Show tooltip on hover
                 * Or highlight current county
                 * It depends on the current Layer position
                 */
                layer.bind('mouseover', function (e, latlon, pxPos, data, layerIndex) {

                    // TODO: Must be deleted, when data will be replaced, now it's hardcoded
                    data.slug = data.slug.replace(/_/g, ' ');

                    hovers[layerIndex] = 1;

                    /**
                     * If hover active
                     */
                    if (_.some(hovers)) {

                        $('.cartodb-map-wrapper').css('cursor', 'pointer');

                        /**
                         * If hover on county layer
                         */
                        if (layerIndex == countySubLayer._position) {
                            drawAppropriatePolygon(data);

                            // Printout received data.
                            console.log(data);
                        } else {
                            removeAllHoverShapes();
                        }

                        /**
                         * Open current tooltip, close another
                         */
                        tooltips.forEach(function (tooltip) {

                            if (tooltip != null) {

                                if (tooltip.getLayerIndex() == layerIndex) {
                                    tooltip.enable();
                                } else {
                                    tooltip.disable();
                                }

                            }

                        })

                    }

                });

                /**
                 * Hide tooltip on hover
                 * Or remove highlight on current county
                 * It depends on the current Layer position
                 */
                layer.bind('mouseout', function (layerIndex) {

                    hovers[layerIndex] = 0;

                    /**
                     * If hover not active
                     */
                    if (!_.some(hovers)) {
                        $('.cartodb-map-wrapper').css('cursor', 'auto');

                        removeAllHoverShapes();

                        /**
                         *  Close all tooltips, if cursor outside of layers
                         */
                        tooltips.forEach(function (tooltip) {

                            if (tooltip != null) {

                                if (tooltip.getLayerIndex() == layerIndex) {
                                    tooltip.disable();
                                }

                            }

                        })

                    }
                });

                /**
                 * Change window location after click on marker or county
                 */
                layer.on('featureClick', function (event, latlng, pos, data, layerIndex) {

                    if(layerIndex == 0) {
                        data.alt_type_slug = 'County';
                    }

                    if (!data.alt_type_slug || !data.slug) {
                        alert('Please verify your data, altTypeSlug or governmentSlug may can not defined, more info in console.log');
                        console.log(data);
                        return false;
                    }

                    /**
                     * TODO: Hardcoded, data must be in underscore style
                     */
                    data.slug = data.slug.replace(/ /g, '_');

                    window.location.pathname += data.alt_type_slug + '/' + data.slug;
                });

            });

        }

        /**
         * Toggle layers
         */
        function initLegendHandlers(altTypes) {
            // TODO generate legend on fly from given altTypes
            var $legend = $('.legend-item');
            /*
                Add new elements.
             */
            //altTypes.forEach(function(altType) {
            //    /**
            //     * Span eleem
            //     * @type {HTMLElement}
            //     */
            //    var okSymbol = document.createElement('span');
            //    okSymbol.className = 'glyphicon glyphicon-ok';
            //
            //    var element = document.createElement('li');
            //
            //    switch (altType) {
            //        case 'county':
            //        case 'minicipio':
            //
            //    }
            //});

            $legend.click(function() {
                $(this).toggleClass('selected');
                LayerActions[$(this).attr('id')]();
            });

            var LayerActions = {
                counties: function(){
                    countySubLayer.toggle();
                    return true;
                },
                cities: function(){
                    citySubLayer.toggle();
                    return true;
                },
                school: function(){
                    schoolSubLayer.toggle();
                    return true;
                },
                special: function(){
                    specialSubLayer.toggle();
                    return true;
                }
            };

        }

        /**
         * Show map, legend, hide loader
         */
        function loadFinished() {
            $('#map').css({"opacity": 1});
            $('#menu').css({"opacity": 1});
            $('.loader').hide();
        }

        // Polygon variables and functions
        var polygon = {};
        // What should our polygon hover style look like?
        var polygon_style = {
            color: "#808080",
            weight: 1,
            opacity: 1,
            fillOpacity: .45,
            fillColor: "#00FF00",
            clickable: false
        };

        function drawAppropriatePolygon(data){
            removeAllHoverShapes();
            polygon = new L.GeoJSON(JSON.parse(data.geometry), {
                style: polygon_style
            });
            map.addLayer(polygon);
            polygon.cartodb_id = data.cartodb_id;
        }
        function removeAllHoverShapes(){
            map.removeLayer(polygon);
            polygon.cartodb_id = null;
        }

    });

});