jQuery.noConflict();

jQuery(document).ready(
	function(){
		jQuery("#timeline-embed").delegate(
			".webPortfolioItemOuter",
			"click",
			function(){
				jQuery(this).find("a").each(
					function(i, el) {
						jQuery(el).attr("target", "_blank");
					}
				);
			}
		);
	}
)

var timeline_config_holder = document.getElementById("timeline-embed");
var timeline_config_holder_link = document.getElementById("timeline-link");
var timeline_config_link = timeline_config_holder_link.innerHTML;
var timeline_config = {
		width:              "100%",
		height:             "600",
		source:             timeline_config_link,
		start_at_end:       true,                          //OPTIONAL START AT LATEST DATE
//		start_at_slide:     '4',                            //OPTIONAL START AT SPECIFIC SLIDE
//		start_zoom_adjust:  '3',                            //OPTIONAL TWEAK THE DEFAULT ZOOM LEVEL
		hash_bookmark:      true,                           //OPTIONAL LOCATION BAR HASHES
		font:               'Bevan-PotanoSans',             //OPTIONAL FONT
		lang:               'en',                           //OPTIONAL LANGUAGE
//		maptype:            'watercolor',                   //OPTIONAL MAP STYLE
//		css:                'path_to_css/timeline.css',     //OPTIONAL PATH TO CSS
//		js:                 'path_to_js/timeline-min.js'    //OPTIONAL PATH TO JS
}

/**

        var timeline_config = {
            width:              "100%",
            height:             "600",
            source:             'path_to_json/or_link_to_googlespreadsheet',
            start_at_end:       false,                          //OPTIONAL START AT LATEST DATE
            start_at_slide:     '4',                            //OPTIONAL START AT SPECIFIC SLIDE
            start_zoom_adjust:  '3',                            //OPTIONAL TWEAK THE DEFAULT ZOOM LEVEL
            hash_bookmark:      true,                           //OPTIONAL LOCATION BAR HASHES
            font:               'Bevan-PotanoSans',             //OPTIONAL FONT
            lang:               'fr',                           //OPTIONAL LANGUAGE
            maptype:            'watercolor',                   //OPTIONAL MAP STYLE
            css:                'path_to_css/timeline.css',     //OPTIONAL PATH TO CSS
            js:                 'path_to_js/timeline-min.js'    //OPTIONAL PATH TO JS
        }
**/
