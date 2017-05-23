/* 
 * Модуль для работы с приватными сообщениями
 */

var dbHandler = require("./dbHandler");

var Private = {
    
    /**
     * Получить список людей, которые состоят в приватной переписке с пользователем
     */
    getPrivateList : function(userId, callback, cityId) {
        var countNew = "(SELECT COUNT(pm1.id) FROM `private_message` AS pm1 WHERE `to_id`=" + userId + " AND pm1.from_id=p.user_id AND pm1.status='new' GROUP BY pm1.from_id) count";
        var query = "SELECT p.user_id AS from_id, p.`status`, " + countNew + ", IF(u.name IS NULL, u.login, u.name) AS `login`" + 
                "FROM (SELECT *, IF (`from_id`=" + userId + ", `to_id`, `from_id`) AS `user_id` " +
                "FROM `private_message` WHERE (`from_id`=" + userId + " OR `to_id`=" + userId + ") ) AS p " +
                "JOIN `users` AS u ON u.id=p.`user_id` " +
                " GROUP BY p.`user_id`";

        var params = {user_id : userId};
        dbHandler.getAll(query, function(err, res){
            if (err) {
                callback(err);
            }

            callback({privateList:res});
        }, params);
    },
    
    getCountNewPrivate: function(userId, callback) {
        var query = "SELECT  count(distinct from_id) as countNew FROM `private_message` WHERE to_id=? AND `status`='new'";
         var params = [userId];

         dbHandler.getRow(query, function(err, res){
            if (err) {
                return callback(err);
            }
            callback(res.countNew);
        }, params);
    },
    
    /**
     * Установить смс как прочитанные
     */
    setReaded : function(to_id, from_id) {
        var query = "UPDATE `private_message` SET status=? WHERE to_id=? AND from_id=?";

        var params = ['read', to_id, from_id];
        // Добавляем сообщение в БД
        // В случае успешного добавления - возвращаем добавленное собщения (с last_inset_id)
        dbHandler.execute(query, function(err, insertId) {
            if (err) {
                //callback(err.message);
                return;
            }
            
        }, params);
    }
};
/*SELECT from_id, count(pm.`from_id`), u.login
FROM `private_message` AS pm 
JOIN `users` AS u ON u.id=pm.`from_id`
WHERE `to_id`=176 
group by pm.`from_id`*/
module.exports = Private;

function getIdsFromRes(res, userId) {
    var list = '';
    res.forEach(function(item){
        list += item.from_id + ',';
    });
    if (list == '') return userId;
    //return list.slice(0, -1);
    return list + userId;
}

function removeEmptyMessage(list) {
    var result = [];
    list.forEach(function(item){
        if (item.text != '') result.push(item);
    });
    return result;
}