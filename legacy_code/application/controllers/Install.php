<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : techexplorer Diagnostic Management System
 * @version : 2.0
 * @developed by : techtune
 * @support : techexplorercoder@yahoo.com
 * @author url : http://codecanyon.net/user/techtune
 * @filename : Install.php
 */

class Install extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('install_model', '_install');
        if ($this->config->item('installed')) {
            redirect(site_url('authentication'));
        }
    }

    public function index()
    {
        $this->data['step'] = 1;
        if ($_POST) {
            if ($this->input->post('step') == 2) {
                $this->data['step'] = 2;
            }
			
            if ($this->input->post('step') == 3) {
                $this->data['step'] = 2;
                // Validating the hostname, the database name and the username. The password is optional
                $this->form_validation->set_rules('purchase_username', 'Envato Username', 'trim|required');
                $this->form_validation->set_rules('purchase_code', 'Purchase Code', 'trim|required|callback_purchase_validation');
                if ($this->form_validation->run() == true) {
					$file = APPPATH.'config/purchase_key.php';
					$text = json_encode(array($this->input->post('purchase_username'), $this->input->post('purchase_code')));
					@chmod($file, FILE_WRITE_MODE);
					write_file($file, $text);
					$this->data['step'] = 3;
                }
            }

            if ($this->input->post('step') == 4) {
                $this->data['step'] = 3;
                // Validating the hostname, the database name and the username. The password is optional
                $this->form_validation->set_rules('hostname', 'Hostname', 'trim|required');
                $this->form_validation->set_rules('database', 'Database', 'trim|required');
                $this->form_validation->set_rules('username', 'Username', 'trim|required');
                if ($this->form_validation->run() == true) {
                    $hostname = $this->input->post('hostname');
                    $username = $this->input->post('username');
                    $password = $this->input->post('password');
                    $database = $this->input->post('database');
                    // Connect to the database
                    try {
                        $link = @mysqli_connect($hostname, $username, $password, $database);
                        if (!$link) {
                            $this->data['mysql_error'] = "Error: Unable to connect to MySQL Database: " . mysqli_connect_error();
                        } else {
                            // Write the new database.php file
                            if ($this->_install->write_database_config($this->input->post())) {
                                $this->data['step'] = 4;
                            }
                            // Close the connection
                            mysqli_close($link);
                        }
                    } catch (Exception $e) {
                        $this->data['mysql_error'] = "Error: Unable to connect to MySQL Database. " . $e->getMessage();
                    }
                }
            }

            if ($this->input->post('step') == 5) {
                // Validating the diagnostic name, superadmin name, superadmin email, login username, login password
                $this->form_validation->set_rules('school_name', 'School Name', 'trim|required');
                $this->form_validation->set_rules('sa_name', 'Superadmin Name', 'trim|required');
                $this->form_validation->set_rules('sa_email', 'Superadmin Email', 'trim|required|valid_email');
                $this->form_validation->set_rules('sa_password', 'Superadmin Password', 'trim|required');
                $this->form_validation->set_rules('timezone', 'Timezone', 'trim|required');
                if ($this->form_validation->run() == true) {
					$purchaseCode = $this->purchase_code_verification();
					if ( isset($purchaseCode->status) && $purchaseCode->status ) {
						if (!empty($purchaseCode->sql)) {
							$encryption_key = bin2hex(substr(md5(rand()), 0, 10));
							$staff_id = substr(md5(rand() . microtime() . time() . uniqid()), 3, 7);
							
							try {
								$this->load->database();
								// Execute a multi query
								if (@mysqli_multi_query($this->db->conn_id, $purchaseCode->sql)) {
									$this->_install->clean_up_db_query();
									$schoolName = $this->input->post('school_name');
									$timezone = $this->input->post('timezone');
									$email = $this->input->post('sa_email');
									$password = $this->input->post('sa_password');
									// Superadmin add in the database
									$staff_data = array(
										'staff_id' => $staff_id,
										'name' => $this->input->post('sa_name'),
										'joining_date' => date('Y-m-d'),
										'email' => $email,
									);
									$this->db->insert('staff', $staff_data);
									$insert_id = $this->db->insert_id();

									// Save superadmin login credential information in the database
									$credential_data = array(
										'user_id' => $insert_id,
										'username' => $email,
										'password' => $this->_install->pass_hashed($password),
										'role' => 1,
										'active' => 1,
									);

									if ($this->db->insert('login_credential', $credential_data)) {
										// global settings DB update
										$this->db->where('id', 1);
										$this->db->update('global_settings', array(
											'institute_name' => $schoolName,
											'timezone' => $timezone,
										));
										// Write the new autoload.php file
										$this->_install->update_autoload_installed();
										// Write the new routes.php file
										$this->_install->write_routes_config();
										$this->_install->update_config_installed($encryption_key);
									}
								}
								$this->data['step'] = 5;
							} catch (Exception $e) {
								$this->data['step'] = 2;
								$this->data['purchase_error'] = "Database Configuration Error: " . $e->getMessage();
							}
						} else {
							$this->data['step'] = 2;
							$this->data['purchase_error'] = "SQL not found";
						}
					} else {
						$this->data['step'] = 2;
						$this->data['purchase_error'] = $purchaseCode->message;
					}
                } else {
                    $this->data['step'] = 4;
                }
            }
        }
        $this->load->view('install/index', $this->data);
    }
	
    public function purchase_validation($purchase_code)
    {
        $username = $this->input->post('purchase_username');
        $domain   = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown';

        $result = $this->_install->verify_license($username, $purchase_code, $domain);

        if ( ! $result->status) {
            $this->form_validation->set_message(
                'purchase_validation',
                $result->message
            );
            return false;
        }
        return true;
    }

	function purchase_code_verification()
	{
        // Read the credentials saved during Step 2
        $key_file = APPPATH . 'config/purchase_key.php';
        if ( ! file_exists($key_file)) {
            return (object)[
                'status'  => false,
                'message' => 'Purchase key file not found. Please restart installation.',
                'sql'     => ''
            ];
        }

        $key_data = json_decode(file_get_contents($key_file));
        if ( ! is_array($key_data) || count($key_data) < 2) {
            return (object)[
                'status'  => false,
                'message' => 'Invalid purchase key data. Please restart installation.',
                'sql'     => ''
            ];
        }

        $username      = $key_data[0];
        $purchase_code = $key_data[1];
        $domain        = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown';

        // Re-verify the license with the remote API
        $result = $this->_install->verify_license($username, $purchase_code, $domain);

        if ( ! $result->status) {
            return (object)[
                'status'  => false,
                'message' => $result->message,
                'sql'     => ''
            ];
        }

        // License valid — load the SQL schema
        $sql = file_exists(FCPATH . 'ramom.sql') ? file_get_contents(FCPATH . 'ramom.sql') : '';

        return (object)[
            'status'  => true,
            'message' => isset($result->message) ? $result->message : 'License verified successfully.',
            'sql'     => $sql
        ];
	}
	
}
