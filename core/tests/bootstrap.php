<?php
/**
 * @name bootstrap.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-12
 * Encoding UTF-8
 */
require dirname(__FILE__).'/../../cms.php';
require dirname(__FILE__).'/TestConfig.php';
$environment = new Environment(new TestConfig(),'CmsApplication','test');
$environment->prepare();