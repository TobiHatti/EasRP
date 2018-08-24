<?php
    echo '<h2>Bestellverlauf</h2>';

    $customer_number = $_GET['s'];
    $strSQL = "SELECT * FROM customer_order
    INNER JOIN customers ON customer_order.customer_number = customers.customer_number
    INNER JOIN orders ON customer_order.order_number = orders.order_number
    WHERE customer_order.customer_number = '$customer_number' ORDER BY orders.order_number DESC";
    echo OrderTable($strSQL,"X|Löschen|R|RM","C|Zahlungsstatus|Y|PS");

?>