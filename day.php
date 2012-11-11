<!DOCTYPE HTML>
<html lang="en-US" manifest="<?php echo $manifest; ?>">
<head>
	<meta charset="UTF-8">
	
	<title>Virtus</title>
	
	<meta name="viewport" content="initial-scale=1, user-scalable=no" />
	<meta name="apple-mobile-web-app-capable" content="yes"  />
	<meta names="apple-mobile-web-app-status-bar-style"  content="black-translucent" />
	
	<link rel="apple-touch-icon" href="<?php echo $icon; ?>" />
	
	<?php Stack::out( 'virtus_header_css', '<link rel="stylesheet" type="text/css" href="%s" media="all" />' . "\n" ); ?>
	
	<script type="text/javascript">
		url = '<?php echo $url; ?>';
		refresh_url = '<?php echo $refresh_url ;?>';
	</script>
	
	<?php Stack::out( 'virtus_header_javascript', '<script src="%s" type="text/javascript"></script>' . "\n" ); ?>
	
</head>
<body class="iphone">
		
	<div id="page">
		
		CONTENT WILL BE THERE
	
	</div>
	
</body>
</html>