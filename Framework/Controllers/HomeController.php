<?php
namespace Framework\Controllers;

use Framework\Core\View as View;

class HomeController extends BaseController
{
	public function index(){
		return new View();
	}
}