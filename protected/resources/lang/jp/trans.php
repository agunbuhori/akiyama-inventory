<?php

	$langs = DB::table('translate')->get();
	$translate = [];

	foreach ($langs as $lang) {
		$translate[$lang->key] = $lang->translate; 
	}

	return array_merge($default, $translate);
?>