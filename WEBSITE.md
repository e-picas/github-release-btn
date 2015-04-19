title:          The Unofficial GitHub Release Button
description:    A dynamic last release button for any GitHub repository with version number and tarball link.
author:         Pierre Cassat


<iframe src="github-release-btn?user=piwi&repo=github-release-btn"
        frameborder="0" scrolling="0" width="190px" height="20px"></iframe>

----

Contents
--------

-   [Tests](#tests)
-   [Usage](#usage)
-   [Available options](#available-options)
-   [Known issues](#known-issues)
-   [Open source](#open-source)

----

Tests
-----

Most of these tests are made to the [original *github-release-btn* repository](https://github.com/piwi/github-release-btn).

<iframe id="github-release-frame" src="github-release-btn?user=piwi&repo=github-release-btn"
        frameborder="0" scrolling="0" width="100%" height="20px"></iframe>

Click on the links below to update the above button dynamically:

-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn')">default test</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=green')">test with green color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=red')">test with red color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=ccc')">test with custom color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=red&title=my+custom+title')">test with red color with a custom title</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&link=zipball')">test with zipball link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&link=html')">test with HTML link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&link=repo')">test with repository link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&link=none')">test with no link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=strict')">test with 'strict' mode</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=metadata')">test with 'metadata' mode</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=no.v.prefix')">test with 'type=no.v.prefix'</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=with.a.very.long.*')">test with 'type=with.a.very.long.*'</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=dotfiles')">test on a repository with no realease or tag</a>

----

Usage
-----

The base URL of the button is `https://ghrb.herokuapp.com/github-release-btn`.

You can use the button in an `iframe`, which will keep the link to the tarball or the original repository:

```html
<iframe src="https://ghrb.herokuapp.com/github-release-btn?user=piwi&repo=github-release-btn" 
    frameborder="0" scrolling="0" 
    width="190px" height="20px">
</iframe>
```

You can also embed the button as a simple image, to let you choose to surround it in a custom link:

```html
<img src="https://ghrb.herokuapp.com/github-release-btn?user=piwi&repo=github-release-btn" 
    alt="last release" />
```

NOTE - The image notation allows to be used in [a markdown](http://daringfireball.net/projects/markdown/syntax) content easily:

    # simple image
    ![alt](https://ghrb.herokuapp.com/github-release-btn?user=piwi&repo=github-release-btn)
    # image linked to the releases' page
    [![alt](https://ghrb.herokuapp.com/github-release-btn?user=piwi&repo=github-release-btn)](https://github.com/piwi/github-release-btn/releases)

----

Available options
-----------------

You can (or must) use the following options as URL parameters to customize
your rendering:

-   `user` (**required**): the GitHub user name
-   `repo` (**required**): the GitHub repository name
-   `type` (*optional, defaults to "`default`"*): the release type to match
    -   `type=default`: will match last version number like `(v)X.Y.Z(-STATE)`
    -   `type=strict`: will match last version number like `(v)X.Y.Z`
    -   `type=metadata`: will match last version number like `(v)X.Y.Z-STATE` ("state" required)
    -   `type=STRING`: will match last version number like `(v)X.Y.Z-STRING`
-   `color` (*optional, defaults to "`blue`"*): the button color
    -   `color=blue`: blue button <span class="showcase-color blue"></span>
    -   `color=green`: green button <span class="showcase-color green"></span>
    -   `color=red`: red button <span class="showcase-color red"></span>
    -   `color=XXXXXX`: custom colored button
-   `link` (*optional, defaults to "`tarball`"*): the button link URL
    -   `link=tarball`: direct link to download a *tar.gz* archive of the release
    -   `link=zipball`: direct link to download a *zip* archive of the release
    -   `link=html`: link to the release's page on GitHub
    -   `link=repo`: link to the repository's homepage on GitHub
    -   `link=none`: no link around the button
-   `title` (*optional, defaults to `last release`*): the title of the button (its left part)

----

Known issues
------------

The button has a dynamic width depending on the text inside each part of it.
As you can't really guess this width, you must find a way to let the browser adapt the size
of the *image* or *iframe* to its content. This can be done quite easily for an image, with
a smart `style` attribute like `<img style="max-width:100%;" ...` but is more difficult for
an iframe. In this case, you may adjust its width dynamically using javascript or set up a 
full page width frame. I use a fixed width of 190 pixels in the examples of this page as it
seems to fit most cases.

In some cases, you may want to use [GitHub OAuth](https://developer.github.com/v3/oauth/)
to skip [requests rate limit](https://developer.github.com/v3/rate_limit/). You can
do so by passing a `api_token=...` URL parameter, which will be used in all API's requests.

----

Open source
-----------

The unofficial GitHub Release Button is open source, available on [GitHub](http://github.com/) 
for downloading, forking and contributing. It is coded as a PHP app freely hosted 
and deployed by [Heroku](http://heroku.com/).

<a href="https://github.com/piwi/github-release-btn" class="btn btn-lg btn-primary">View it on GitHub</a>
