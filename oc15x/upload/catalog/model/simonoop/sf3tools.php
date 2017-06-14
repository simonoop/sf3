<?php
/*####################################################################################
##
## FRONTEND
##
## sf3 - 3.0.0 Build 0052
##
####################################################################################*/
class ModelSimonoopSF3tools extends Model {

	public function loadsf3(){
		if(isset($_SESSION['_sf3data']) && $_SESSION['_sf3data']=='nocarrier')return false;
		$DIR_APPLICATION = $this->config->get("sf3_DIR_APPLICATION");
		$file  = $DIR_APPLICATION . 'model/simonoop/sf3.php';
		if(file_exists($file)) {
			include_once($file);
			$class = "ModelSimonoopSf3";
			$this->registry->set('model_simonoop_sf3_manager', new $class($this->registry));
			if (isset($_SESSION['_sf3data'])) {
				$file = DIR_APPLICATION . 'model/' . $_SESSION['_sf3data'] . '.php';
				$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['_sf3data']);
				if (file_exists($file)) {
					include_once($file);
					$this->registry->set('model_simonoop_sf3', new $class($this->registry));
					return true;
				}
			}
		}

		return false;
	}
}