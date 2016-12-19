<?php

return array(

	'default' => 'sqlite',
	'connections' => array(

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => '',
			'username'  => 'root',
			'password'  => '',
		),

		'sqlite' => array(
			'database' => app_path() .'/database/production.sqlite',
		),
	),

);
