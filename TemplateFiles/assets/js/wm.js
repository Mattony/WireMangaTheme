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
	var keywords = $(".header-search-input").val();
	$(".header-search-results").addClass("active");
	$(".header-search-results").html("<div class='uk-text-center'><div uk-spinner></div></div>");

	$.ajax({
		url: config.ajaxUrl,
		type: "POST",
		dataType: "json",
		data: {
			"action": "ajaxSearch",
			"keywords": keywords
		},
	}).done(function(data){
		$(".header-search-results").html(data.html);
	});
}
$(document).on("click", ".header-search-toggle", function() {
	$(".header-search").toggleClass("active");
	$(".header-search .fa").toggleClass("fa-search fa-close");
	$(".header-search-input").focus();
})

var timeoutID = null;
$(".header").on("keyup input", ".header-search-input", function(e){
	if (this.value.length > 3) {
		timeoutID = setTimeout(ajaxSearch.bind(undefined, e.target.value), 500);
	} else {
		clearTimeout(timeoutID);
		$(".header-search-results").html("");
	}
});

$(".header").on("click", ".header-search-close, .fa-close", function(e){
	e.preventDefault();
	$(".header-search-input").val("");
	$(".header-search-results").html("");
	$(".header-search-results").removeClass("active");
});


//Switch between chapters and comments
function mangaToggler(action, putResultHere, hideThis) {
	if(!$(putResultHere).hasClass("loaded")) {
		var content = $(putResultHere).html();
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
				$(hideThis).removeClass("active");
				$(putResultHere).addClass("loaded active");
				$(putResultHere).html(content+data.html);
			} else {
				$(".wm-message").addClass("error");
				$(".wm-message").html(data.message);
			}
		});
	} else {
		$(putResultHere).addClass("active");
		$(hideThis).removeClass("active");
	}

}

$(".manga-get-chapters").on("click", function(){
	mangaToggler("showChapters", ".manga-chapters", ".manga-comments");
	$(".manga-get-comments").removeClass("active");
	$(".manga-get-chapters").addClass("active");
});
$(".manga-get-comments").on("click", function(){
	mangaToggler("showComments", ".manga-comments", ".manga-chapters");
	$(".manga-get-chapters").removeClass("active");
	$(".manga-get-comments").addClass("active");
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
