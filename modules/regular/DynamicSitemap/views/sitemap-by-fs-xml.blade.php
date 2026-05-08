<?php
/** @var $cfg \STORMS\webframe\Modules\DynamicSitemapConfig */
/** @var $ignore array */
/** @var $page string|array */

use \STORMS\webframe\Core\SEO;

echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach(\STORMS\webframe\Core\Traits\UtilityMethods::globRecursive(Config::PAGES_DIR . '/[!_]*.blade.php') as $i => $page)
        <?php
        $page_parsed = str_replace(['pages/', '.blade.php'], '', $page);

        // make files that are set to be ignored being skipped
        if(in_array($page_parsed, $ignore))
            continue;

        // check if the file has the comment #-SITEMAP (or # -SITEMAP) - if yes: omit the file from the dynamic sitemap
        $ignore_by_comment = null;
        preg_match('/#\s*?\-SITEMAP/', file_get_contents($page), $ignore_by_comment);
        $ignore_by_comment = !empty($ignore_by_comment);
        if($ignore_by_comment)
            continue;

        // make the page that is defined as the home/default page (@Config) being referenced without eg. '/home' and make it being prioritized with 1.0
        if($page_parsed === Config::DEFAULT_PAGE) {
            $page_parsed = '';
            $prio_map[''] = '1.0';
        }

        /*
         * try to find settings for the dyn sitemap within the file
         * expected format looks like this:
         * # SITEMAP:p=0.9;c=daily
         * -> all parameters are optional
         * -> also "," and "&" can be used instead of ";"
         * -> whitespaces are trimmed
         */
        $page_sitemap_settings = [];
        $page_sitemap_settings_tmp = [];
        preg_match('/#\s*?SITEMAP:(.*)/', file_get_contents($page), $page_sitemap_settings_tmp);
        if(!empty($page_sitemap_settings_tmp)) {
            $sep = ';';
            if(strpos($page_sitemap_settings_tmp[1], ','))
                $sep = ',';
            elseif(strpos($page_sitemap_settings_tmp[1], '&'))
                $sep = '&';
            $page_sitemap_settings_tmp = str_getcsv($page_sitemap_settings_tmp[1], $sep);
            foreach($page_sitemap_settings_tmp as $setting) {
                list($setting_key, $settings_val) = array_map('trim', explode('=', $setting));
                $page_sitemap_settings[$setting_key] = $settings_val;
            }
        }

        // ...
        $ts = filemtime($page);
        ?>
        <url>
            <loc>{{WEB_URL_FULL}}/{{$page_parsed}}</loc>
            <lastmod>{{date('Y-m-d', $ts)}}</lastmod>
            <changefreq>{{$page_sitemap_settings['c'] ?? $cfg->defaultChangeFreq()}}</changefreq>
            <priority>{{$page_sitemap_settings['p'] ?? $prio_map[$page_parsed] ?? $cfg->defaultPriority()}}</priority>
        </url>
    @endforeach
</urlset>
