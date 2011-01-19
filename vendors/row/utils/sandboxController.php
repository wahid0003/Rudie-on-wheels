<?php

namespace row\utils;

use row\Controller;
use row\database\Model;

class sandboxController extends Controller {

	protected function _pre_action() {
		echo '<style>table { border-spacing:0; border-collapse:collapse; } tr > * { border:solid 2px #888; padding:5px; }</style>'."\n\n";
	}

	public function table_data( $table = null ) {
		if ( !$table ) {
			return $this->index();
		}
		$data = Model::dbObject()->select($table, '1');
		if ( !$data ) {
			exit('no data');
		}
		$this->printData($data);
	}

	public function table_structure( $table = null ) {
		if ( !$table ) {
			return $this->index();
		}
		$columns = Model::dbObject()->_getTableColumns($table);
		$this->printData($columns);
	}

	public function index() {
		$tables = Model::dbObject()->_getTables();
		echo '<ul>';
		foreach ( $tables AS $table ) {
			echo '<li><a href="'.$this->url('table-structure', $table).'">'.$table.' (<a href="'.$this->url('table-data', $table).'">data</a>)</a></li>';
		}
		echo '</ul>';
	}

	private function printData( $data ) {
		echo '<table><thead><tr>';
		foreach ( $data[0] AS $k => $v ) {
			echo '<th>'.$k.'</th>';
		}
		echo '</tr></thead><tbody>';
		foreach ( $data AS $row ) {
			echo '<tr>';
			foreach ( $row AS $v ) {
				echo '<td>'.$v.'</td>';
			}
			echo '</tr>';
		}
		echo '</tbody></table>';
	}

	private function url( $action, $more = '' ) {
		return '/'.$this->_dispatcher->_module.'/'.$action.( $more ? '/'.$more : '' );
	}

}

