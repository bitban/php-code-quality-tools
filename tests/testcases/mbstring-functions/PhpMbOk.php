<?php

$foo = mb_ereg("#[0-9]#", "bar");
$foo = mb_eregi("#[0-9]#", "bar");
$baz = "baz";
$foo = mb_eregi_replace("#[0-9]#", "bar", $baz);
$baz = "baz";
$foo = mb_ereg_replace("#[0-9]#", "bar", $baz);
$to = "falvarez@bitban.com";
$subject = "test mail";
$message = "this is a test mail";
mb_send_mail($to, $subject, $message);
$string = "bar";
$foo = mb_split("#[0-9]#", $string);
$pos = mb_stripos("foo-bar", "-");
$result = mb_stristr("foo-bar", "-");
$len = mb_strlen("foo-bar-baz");
$result = mb_strpos("foo-bar", "-");
$pos = mb_strrchr("foo-bar-baz", "-");
$pos = mb_strripos("foo-bar-baz", "-");
$pos = mb_strrpos("foo-bar-baz", "-");
$pos = mb_strstr("foo-bar-baz", "-");
$lower = mb_strtolower("THIS IS A UPPERCASE STRING");
$upper = mb_strtoupper("this is a lowercase string");
$substring = mb_substr("foo-bar-baz", 1, 4);
$count = mb_substr_count("foo-bar-foo-baz", "foo");
