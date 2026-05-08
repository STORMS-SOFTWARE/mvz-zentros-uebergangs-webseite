<?php
/*
 * DynamicSitemap Module
 * This module will auto make a (virtual) sitemap available @ url.tld/sitemap.xml
 * The generation can use different data sources that can be forced with the config use() (see DynamicSitemapConfig)
 */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\LimeJuice\Response;
use STORMS\webframe\Core\SEO\SEO;

$this->extend([
    'init' => function(DynamicSitemapConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {

        $current_base = $this->_base;

        $router->before('GET', '/sitemap.xml', function() use ($current_base, $cfg, $blade) {
            $r = new Response();
            if(!isset($_GET['test']))
                $r->mime = 'xml_application';

            // ============================================
            $prio_map = [ // pages with these names will be set these prios
                'home' => 1.0,
                'start' => 1.0,

                'datenschutz' => 0.4,

                'impressum' => 0.4,
                'imprint' => 0.4,
            ];

            $ignore = array_merge(['404'], $cfg->ignore() ?? []);
            // ============================================

            $handler_view = null;
            switch ($cfg->use()) {
                case DynamicSitemapConfig::USE__INDEX:
                    if(count($cfg->index()) > 0)
                        $handler_view = 'sitemap-by-index';
                    else
                        $handler_view = null;
                break;
                case DynamicSitemapConfig::USE__PAGES_SCAN:
                    $handler_view = 'sitemap-by-fs-xml';
                break;
                case DynamicSitemapConfig::USE__SEO_HELPER:
                    if(count(SEO::getEntries()) > 0)
                        $handler_view = 'sitemap-by-seohelper-xml';
                    else
                        $handler_view = null;
                break;
            }

            if($handler_view === null && $cfg->fallBackToFileSystem())
                $handler_view = 'sitemap-by-fs-xml';

            if(!$handler_view)
                throw new \Exception('[WF][DynamicSitemap] CANNOT DETERMINE METHOD HANDLER');

            $r->body = $blade->run("{$current_base}/views/{$handler_view}.blade.php", [
                'cfg' => $cfg,
                'blade' => $blade,
                '_' => WebFrame::inst(),
                'prio_map' => $prio_map,
                'ignore' => $ignore
            ]);
            $r->flush();
            exit;
        });

    }
]);
