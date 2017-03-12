$(document).ready(function(){

    console.log(config.parentURL);
    $(".reader--chapters-list").on("change", function(){
        window.location.href = config.parentURL + this.value + "/1/";
    });
    $(".reader--pages-list").on("change", function(){
        window.location.href = config.parentURL + config.currentChapter + "/" + this.value;
    });

})
