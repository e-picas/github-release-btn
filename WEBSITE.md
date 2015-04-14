title:          The Unofficial GitHub Release Button
description:    A dynamic last release button for any GitHub repository with version number and tarball link.
author:         Pierre Cassat


<iframe id="github-release-frame" src="github-release-btn?user=piwi&repo=github-release-btn"
        frameborder="0" scrolling="0" width="100%" height="20px"></iframe>

----

Tests
-----

Most of the tests below work on the [original *github-release-btn* repository](https://github.com/piwi/github-release-btn):

-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn')">default test</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=green')">test with green color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=red')">test with red color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=ccc')">test with custom color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=red&title=my+custom+title')">test with red color with a custom title</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&link=zipball')">test with zipball link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&link=html')">test with HTML link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&link=repo')">test with repository link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=strict')">test with 'strict' mode</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=no.v.prefix')">test with 'type=no.v.prefix'</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=with.a.very.long.*')">test with 'type=with.a.very.long.*'</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=dotfiles')">test on a repository with no realease or tag</a>

----

Usage
-----

The base URL of the button is `https://ghrb.herokuapp.com/github-release-btn`
(a PHP app freely hosted by [Heroku](http://heroku.com/)).

You can use the button in an `iframe`, which will keep the link on the button to the tarball or original repository:

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

    ![alt](https://ghrb.herokuapp.com/github-release-btn?user=piwi&repo=github-release-btn)

----

Available options
-----------------

You can (or may) use the following options as URL parameters to customize
your rendering:

-   `user` (**required**): the GitHub user name
-   `repo` (**required**): the GitHub repository name
-   `type` (*optional, defaults to "`default`"*): the release type to match
    -   `type=default`: will match last version number like `(v)X.Y.Z-STATE`
    -   `type=strict`: will match last version number like `(v)X.Y.Z`
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
-   `title` (*optional, defaults to `last release`*): the title of the button

----

Open source
-----------

The unofficial GitHub Release Button is open source, available on GitHub for
downloading, forking and contributing.

<a href="https://github.com/piwi/github-release-btn" class="btn btn-lg btn-primary">View it on GitHub</a>
