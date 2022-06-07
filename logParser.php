<?php

function getUrl(string $logLine)
{
    preg_match('/(http|ftp|https):\/\/([\w_-]+(?:(?:\.[\w_-]+)+))([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-])/', $logLine, $matches);
    return $matches[0];
}

function getRequestMethod(string $logLine)
{
    preg_match('/\w+(?=\s\/)/', $logLine, $matches);
    return $matches[0];
}

function getTraffic(string $logLine)
{
    preg_match('/\d+(?= "http)/', $logLine, $matches);
    return $matches[0];
}

function getCrawlerBot(string $logLine)
{
    preg_match('/Googlebot|Yandex|bingbot|Baiduspider/', $logLine, $matches);
    if ($matches != []) {
        return $matches[0];
    }
}

function getStatusCode(string $logLine)
{
    preg_match('/(?<=)\d{3}(?=\s\d)/', $logLine, $matches);
    return $matches[0];
}

function getUrlsViews(array $urls)
{
    return array_count_values($urls);
}

function getCrawlerCount(array $crawlers)
{
    return array_count_values(array_filter($crawlers));
}

function getStatusCodesCount(array $statusCodes)
{
    return array_count_values($statusCodes);
}

// Get log from terminal.
$log = file($argv[1]);

$urls = [];
$traffic = [];
$crawlerBots = [];
$statusCodes = [];

foreach ($log as $line) {

    $urls[] = getUrl($line);

    $methodOfRequest = getRequestMethod($line);

    if ($methodOfRequest === 'POST') {
        $traffic[] = getTraffic($line);
    }

    $crawlerBots[] = getCrawlerBot($line);

    $statusCodes[] = getStatusCode($line);

}

$urlsViewsCount = getUrlsViews($urls);
$crawlerBotsCount = getCrawlerCount($crawlerBots);
$statusCodesCount = getStatusCodesCount($statusCodes);

$result = json_encode(
    [
        'views' => count($log),
        'urls' => count($urlsViewsCount),
        'traffic' => array_sum($traffic),
        'crawlers' => $crawlerBotsCount,
        'statusCodes' => $statusCodesCount,
    ], JSON_PRETTY_PRINT);

print_r($result);