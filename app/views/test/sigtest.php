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
<br>
<form action="/trade/token" method="post">
<input name="f160" placeholder="f160"><br>
<input name="t160" placeholder="t160"><br>
<input name="v" value="1"><br>
<input type="submit"><br>
</form>
<br>
<br>
<form action="/trade/go" method="post">
<input name="pubkey" placeholder="pubkey"><br>
<input name="msg" placeholder="msg"><br>
<input name="sig" placeholder="sig"><br>
<input type="submit"><br>
</form>
<br>

<?=date("Y-m-d H:i:s")?>
</body>
</html>