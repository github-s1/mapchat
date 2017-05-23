$(document).ready(function() {
    $(".openUl").click(function () {
        var elem = $(this);
        var text = elem.text();
        elem.prev("ul").toggleClass("opened");
        elem.toggleClass("closeUl").text(text == "Скрыть" ? "Показать больше >>" : "Скрыть");
    });

    //$( ".date_picker" ).datepicker({ dateFormat: 'yy-mm-dd' });
    $( ".date_picker" ).datetimepicker({ 	
        dateFormat: 'yy-mm-dd',
        timeOnlyTitle: 'Выберите время',
        timeText: 'Время',
        hourText: 'Часы',
        minuteText: 'Минуты',
        secondText: 'Секунды',
        currentText: 'Сейчас',
        showOn: "button",
        buttonImage: "/img/calendar.png",
        buttonImageOnly: true,
        closeText: 'Закрыть'
    });
	
    $( ".datepicker" ).datepicker({
        showOn: "button",
        buttonImage: "/img/calendar.png",
        buttonImageOnly: true
    });

    $( ".tabs li a.active" ).parent().addClass("active");
    $( ".tabs li a" ).click(function () {
        var changed = $( ".tabs li.active" );
        changed.removeClass("active");
        $(this).parent().addClass("active");
    });

    $(".pop_wrapper").css( "margin-top", function () {
    	var wrHeight = $(this).height(),
            docHeight = $(window).height(),
            itog = docHeight / 2 - (wrHeight / 2);
    	return itog;
    });
	/*  
    $('.popup').click(function(){
        popup($(this).attr('href'), $(this).attr('data-title'), $(this).attr('data-css_class'));
        return false;
    });
	*/
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

    $(".img_eye").fancybox({
        openEffect  : 'none',
        closeEffect : 'none'
    });
	
    $(function() {
    	$( "#tabsNew" ).tabs();
    });

    $('.info').click(function () {
        var divToggle = $(this).next();
        divToggle.slideToggle(0);
        $(this).toggleClass('active');
    });
});

function checkUl (idUl) {
    var liCount = $("#"+idUl);
    if (liCount.find("> li").length <= 2) {
        $(liCount).parent("div").find(".openUl").css("display", "none");
    }
    else {
        $(liCount).parent("div").find(".openUl").css("display", "block");
    };
};

function popup(url, title, css_class){
    $('#popup, #shadow').remove();
    // $('body').append('<div id="shadow"/>');
    if(url ==''){
        return;
    }
    return $.ajax({
        url: url,
        type: 'get',
        success: function(data){
//            console.log(data);
            $('body').append('<div id="shadow"><div id="popup"><div class="pop_wrapper '+css_class+'"><h1>'+title+'<a href="javascript: void(0);" id="close" onclick="closePopup();"></a></h1><div id="popup_content">'+data+'</div></div></div></div>');

           // $('#popup #close').click(closePopup); 
            $(".pop_wrapper").css( "top", function (itog) {
                var wrHeight = $(this).height(),
                    docHeight = $(window).height(),
                    contentWidth = $( "#popup_content > div").width(),
                    pLeft = parseFloat($( "#popup_content > div").css( "padding-left" )),
                    pRight = parseFloat($( "#popup_content > div").css( "padding-right" )),
                    pAll = pLeft + pRight + contentWidth,
                    itog;
                    $(".pop_wrapper").css("width", pAll);
                if (wrHeight > docHeight) {
                    itog = 0;
                }
                else {
                    itog = docHeight / 2 - (wrHeight / 2);
                };
                return itog;
            });
            $("#popup").css( "left", function (itog) {
                var wrWidth = $(this).width(),
                    docWidth = $(window).width(),
                    itog = docWidth / 2 - (wrWidth / 2);
                return itog;
            });
            $("body").css("overflow", "hidden");
        },
        failure:function(){
            $('#popup, #shadow').remove();
            $("body").css("overflow", "visible");
        }
    });
}

function closePopup(){
    $('#shadow').fadeTo('slow',0,function(){$(this).remove();});
    $('#popup').fadeTo('slow',0,function(){$(this).remove();});
    $("body").css("overflow", "visible");
   // location.reload();
    return false;
}

