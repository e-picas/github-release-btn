GitHub Release Button - Development doc
=======================================

The application is hosted and deployed by [Heroku](heroku.com/).
You MUST install the *heroku toolbelt* application.

Basic Heroku commands
---------------------

To create or destroy an application, run:

    heroku apps:(create|destroy) APP_NAME

To update an app (it is automatic if the app is "plugged" with a GitHub repo), run:

    git push heroku master

To deploy a dyno, run:

    heroku ps:scale web=1

To view an app's logs:

    heroku logs --tail

PHP app
-------

The PHP application is developed on the `master` branch.
The [Markdown Extended](https://github.com/piwi/markdown-extended) package is used
to generate the `index.html` page running:

    composer website

To install all dependencies locally, you must skip heroku's specific packages with:

    composer update --ignore-platform-reqs

JS app
------

The javascript application is developed on the `npm-app` branch.
It is based on [Express](http://expressjs.com/) and [Nunjucks](https://mozilla.github.io/nunjucks/).

To run the app locally, use:

    foreman start web
