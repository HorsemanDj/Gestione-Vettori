<?php
/*
Plugin Name: Gestione Vettori 1.0
Description: Gestione dei vettori nel database.
Version: 1.0
Author: Riccardi Gaetano
*/

// Includi i file necessari
include_once plugin_dir_path(__FILE__) . 'includes/database.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-pages.php';
include_once plugin_dir_path(__FILE__) . 'includes/ajax-functions.php';

// Hook per l'attivazione e la disattivazione
register_activation_hook(__FILE__, 'gv_activate');
register_deactivation_hook(__FILE__, 'gv_deactivate');

// Hook per aggiungere le pagine di amministrazione
add_action('admin_menu', 'gv_add_admin_pages');

// Hook per gli script
function gv_enqueue_scripts() {
    wp_enqueue_style('gv-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('gv-ajax-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);
    wp_localize_script('gv-ajax-script', 'gv_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'gv_enqueue_scripts');

function gv_puntualita_vettori_shortcode() {
    global $wpdb;
    $table_ordini = $wpdb->prefix . 'ordini';
    $table_vettori = $wpdb->prefix . 'vettori';

    $query = "SELECT v.nome_vettore, COUNT(*) as totali, 
                     SUM(CASE WHEN o.data_consegna < o.data_ordine THEN 1 ELSE 0 END) as anticipati,
                     SUM(CASE WHEN o.data_consegna = o.data_ordine THEN 1 ELSE 0 END) as puntuali,
                     SUM(CASE WHEN o.data_consegna > o.data_ordine THEN 1 ELSE 0 END) as ritardati
              FROM $table_ordini o 
              INNER JOIN $table_vettori v ON o.id_vettore = v.id
              GROUP BY v.nome_vettore";

    $results = $wpdb->get_results($query, ARRAY_A);

    $data_for_chart = [["Vettore", "Anticipati", "Puntuali", "Ritardati"]];
    foreach ($results as $row) {
        $total = $row['totali'] > 0 ? $row['totali'] : 1; // Evita la divisione per zero
        $data_for_chart[] = [
            $row['nome_vettore'], 
            (int)$row['anticipati'], 
            (int)$row['puntuali'], 
            (int)$row['ritardati']
        ];
    }

    // Aggiunta dello script di Google Charts
    $chart_html = '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <div id="puntualita_vettori_chart"></div>
    <script type="text/javascript">
        google.charts.load("current", {"packages":["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable(' . json_encode($data_for_chart) . ');

            var options = {
                title: "Puntualità Vettori",
                hAxis: {title: "Vettori",  titleTextStyle: {color: "#333"}},
                vAxis: {minValue: 0},
                isStacked: true,
                colors: ["#FFFF00", "#008000", "#FF0000"] // Giallo per anticipi, Verde per puntuali, Rosso per ritardi
            };

            var chart = new google.visualization.ColumnChart(document.getElementById("puntualita_vettori_chart"));
            chart.draw(data, options);
        }
    </script>';

    return $chart_html;
}
add_shortcode('gv_puntualita_vettori', 'gv_puntualita_vettori_shortcode');


?>
