<?php

// hard debug
//define('HARD_DEBUG', true);

// show errors
@error_reporting(-1);
@ini_set('display_errors', 1);

// -----------------
// library
// -----------------

function _settings($name = null, $value = null)
{
    static $settings = array();
    if (is_array($name)) {
        $settings = array_merge($settings, $name);
    } elseif (!empty($name) && !empty($value)) {
        $settings[$name] = $value;
    } elseif (!empty($name)) {
        return isset($settings[$name]) ? $settings[$name] : null;
    }
    return $settings;
}

function getParams()
{
    $params = array();
    if (isset($_GET) && !empty($_GET)) {
        foreach ($_GET as $var=>$val) {
            $params[$var] = urldecode($val);
        }
    }
    return $params;
}

function guessMask()
{
    $semver_strict = 'v?\\d+\.\\d+\.\\d+';
    $semver_default = $semver_strict . '(-[0-9A-Za-z-\.]+)*';
    $type = _settings('type');
    switch ($type) {
        case 'default':
            return $semver_default;
            break;
        case 'strict':
            return $semver_strict;
            break;
        default:
            return $semver_strict . '-' . $type;
    }
}

function guessColor()
{
    $colors = array(
        'blue'  => '4183c4',
        'green' => '4c1',
        'red'   => 'd9534f',
    );
    $col = _settings('color');
    return isset($colors[$col]) ? $colors[$col] : $col;
}

function githubApiReq($url) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/'._settings('user').'/'._settings('repo').'/'.$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'GitHubReleaseButtonApp');
    curl_setopt($ch, CURLOPT_USERPWD, 'ec8e5cae540203319afb79fc418a6f96213b0d69:x-oauth-basic');
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $content = curl_exec($ch);
    curl_close($ch);
    return json_decode($content, true);
}

function matchTag($data)
{
    $mask = _settings('mask');
    foreach ($data as $i=>$item) {
        $name = isset($item['tag_name']) ? $item['tag_name'] : isset($item['name']) ? $item['name'] : '';
        if (0 !== preg_match('#^'.$mask.'$#i', $name)) {
            return $item;
        }
    }
    return null;
}

function parseTag()
{
    $tag = _settings('tag');
    $data = array();
    $data['name']   = isset($tag['tag_name']) ? $tag['tag_name'] : $tag['name'];
    $data['title']  = _settings('title');
    $data['color']  = _settings('color');
    $data['base_link']  = 'https://github.com/' . _settings('user').'/'._settings('repo');
    $url = _settings('url');
    switch ($url) {
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

function guessTextWidth($str)
{
    $box = imageTTFBbox(11,0,__DIR__.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'DejaVuSans.ttf',$str);
    return abs($box[4] - $box[0]) * (72/96);
}

function render()
{
    $data       = _settings('tag_data');
    $padding    = 6;
    $separator  = 4;
    $sizes      = array();
    // left part
    $sizes['left-width'] = (2*$padding) + guessTextWidth($data['title']) + ($separator/2);
    $sizes['left-padding'] = $padding;
    // right part
    $sizes['right-width'] = (2*$padding) + guessTextWidth($data['name']);
    $sizes['right-padding'] = $sizes['left-width'] + $separator;
    // separator
    $sizes['separator-x'] = $sizes['left-width'];
    // full
    $sizes['full-width'] = $sizes['left-width'] + $sizes['right-width'];
    // content
    dbg('Sizes are:', $sizes);
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
    <a xlink:href="{$data['tag_link']}" class="svg">
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
    </a>
</svg>
MSG;
    if (!headers_sent() && (!defined('HARD_DEBUG') || HARD_DEBUG!==true)) {
        header('Content-Type: image/svg+xml');
    }
    echo $svg;
}

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
    'title' => 'last release',
    'type'  => 'default',
    'color' => 'blue',
    'url'   => 'tarball',
));

// URL request
_settings(getParams());

// mask to match
_settings('mask', guessMask());

// button color
_settings('color', guessColor());

dbg('Settings are:', _settings());

// ok to go?
if (_settings('user') && _settings('repo')) {
    $json = githubApiReq('releases');
    if (empty($json)) {
        $json = githubApiReq('tags');
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
