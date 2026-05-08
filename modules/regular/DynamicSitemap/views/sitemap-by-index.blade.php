<?php
/** @var $cfg \STORMS\webframe\Modules\DynamicSitemapConfig */
/** @var $ignore array */
/** @var $page string|array */

echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";

/*
 * TODO
 * NOT TESTED YET - FIX THIS AS SOON AS NEEDED
 */

$compiled_last_file = glob(ltrim(Config::STORAGE_PATH, '/') .  '/compiled/*.*')[0];
$date = date ('Y-m-d', filemtime($compiled_last_file));
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($cfg->index() as $i => $page)
        <url>
            <loc>{{WEB_URL_FULL}}/{{$page['page']}}</loc>
            <lastmod>{{$date}}</lastmod>
            <changefreq>{{$page['c'] ?? 'monthly'}}</changefreq>
            <priority>{{$page['p'] ?? $cfg->defaultPriority()}}</priority>
        </url>
    @endforeach

    @if(is_dir('pages/kontakt') || is_file('pages/kontakt.blade.php'))
        <url>
            <loc>{{WEB_URL_FULL}}/kontakt</loc>
            <lastmod>{{$date}}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endif

    <url>
        <loc>{{WEB_URL_FULL}}/datenschutz</loc>
        <lastmod>{{$date}}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>{{WEB_URL_FULL}}/impressum</loc>
        <lastmod>{{$date}}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
</urlset>
