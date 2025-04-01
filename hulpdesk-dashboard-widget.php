<?php
/**
 * Plugin Name: Cloudwise dashboard widget
 * Plugin URI: https://github.com/heutinkict-nkrikken/Cloudwise-helpdesk-plugin.git
 * Description: Een eenvoudige widget voor het tonen van hulpdeskinformatie in het WordPress-dashboard.
 * Version: 1.0
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
  echo '<p><strong>Website dashboard</strong><br />';
  echo 'Welkom op het dashboard van je website. Gebruik de menu\'s hiernaast om je website aan te passen..</p>';
  echo '<p><strong>Contact met onze servicedesk</strong><br />';
  echo 'Heb je hulp nodig bij het beheren van je website, neem dan een kijkje in onze online kennisbank. Hier vind je antwoord op de meest gestelde vragen. Kom je er niet uit, dan kun je contact opnemen met onze servicedesk.</p>';
  echo '<p><strong>Ondersteuning</strong><br/ >';
  echo 'Heb je vragen over je website waar je met behulp van onze handleidingen niet uitkomt? Dan kun je contact opnemen met onze servicedesk op nummer <strong>074 240 46 66</strong> of mailen naar <a href="mailto:schoolwebsite@cloudwise.nl">schoolwebsite@cloudwise.nl</a>';
  echo '<p>Je kunt ook een melding indienen via de Online Servicedesk. Meer informatie over hoe je ons kunt bereiken vind je op <a href="https://www.cloudwise.nl/service" target="_blank">www.cloudwise.nl/service</a></p>';
}
