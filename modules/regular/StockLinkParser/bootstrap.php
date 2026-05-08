<?php
/**
 * Blade directive for the Stock Link Parser
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame as WebFrame;
use \DOMDocument as DOMDocument;
use \DOMXPath as DOMXPath;
use \Config as Config;

$this->extend([
    'always' => function($cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {
        /* @var $moduleObj Module */
        $moduleObj = $this;
        
        $blade->directive('stockLinks', function($expression) use ($blade, $_, $cfg, $moduleObj) {
            $str = $blade->stripParentheses($expression);
            $str = $blade->stripQuotes($str);
            return '<?php echo $_->modules[\'regular\']->getModuleByName(\''.$moduleObj->getName().'\')->generate_stock_links(\''.$str.'\') ?>';
        });
    },
    'generate_stock_links' => function ($stock_links_str) {

        // get all plain links
        $stock_links = array_values(array_filter(array_map('trim', explode("\n", $stock_links_str))));

        // TODO: create one schema for each element (istock, shutterstock, adobestock...) 
        $html_selectors = [
            'www.istockphoto.com' => [
                'author' => '(//a[@data-testid="photographer"]/span)[2]',
                'id' => $id_getter__istock_and_shutterstock = function ($str) {
                    $url_chunks = explode('/', $str); // split full query into "path" chunks
                    $name_slug = explode("-", $url_chunks[5]); // the last chunk (for both current hosts) contains the img slug - so lets get it here
                    $id = end($name_slug); // now get the last element of the name slug chunk (which contains the id (for the host we currently got))
                    return $id;
                }
            ],
            'www.shutterstock.com' => [
                'author' => '//a[@data-track-label="contributorLink"]',
                'id' => $id_getter__istock_and_shutterstock // url shema is the same, so we can use the same id getter function
            ],
            'stock.adobe.com' => [
                'author' => '//script[@id="image-detail-json"]',
                'filter_author' => function($str) {
                    $json = json_decode($str,true);
                    return array_values($json)[0]['author'] ?? '??UNKNOWN??';
                },
                'id' => function ($str) {
                    $url_chunks = explode('/', $str);
                    $id = end($url_chunks);
                    return $id;
                }
            ],
        ];

        $dom = new DOMDocument();

        $simple_db_name = sprintf('%s/stock-link-cache.json', ltrim(Config::STORAGE_PATH, '/'));

        if(is_file($simple_db_name))
            $simple_db = json_decode(file_get_contents($simple_db_name), true);
        else
            $simple_db = [];

        foreach ($stock_links as $full_query) {

            $host = parse_url($full_query, PHP_URL_HOST);

            $id = call_user_func($html_selectors[$host]['id'], $full_query); // apply the img id getter method for the current host to the request-uri/query

            if(!array_key_exists($host, $html_selectors)) {
                echo '<b style="color: red;">!!!No selector-set for the given stock-host '.$host.' defined!!!</b>';
            }
            else {

                if(array_key_exists($id, $simple_db)) {
                    /*
                     * use cache
                     */
                    $author = $simple_db[$id]['author'];
                }
                else {
                    /*
                     * fetch markup from host for the given stock link, parse it and then cache the information
                     */

                    // get the stock page html
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $full_query);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");
                    $output = curl_exec($ch);
                    curl_close($ch);
                    // - end dom fetching
                    
                    @$dom->loadHTML($output);
                    $dom_xpath = new DOMXPath($dom);

                    // get the plain author string determined by the xpath for the current host
                    $author = $dom->saveHTML($dom_xpath->query($html_selectors[$host]['author'])->item(0)->childNodes->item(0)); // alternative version: strip_tags($dom->saveHTML($dom_xpath->query($html_selectors[$host]['author'])->item(0)))

                    // apply string filter for the author if one is defined for the host
                    if(is_callable($html_selectors[$host]['filter_author']??false))
                        $author = call_user_func($html_selectors[$host]['filter_author'], $author);

                    // build flat line db-data...
                    $simple_db[$id] = compact('author');
                    // ... and persist it
                    file_put_contents($simple_db_name, json_encode($simple_db));
                }

                // just print the fresh caught or restored (from cache) data to an link
                printf('<a href="%s" target="_blank">#%s | &copy; %s - %s</a><br/>', $full_query, $id, $author, $host);

            }

        }

    }
]);
