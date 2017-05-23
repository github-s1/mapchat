/* 
 * Модульдля работы с сообщениями
 */

var dbHandler = require("./dbHandler");
var _ = require('underscore');

var Message = {
    /**
     * Вставить сообщение в БД
     */
    insertMessage: function(message, callback, dataUser, cityId) {
        
        if(message.content.trim() == '') {
            return;
        }
        
        var query = "INSERT INTO `messages` SET ?";

        var params = {
            date_create: message.date_create,
            user_id: dataUser.id,
            id_city: cityId,
            content: message.content
        };
        // Добавляем сообщение в БД
        // В случае успешного добавления - возвращаем добавленное собщения (с last_inset_id)
        dbHandler.insert(query, function(err, insertId) {
            if (err) {
                callback(err.message);
                return;
            }
            message.id = insertId;
            message.user_id = parseInt(message.user_id);
            callback(null, message);
        }, params);
    }
}

module.exports = Message;

