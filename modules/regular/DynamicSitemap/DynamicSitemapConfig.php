<?php

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\SEO\SEO;

abstract class DynamicSitemapConfig implements ModuleConfig {

    const USE__INDEX            = 0; // fixed array def.
    const USE__PAGES_SCAN       = 1; // FS scan over the pages dir
    const USE__SEO_HELPER       = 2; // pull pages to use for the sitemap from the seohelper

    public function ignore() { return []; } // pages (top level names only) to be ignored for the generation (relevant for USE__INDEX & USE__PAGES_SCAN)
    public function fallBackToFileSystem() { return true; } // if use is set to USE__INDEX or USE__SEO_HELPER but the index/seo-list is empty: should we fall back to the fs scan?
    public function index() { return []; } // for USE__INDEX only: a manual page index for the sitemap. Structure [['page' => 'home', 'c'=>'daily', 'p'=>0.x], ...]
    public function use() { return self::USE__SEO_HELPER; } // where to pull sitemap entries from? fs scan/pages-dir OR a fixed defined array OR the SEO Helper? Use the class constants of this config class
    public function defaultPriority() { return 0.6; }
    public function defaultChangeFreq() { return SEO::SITEMAP_CHANGE_FREQ__MONTHLY; }

}
