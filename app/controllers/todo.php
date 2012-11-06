<?php
class todoController extends Controller {

	function indexAction() {
		$this->data('page','todo')->view('todo/index','body',true);
	}

} /* end controller */
