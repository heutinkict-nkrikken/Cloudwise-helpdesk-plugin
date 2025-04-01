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
    echo '<h2>Cloudwise!</2>';
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

  // Haal de nieuwste versie op van GitHub
  $latest_version = $data->tag_name;

  // Haal de huidige versie van de plugin op
  $current_version = get_plugin_data(WP_PLUGIN_DIR . '/cloudwise-helpdesk-plugin/cloudwise-helpdesk-plugin.php')['Version'];

  // Vergelijk de huidige versie met de laatste versie op GitHub
  if (version_compare($current_version, $latest_version, '<')) {
      // Als er een nieuwe versie is, bied dan de update aan via transients
      add_filter('site_transient_update_plugins', function($transient) use ($latest_version) {
          $transient->response['cloudwise-helpdesk-plugin/cloudwise-helpdesk-plugin.php'] = (object) [
              'slug' => 'cloudwise-helpdesk-plugin',
              'plugin' => 'cloudwise-helpdesk-plugin/cloudwise-helpdesk-plugin.php',
              'new_version' => $latest_version,
              'url' => 'https://github.com/heutinkict-nkrikken/Cloudwise-helpdesk-plugin/releases',
              'package' => 'https://github.com/heutinkict-nkrikken/Cloudwise-helpdesk-plugin/archive/' . $latest_version . '.zip'
          ];
          return $transient;
      });

      // Toon een admin-notice voor de update
      add_action('admin_notices', 'cloudwise_update_notification');
  }
}
add_action('admin_init', 'cloudwise_check_for_plugin_update');

// Toon een melding in het WordPress dashboard als er een update beschikbaar is
function cloudwise_update_notification() {
    echo '<div class="updated"><p><strong>Er is een nieuwe versie van de Cloudwise Helpdesk Plugin beschikbaar!</strong> <a href="' . esc_url(admin_url('update-core.php')) . '">Klik hier om bij te werken.</a></p></div>';
}

// Functie om de plugin bij te werken via de GitHub-repository
function cloudwise_plugin_update($transient) {
    // Controleer alleen op updates als de plugin daadwerkelijk geïnstalleerd is
    if (empty($transient->checked)) {
        return $transient;
    }

    // Plugin URL en versie-informatie instellen
    $plugin_slug = 'cloudwise-helpdesk-plugin/cloudwise-helpdesk-plugin.php';
    $plugin_version = '2.0'; // Dit is de versie die je in je plugin bestand hebt gedefinieerd

    // Get the GitHub latest release data
    $response = wp_remote_get('https://api.github.com/repos/heutinkict-nkrikken/Cloudwise-helpdesk-plugin/releases/latest');
    if (is_wp_error($response)) {
        return $transient; // Stop als er een fout is
    }

    $data = json_decode(wp_remote_retrieve_body($response));
    if (isset($data->tag_name)) {
        // Als er een nieuwe versie beschikbaar is
        $new_version = $data->tag_name;
        if (version_compare($plugin_version, $new_version, '<')) {
            // Voeg de update informatie toe aan het transient object
            $transient->response[$plugin_slug] = (object) array(
                'slug' => $plugin_slug,
                'new_version' => $new_version,
                'url' => 'https://github.com/heutinkict-nkrikken/Cloudwise-helpdesk-plugin',
                'package' => $data->assets[0]->browser_download_url // Dit is de URL naar de ZIP van de release
            );
        }
    }

    return $transient;
}
add_filter('site_transient_update_plugins', 'cloudwise_plugin_update');
