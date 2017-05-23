//Отрисовка карт и автозаполнение
$(document).ready(function() {
    
    $("a.edit, a.add_button").click(function(e){
        e.preventDefault();
        var href = $(this).attr('href'), 
            title = $(this).attr('data-title'), 
            className = $(this).attr('data-css_class');
            
        $.when(popup(href, title, className)).then(function(){
            $("#geocomplete").geocomplete({
                map: "#map_canvas",
                details: "#addresses-form",
                types: ["geocode", "establishment"],
                markerOptions: {
                    draggable: true
                }
            });

            $("#geocomplete").trigger("geocode");

            $("#geocomplete").bind("geocode:dragged", function(event, latLng){
                $(".latitude").val(latLng.lat());
                $(".longitude").val(latLng.lng());
            });
        });
    });
    
    /*
    $("#geocomplete").geocomplete({
        map: "#map_canvas",
        details: "form ",
        markerOptions: {
            draggable: true
        }
    });
		
    $("#geocomplete").bind("geocode:dragged", function(event, latLng){
        $(".latitude").val(latLng.lat());
        $(".longitude").val(latLng.lng());
        $("#reset").show();
    });


    $("#reset").click(function(){
        $("#geocomplete").geocomplete("resetMarker");
        $("#reset").hide();
        return false;
    });

    $("#find").click(function(){
        $("#geocomplete").trigger("geocode");
    }).click();
    */
	
});
