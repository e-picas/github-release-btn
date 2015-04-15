/*
 * This file is part of the GitHub-Release-Button app
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
var express     = require('express');
var githubrb    = require('./github-release-btn');
var nunjucks    = require('nunjucks');

var app         = express();

nunjucks.configure(__dirname, {
    autoescape: true,
    express: app
});

app.set('port', (process.env.PORT || 5000));
app.use(express.static(__dirname));

app.get('/github-release-btn', function(req, resp) {
    githubrb.parse(req, resp, function(resp, data) {
        console.dir(githubrb.data);
        resp.render('./github-release-btn.svg.j2', data);
    });
});

app.listen(app.get('port'), function() {
   console.log("Node app is running at localhost :" + app.get('port'));
});
