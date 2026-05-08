<?php
/*
 * Blade directive for lorem ipsum text (@_lorem) AND random names (@_name(w), @_name(m))
 */

/* @var $this \STORMS\webframe\Modules\Module */

use STORMS\webframe\Core\WebFrame;

$this->extend([
    'init' => function($ret, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {

        /* @var $moduleObj \STORMS\webframe\Modules\Module */
        $moduleObj = $this;

        // ================================================

        /*
         * Lorem Ipsum dummy text directive
         * => @_lorem(<number of words>)
         */
        $blade->directive('_lorem', function (string $expression = null) use ($moduleObj) {
            return '<?php echo STORMS\webframe\Modules\Modules::inst("BladeDirectives")->getModuleByName(\''.$moduleObj->getName().'\')->getLoremIpsum('.$expression.') ?>';
        });

        /*
         * Dummy first name directive
         * => @_name(m) / @_name(w)
         */
        $blade->directive('_name', function (string $expression) use ($moduleObj) {
            return '<?php echo STORMS\webframe\Modules\Modules::inst("BladeDirectives")->getModuleByName(\''.$moduleObj->getName().'\')->getFirstName(\''.$expression.'\') ?>';
        });

        /*
         * Dummy surname directive
         * => @_sname
         */
        $blade->directive('_sname', function (string $expression) use ($moduleObj) {
            return '<?php echo STORMS\webframe\Modules\Modules::inst("BladeDirectives")->getModuleByName("'.$moduleObj->getName().'")->getSurname() ?>';
        });

        /*
         * Placerholder im
         * => @_placeholderImg
         */
        $blade->directive('_placeholderImgUrl', function (string $expression) use ($moduleObj) {
            // TODO
            //d($expression);

            // test.... gened by chatgpt
            $foobar = function (/*string $functionName, */string $signatureString) {
                // Entferne Leerzeichen und trenne die Werte durch Komma
                $signatureArray = explode(',', str_replace(' ', '', $signatureString));

                // Extrahiere die Werte und weise sie den Parametern zu
                $params = [];
                foreach ($signatureArray as $param) {
                    $params[] = trim($param, "'");
                }

                // Rufe die Funktion mit den extrahierten Parametern auf
                //call_user_func_array($functionName, $params);
                return $params;
            };

            //d($foobar($expression));

            return '<?php echo STORMS\webframe\Modules\Modules::inst("BladeDirectives")->getModuleByName("'.$moduleObj->getName().'")->getPlaceholderImgUrl('.$expression.') ?>';
        });

    },
    'getFirstName' => function (string $gender) { // TODO allow to pass an index for the line - so this method can be used to get a specific name
        /* @var $this \STORMS\webframe\Modules\Module */

        $gender = trim($gender);

        if($gender !== 'm' && $gender !== 'w')
            throw new \InvalidArgumentException('Please provide a gender (m/w)');

        $names_all = file("$this->_base/names_{$gender}_de_top_50.txt");
        $rnd_name = $names_all[array_rand($names_all)];

        if(strpos($rnd_name, '/') !== false) { // the file contains name variants separated by '/' ...
            $names_all = explode('/', $rnd_name);
            $rnd_name = $names_all[array_rand($names_all)];
        }

        return trim($rnd_name);
    },
    'getSurname' => function () { // TODO allow to pass an index for the line - so this method can be used to get a specific surname
        /* @var $this \STORMS\webframe\Modules\Module */

        $surnames_all = file("$this->_base/nachnamen_top_100.txt");
        $rnd_surname = $surnames_all[array_rand($surnames_all)];

        return trim($rnd_surname);
    },
    'getLoremIpsum' => function (int $words = 25) {
        /* @var $this \STORMS\webframe\Modules\Module */

        $words = $words > 0 ? $words: 150;

        $lorem_all = file_get_contents("$this->_base/lorem.txt");
        $lorem = array_slice(explode(' ', $lorem_all), 0, $words);

        return rtrim(implode(' ', $lorem),'.,') . '.';
    },
    'getPlaceholderImgUrl' => function (int $w, int $h, string $bgColor = 'f0f0f0', string $fgColor = 'cec', string $text = '') {
        /* @var $this \STORMS\webframe\Modules\Module */

        return sprintf('https://placehold.co/%dx%d/%s/%s?text=%s', $w, $h, str_replace('#', '', $bgColor), str_replace('#', '', $fgColor), urlencode($text));
    }
]);
