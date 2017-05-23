define([
    'backbone'
],
function(Backbone){
    "use strict";
    
    return Backbone.Model.extend({
        system: {},
        params : 'left=100,top=100,height=350,width=600',
        baseUrl: 'map.taxiavenue.com',
        vk_enter: {
            api_url : 'http://api.vk.com/oauth/authorize',
            redirect_uri : 'http://map.taxiavenue.com/auth/openAuth/system/vkontakte',
            client_id: '4696521',
            makeUrl: function(baseUrl) {
                var url = this.api_url + '?';
                url += 'client_id=' + this.client_id;
                url += '&display=popup';
                url += '&scope=notify,friends,wall,notifications,email';
                url += '&redirect_uri=' + this.redirect_uri;
                url += '&response_type=code';
                url += '&v=5.27';
                return url;
            }
        },
        facebook_enter : {
            api_url: 'https://www.facebook.com/dialog/oauth',
            //redirect_uri: 'http://map.taxiavenue.com/auth/openAuth/system/facebook',
            redirect_uri : 'http://map.taxiavenue.com/auth/openAuth/system/facebook',
            client_id: '1537195736551607',
            makeUrl: function(baseUrl) {
                var url = this.api_url + '?';
                url += 'client_id=' + this.client_id;
                url += '&redirect_uri=' + this.redirect_uri;
                url += '&scope=email,user_photos';
                url += '&display=popup';
                return url;
            }
        },
        mail_ru_enter: {
            api_url: 'https://connect.mail.ru/oauth/authorize',
            //redirect_uri: 'http://map.taxiavenue.com/auth/openAuth/system/mail_ru',
            redirect_uri : 'http://map.taxiavenue.com/auth/openAuth/system/mailru',
            client_id: '728852',
            makeUrl: function(baseUrl) {
                var url = this.api_url + '?';
                url += 'client_id=' + this.client_id;
                url += '&redirect_uri=' + this.redirect_uri;
                url += '&response_type=code';
                return url;
            }
        },
        initialize: function(system_name) {
            this.system = this[system_name];
        },
        openDialog: function() {
            var url = this.system.makeUrl(this.baseUrl);
            window.open(url, 'loginSocial', this.params);
            //console.log(this.system.makeUrl(this.baseUrl));
        }
    });
});


