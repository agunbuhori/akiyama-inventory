<?php

$userLanguage = 'japanese';

if (auth()->check())
	switch (auth()->user()->language) {
		case 'en':
			$userLanguage = 'english';
			break;
		case 'jp':
			$userLanguage = 'japanese';
			break;
		case 'id':
			$userLanguage = 'indonesia';
			break;
	}

$translates = App\Translate::pluck($userLanguage, 'key');

return $translates;