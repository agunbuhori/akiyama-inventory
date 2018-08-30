<?php

namespace App\Http\Controllers;

use App\Translate;
use Illuminate\Http\Request;

class JavascriptController extends Controller
{
    public function phpjs()
    {
        $userLanguage = 'jp';
        $userCurrency = '¥';
        $dateFormat = 'YYYY年MM月DD日';

        if (auth()->check())
            switch (auth()->user()->language) {
                case 'en':
                    $userLanguage = 'english';
                    $userCurrency = '¥';
                    $dateFormat = 'YYYY-MM-DD';
                    break;
                case 'jp':
                    $userLanguage = 'japanese';
                    $userCurrency = '¥';
                    $dateFormat = 'YYYY年MM月DD日';
                    break;
                case 'id':
                    $userLanguage = 'indonesia';
                    $userCurrency = 'Rp';
                    $dateFormat = 'YYYY MM DD';
                    break;
            }

        $langs = Translate::pluck($userLanguage, 'key');

        echo 'var languages = '.json_encode($langs).'; const dateFormat  = "'.$dateFormat.'";';
    	
        $scripts = "
    		function isCentral() {
    			return ".(auth()->user()->isCentral() ? "true" : "false").";
    		}
    		function isStore() {
    			return ".(auth()->user()->isStore() ? "true" : "false").";
    		}
            function translate(word) {
                return typeof languages[word] != 'undefined' ? languages[word] : word;
            }
            function currency(num) {
                if (typeof num != 'undefined' || num != null) {
                    var negative = num < 0 ? '-' : '';
                    num = Math.abs(parseInt(num));
                    return negative+'".$userCurrency."'+num.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, `$1,`);
                }

                return num;
            }
    	";

    	return response(trim(preg_replace('/\s\s+/', ' ', $scripts)))->header('Content-Type', 'application/javascript');
    }
}
