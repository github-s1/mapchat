/**
 * Модуль для работы с БД mysql
 */
var config = require('../config');
var mysql = require('mysql');
var dbConfig = config.get("db");

var pool  = mysql.createPool({
    host        : dbConfig.host,
    user        : dbConfig.user,
    password    : dbConfig.password,
    database    : dbConfig.database
});

var executeQuery = function(query, callback, success, params){
    if(params){
        query = mysql.format(query, params);
    }
    //console.log(query);
    pool.getConnection(function(err, connection) {
        if(err){
            callback(err);
            return;
        }

        connection.query(query, function(err, rows) {
            if (err) {
                callback(err);
            }
            else {
                success(rows);
            }
            connection.release();
        });
    });
};

module.exports = {

    getAll: function(query, callback, params){
        executeQuery(query, callback, function(rows){
            callback(null, rows);
        }, params);
    },

    getRow: function(query, callback, params){
        executeQuery(query, callback, function(rows){
            callback(null, rows[0]);
        }, params);
    },

    execute: function(query, callback, params){
        executeQuery(query, callback, function(rows){
            callback(null, rows.affectedRows);
        }, params);
    },

    insert: function(query, callback, params){
        executeQuery(query, callback, function(rows){
            callback(null, rows.insertId);
        }, params);
    }
};
