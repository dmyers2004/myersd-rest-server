<?php
class mainController extends Controller {

	function indexAction() {
		$this->data('page','main')->view('main/index','body',true);
	}

} /* end controller */
