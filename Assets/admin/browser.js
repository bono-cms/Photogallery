$(function(){
	$("td img").elevateZoom({
		zoomWindowFadeIn: 500, 
		zoomWindowFadeOut: 500, 
		lensFadeIn: 500, 
		lensFadeOut: 500,
		easing : true
	});
	
	// Handle delete buttons
	$.delete({
		categories : {
			photo : {
				url : "/admin/module/photogallery/photo/delete"
			},
			category : {
				url : "/admin/module/photogallery/album/delete"
			}
		}
	});
});