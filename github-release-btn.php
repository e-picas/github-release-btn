<?php
/*
 * This file is part of the GitHub-Release-Button app
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// hard debug
//define('HARD_DEBUG', true);

// show errors
@error_reporting(-1);
@ini_set('display_errors', 1);

// set a default timezone to avoid PHP5 warnings
$dtmz = @date_default_timezone_get();
@date_default_timezone_set($dtmz?:'UTC');

// -----------------
// library
// -----------------

/**
 * App settings setter/getter
 *
 *      _settings( (string)var , val )              : set 'var' on 'val'
 *      _settings( (string)var )                    : get 'var' current value
 *      _settings( (string)var , null , params )    : call 'var' closure with params 'params'
 *      _settings( (array)var )                     : set 'var' keys on 'var' values
 *      _settings()                                 : get the full current settings array
 *
 * @param null|string $name
 * @param null|mixed $value
 * @param null|array $params
 * @return array|null
 */
function _settings($name = null, $value = null, array $params = null)
{
    static $settings = array();
    if (is_array($name)) {
        $settings = array_merge($settings, $name);
    } elseif (!empty($name) && !empty($value)) {
        $settings[$name] = $value;
    } elseif (!empty($name)) {
        $val = isset($settings[$name]) ? $settings[$name] : null;
        if (!empty($val) && is_callable($val) && !empty($params)) {
            $val = call_user_func_array($val, $params);
        }
        return $val;
    }
    return $settings;
}

/**
 * Get a well-formatted path
 *
 * @param string|array $parts
 * @return string
 */
function getPath($parts)
{
    return implode(DIRECTORY_SEPARATOR, array_map(
        function ($p) { return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $p); },
        is_array($parts) ? $parts : array($parts)
    ));
}

/**
 * Parse current request sanitized parameters
 *
 * @param array $allowed List of allowed parameters
 * @return array
 */
function getParams(array $allowed)
{
    $params = array();
    if (isset($_GET) && !empty($_GET)) {
        foreach ($_GET as $var=>$val) {
            if (in_array($var, $allowed)) {
                $params[$var] = filter_input(INPUT_GET, $var, FILTER_SANITIZE_STRING);
            }
        }
    }
    return $params;
}

/**
 * Process a cURL request to the GitHub API
 *
 * @param string $url   The type of request in 'tags', 'releases'
 * @return mixed
 */
function githubApiRequest($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, _settings('github_api_url', null, array(
        _settings('user'), _settings('repo'), $url
    )));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'GitHubReleaseButtonApp');
    $api_token = _settings('api_token');
    if (!empty($api_token)) {
        curl_setopt($ch, CURLOPT_USERPWD, $api_token.':x-oauth-basic');
    }
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $content = curl_exec($ch);
    curl_close($ch);
    return json_decode($content, true);
}

/**
 * Find concerned tag in an API response with mask builds by `guessMask()`
 *
 * @param array $data
 * @return null
 */
function matchTag($data)
{
    if (is_array($data)) {
        $mask = _settings('mask');
        foreach ($data as $i=>$item) {
            $name = isset($item['tag_name']) ? $item['tag_name'] : (isset($item['name']) ? $item['name'] : '');
            if (0 !== preg_match('#^'.$mask.'$#i', $name)) {
                return $item;
            }
        }
    }
    return null;
}

/**
 * Parses found tag to extract relative info
 */
function parseTag()
{
    $tag                = _settings('tag');
    $data               = array();
    $data['name']       = isset($tag['tag_name']) ? $tag['tag_name'] : $tag['name'];
    $data['title']      = _settings('title');
    $data['color']      = _settings('color');
    $data['base_link']  = _settings('github_url', null, array(_settings('user'), _settings('repo')));
    $link               = _settings('link');
    switch ($link) {
        case 'none':
            $data['tag_link'] = null;
            break;
        case 'repo':
            $data['tag_link'] = $data['base_link'];
            break;
        case 'zipball':
            $data['tag_link'] = $tag['zipball_url'];
            break;
        case 'html':
            $data['tag_link'] = isset($tag['html_url']) ? $tag['html_url'] : $data['base_link'] . '/releases/tag/' . $data['name'];
            break;
        default:
            $data['tag_link'] = $tag['tarball_url'];
    }
    _settings('tag_data', $data);
}

/**
 * Builds the mask to match release tag name
 *
 * @return string
 */
function guessMask()
{
    $type = _settings('type');
    switch ($type) {
        case 'default':
            return _settings('semver_default');
            break;
        case 'strict':
            return _settings('semver_strict');
            break;
        case 'metadata':
            return _settings('semver_metadata');
            break;
        default:
            return _settings('semver_strict') . '-' . $type;
    }
}

/**
 * Treats the `color` argument
 *
 * @return array|null
 */
function guessColor()
{
    $colors = _settings('colors');
    $col    = _settings('color');
    return isset($colors[$col]) ? $colors[$col] : $col;
}

/**
 * Calculates a text box width with DejaVu font in 11 points' size
 *
 * @param string $str
 * @return number
 * @link http://php.net/manual/fr/function.imagettfbbox.php#108536
 */
function guessTextWidth($str)
{
    $box = imageTTFBbox(11, 0, getPath(array(__DIR__, 'fonts', 'DejaVuSans.ttf')), $str);
    return abs($box[4] - $box[0]) * (72 / 96);
}

/**
 * Calculate sizes for the SVG button
 *
 * @param array $data
 * @return array
 */
function guessSizes(array $data)
{
    $padding    = _settings('padding');
    $separator  = _settings('separator');
    $sizes      = array();
    // left part
    $sizes['left-width']    = (2 * $padding) + guessTextWidth($data['title']) + ($separator / 2);
    $sizes['left-padding']  = $padding;
    // right part
    $sizes['right-width']   = (2 * $padding) + guessTextWidth($data['name']);
    $sizes['right-padding'] = $sizes['left-width'] + $separator;
    // separator
    $sizes['separator-x']   = $sizes['left-width'];
    // full
    $sizes['full-width']    = $sizes['left-width'] + $sizes['right-width'];
    return $sizes;
}

/**
 * Render the SVG content
 */
function render()
{
    $data   = _settings('tag_data');
    $sizes  = guessSizes($data);
    // link ?
    $link_opentag   = !empty($data['tag_link']) ? '<a xlink:href="'.$data['tag_link'].'" class="svg">' : '';
    $link_closetag  = !empty($data['tag_link']) ? '</a>' : '';
    // content
    $svg = <<<MSG
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">
<svg version="1.1"
     baseProfile="full"
     xmlns="http://www.w3.org/2000/svg"
     xmlns:xlink="http://www.w3.org/1999/xlink"
     width="{$sizes['full-width']}px" height="20px"
     id="button">
<style>
/* <![CDATA[ */
a.svg {
    text-decoration: none;
    position: relative;
    display: inline-block;
}
a.svg:after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left:0;
}
/* ]]> */
</style>
    {$link_opentag}
        <linearGradient id="a" x2="0" y2="100%">
            <stop offset="0" stop-color="#bbb" stop-opacity=".1" />
            <stop offset="1" stop-opacity=".1" />
        </linearGradient>
        <rect rx="3" width="90%" height="100%" fill="#555" />
        <rect rx="3" x="{$sizes['left-width']}" width="{$sizes['right-width']}" height="100%" fill="#{$data['color']}" />
        <path fill="#{$data['color']}" d="M{$sizes['separator-x']} 0h4v20h-4z" />
        <rect rx="3" width="{$sizes['full-width']}" height="100%" fill="url(#a)" />
        <g fill="#fff" text-anchor="start" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
            <text x="{$sizes['left-padding']}" y="15" fill="#010101" fill-opacity=".3">{$data['title']}</text>
            <text x="{$sizes['left-padding']}" y="14">{$data['title']}</text>
            <text x="{$sizes['right-padding']}" y="15" fill="#010101" fill-opacity=".3">{$data['name']}</text>
            <text x="{$sizes['right-padding']}" y="14">{$data['name']}</text>
        </g>
    {$link_closetag}
</svg>
MSG;
    if (!headers_sent() && (!defined('HARD_DEBUG') || HARD_DEBUG!==true)) {
        header('Content-Type: '._settings('svg-mime'));
    }
    echo $svg;
    exit(0);
}

/**
 * Render an empty button
 */
function renderEmpty()
{
    _settings('tag_data', _settings('empty_tag'));
    render();
}

// debug
function dbg()
{
    if (defined('HARD_DEBUG') && HARD_DEBUG===true) {
        foreach (func_get_args() as $var=>$val) {
            if (is_string($val)) {
                echo PHP_EOL . '# ' . $val . PHP_EOL;
            } else {
                var_export($val);
            }
        }
    }
}

// -----------------
// app
// -----------------

// prepare debug
if (defined('HARD_DEBUG') && HARD_DEBUG===true) {
    header('Content-Type: text/plain');
    ini_set('html_errors', 0);
}

// defaults
_settings(array(
    'title'         => 'last release',
    'type'          => 'default',
    'color'         => 'blue',
    'link'          => 'tarball',
    'empty_tag'     => array(
        'name'          => '                ',
        'title'         => 'last release',
        'color'         => '404040',
        'base_link'     => '#',
        'tag_link'      => '#',
    ),
    'semver_strict' => 'v?\\d+\.\\d+\.\\d+',
    'semver_default' => 'v?\\d+\.\\d+\.\\d+(-[0-9A-Za-z-\.]+)*',
    'semver_metadata' => 'v?\\d+\.\\d+\.\\d+(-[0-9A-Za-z-\.]+)+',
    'colors'        => array(
        'blue'          => '4183c4',
        'green'         => '4c1',
        'red'           => 'd9534f',
    ),
    'padding'       => 6,
    'separator'     => 4,
    'svg-mime'      => 'image/svg+xml',
    'github_url'    => function ($user, $repo, $path=null) {
        return 'https://github.com/'.$user.'/'.$repo.(!empty($path) ? '/'.$path : '');
    },
    'github_api_url'=> function ($user, $repo, $path=null) {
        return 'https://api.github.com/repos/'.$user.'/'.$repo.(!empty($path) ? '/'.$path : '');
    },
));

// URL request
_settings(getParams(array(
    'user', 'repo', 'title', 'type', 'color', 'link', 'api_token'
)));

// mask to match
_settings('mask', guessMask());

// button color
_settings('color', guessColor());

// debug full settings
dbg('Settings are:', _settings());

// ok to go?
if (_settings('user') && _settings('repo')) {
    $json = githubApiRequest('releases');
    if (empty($json)) {
        $json = githubApiRequest('tags');
    }
    dbg('JSON response is:', $json);
    if (!empty($json)) {
        // concerned tag
        $tag = matchTag($json);
        dbg('Found tag is:', $tag);
        if (!empty($tag)) {
            _settings('tag', $tag);
            parseTag();
            render();
        }
    }
}

// else empty button
renderEmpty();
