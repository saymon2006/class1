<?php
$url = "https://everify.bdris.gov.bd";

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Set the path to the CA certificate file
$cacertPath = "/home/upitcarecom/public_html/birth/cacert.pem";
curl_setopt($ch, CURLOPT_CAINFO, $cacertPath);

// Disable SSL verification temporarily (for troubleshooting purposes)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

// Execute cURL session and fetch response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    die("Error: " . curl_error($ch));
}

// Get the HTTP response code
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($http_code != 200) {
    die("Error: Unable to load page. HTTP Code: " . $http_code);
}

// Close cURL session
curl_close($ch);

// Use DOMDocument to parse the HTML response
$doc = new DOMDocument();
libxml_use_internal_errors(true); // Suppress warnings about invalid HTML
$doc->loadHTML($response);

// Get the xpath object
$xpath = new DOMXPath($doc);

// Query for the captcha image URL using CSS selectors
$captchaNodes = $xpath->query('//img[contains(@src, "captcha-image")]');

if ($captchaNodes->length > 0) {
    $captchaImageUrl = $captchaNodes->item(0)->getAttribute('src');
    echo "<img src=\"$captchaImageUrl\" alt=\"Captcha Image\" />";
} else {
    echo "Captcha not found in the response.";
}

// Get and display the entire HTML body content
$htmlBody = $xpath->query('//body')->item(0)->C14N(); // Extract body content as plain HTML
echo $htmlBody;
?>
