<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>SIG TEST</title>
</head>

<body>
<form action="/data/sigtest/" method="post">
PUBLIC KEY: <input name="publickey" maxlength="66"><br>
MSG: <input name="msg" maxlength="64"><br>
SIG: <input name="sig"><br>
<input type="submit" value="提交">
</form>
<br>
<?=date("Y-m-d H:i:s")?>
</body>
</html>