
<? $this->title('Structure of `'.$table.'`') ?>

<p><a href="<?=$app->_url('table-data', $table)?>">Table data</a></p>

<?php

echo '<table><thead><tr>';
$k0 = key($columns);
foreach ( $columns[$k0] AS $k => $v ) {
	echo '<th>'.$k.'</th>';
}
echo '</tr></thead><tbody>';
foreach ( $columns AS $row ) {
	echo '<tr>';
	$first = true;
	foreach ( $row AS $k => $v ) {
		echo '<td>' . ( $first ? row\utils\Inflector::spacify($v) : $v ) . '</td>';
		$first = false;
	}
	echo '</tr>';
}
echo '</tbody></table>';


