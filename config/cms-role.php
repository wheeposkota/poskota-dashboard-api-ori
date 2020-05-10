<?php

return [
    'name' => 'Role',
    'exclude_permission_id' => [
    	'post' => [
    		'delete'
    	]
    ],
    'aclRepository' => \Gdevilbat\SpardaCMS\Modules\Role\Repositories\SingleBrandAuthentication::class
];
