$(function(){
    // Make sure that plugin is loaded before applying it
    if (jQuery().elevateZoom) {
        $("div.col-lg-10 img").elevateZoom({
            zoomWindowFadeIn : 500, 
            zoomWindowFadeOut : 500, 
            lensFadeIn : 500, 
            lensFadeOut : 500,
            easing : true
        });
    }
});