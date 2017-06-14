<?php
/*####################################################################################
##
## FRONTEND
##
## sf3 - 3.0.0 Build 0052
##
####################################################################################*/
require_once("sf3oc1564.php");

class ModelSimonoopSF3oc2011 extends ModelSimonoopSF3oc1564
{

	/*####################################################################################
   ##
   ## return correct replace this OC version
   ##
   ####################################################################################*/
	protected function replaceSQL($sql, $replacement)
	{
		$preg_replace_nth = function ($pattern, $replacement, $subject, $nth = 1) {
			return preg_replace_callback($pattern, function ($found) use (&$pattern, &$replacement, &$nth) {
				$nth--;
				if ($nth == 0) return preg_replace($pattern, $replacement, reset($found));
				return reset($found);
			}, $subject, $nth);
		};
		$i = array(
			'getProducts' => 4,
			'getTotalProducts' => 1,
			'getProductSpecials' => 0,
			'iSearchStandard' => 3,
		);

		return $preg_replace_nth('/where/i', " WHERE " . $replacement, $sql, $i[$this->OCFunction]);
	}

}	
