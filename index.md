---
layout: default
---

<iframe id="github-release-frame" src="github-release-btn.html?user=piwi&repo=markdown-extended"
        frameborder="0" scrolling="0" width="190px" height="20px"></iframe>

----

<h3>Contents</h3>

* Comment to trigger ToC generation
{:toc}

----

Tests
-----

-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=markdown-extended')">markdown-extended</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=markdown-extended&color=green')">markdown-extended with green color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=markdown-extended&color=red')">markdown-extended with red color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=markdown-extended&color=ccc')">markdown-extended with custom color</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=markdown-extended&url=zipball')">markdown-extended with zipball link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=markdown-extended&url=html')">markdown-extended with HTML link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=markdown-extended&url=repo')">markdown-extended with repository link</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=nunjucks-date-filter')">nunjucks-date-filter</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=html5-quick-template')">html5-quick-template</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=html5-quick-template&type=strict')">html5-quick-template with strict match</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=html5-quick-template&type=mde')">html5-quick-template with "mde" match</a>
-   <a href="javascript:void(0);" onclick="loadFrame('user=piwi&repo=dotfiles')">dotfiles</a> (no realase or tag)

----

Usage
-----

{% highlight html %}
<iframe src="github-release-btn.html?user=piwi&repo=github-release-btn" frameborder="0" scrolling="0" width="190px" height="20px"></iframe>
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
