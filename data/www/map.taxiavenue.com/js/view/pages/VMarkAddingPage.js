/* 
 * 
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'), 
        _ = require('underscore'), 
        Backbone = require('backbone'),
        Mark = require('model/Mark'),
        AppError = require('appError'), 
        VNotice = require('view/VNotice'), 
        Config = require('config'),
        VSelectKind = require('view/VSelectKind');

    return Backbone.View.extend({

        pageName : 'markAdding',

        initialize: function(options) {
            this.parent = options.parent;
            this.model = this.parent.model;
            this.selfUser = this.model.selfUser;
            
            this.specifyType = options.specifyType;
            if (this.specifyType === true) {
                // При смене общего вида на конкретный остальная инициализация не нужна
                this.selectedKind = null;
                return;
            }

            this.parent.hideSidebar();
			
			//скрыть элементы управления
			$(".buttons").hide();
			
            //this.parent.router.navigate("");
            this.parent.createMapMain();
            
            this.map = this.parent.mainMap;

//            this.isStartPaint = false;

            this.selectedKind = null;
            this.listenTo(this.map, "map:click", this.clickMap);
            this.listenTo(this.map, "map:dblclick", this.dblclickMap);
        },
        
        create: function() {
            console.log("VMarkAddingPage/create: ???");
        },
        
        set: function() {
            console.log("VMarkAddingPage:set");
            this.selectKind();
        },
        
        clear: function() {
            console.log("VMarkAddingPage:clear");
            this.map.removeMarksFromMap();
            this.undelegateEvents();
            this.stopListening();
        },
        
        /**
         * Выбор типа значка (createDialog)
         */
        selectKind: function() {
            if(this.selectKindView){
                this.selectKindView.remove();
                delete this.selectKindView;
            }
            
            this.selectKindView = new VSelectKind({
                model: this.model, parent: this, specifyType: this.specifyType
            });
        },
        
        /**
         * Сохранение значка по клике на карту (если есть выбранный тип значка)
         */
        clickMap: function(data) {
            var type = this.selectedKind ? this.selectedKind.get("type").get("name_en") : null;
            if(!type){
                return;
            }
            else if(type === "Point"){
                var points = [{
                    lat: data.coords[0],
                    lng: data.coords[1],
                    order: 0
                }];
                this.save(points);
            }
            else {
                this.map.startEditing(this, data.coords);
            }
        },
        
        /**
         * Сохранение значка (point | line) в БД. this
         */
        save: function(points) {
            var self = this,
                mark = new Mark({
                    points: points,
                    id_kind: this.selectedKind.get("id")
                });

            mark.save(function(err, res){
                if(err){
                    Backbone.trigger("logger:error", err);
                }
                else {
                    var city = self.getCity(res);
                    console.log(city);
                    //if(city){
                        self.model.trigger("changePage", {
                            pageName: "mark", 
                            pageData: {
                                city: ((city) ? city.name_en : ''),
                                id: res.mark.id
                            }
                        });
                    /*}
                    else {
                        alert("Не удалось сохранить значок");
                        Backbone.trigger("logger:warning", new AppError({
                            type: "ServerDataError",
                            data: res,
                            className: "VMarkAddingPage",
                            methodName: "save",
                            message: "Не удалось разобрать местоположение метки"
                        }));
                    }*/
                    /*
                    if(res.city && _.isObject(res.city)){
                        self.model.trigger("changePage", {
                            pageName: "mark", 
                            pageData: {
                                city: res.city.name_en,
                                id: res.mark.id
                            }
                        });
                    }
                    else {
                        alert("Не удалось установить город для метки");
                    }
                    */
                }
            });
        },
        
        getCity: function(res) {

            var location = res.location;
            if(!location || !_.isObject(location)){
                return false;
            }
            
            var cities = location.cities, 
                regions = location.regions, 
                countries = location.countries;
            
            if(cities && _.isArray(cities)){
                return cities[0];
            }
            if(regions && _.isArray(regions)){
                return regions[0];
            }
            if(countries && _.isArray(countries)){
                return countries[0];
            }
        },
        
        stopEditing: function(coords) {
            console.log(coords);
            var points = [];
            for(var i = 0, length = coords.length; i < length; i++){
                points.push({
                    lat: coords[i][0],
                    lng: coords[i][1],
                    order: i
                });
            }
            this.save(points);
        },
        
        dblclickMap: function(coords) {
            console.log("dblclickMap: ");
            console.log(coords);
        },
        
        setPlace: function(kind) {
            this.selectedKind = kind;
//            this.selectKindView.close();
            
            var type = kind.get("type"),
                html = "";
			if(typeof(window.rcoords)!='undefined') { // добавление значка сразу, после выбора типа значка
				 //this.parent.model.trigger("map:click", { coords: window.rcoords });
				this.clickMap({ coords: window.rcoords });
				delete window.rcoords;
			} else
			{ 
				if(type.get("name_en") === "Point"){
					html = "Нажмите на карте в том месте, где Вы хотите<br>поставить ";
					html += "значок вида '" + kind.get("name_ru") + "'.";
				}
				else {
	//                html = "Нажмите на карте 1 раз для начала рисования,<br>";
	//                html += "двойное нажатие - окончание рисования.";
					html = "Нажмите на карте 1 раз для начала рисования.";
				}
				window.noticeAddMark = new VNotice({parent: this, html: html, autoHidden: false});
				
				$( ".closeNotice" ).click(function() {
				  $("#sidebar").removeClass("hide");$(".buttons").show();
				  $( ".closeNotice" ).off( "click" )
				  delete window.noticeAddMark;
				});
				// 
			}
        },
        
        /**
         * Изменить вид значка из общего
         */
        changeKind: function(kind) {
            var self = this;
            $.ajax({
                type: "POST",
                data: { 
                    id_mark: this.parent.page.model.get("id"),
                    id_kind: kind.get('id')
                },
                url: Config.baseUrlJSON + "kind_json/changeTypeKind"
            })
            .done(function(response){
                //console.log(response);
                //self.parent.page.model.set(response);
                //self.trigger('changeKind', response);
                location.reload();
            })
            .fail(function(err){
                Backbone.trigger("logger:error", new AppError({
                    inner: err,
                    className: "Mark",
                    type: "ServerDataError",
                    methodName: "changeKind"
                }));
            });
            //console.log(kind);
            //console.log(this.parent.page.model.attributes);
        },
        
        /*
        remove: function() {
            this.undelegateEvents();
            this.stopListening();
            this.$el.remove();
        }
        */
    });
});

