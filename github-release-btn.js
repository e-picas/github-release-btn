/*
 * This file is part of the GitHub-Release-Button app
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

var request = require('request');

var HARD_DEBUG = true;

// debug with a title
function log(title, obj) {
    if (HARD_DEBUG!==undefined && HARD_DEBUG) {
        console.log(title);
        console.dir(obj);
    }
}

// Read a page's GET URL variables and return them as an associative array.
// Source: http://jquery-howto.blogspot.com/2009/09/get-url-parameters-values-with-jquery.html
function getParams(request) {
    var vars = [], hash, i,
        hashes = request.slice(request.indexOf('?') + 1).split('&');
    for (i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

// find concerned release or tag
function matchTag(obj, mask) {
    var tag = null, i, name,
        reg = new RegExp('^' + mask + '$', 'i');
    if (obj !== undefined && obj.length>0) {
        for (i=0; i<obj.length; i++) {
            name = obj[i].tag_name || obj[i].name;
            if (reg.test(name)) {
                tag = obj[i];
                break;
            }
        }
    }
    return tag;
}

// merge some objects with priority on last one
function merge(/* obj1, obj2, ... */) {
    var args = Array.prototype.slice.call(arguments),
        result = {},
        obj, arg, index;
    for (arg in args) {
        obj = args[arg];
        for (index in obj) {
            result[index] = obj[index];
        }
    }
    return result;
}

// guess the width of a text
function guessTextWidth(str) {
    return Math.round((str.length * 5.5), 2);
}

// the app itslef
if (HARD_DEBUG===undefined) {
    var HARD_DEBUG = false;
}
module.exports = {

    data:       {},
    cbName:     null,
    opts:       null,
    mask:       null,
    repo_link:  null,
    api_link:   null,
    _defaults:  {
        title:  'last release',
        type:   'default',
        color:  'blue',
        link:   'tarball'
    },
    colors:     {
        blue:  '4183c4',
        green: '4c1',
        red:   'd9534f'
    },
    requests:   0,
    padding:    6,
    separator:  4,

    parse: function(request, response, cbName) {
        this.request = request;
        this.response = response;
        this.cbName = cbName;
        this.semver_strict  = 'v?\\d+\.\\d+\.\\d+';
        this.semver_default = this.semver_strict + '(-[0-9A-Za-z-\.]+)*';
        // extend defaults with params
        this.opts = merge(this._defaults, getParams(request.originalUrl));
        log('options are', this.opts);
        // version to match
        switch (this.opts.type) {
            case 'default':
                this.mask = this.semver_default;
                break;
            case 'strict':
                this.mask = this.semver_strict;
                break;
            default:
                this.mask = this.semver_strict + '-' + this.opts.type;
        }
        // load the api request if so
        if (this.opts.user!==undefined && this.opts.repo!==undefined) {
            this.repo_link  = 'https://github.com/' + this.opts.user + '/' + this.opts.repo;
            this.api_link   = 'https://api.github.com/repos/' + this.opts.user + '/' + this.opts.repo;
            this.load('releases');
        } else {
            this.data = {
                name:   '      ',
                title:  this.opts.title,
                color:  '404040',
                link:   '#'
            };
            this.send();
        }
    },

    // make the request
    load: function(type) {
        if (this.requests > 2) {
            return null;
        }
        this.jsonp(this.api_link + '/' + type);
    },

    // request with callback
    jsonp: function(path) {
        log('calling', path);
        var _this = this,
            options = {
                url: path,
                headers: { 'User-Agent': 'request' }
            };
        request(options,
            function (error, response, body) {
//                log('response is', response);
//                log('response is', body);
                if (!error && response.statusCode == 200) {
                    var info = JSON.parse(body);
                    log('response is', info);
                    _this.callback(info);
                }
                if (error) {
                    console.log('Request error: '+error);
                }
            }
        );
        this.requests++;
    },

    // data callback
    callback: function(obj) {
        log('callback on', obj);
        var tag = matchTag(obj, this.mask);
        log('tag is', tag);
        if (tag) {
            var tag_name    = tag.tag_name || tag.name,
                tag_color   = (this.colors[this.opts.color] !== undefined ? this.colors[this.opts.color] : this.opts.color),
                tag_link;
            switch (this.opts.link) {
                case 'repo':
                    tag_link = this.repo_link;
                    break;
                case 'zipball':
                    tag_link = tag.zipball_url;
                    break;
                case 'html':
                    tag_link = tag.html_url || this.repo_link + '/releases/tag/' + (tag.tag_name || tag.name);
                    break;
                default:
                    tag_link = tag.tarball_url;
            }
            // full data
            this.data = {
                title: this.opts.title,
                name: tag_name,
                link: tag_link,
                color: tag_color
            };
            this.send();
        } else {
            this.load('tags');
        }
    },

    guessSizes: function() {
        // sizes
        var sizes = {};
        // left part
        sizes.left_width = (2*this.padding) + guessTextWidth(this.data.title) + (this.separator/2);
        sizes.left_padding = this.padding;
        // right part
        sizes.right_width = (2*this.padding) + guessTextWidth(this.data.name);
        sizes.right_padding = sizes.left_width + this.separator;
        // separator
        sizes.separator_x = sizes.left_width;
        // full
        sizes.full_width = sizes.left_width + sizes.right_width;

        return sizes;
    },

    send: function() {
        log('data are', this.data);
        this.cbName(this.response, merge(this.data, this.guessSizes()));
    }

};
