<?php
// Aggiungi i hook per le funzioni AJAX
add_action('wp_ajax_add_vettore', 'gv_add_vettore_ajax');
add_action('wp_ajax_delete_vettore', 'gv_delete_vettore_ajax');
add_action('wp_ajax_add_ordine', 'gv_add_ordine_ajax');
add_action('wp_ajax_delete_ordine', 'gv_delete_ordine_ajax');
add_action('wp_ajax_update_ordine', 'gv_update_ordine_ajax');
add_action('wp_ajax_gv_get_puntualita_data', 'gv_get_puntualita_data');
add_action('wp_ajax_nopriv_gv_get_puntualita_data', 'gv_get_puntualita_data');

// Funzioni AJAX
function gv_add_vettore_ajax() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vettori';
    $nome_vettore = sanitize_text_field($_POST['nome_vettore']);
    
    $wpdb->insert($table_name, ['nome_vettore' => $nome_vettore]);
    
    echo 'Vettore Aggiunto';
    wp_die();
}

function gv_delete_vettore_ajax() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vettori';
    $id_vettore = intval($_POST['id']);
    
    $wpdb->delete($table_name, ['id' => $id_vettore]);
    
    echo 'Vettore Eliminato';
    wp_die();
}

function gv_add_ordine_ajax() {
    global $wpdb;
    $table_ordini = $wpdb->prefix . 'ordini';
    
    $numero_ordine = sanitize_text_field($_POST['numero_ordine']);
    $data_ordine = sanitize_text_field($_POST['data_ordine']);
    $data_consegna = sanitize_text_field($_POST['data_consegna']);
    $id_vettore = intval($_POST['id_vettore']);

    $wpdb->insert(
        $table_ordini,
        array(
            'numero_ordine' => $numero_ordine,
            'data_consegna' => $data_consegna,
            'id_vettore' => $id_vettore
        )
    );

    echo 'Ordine inserito con successo';
    wp_die();
}

function gv_delete_ordine_ajax() {
    global $wpdb;
    $table_ordini = $wpdb->prefix . 'ordini';
    $id_ordine = intval($_POST['id']);
    
    $wpdb->delete($table_ordini, ['id' => $id_ordine]);
    
    echo 'Ordine Eliminato';
    wp_die();
}

function gv_update_ordine_ajax() {
    global $wpdb;
    $table_ordini = $wpdb->prefix . 'ordini';
    $id_ordine = intval($_POST['id']);
    $data_consegna = sanitize_text_field($_POST['data_consegna']);

    $wpdb->update(
        $table_ordini,
        ['data_consegna' => $data_consegna],
        ['id' => $id_ordine]
    );

    echo 'Data di consegna aggiornata con successo';
    wp_die();
}

// Funzione per gestire la richiesta AJAX e restituire i dati per il grafico
function gv_get_puntualita_data() {
    global $wpdb;
    $table_ordini = $wpdb->prefix . 'ordini';
    $table_vettori = $wpdb->prefix . 'vettori';

    $data_inizio = isset($_POST['data_inizio']) ? sanitize_text_field($_POST['data_inizio']) : '';
    $data_fine = isset($_POST['data_fine']) ? sanitize_text_field($_POST['data_fine']) : '';

    $where_clause = '1=1';
    $query_params = array();
    if (!empty($data_inizio)) {
        $where_clause .= " AND o.data_ordine >= %s";
        $query_params[] = $data_inizio;
    }
    if (!empty($data_fine)) {
        $where_clause .= " AND o.data_ordine <= %s";
        $query_params[] = $data_fine;
    }

    $query = "SELECT v.nome_vettore, COUNT(*) as totali,
              SUM(CASE WHEN o.data_consegna < o.data_ordine THEN 1 ELSE 0 END) as anticipati,
              SUM(CASE WHEN o.data_consegna = o.data_ordine THEN 1 ELSE 0 END) as puntuali,
              SUM(CASE WHEN o.data_consegna > o.data_ordine THEN 1 ELSE 0 END) as ritardati
              FROM $table_ordini o
              INNER JOIN $table_vettori v ON o.id_vettore = v.id
              WHERE $where_clause
              GROUP BY v.nome_vettore";
    $prepared_query = $wpdb->prepare($query, $query_params);
    $results = $wpdb->get_results($prepared_query, ARRAY_A);

    $data_for_chart = [["Vettore", "Anticipati", "Puntuali", "Ritardati"]];
    foreach ($results as $row) {
        $data_for_chart[] = [
            $row['nome_vettore'],
            (int)$row['anticipati'],
            (int)$row['puntuali'],
            (int)$row['ritardati']
        ];
    }

    wp_send_json(array('data' => $data_for_chart));
    wp_die();
}

?>