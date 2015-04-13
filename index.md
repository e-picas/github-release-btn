---
layout: default
---

<iframe id="github-release-frame" src="github-release-btn.html?user=piwi&repo=github-release-btn"
        frameborder="0" scrolling="0" width="100%" height="20px"></iframe>

----

<h3>Contents</h3>

* Comment to trigger ToC generation
{:toc}

----

Tests
-----

Most of the tests below work on the [original *github-release-btn* repository](https://github.com/piwi/github-release-btn):

-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn')">default test</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=green')">test with green color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=red')">test with red color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&color=ccc')">test with custom color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&url=zipball')">test with zipball link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&url=html')">test with HTML link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&url=repo')">test with repository link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=strict')">test with 'strict' mode</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=no.v.prefix')">test with 'type=no.v.prefix'</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=github-release-btn&type=with.a.very.long.status.suffix.for.test.0123456789')">test with 'type=with.a.very.long.status.suffix.for.test.0123456789'</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=dotfiles')">test on a repository with no realease or tag</a>

----

Usage
-----

{% highlight html %}
<iframe src="http://piwi.github.io/github-release-btn/github-release-btn.html?user=piwi&repo=github-release-btn" frameborder="0" scrolling="0" width="190px" height="20px"></iframe>
{% endhighlight %}

----

Available options
-----------------

-   `user` (**required**): the GitHub user name
-   `repo` (**required**): the GitHub repository name
-   `type` (*optional, defaults to "`default`"*): the release type to match
    -   `type=default`: will match last version number like `(v)X.Y.Z-STATE`
    -   `type=strict`: will match last version number like `(v)X.Y.Z`
    -   `type=STRING`: will match last version number like `(v)X.Y.Z-STRING`
-   `color` (*optional, defaults to "`red`"*): the button color
    -   `color=blue`: blue button <span class="showcase-color blue"></span>
    -   `color=green`: green button <span class="showcase-color green"></span>
    -   `color=red`: red button <span class="showcase-color red"></span>
    -   `color=XXXXXX`: custom colored button
-   `url` (*optional, defaults to "`tarball`"*): the button link URL
    -   `url=tarball`: direct link to download a *tar.gz* archive of the release
    -   `url=zipball`: direct link to download a *zip* archive of the release
    -   `url=html`: link to the release's page on GitHub
    -   `url=repo`: link to the repository's homepage on GitHub

----

Open source
-----------

The unofficial GitHub Release Button is open source, available on GitHub for
downloading, forking and contributing.

<a href="https://github.com/piwi/github-release-btn" class="btn btn-lg btn-primary">View it on GitHub</a>
