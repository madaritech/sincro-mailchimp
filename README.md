Sincro Mailchimp Trial WP Plugin

To use plugin you need to add settings script to wp-config.php file as the following example :

$list['acme'] = 'e87b1536bb';
$list['test'] = '060a231f4f';
$interest['group1'] 		= 'dafaf73c29';
$interest['group2'] 		= 'ab364ca78d';
$interest['group3'] 		= '2bec2ca41d';
$interest['acme-group1'] 	= '27e89ce542';
$interest['acme-group2'] 	= '9e05bcd6ce';
$interest['acme-group3'] 	= 'cf5965131a';
$interest['acme-group4'] 	= '7cd76ab7e9';

$smc = array('administrator' => array($list['test'] => array($interest['group1'] => false,
			  												 $interest['group2'] => true,
			  												 $interest['group3'] => true)),

			 'editor' 		 => array($list['acme'] => array($interest['acme-group1'] => true, 
															 $interest['acme-group2'] => false, 
															 $interest['acme-group3'] => true, 
															 $interest['acme-group4'] => false)),
			 'author' 		 => array(),

			 'contributor'   => array($list['test'] => array($interest['group1'] => true,
			  												 $interest['group2'] => false,
			  												 $interest['group3'] => true)),

			 'subscriber' 	 => array($list['acme'] => array($interest['acme-group1'] => true, 
															 $interest['acme-group2'] => false, 
															 $interest['acme-group3'] => true, 
															 $interest['acme-group4'] => true), 
			  						  $list['test'] => array($interest['group1'] => false,
			  												 $interest['group2'] => true,
			  												 $interest['group3'] => false)));

define( "SINCRO_MAILCHIMP_CONFIG", serialize($smc) );