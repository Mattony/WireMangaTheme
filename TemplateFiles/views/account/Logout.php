<?php namespace ProcessWire;
$session->logout();
$session->redirect($config->urls->httpRoot);
?>
