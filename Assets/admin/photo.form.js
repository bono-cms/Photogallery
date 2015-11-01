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
    
    if (jQuery().preview) {
        $("[name='file']").preview(function(data) {
            $("[data-image='preview']").fadeIn(1000).attr('src', data);
        });
    }
});