require('babel-register')({
    presets: ['env'],
    plugins: ['transform-class-properties']
});

/**
 * Require entrypoint
 */
module.exports = require('./entrypoint');