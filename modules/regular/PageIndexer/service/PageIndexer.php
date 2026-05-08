<?php

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\Core\Page;

class PageIndexer {

    public static function onIndex($fn) {
        return call_user_func($fn);
    }

    public static function init() {

        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(\Config::PAGES_DIR));
        foreach ($rii as $file) {
            /* @var $file \SplFileInfo */
            if ($file->isDir())
                continue;

            $str = file_get_contents($file->getPathname());

            if(strpos($str, 'PageIndexer') === false || Page::inst()->getFilePath() === $file->getPathname())
                continue;

            $tokens = token_get_all($str);

            // flatten token array
            array_walk($tokens, function(&$data, $i) {
                $data = is_array($data) ? $data[1] : $data;
            });

            //$search_start = 'new PageIndexerAction ( function ( ) {'; // later exploded into chunks delimited by empty-space-char
            //$search_start = 'new STORMS\webframe\Modules\PageIndexerAction(function(){';
            $search_start = 'STORMS\webframe\Modules\PageIndexer::onIndex(function(){';
            //$search_start = 'function foobar(){';
            //$search_closing = '} )'; // also later exploded into chunks
            $search_closing = '})';
            //$search_closing = '}';

            $bracket_counter = ['_ROUND' => 0, '_CURLY' => 0, '_SQUARE' => 0]; // used internally so the loop won't stop too early

            //d(array_sum($bracket_counter));

            $start = -1; // will later contain the index of the token array where the match starts
            $end_start_match = -1; // ... where the search start index ends

            $end = -1; // ... where the full match ends

            $start_complete = false; // will be set to true when the start search sequence was completly found

            //$search_foo = explode(' ', $search_start); // explode the search string into chunks we can work with in the token iteration

            // -------------------------------------
            // auto token from simple string test (till now the search string had to be seperated into the tokens manually by using spaces at the right place - now with this it will work without the need of specific definition)
            function autoTokenSearchStrings($searchString) {
                $quxxing = token_get_all("<?php $searchString ?>");
                array_walk($quxxing, function(&$data, $i) { // flatten token array
                    $data = is_array($data) ? $data[1] : $data;
                    $data = trim($data);
                    if ($data === '<?php' || $data === '?>' || $data === '')
                        $data = null;
                });
                $quxxing = array_values(array_filter($quxxing));

                return $quxxing;
                //d($search_foo, $quxxing);
            }

            $search_foo = autoTokenSearchStrings($search_start);
            // end test auto token
            // -------------------------------------

            //$search_foo = ['new', ' ', 'PageIndexerAction'];

            foreach ($tokens as $i => $token) {
                if (trim($token) !== '') {
                    if ($start !== -1 && $end === -1) {
                        // only increase / descrease bracket counting as long as we are within a matching phase (so the start was already found, but not yet the end)
                        if ($token === '(')
                            $bracket_counter['_ROUND']++;
                        if ($token === '{')
                            $bracket_counter['_CURLY']++;
                        if ($token === '[')
                            $bracket_counter['_SQUARE']++;
                        if ($token === ')')
                            $bracket_counter['_ROUND']--;
                        if ($token === '}')
                            $bracket_counter['_CURLY']--;
                        if ($token === ']')
                            $bracket_counter['_SQUARE']--;
                    }

                    $curr_search = current($search_foo); // get the current search word from the search string chunks (by using array traversion pointer)

                    //d($foo, $i);

                    //d($curr_search);

                    if ($token === $curr_search) { // if the currently iterated token from the php string matches the current search chunk string:

                        //d('found', $token,  $curr_search);
                        if ($start === -1) // for the first match: save the index where we found that match
                            $start = $i;

                        // if
                        // 1. the start search string was already completly found
                        // 2. we did not reach the end of the search
                        // 3. and all brackets that were opened within the full matching phase were all closed again already:
                        // -> we reached the end of our matching phase and are fully done (because we found the last search chunk of the closing search-string chunk)
                        if ($start_complete && $end === -1 && array_sum($bracket_counter) === 0) {
                            $end = $i;
                        }

                        if (!next($search_foo)) { // if the pointer on the current search word chunk can not be pushed forth (because it reached the end): set the search chunk array to the content of the CLOSING search chunk array - this means that we finishe the start-search string matching

                            //if($search_foo !== ($quxx = explode(' ', $search_closing))) { // .. only if we do not use the closing search chunks already...
                            if ($search_foo !== ($quxx = autoTokenSearchStrings($search_closing))) {
                                $search_foo = $quxx; // set the search array to the closing string array (instead of start)
                                $start_complete = true;
                            }
                        }

                        // bonus: mark where the start search chunk ends...
                        if ($start_complete && $end_start_match === -1)
                            $end_start_match = $i;
                    } else { // when the current iterated token does NOT match the current search string chunk

                        if (!$start_complete) { // if we did not finish the start search sequence yet: rest everything back to the start (because this means that we only had a partial match to this point but everything following won't match the rest of our search string)
                            $start = -1;
                            reset($search_foo);
                            $bracket_counter = ['_ROUND' => 0, '_CURLY' => 0, '_SQUARE' => 0]; // untested... do we really need this? (I think we do...)
                        } // TODO I think we need a branch here for the ending string search as well?
                        elseif ($end !== -1) {
                            // if we found the end of our search and are completly done: cancel the search.
                            // NOTE: this will prevent us from having multiple definitions interpreted!!
                            break;
                        }
                        //d('resetting', $token,  $curr_search);
                    }
                } // end main conditon (which skipps empty token slots)

            } // main foreach loop

            //d($start, $end_start_match, $end);

            //!d($search_foo);

            //!d($bracket_counter);

            //!d(implode('', array_slice($tokens, $start, $end - $start + 1)));
            eval(
                rtrim(implode('', array_slice($tokens, $start, $end - $start + 1)), ';') . ';'
            );
        }

    }

}
