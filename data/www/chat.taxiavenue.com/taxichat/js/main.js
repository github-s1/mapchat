$(function() {
    $( ".datepicker" ).datepicker({
        showOn: "button",
        buttonImage: "img/calendar.png",
        buttonImageOnly: true
    });

    $(".pop_wrapper").css( "margin-top", function (itog) {
    	var wrHeight = $(this).height(),
    		docHeight = $(window).height(),
    		itog = docHeight / 2 - (wrHeight / 2);
    	return itog;
    });

    $(function() {
        $( "#slider-micro" ).slider({
          range: "min",
          value: 50,
          min: 1,
          max: 100,
          animate: true,
          slide: function( event, ui ) {
            $( "#amount" ).val( "$" + ui.value );
          }
        });
        $( "#amount" ).val( "$" + $( "#slider-micro" ).slider( "value" ) );
      });
    $(".img_eye").fancybox({
      openEffect  : 'none',
      closeEffect : 'none'
    });
});