<?php
// Funzioni per aggiungere le pagine di amministrazione
function gv_add_admin_pages() {
    add_menu_page('Riepilogo', 'Gestione Vettori', 'manage_options', 'gv_riepilogo', 'gv_riepilogo_page');
    add_submenu_page('gv_riepilogo', 'Vettori', 'Vettori', 'manage_options', 'gv_vettori', 'gv_vettori_page');
    add_submenu_page('gv_riepilogo', 'Ordini', 'Ordini', 'manage_options', 'gv_ordini', 'gv_ordini_page');
}

function gv_riepilogo_page() {
    echo '<div class="wrap"><h1>Riepilogo</h1></div>';
}

function gv_vettori_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vettori';

    echo '<div class="wrap">
            <h1>Vettori</h1>
            <form id="new-vettore-form">
                <input type="text" id="nome_vettore" name="nome_vettore" required>
                <input type="submit" value="Aggiungi Vettore">
            </form>';

    // Recupero dei vettori dal database
    $vettori = $wpdb->get_results("SELECT * FROM $table_name");

    if($vettori) {
        echo '<h2>Elenco Vettori</h2>
              <ul id="elenco-vettori">';
        foreach ($vettori as $vettore) {
            echo '<li id="vettore-' . esc_attr($vettore->id) . '">'
                 . esc_html($vettore->nome_vettore)
                 . ' <button class="delete-vettore" data-id="' . esc_attr($vettore->id) . '">Elimina</button></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>Nessun vettore trovato.</p>';
    }

    echo '</div>';
}

function gv_ordini_page() {
    global $wpdb;
    $table_vettori = $wpdb->prefix . 'vettori';
    $vettori = $wpdb->get_results("SELECT * FROM $table_vettori");

    // Form per inserire un nuovo ordine
    echo '<div class="wrap">
            <h1>Ordini</h1>
            <form id="new-ordine-form">
                <label for="numero_ordine">N. Ordine</label>
                <input type="text" id="numero_ordine" name="numero_ordine" required>

                <label for="data_ordine">Data dell\'Ordine</label>
                <input type="date" id="data_ordine" name="data_ordine" required>
                
                <label for="data_consegna">Data di Consegna</label>
                <input type="date" id="data_consegna" name="data_consegna">

                <label for="id_vettore">Vettore</label>
                <select id="id_vettore" name="id_vettore">';
    foreach ($vettori as $vettore) {
        echo '<option value="' . esc_attr($vettore->id) . '">' . esc_html($vettore->nome_vettore) . '</option>';
    }
    echo '</select>
          <input type="submit" value="Inserisci Ordine">
          </form>';

    // Recupero degli ordini dal database
    $table_ordini = $wpdb->prefix . 'ordini';
    $ordini = $wpdb->get_results("SELECT o.id, o.numero_ordine, o.data_ordine, o.data_consegna, v.nome_vettore FROM $table_ordini o INNER JOIN $table_vettori v ON o.id_vettore = v.id");

    echo '<h2>Elenco Ordini</h2>';
    if ($ordini) {
        echo '<table class="gv-ordini-tabella">
                <thead>
                    <tr>
                        <th>Numero Ordine</th>
                        <th>Data Ordine</th>
                        <th>Data Consegna</th>
                        <th>Vettore</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($ordini as $ordine) {
            echo '<tr>
                    <td>' . esc_html($ordine->numero_ordine) . '</td>
                    <td>' . esc_html($ordine->data_ordine) . '</td>
                    <td><input type="date" class="data-consegna" data-id="' . esc_attr($ordine->id) . '" value="' . esc_attr($ordine->data_consegna) . '"></td>
                    <td>' . esc_html($ordine->nome_vettore) . '</td>
                    <td>
                        <button class="update-ordine" data-id="' . esc_attr($ordine->id) . '">Aggiorna</button>
                        <button class="delete-ordine" data-id="' . esc_attr($ordine->id) . '">Elimina</button>
                    </td>
                  </tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>Nessun ordine trovato.</p>';
    }

    echo '</div>';
}
?>
