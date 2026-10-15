<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width,initial-scale=1" name="viewport">
    <meta name="keywords" content=""/>
    <meta name="description" content="Techexplorer - Installation">
    <meta name="author" content="Techexplorer">
    <title>School Management System — Installation</title>
    <link rel="shortcut icon" href="<?=base_url('uploads/app_image/logo-small.png')?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?=base_url('assets/vendor/bootstrap/css/bootstrap.css')?>"/>
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/select2/css/select2.css');?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/select2-bootstrap-theme/select2-bootstrap.min.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/vendor/font-awesome/css/all.min.css')?>" />
    <link rel="stylesheet" href="<?=base_url('assets/css/install.css')?>" />
    <script src="<?=base_url('assets/vendor/jquery/jquery.js')?>"></script>
</head>
	<body>
		<div class="container pmx">
			<div class="logo">
		        <img src="<?=base_url('uploads/app_image/logo-small.png');?>" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%220.9em%22 font-size=%2290%22>🎓</text></svg>';">
		    </div>

            <?php
            // Helper function to assign classes to step items
            function get_step_class($current_step, $this_step) {
                if ($current_step == $this_step) {
                    return 'active';
                } elseif ($current_step > $this_step) {
                    return 'completed';
                }
                return '';
            }
            ?>

            <section class="panel p-shadow">
                <div class="tabs-custom">
                    <ul class="nav nav-tabs txt-font-et">
                        <li class="<?=get_step_class($step, 1); ?>">
                            <a href="javascript:void(0);"><i class="fas fa-parachute-box"></i> <h5>Requirements</h5></a>
                        </li>
                        <li class="<?=get_step_class($step, 2); ?>">
                            <a href="javascript:void(0);"><i class="fas fa-laptop-code"></i> <h5>Purchase Code</h5></a>
                        </li>
                        <li class="<?=get_step_class($step, 3); ?>">
                            <a href="javascript:void(0);"><i class="fas fa-database"></i> <h5>Database</h5></a>
                        </li>
                        <li class="<?=get_step_class($step, 4); ?>">
                            <a href="javascript:void(0);"><i class="fas fa-server"></i> <h5>Configure</h5></a>
                        </li>
                        <li class="<?=get_step_class($step, 5); ?>">
                            <a href="javascript:void(0);"><i class="fas fa-check"></i> <h5>Completed</h5></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <?php if ($step == 1): ?>
                            <div class="tab-pane active">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><b>Extensions</b></th>
                                            <th><b>Result</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>PHP 5.6+ </td>
                                            <td>
                                                <?php
                                                    $error = false;
                                                    if (phpversion() < "5.6") {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>Your PHP version is " . phpversion() . "</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>v." . phpversion() . "</span>";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>MySQLi PHP Extension</td>
                                            <td>
                                                <?php 
                                                    if (!extension_loaded('mysqli')) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>Not enabled</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Enabled</span>";
                                                    }
                                                ?>
                                             </td>
                                        </tr>
                                        <tr>
                                            <td>cURL PHP Extension</td>
                                            <td>
                                                <?php 
                                                    if (!extension_loaded('curl')) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>Not enabled</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Enabled</span>";
                                                    }
                                                ?>
                                             </td>
                                        </tr>
                                        <tr>
                                            <td>OpenSSL PHP Extension</td>
                                            <td>
                                                <?php
                                                    if (!extension_loaded('openssl')) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>Not enabled</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Enabled</span>";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>MBString PHP Extension</td>
                                            <td>
                                                <?php
                                                    if (!extension_loaded('mbstring')) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>Not enabled</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Enabled</span>";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>GD PHP Extension</td>
                                            <td>
                                                <?php
                                                    if (!extension_loaded('gd')) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>Not enabled</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Enabled</span>";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Zip PHP Extension</td>
                                            <td>
                                                <?php
                                                    if (!extension_loaded('zip')) {
                                                        $error = true;
                                                        echo  "<span class='label label-danger'>Zip Extension is not enabled</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Enabled</span>";
                                                    }
                                                ?>
                                             </td>
                                        </tr>
                                        <tr>
                                            <td>allow_url_fopen</td>
                                            <td>
                                                <?php
                                                    $url_f_open = ini_get('allow_url_fopen');
                                                    if ($url_f_open != "1"
                                                        && strcasecmp($url_f_open,'On') != 0
                                                        && strcasecmp($url_f_open,'true') != 0
                                                        && strcasecmp($url_f_open,'yes') != 0) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>Allow_url_fopen is not enabled!</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Enabled</span>";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>application/config/autoload.php Writable</td>
                                            <td>
                                                <?php
                                                    if (!is_really_writable(APPPATH . 'config/autoload.php')) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>No (Permissions - 755)</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Yes</span>";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>application/config/config.php Writable</td>
                                            <td>
                                                <?php
                                                    if (!is_really_writable(APPPATH . 'config/config.php')) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>No (Permissions - 755)</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Yes</span>";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>application/config/database.php Writable</td>
                                            <td>
                                                <?php
                                                    if (!is_really_writable(APPPATH . 'config/database.php')) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>No (Permissions - 755)</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Yes</span>";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>application/config/routes.php Writable</td>
                                            <td>
                                                <?php
                                                    if (!is_really_writable(APPPATH . 'config/routes.php')) {
                                                        $error = true;
                                                        echo "<span class='label label-danger'>No (Permissions - 755)</span>";
                                                    } else {
                                                        echo "<span class='label label-success'>Yes</span>";
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <footer class="panel-footer">
                                    <?php 
                                        if ($error == false){
                                            echo '<div class="text-right">';
                                            echo form_open($this->uri->uri_string());
                                            echo '<button type="submit" name="step" class="btn btn-default" value="2">Start Installation <i class="fas fa-arrow-right"></i></button>';
                                            echo form_close();
                                            echo '</div>';
                                        }else{
                                            echo '<div class="text-right">';
                                            echo '<button class="btn btn-default" disabled value="2">Fix requirements to continue</button>';
                                            echo '</div>';
                                        }
                                     ?>
                                </footer>
                            </div>
                        <?php elseif ($step == 2) : ?>
                            <div class="tab-pane active">
                                <?php if (isset($purchase_error) && $purchase_error != '') { ?>
                                    <div class="alert alert-danger text-left">
                                        <i class="fas fa-exclamation-triangle"></i> <?php echo $purchase_error; ?>
                                    </div>
                                <?php } ?>
                                <?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered validate')); ?>
                                    <div class="form-group">
                                        <label for="purchase_username" class="col-md-3 control-label">Tech Explorer Username <span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="purchase_username" value="<?=set_value('purchase_username', 'local-developer')?>" autocomplete="off" required />
											<?php echo form_error('purchase_username', '<label id="purchase_username-error" class="error" for="purchase_username">', '</label>'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="purchase_code" class="col-md-3 control-label">Purchase Code <span class="required">*</span></label>
                                        <div class="col-md-9 mb-ma">
                                            <input type="text" class="form-control" name="purchase_code" value="<?=set_value('purchase_code', 'offline-installation-license-bypassed')?>" autocomplete="off" required />
											<?php echo form_error('purchase_code', '<label id="purchase_code-error" class="error" for="purchase_code">', '</label>'); ?>
                                        </div>
                                    </div>
                                    <footer class="panel-footer">
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-default" name="step" value="3">Next Step <i class="fas fa-arrow-right"></i></button>
                                        </div>
                                    </footer>
                                <?php echo form_close(); ?>
                            </div>
                        <?php elseif ($step == 3) : ?>
                            <div class="tab-pane active">
                                <?php if (isset($mysql_error) && $mysql_error != '') { ?>
                                    <div class="alert alert-danger text-left">
                                        <i class="fas fa-database"></i> <?php echo $mysql_error; ?>
                                    </div>
                                <?php } ?>
                                <?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered validate')); ?>
                                    <div class="form-group">
                                        <label for="hostname" class="col-md-3 control-label">Hostname <span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="hostname" value="127.0.0.1" required>
											<span class="error"><?=form_error('hostname')?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="database" class="col-md-3 control-label">Database Name <span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="database" value="<?=set_value('database', 'rtag')?>" required>
                                             <?php echo form_error('database', '<label id="database-error" class="error" for="database">', '</label>'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="username" class="col-md-3 control-label">Username <span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="username" value="<?=set_value('username', 'root')?>" required>
											<?php echo form_error('username', '<label id="username-error" class="error" for="username">', '</label>'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="col-md-3 control-label">Password</label>
                                        <div class="col-md-9">
                                            <input type="password" class="form-control mb-ma" name="password" value="" placeholder="Database password (optional)">
                                        </div>
                                    </div>
                                    <footer class="panel-footer">
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-default" name="step" value="4">Setup Database <i class="fas fa-plug"></i></button>
                                        </div>
                                    </footer>
                                <?php echo form_close(); ?>
                            </div>
                        <?php elseif ($step == 4) : ?>
                            <div class="tab-pane active">
                                <?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered validate')); ?>
    								<div class="form-group">
    									<label class="col-md-3 control-label">School Name <span class="required">*</span></label>
    									<div class="col-md-9">
    										<input type="text" class="form-control" name="school_name" placeholder="E.g., Tech Academy" value="<?=set_value('school_name')?>" required />
                                            <?php echo form_error('school_name', '<label id="school_name-error" class="error" for="school_name">', '</label>'); ?>
    									</div>
    								</div>
    								<div class="form-group">
    									<label class="col-md-3 control-label" for="sa_name">Superadmin Name <span class="required">*</span></label>
    									<div class="col-md-9">
    										<input type="text" class="form-control" name="sa_name" placeholder="Full Name" value="<?=set_value('sa_name')?>" required />
    										<?php echo form_error('sa_name', '<label id="sa_name-error" class="error" for="sa_name">', '</label>'); ?>
    									</div>
    								</div>
    								<div class="form-group">
    									<label class="col-md-3 control-label" for="sa_email">Superadmin Email <span class="required">*</span></label>
    									<div class="col-md-9">
    										<input type="email" class="form-control" name="sa_email" placeholder="superadmin@school.edu" value="<?=set_value('sa_email')?>" required />
                                            <?php echo form_error('sa_email', '<label id="sa_email-error" class="error" for="sa_email">', '</label>'); ?>
    									</div>
    								</div>
    								<div class="form-group">
    									<label class="col-md-3 control-label">Superadmin Password <span class="required">*</span></label>
    									<div class="col-md-9">
    										<input type="password" class="form-control" name="sa_password" id="sa_password" placeholder="At least 6 characters" value="" required />
    										<?php echo form_error('sa_password', '<label id="sa_password-error" class="error" for="sa_password">', '</label>'); ?>
    									</div>
    								</div>
    								<div class="form-group">
    									<label class="col-md-3 control-label">Timezone <span class="required">*</span></label>
    									<div class="col-md-9 mb-ma">
    										<?php
    										$timezones = $this->_install->timezone_list();
    										echo form_dropdown("timezone", $timezones, set_value('timezone', 'Africa/Monrovia'), "class='form-control' id='timezone' ");
    										?>
    										<?php echo form_error('timezone', '<label id="timezone-error" class="error" for="timezone">', '</label>'); ?>
    									</div>
    								</div>
                                    <footer class="panel-footer">
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-default" name="step" value="5">Complete Installation <i class="fas fa-magic"></i></button>
                                        </div>
                                    </footer>
                                <?php echo form_close(); ?>
                            </div>
                        <?php elseif ($step == 5) : ?>
                            <div class="tab-pane active text-center">
                                <div style="font-size: 4rem; color: var(--success-color); margin-bottom: 1rem;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h4 style="color: #fff; font-weight: 700; margin-bottom: 1rem;">Installation Successful!</h4>
                                <p style="color: var(--text-secondary); max-width: 500px; margin: 0 auto 1.5rem;">
                                    The system has been installed successfully. You can now log in using the Superadmin credentials you created.
                                </p>
                                <ul class="fi-msg-s">
                                    <li><a href="<?=base_url('authentication'); ?>" class="btn btn-default" style="display: inline-flex;"><i class="fas fa-sign-in-alt"></i> Go to Login Page</a></li>
                                </ul>
                                <br>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <center> &copy; <?=date('Y')?> <a href="#" target="_blank">Techexplorer School Management System</a>. All Rights Reserved.</center>
		</div>
		<script src="<?=base_url('assets/vendor/bootstrap/js/bootstrap.js')?>"></script>
		<script src="<?php echo base_url('assets/vendor/select2/js/select2.js');?>"></script>
		<script src="<?=base_url('assets/vendor/jquery-placeholder/jquery-placeholder.js')?>"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#timezone').select2({
                    theme: "bootstrap"
                });
            });
        </script>
	</body>
</html>