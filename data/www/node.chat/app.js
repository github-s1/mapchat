/**
 * Точка входа в приложение
 * Инициализация необходимых модулей
 * настройка сервера, инициализация глобальных переменных
 */

var config = require('./config');

var express = require('express');
var app = express();
var server = require('http').createServer(app).listen(config.get('port'));

// заглушка expressa, если заходить через http
app.get('/', function(req, res){
    res.end();
});

app.set('env', config.get("mode"));
app.set('port', config.get("port"));

require('./socket')(server);


