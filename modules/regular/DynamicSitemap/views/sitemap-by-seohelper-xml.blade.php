<?php
/** @var $cfg \STORMS\webframe\Modules\DynamicSitemapConfig */
/** @var $seo_entry \STORMS\webframe\Core\SEO\SEOEntry */
/** @var $ignore array */

use \STORMS\webframe\Core\SEO\SEO;

echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach(\STORMS\webframe\Core\WebFrame::globRecursive('pages/[!_]*.blade.php') as $i => $page)
        @php
        $page_parsed=str_replace(['pages/', '.blade.php'], '', $page); // path like "leistungen/leistung-1" (so without leading slash .....)

        // prevent eg. /test/test as result if we got an test.blade.php in /pages/test/ (so /pages/test/test.blade.php)
        $di = pathinfo($page);
        if(basename($di['dirname']) === basename($page_parsed))
            $page_parsed = basename($page_parsed);

        $top_page = basename($page_parsed);

        // skip those that are not defined with an seo entry
        if(!in_array($top_page, array_keys(SEO::getEntries())))
            continue;

        // skip those that shall be ignored by config
        if(in_array($page_parsed, $ignore))
            continue;

        $seo_entry = SEO::getEntry($page_parsed);

        $date = date ('Y-m-d', filemtime($page));

        $pi = pathinfo($page_parsed);
        @endphp
        <url>
            <loc>{{WEB_URL_FULL}}/{{$page_parsed}}</loc>
            <lastmod>{{$date}}</lastmod>
            <changefreq>{{$seo_entry ? $seo_entry->getChangeFrequency() : $cfg->defaultChangeFreq()}}</changefreq>
            <priority>{{$prio_map[$page_parsed] ?? $cfg->defaultPriority()}}</priority>
        </url>
    @endforeach
</urlset>
