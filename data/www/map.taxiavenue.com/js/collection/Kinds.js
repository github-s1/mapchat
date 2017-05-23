define(function(require){    "use strict";        var Backbone = require('backbone'),        Config = require('config'),        Kind = require('model/Kind');        return Backbone.Collection.extend({        model: Kind,        city: null,        newKindIcon: Config.MarkIconPath + "new_icon.png",                createKinds: function(objKinds, objMarks, colIcons, colTypes, colThemes, cityPartOfUri){            var kinds = [];            for(var i in objKinds){                this.setIcon(objKinds[i], colIcons);                this.setType(objKinds[i], colTypes);                this.setTheme(objKinds[i], colThemes);                                var countOfMarks = this.getCountOfMarks(objKinds[i].id, objMarks);                objKinds[i].countOfMarks = countOfMarks;                                objKinds[i].cityPartOfUri = (typeof objKinds[i].city_name_en != 'undefined') ? objKinds[i].city_name_en : cityPartOfUri;                kinds.push(objKinds[i]);            }            this.set(kinds);        },                createKindsWithoutMarks: function(objKinds, collectionIcons, collectionTypes, collectionThemes){            var kinds = [];            for(var i in objKinds){                this.setIcon(objKinds[i], collectionIcons);                this.setType(objKinds[i], collectionTypes);                this.setTheme(objKinds[i], collectionThemes);                                kinds.push(objKinds[i]);            }            this.set(kinds);        },                getCountOfMarks: function(kindId, objMarks) {            var count = 0;            for (var i = 0; i < Object.keys(objMarks).length; i++) {                if (typeof objMarks[i] == 'undefined') continue;                if(objMarks[i].id_kind === kindId){                    count++;                }            }            /*objMarks.forEach(function(mark){                if(mark.id_kind === kindId){                    count++;                }            });*/            return count;        },                setIcon: function(kind, collectionIcons) {            var icon = collectionIcons.get(kind.id_icon);            if(icon){                kind.icon = icon;            }        },                setType: function(kind, collectionTypes) {            var type = collectionTypes.get(kind.id_type);            if(type){                kind.type = type;            }        },                setTheme: function(kind, collectionThemes) {            var theme = collectionThemes.get(kind.id_theme);            if(theme){                kind.theme = theme;            }        }    });});