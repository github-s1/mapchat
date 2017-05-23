$( document ).ready(function() {
	jQuery.fn.justtext = function() {
		return $(this).clone().children().remove().end().text();
	};
/*--------------------------------------------------------------------------------------------------------------------------
	Построение графика
*/
	$(function () {
		$('#container').highcharts({
			chart: {
				zoomType: 'x'
			},
			title: {
				text: false
			},
			subtitle: {
				text: document.ontouchstart === undefined ?
						'Выделите область для увеличения' :
						'Pinch the chart to zoom in'
			},
			xAxis: {
				type: 'datetime',
				minRange: 14 * 24 * 3600000, // fourteen days
			},
			yAxis: {
				title: {
					text: false
				}
			},
			legend: {
				enabled: false
			},
			plotOptions: {
				area: {
					fillColor: {
						linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
						stops: [
							[0, Highcharts.getOptions().colors[0]],
							[1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
						]
					},
					marker: {
						radius: 2
					},
					lineWidth: 1,
					states: {
						hover: {
							lineWidth: 1
						}
					},
					threshold: null
				}
			},
			series: [{
				type: 'area',
				name: 'Заказов создано',
				pointInterval: 24 * 3600 * 1000 * 30,
				pointStart: Date.UTC(2006, 0, 1),
				data: []
			}]
		});
	});
/*--------------------------------------------------------------------------------------------------------------------------
	Передача данных в график при загрузке страницы
*/
	function getFirstChart (type) {
		var dateFrom = $("input:hidden[name='dateFrom']").val(),
			driver = $("input:hidden[name='driver_id']").val();
			if (driver == undefined) {
				driver = "";
			} else {
				driver = driver;
			};
		$.getJSON("http://chat.taxiavenue.com/admin/statistics/orderChart/?id_op=1&type="+type+"&date_from="+dateFrom+"&driver="+driver, function(json) {
			var JSONdata = [],
				chart = $('#container').highcharts();
			$.each(json, function(i,item) {
				JSONdata.push(item);
			});
			var strDate = dateFrom.split(".");
			chart.series[0].setData(JSONdata);
			chart.series[0].update({
				pointStart: Date.UTC(strDate[2], strDate[1] - 1, strDate[0])
			});
		});
	};
	getFirstChart(1);
/*--------------------------------------------------------------------------------------------------------------------------
	Функция обновления графика при смене radiobutton
*/
	$("input:radio[name='chartFilter']").change(function () {
		var type = $("input:checked:radio[name='chartFilter']").val(),
			chart = $('#container').highcharts(),
			active = $('.chartLink.active').attr("data-fn");
			if (type == 0) {
				chart.series[0].update({
					pointInterval: 24 * 3600 * 1000
				});				
			} else {
				chart.series[0].update({
					pointInterval: 24 * 3600 * 1000 * 30
				});	
			};
		getChart(active);
	});
});
/*--------------------------------------------------------------------------------------------------------------------------
	Функция обновления графика
*/
	function getChart (chart_id) {
		var dateFrom = $("input:text[name='filter[date_from]']").val(),
			chartFilter = $("input:checked:radio[name='chartFilter']").val(),
			driver = $("input:hidden[name='driver_id']").val();
			if (driver == undefined) {
				driver = "";
			} else {
				driver = driver;
			};
		$(".chartLink.active").removeClass("active");
		$(".chartLink[onclick='getChart("+chart_id+")']").addClass("active");
		$.getJSON("http://chat.taxiavenue.com/admin/statistics/orderChart/?id_op="+chart_id+"&date_from="+dateFrom+"&type="+chartFilter+"&driver="+driver, function(json) {
			var JSONdata = [],
				label = $(".chartLink.active").justtext(),
				chart = $('#container').highcharts();
			$.each(json, function(i,item) {
				JSONdata.push(item);
			});
			var strDate = dateFrom.split(".");
			chart.series[0].setData(JSONdata);
			chart.series[0].update({
				name: label,
				pointStart: Date.UTC(strDate[2], strDate[1] - 1, strDate[0])
			});
		});
	};