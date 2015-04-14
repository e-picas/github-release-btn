
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

app.get('/', function(req, resp) {
    githubrb.parse(req, resp, function(resp, data) {
        console.dir(githubrb.data);
//        var svg = nunjucks.render('./github-release-btn.svg.j2', data);
//        githubrb.resize(doc);
        resp.render('./github-release-btn.svg.j2', data);
    });
});

app.listen(app.get('port'), function() {
   console.log("Node app is running at localhost:" + app.get('port'));
});
