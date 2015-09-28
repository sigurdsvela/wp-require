<?php
use std\lang\ClassLoader as ClassLoader;

//Register a class loader that loads everything inside the WPRequire namespace
//and loads from the current directory
(new ClassLoader("WPRequire", __DIR__))->register();
