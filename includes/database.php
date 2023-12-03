<?php
// Funzioni per l'attivazione e la disattivazione del plugin
function gv_activate() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    
    // Creazione tabella Vettori
    $table_vettori = $wpdb->prefix . 'vettori'; 
    $sql_vettori = "CREATE TABLE $table_vettori (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nome_vettore text NOT NULL,
        PRIMARY KEY  (id)
    ) " . $wpdb->get_charset_collate() . ";";
    dbDelta($sql_vettori);

    // Creazione tabella Ordini con la nuova colonna data_ordine
    $table_ordini = $wpdb->prefix . 'ordini';
    $sql_ordini = "CREATE TABLE $table_ordini (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        numero_ordine varchar(255) NOT NULL,
        data_ordine date NOT NULL,         
        data_consegna date NOT NULL,
        id_vettore mediumint(9) NOT NULL,
        PRIMARY KEY  (id),
        FOREIGN KEY  (id_vettore) REFERENCES $table_vettori(id)
    ) " . $wpdb->get_charset_collate() . ";";
    dbDelta($sql_ordini);
}

function gv_deactivate() {
    global $wpdb;
    $table_vettori = $wpdb->prefix . 'vettori';
    $table_ordini = $wpdb->prefix . 'ordini';
    $sql_vettori = "DROP TABLE IF EXISTS $table_vettori;";
    $sql_ordini = "DROP TABLE IF EXISTS $table_ordini;";
    $wpdb->query($sql_vettori);
    $wpdb->query($sql_ordini);
}
?>
