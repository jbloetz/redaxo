<?php

echo rex_view::title('TestResults');

$runner = new rex_test_runner();
$runner->setUp();

echo '<pre>';
$runner->run(rex_test_locator::defaultLocator());
echo '</pre>';
