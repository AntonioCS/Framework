<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->title ?></title>

<style type="text/css">
<?php //if ($this->auth_error) $this->getCSS('boxes.css') ?>
#auth_fset {
	position: relative;width: 400px;
}

body {
	margin:200px 0px; padding:0px;
	text-align:center;
}
	
#Content {
	width:500px;
	margin:0px auto;
	text-align:left;
	padding:15px;
}

</style>
</head>

<body>
<div id="Content">
	<fieldset id="auth_fset">
		<legend>Login</legend>		
		<?php echo $this->loadmodel('auth_ident_form')->callForm() ?>
	</fieldset>
</div>
</body>
</html>
