/*
 * This file is part of the GitHub-Release-Button app
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

var path        = require('path');
var express     = require('express');
var githubrb    = require('./github-release-btn');
var nunjucks    = require('nunjucks');

var app         = express();

nunjucks.configure(__dirname, {
    autoescape: true,
    express: app
});

app.use(express.static(path.join(__dirname, 'web')));

app.get('/github-release-btn', function(req, res) {
    githubrb.parse(req, res, function(res, data) {
//        console.dir(githubrb.data);
        res.set('Content-Type', 'image/svg+xml');
        res.render('github-release-btn.svg.j2', data);
        res.end();
    });
});

app.set('port', (process.env.PORT || 5000));
app.listen(app.get('port'), function() {
   console.log("Node app is running at localhost:" + app.get('port'));
});

module.exports = app;
