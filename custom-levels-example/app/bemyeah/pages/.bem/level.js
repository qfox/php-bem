var extend = require('bem/lib/util').extend;

exports.getTechs = function() {
    return {
        'bemjson.js': '',
        'ie.css': 'ie.css',
        'ie6.css': 'ie6.css',
        'ie7.css': 'ie7.css',
        'ie8.css': 'ie8.css',
        'ie9.css': 'ie9.css',
        'bemhtml': '../../../../core/bem-bl/blocks-common/i-bem/bem/techs/bemhtml.js',
        'html': '../../../../core/bem-bl/blocks-common/i-bem/bem/techs/html'
    };
};

exports.getConfig = function() {
    return extend({}, this.__base() || {}, {
        bundleBuildLevels: this.resolvePaths([
            '../../../../core/bem-bl/blocks-common',
            '../../../../core/bem-bl/blocks-desktop',
            '../../../../app/bemyeah/blocks'
        ])
    });
};
