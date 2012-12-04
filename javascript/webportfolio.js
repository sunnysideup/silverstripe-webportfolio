
 /**
 * @author Nicolaas [at] sunnysideup.co.nz
 *
 *
 *
 *
 *
 *
 */
jQuery.noConflict();
;(function($) {
	$(document).ready(
		function() {
			webportfolio.init();
		}
	);

	var webportfolio = {

		init: function() {
			jQuery(".webPortfolioMoreInfo").hide();
			jQuery(".webPortfolioShowMore").click(
				function(event) {
					var id = jQuery(this).attr("rel");
					jQuery("#" + id).slideToggle();
					return false;
				}
			);
			if(jQuery(".webPortfolioShowMore").length == 1) {
				window.setTimeout(
					function(){
						jQuery(".webPortfolioMoreInfo").slideDown(
							"slow",
							function() {
								jQuery(".screenshotPopup").click();
							}
						);
					}, 700
				);
			}
		}

	}
})(jQuery);
