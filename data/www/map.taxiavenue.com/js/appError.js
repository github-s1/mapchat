/* 
 * 
 */
define(function(){
    "use strict";
    
    var AppError = function(err){
        
        /*
         *      ТИПЫ ОШИБОК:
         * YandexError      - Яндекс не дал результат
         * YandexParseError - не удалось разобрать данные Яндекса
         * ServerError      - сервер не дал ответ
         * ServerDataError  - сервер дал некорректный ответ
         * ServerEmptyData  - на сервере отсутствуют данные
         * ScriptError      - ошибка в работе скрипта
         * 
         */
        this.type = err.type;
        this.message = err.message;
        this.data = err.data || null;
        this.inner = err.inner || null;
        this.className = err.className || null;
        this.methodName = err.methodName || null;
    };
    AppError.prototype.constructor = AppError;
    
    AppError.prototype.toString = function(){
        return this.message || "Объект AppError. Сообщение об ошибке отсутствует";
    };
    
    return AppError;
});
