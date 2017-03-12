$(document).ready(function(){

	// sticky sidebar
	if (typeof $('.widget-area, .content-area').theiaStickySidebar !== "undefined") {
		$('.main-content, .secondary-content').theiaStickySidebar({
		// Settings
		additionalMarginTop: 30,
		additionalMarginBottom: 30
		});
    };


	/*
	 *------------------------------------------------------------------------------
	 *	Headroom
	 *------------------------------------------------------------------------------
	 *
	 * Show and hide main menu when scrolling
	 *
	 */
	if (typeof Headroom !== "undefined") {
		// grab an element
		var myElement = document.querySelector(".header");
		// construct an instance of Headroom, passing the element
		var headroom  = new Headroom(myElement,
			{
				// vertical offset in px before element is first unpinned
				offset : 200,
				// or you can specify tolerance individually for up/down scroll
				tolerance : {
					up : 50,
					down : 30
				},
				classes : {
					// when element is initialised
					initial : "menu",
					// when scrolling up
					pinned : "menu--pinned",
					// when scrolling down
					unpinned : "menu--unpinned",
					// when above offset
					top : "menu--top",
					// when below offset
					notTop : "menu--not-top",
					// when at bottom of scoll area
					bottom : "menu--bottom",
					// when not at bottom of scroll area
					notBottom : "menu--not-bottom"
				}
			});
			// initialise headroom
			headroom.init();
	}


	// Menu toggle for small displays
	$(document).on("click", ".header--menu-toggle", function(){
		$(".header--menu.small-screen").toggleClass("is-visible");
		$(".header--menu-toggle").toggleClass("off on");
	})
	$(".has-child").prepend("<div class='has-child-icon'><i class='fa fa-chevron-down' aria-hidden='true'></i></div>");
	$(document).on("click", ".has-child-icon", function(){
		$(this).siblings('.header--sub-menu-warpper').toggle();
	})

/*
 *------------------------------------------------------------------------------
 *	Ajax Search
 *------------------------------------------------------------------------------
 *
 * Ajax search in the menu
 *
 */
function ajaxSearch() {
	var keywords = $(".js-search-input").val();
	console.log(keywords);
	$(".js-search-submit").addClass("js-searching");
	$(".js-search-submit").removeClass("js-search-submit");
	$(".js-search-results").addClass("js-show");
	$(".js-search-results").html("<div class='uk-text-center'><div uk-spinner></div></div>");

	$.ajax({
		url: config.ajaxUrl,
		type: "POST",
		dataType: "json",
		data: {
			"action": "ajaxSearch",
			"keywords": keywords
		},
	}).done(function(data){
		$(".js-searching").addClass("js-search-submit");
		$(".js-searching").removeClass("js-searching");
		$(".js-search-results").html(data.html);
		//console.log(data);
	})
}
var timeoutID = null;
$(".header").on("keyup input", ".js-search-input", function(e){
	if (this.value.length > 0) {
		clearTimeout(timeoutID);
		timeoutID = setTimeout(ajaxSearch.bind(undefined, e.target.value), 500);
	} else {
		clearTimeout(timeoutID);
		$(".js-search-results").html("");
	}
});

$(".header").on("click", ".header-search--close", function(e){
	e.preventDefault();
	$(".js-search-input").val("");
	$(".js-search-results").html("");
});



/*
 *------------------------------------------------------------------------------
 *	Switch between chapters and comments
 *------------------------------------------------------------------------------
 */
	function mangaToggler(action, putResultHere) {
		$(putResultHere).html("<div class='uk-text-center'><div uk-spinner></div></div>");
		$.ajax({
			url: config.ajaxUrl,
			type: "POST",
			dataType: "json",
			data: {
				"action": action,
				"pageID": config.pageID
			},
		}).done(function(data){
			if(data.success == true) {
				$(putResultHere).html(data.html);
			} else {
				$(".wm-message").addClass("error");
				$(".wm-message").html(data.message);
			}
		})
	}
	
	$(".manga--get-chapters").on("click", function(){
		mangaToggler("showChapters", ".manga--chapters-comments");
		$(".manga--get-comments").removeClass("js-active");
		$(".manga--get-chapters").addClass("js-active");
	});
	$(".manga--get-comments").on("click", function(){
		mangaToggler("showComments", ".manga--chapters-comments");
		$(".manga--get-chapters").removeClass("js-active");
		$(".manga--get-comments").addClass("js-active");
	});
	


/*------------------------------------------------------------------------------
 # Ajax for manga directory
------------------------------------------------------------------------------*/
	$(".directory--manga").on("click", ".js-manga-info.js-show", function(){
		var pageID = $(this).attr("data-id");
		$("#"+pageID+" .js-manga-info.js-show").removeClass("js-show");
		$.ajax({
			url: config.ajaxUrl,
			type: "POST",
			dataType: "json",
			data: {
				"action": "showInfo",
				"pageID": pageID
			},
		}).done(function(data){
			$("#"+pageID+" .js-manga-info").addClass("js-hide");
			$("#"+pageID+" .js-directory--ajax-content").html(data);
			$("#"+pageID+" .js-manga-info.js-hide .fa").replaceWith("<i class='fa fa-times-circle' aria-hidden='true'></i>");
			$("#"+pageID+".directory--manga").toggleClass("js-visible");
		})
	})

	$(".directory--manga").on("click", ".js-manga-info.js-hide", function(){
		var pageID = $(this).attr("data-id");
		$("#"+pageID+" .js-manga-info").addClass("js-show");
		$("#"+pageID+" .js-manga-info.js-hide").removeClass("js-hide");
		$("#"+pageID+" .js-directory--ajax-content").html("");
		$("#"+pageID+" .js-manga-info.js-show .fa").replaceWith("<i class='fa fa-info-circle' aria-hidden='true'></i>");
		$("#"+pageID+".directory--manga").toggleClass("js-visible");
	})


})
