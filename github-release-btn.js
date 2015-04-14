

'use strict';

var request = require('request');

// debug with a title
function log(title, obj) {
    console.log(title);
    console.dir(obj);
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

// the app itslef
module.exports = {

    data:       {},
    cbName:     null,
    opts:       null,
    mask:       null,
    repo_link:  null,
    api_link:   null,
    _defaults:  {
        type:   'default',
        color:  'blue',
        url:    'tarball'
    },
    colors:     {
        blue:  '4183c4',
        green: '4c1',
        red:   'd9534f'
    },
    requests:   0,

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
        var _this = this,
            options = {
                url: path,
                headers: { 'User-Agent': 'request' }
            };
        request(options,
            function (error, response, body) {
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
            switch (this.opts.url) {
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
            this.data = {
                name: tag_name,
                link: tag_link,
                color: tag_color
            };
console.dir(this.data);
            this.cbName(this.response, this.data);
        } else {
            this.load('tags');
        }
    },

    // width
    resize: function(elt) {
        var bbox        = elt.getElementById('name').getBBox(),
            width       = bbox.width + 10,
            fullwidth   = width + 77,
            width_elts  = elt.getElementsByClassName('set-width'),
            fullwidth_elts = elt.getElementsByClassName('set-fullwidth');
        elts.button.setAttribute('width', fullwidth+'px');
        for (i=0; i < width_elts.length; i++) {
            width_elts.item(i).setAttribute('width', width+'px');
        }
        for (i=0; i < fullwidth_elts.length; i++) {
            fullwidth_elts.item(i).setAttribute('width', fullwidth+'px');
        }
    }

/*
    // populate svg
    populate: function(name, url, color) {
        var elts = {
            button:     document.getElementById('button'),
            name_box:   document.getElementById('name'),
            color:      document.getElementsByClassName('set-color'),
            name:       document.getElementsByClassName('set-name'),
            width:      document.getElementsByClassName('set-width'),
            fullwidth:  document.getElementsByClassName('set-fullwidth'),
            url:        document.getElementsByClassName('set-url')
        };
        // color
        for (i=0; i < elts.color.length; i++) {
            elts.color.item(i).setAttribute('fill', '#'+color);
        }
        // name
        for (i=0; i < elts.name.length; i++) {
            elts.name.item(i).innerHTML = name;
        }
        // url
        for (i=0; i < elts.url.length; i++) {
            elts.url.item(i).setAttribute('xlink:href', url);
        }
        // width
        var bbox        = elts.name_box.getBBox(),
            width       = bbox.width + 10,
            fullwidth   = width + 77;
        elts.button.setAttribute('width', fullwidth+'px');
        for (i=0; i < elts.width.length; i++) {
            elts.width.item(i).setAttribute('width', width+'px');
        }
        for (i=0; i < elts.fullwidth.length; i++) {
            elts.fullwidth.item(i).setAttribute('width', fullwidth+'px');
        }
    }
*/
};
