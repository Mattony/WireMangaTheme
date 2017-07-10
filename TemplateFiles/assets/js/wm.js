$(document).ready(function() {

// Menu toggles
$(document).on("click", ".menu-toggle", function() {
	$(".menu").toggle(200);
});
$(document).on("click", ".submenu-toggle", function(e) {
	e.stopPropagation();
	$(this).toggleClass("active");
	var thisIcon = $(this).children(".fa");
	var thisSubWrap = $(this).siblings(".submenu");
	$(thisSubWrap).toggleClass("active");

	//$(".submenu").not(thisSubWrap).hide();
	$(".fa-minus").not(thisIcon).addClass("fa-plus").removeClass("fa-minus");

	thisIcon.toggleClass("fa-plus fa-minus");
	//thisSubWrap.toggle();
	console.log(thisSubWrap);
});

$(document).on("click", function () {
	//$(".submenu").hide();
	$(".fa-minus").addClass("fa-plus").removeClass("fa-minus");
});


/* #### AJAX #### */

/*
 * Manga Subscription
 *
 * (Un)Subscribe users to manga
 *
 */

$("form.subscribe").submit(function(e){
	e.preventDefault();
});
$("form.unsubscribe").submit(function(e){
	e.preventDefault();
});

$(document).on("click", ".manga-subscribe", function(){
	var mangaID = $(".manga-subscribe").data("manga-id") || config.pageID;
	var el = this;
	$.ajax({
		url: config.ajaxUrl,
		type: "POST",
		dataType: "json",
		data: {
			"action": "subscribe",
			"pageID": mangaID
		},
	}).done(function(data){
		$(el).addClass("manga-unsubscribe uk-button-danger").removeClass("manga-subscribe uk-button-primary");
		$(el).val("Unsubscribe");
	});
});

$(document).on("click", ".manga-unsubscribe", function(){
	var mangaID = $(".manga-unsubscribe").data("manga-id") || config.pageID;
	var el = this;
	$.ajax({
		url: config.ajaxUrl,
		type: "POST",
		dataType: "json",
		data: {
			"action": "unsubscribe",
			"pageID": mangaID
		},
	}).done(function(data){
		$(el).addClass("manga-subscribe uk-button-primary").removeClass("manga-unsubscribe uk-button-danger");
		$(el).val("Subscribe");
	});
});


// Ajax Search in the header
function ajaxSearch() {
	var keywords = $(".js-search-input").val();
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
	});
}

var timeoutID = null;
$(".header").on("keyup input", ".js-search-input", function(e){
	if (this.value.length > 3) {
		timeoutID = setTimeout(ajaxSearch.bind(undefined, e.target.value), 500);
	} else {
		clearTimeout(timeoutID);
		$(".js-search-results").html("");
	}
});

$(".header").on("click", ".header-search-close", function(e){
	e.preventDefault();
	$(".js-search-input").val("");
	$(".js-search-results").html("");
});


//Switch between chapters and comments
function mangaToggler(action, putResultHere, hideThis) {
	if($(putResultHere).is(":empty")) {
		$(hideThis).css("display", "none");
		$(putResultHere).html("<div class=''><div uk-spinner></div></div>");
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
		});
	} else {
		$(putResultHere).css("display", "block");
		$(hideThis).css("display", "none");
	}

}

$(".manga-get-chapters").on("click", function(){
	mangaToggler("showChapters", ".manga-chapters", ".manga-comments");
	$(".manga-get-comments").removeClass("tab-active");
	$(".manga-get-chapters").addClass("tab-active");
});
$(".manga-get-comments").on("click", function(){
	mangaToggler("showComments", ".manga-comments", ".manga-chapters");
	$(".manga-get-chapters").removeClass("tab-active");
	$(".manga-get-comments").addClass("tab-active");
});
	

//Ajax for manga directory
var cache = {};
$(document).on("click", ".js-hidden .js-toggle", function(){
	var pageID = $(this).attr("data-id");
	var selector = "#"+pageID;
	if(!cache[pageID]) {
		var ajax = $.ajax({
			url: config.ajaxUrl,
			type: "POST",
			dataType: "json",
			data: {
				"action": "showInfo",
				"pageID": pageID
			},
		}).done(function(data){
			cache[pageID] = true;
			$(selector).toggleClass("js-hidden js-visible");
			$(selector + " .dir-manga-content").html(data);
			$(selector + " .fa").toggleClass("fa-info-circle fa-times-circle");
		});
	} else {
		$(selector).toggleClass("js-hidden js-visible");
		$(selector + " .fa").toggleClass("fa-info-circle fa-times-circle");
	}
});

$(document).on("click", ".js-visible .js-toggle", function(){
	var selector = "#" + $(this).attr("data-id");
	$(selector).toggleClass("js-hidden js-visible");
	$(selector + " .fa").toggleClass("fa-info-circle fa-times-circle");
});

});
