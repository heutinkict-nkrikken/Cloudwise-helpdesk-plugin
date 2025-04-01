<?php
/**
 * Plugin Name: Cloudwise dashboard widget
 * Plugin URI: https://github.com/heutinkict-nkrikken/Cloudwise-helpdesk-plugin.git
 * Description: Een eenvoudige widget voor het tonen van hulpdeskinformatie in het WordPress-dashboard.
 * Version: 2.0
 * Author: Cloudwise
 * Author URI: https://cloudwise.nl
 * License: GPL2
 */

// Voorkom directe toegang
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// Voeg de widget toe aan het WordPress-dashboard
function hulpdesk_dashboard_widget() {
  wp_add_dashboard_widget(
      'hulpdesk_widget',         // Widget ID
      'Hulpdesk Informatie',     // Widget titel
      'hulpdesk_widget_content'  // Functie die de inhoud van de widget weergeeft
  );
}
add_action('wp_dashboard_setup', 'hulpdesk_dashboard_widget');

// Functie die de inhoud van de widget genereert
function hulpdesk_widget_content() {
  echo '<h2>Cloudwise</2>';
  echo '<p><strong>Website dashboard</strong><br />';
  echo 'Welkom op het dashboard van je website. Gebruik de menu\'s hiernaast om je website aan te passen.</p>';
  echo '<p><strong>Contact met onze servicedesk</strong><br />';
  echo 'Heb je hulp nodig bij het beheren van je website, neem dan een kijkje in onze online kennisbank. Hier vind je antwoord op de meest gestelde vragen. Kom je er niet uit, dan kun je contact opnemen met onze servicedesk.</p>';
  echo '<p><strong>Ondersteuning</strong><br/ >';
  echo 'Heb je vragen over je website waar je met behulp van onze handleidingen niet uitkomt? Dan kun je contact opnemen met onze servicedesk op nummer <strong>074 240 46 66</strong> of mailen naar <a href="mailto:schoolwebsite@cloudwise.nl">schoolwebsite@cloudwise.nl</a>';
  echo '<p>Je kunt ook een melding indienen via de Online Servicedesk. Meer informatie over hoe je ons kunt bereiken vind je op <a href="https://www.cloudwise.nl/service/" target="_blank">www.cloudwise.nl/service</a></p>';
}

// Voeg een versiecontrole toe via de GitHub API
function cloudwise_check_for_plugin_update() {
// GitHub repository URL voor de plugin release
$url = 'https://api.github.com/repos/heutinkict-nkrikken/Cloudwise-helpdesk-plugin/releases/latest';

// cURL-aanroep om de laatste release-informatie op te halen
$response = wp_remote_get($url, array(
    'timeout' => 15,
    'headers'  => array(
        'User-Agent' => 'WordPress' // GitHub vereist een User-Agent header
    )
));

if (is_wp_error($response)) {
    return; // Stop als er een fout optreedt
}

// Zet de response om in een object
$data = json_decode(wp_remote_retrieve_body($response));

// Check of er een geldig resultaat is
if (!isset($data->tag_name)) {
    return; // Stop als er geen geldige versie is gevonden
}

// Haal de nieuwste versie op van GitHub
$latest_version = $data->tag_name;

// Haal de huidige versie van de plugin op
$current_version = get_plugin_data(WP_PLUGIN_DIR . '/cloudwise-helpdesk-plugin/cloudwise-helpdesk-plugin.php')['Version'];

// Vergelijk de huidige versie met de laatste versie op GitHub
if (version_compare($current_version, $latest_version, '<')) {
    // Als er een nieuwe versie is, bied dan de update aan via transients
    add_filter('site_transient_update_plugins', 'cloudwise_add_update_to_transient', 10, 1);

    // Toon een admin-notice voor de update
    add_action('admin_notices', 'cloudwise_update_notification');
}
}
add_action('admin_init', 'cloudwise_check_for_plugin_update');

// Functie om de update-informatie toe te voegen aan de transients
function cloudwise_add_update_to_transient($transient) {
// Haal de GitHub release data opnieuw op
$url = 'https://api.github.com/repos/heutinkict-nkrikken/Cloudwise-helpdesk-plugin/releases/latest';
$response = wp_remote_get($url, array(
    'timeout' => 15,
    'headers'  => array(
        'User-Agent' => 'WordPress' // GitHub vereist een User-Agent header
    )
));

if (is_wp_error($response)) {
    return $transient; // Stop als er een fout optreedt
}

// Zet de response om in een object
$data = json_decode(wp_remote_retrieve_body($response));

// Haal de laatste versie- en downloadinformatie
$latest_version = $data->tag_name;
$package_url = $data->zipball_url;

$transient->response['cloudwise-helpdesk-plugin/cloudwise-helpdesk-plugin.php'] = (object) [
    'slug' => 'cloudwise-helpdesk-plugin',
    'plugin' => 'cloudwise-helpdesk-plugin/cloudwise-helpdesk-plugin.php',
    'new_version' => $latest_version, // Haal de versie op uit de release
    'url' => 'https://github.com/heutinkict-nkrikken/Cloudwise-helpdesk-plugin/releases',
    'package' => $package_url // Gebruik de juiste versie van je release
];

return $transient;
}

// Functie om de notificatie weer te geven in het WordPress-dashboard
function cloudwise_update_notification() {
echo '<div class="updated notice is-dismissible">';
echo '<p><strong>Er is een nieuwe versie van de Cloudwise Helpdesk Plugin beschikbaar!</strong></p>';
echo '</div>';
}
