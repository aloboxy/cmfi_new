<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : techexplorer school management system
 * @version : 2.0
 * @developed by : techexplorerCoder
 * @support : techexplorercoder@yahoo.com
 * @author url : http://codecanyon.net/user/techexplorerCoder
 * @filename : Accounting.php
 * @copyright : Reserved techexplorerCoders Team
 */

class Errors extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('errors/error_404_message.php');
    }
}
