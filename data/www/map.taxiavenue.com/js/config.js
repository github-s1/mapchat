/* 
 * Конфигурация приложения
 * Если надо какие-то настройки переопределить в локальном окружении - создайте файл main.local.php
 */
define([
    'underscore',
    'config.local'
],function(_, loc){
    "use strict";

    var commonConfig =  {
        YMapNS                  : ymaps,
        YandexTranslateKey      : "trnsl.1.1.20140627T080013Z.5758668491fd5631.3d19252e4806c2c0dabcf296cd4076edb9bb9f67",
        mode                    : 'development',
        
        socketUrl               : baseUrl + ':' + socketPort + '/',
        
        baseUrl                 : baseUrl + '/',
        baseUrlJSON             : baseUrl + '/api/',

        ImgPath                 : baseUrl + '/img/',
        AvatarPath              : baseUrl + '/img/users_avatar/',
        SmallAvatarPath         : baseUrl + '/img/users_avatar/small/',
        AudioPath               : baseUrl + '/audio/',
        MarkIconPath            : baseUrl + '/img/mark_icons/',
        MarkPhotoPath           : baseUrl + '/img/mark_photos/'
    }
    return _.extend(commonConfig, loc);

});
