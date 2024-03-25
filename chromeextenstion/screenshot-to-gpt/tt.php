<?php
/**
 *
 * @author    balconet.co
 * @package   Tigon
 * @version   1.0.0
 * @since     1.0.0
 */

class excepter1 extends Exception {
}

class excepter2 extends Exception {
}

class ExcepterClass {
	public function run() {
		try {
			try {
				throw new excepter1( 'ONE' );
				throw new excepter2( 'TWO' );
				throw new excepter( 'THREE' );
			} catch ( Exception $ex ) {
				echo "1";
				throw $ex;
			} catch ( excepter1 $ex ) {
				echo "2";
				throw $ex;
			} catch ( excepter2 $ex ) {
			}
			echo "3";
			throw $ex;
		} catch ( excepter2 $ex ) {
			echo "A";
		} catch ( excepter1 $ex ) {
			echo "B";
		} catch ( excepter $ex ) {
			echo "C";
		}
	}
}

$obj = new ExcepterClass();
$obj->run();
