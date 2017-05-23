var nconf = require('nconf');
var path = require('path');

/**
 * Режим работы процесса
 * Доступные режимы - loc | dev
 */
var mode = process.argv[2];
if (mode !== 'loc' && mode !== 'dev') mode = 'dev';

nconf.argv().env().file({file: path.join(__dirname, mode + '.json')});

module.exports = nconf;


