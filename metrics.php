<?php
require __DIR__ . '/vendor/autoload.php';
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\APC;


if (!extension_loaded('apcu')) {
    die('APCu extension not loaded');
}

if (!function_exists('apcu_store')) {
    die('APCu functions not available');
}

$registry = new CollectorRegistry(new APC());
$renderer = new RenderTextFormat();
$result = $renderer->render($registry->getMetricFamilySamples());

header('Content-type: ' . RenderTextFormat::MIME_TYPE);
echo $result;