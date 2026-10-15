<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width,initial-scale=1" name="viewport">
	<meta name="description" content="CMFI School Management System - Premium Portal">
	<meta name="author" content="CMFI">
	<title><?php echo translate('login'); ?> — <?php echo html_escape($global_config['institute_name']); ?></title>
	<link rel="shortcut icon" href="<?php echo cache_busted_url('uploads/app_image/logo-small.png');?>" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%220.9em%22 font-size=%2290%22>🎓</text></svg>';">

	<!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

	<!-- Bootstrap & Icons -->
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.css');?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/font-awesome/css/all.min.css'); ?>">

	<!-- SweetAlert -->
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/sweetalert/sweetalert-custom.css');?>">
	<script src="<?php echo base_url('assets/vendor/sweetalert/sweetalert.min.js');?>"></script>

	<!-- jQuery -->
	<script src="<?php echo base_url('assets/vendor/jquery/jquery.js');?>"></script>

	<!-- Our modern login CSS -->
	<link rel="stylesheet" href="<?php echo base_url('assets/login_page/css/style.css');?>">

	<script>
		var base_url = '<?php echo base_url() ?>';
	</script>

	<!-- Inject Customizable Theme Colors dynamically from Settings -->
	<?php if (isset($theme_config)): ?>
	<style type="text/css">
	  :root {
	    <?php if (!empty($theme_config['primary_color'])): ?>
	    --primary-color: <?php echo $theme_config['primary_color']; ?> !important;
	    --primary-glow: <?php echo $theme_config['primary_color']; ?>4d !important; /* 30% opacity glow */
	    <?php endif; ?>
	    <?php if (!empty($theme_config['sidebar_bg'])): ?>
	    --sidebar-bg: <?php echo $theme_config['sidebar_bg']; ?> !important;
	    <?php endif; ?>
	  }
	</style>
	<?php endif; ?>
</head>
<body>

<div class="auth-wrapper">

	<!-- ══ LEFT HERO PANEL ══ -->
	<aside class="auth-hero">
		<!-- Floating particles -->
		<div class="particles" id="particles"></div>

		<!-- Brand -->
		<div class="hero-brand">
			<div class="hero-brand-icon">🎓</div>
			<div>
				<div class="hero-brand-name"><?php echo html_escape($global_config['institute_name']); ?></div>
				<div class="hero-brand-sub">Management Portal</div>
			</div>
		</div>

		<!-- Main content -->
		<div class="hero-content">
			<h1 class="hero-headline">
				Empowering<br>
				<span>Education</span><br>
				&amp; Administration
			</h1>
			<p class="hero-description">
				Welcome back! Access your classes, grades, payroll, fees, and records through our unified and customizable school management dashboard.
			</p>
			<ul class="hero-features">
				<li>
					<span class="feat-icon"><i class="fas fa-users"></i></span>
					Student &amp; Parent Portals
				</li>
				<li>
					<span class="feat-icon"><i class="fas fa-file-alt"></i></span>
					Exam Management &amp; Report Cards
				</li>
				<li>
					<span class="feat-icon"><i class="fas fa-wallet"></i></span>
					Automated Fees Collection
				</li>
				<li>
					<span class="feat-icon"><i class="fas fa-chart-line"></i></span>
					Attendance &amp; Performance Analytics
				</li>
			</ul>
		</div>

		<!-- Stats footer -->
		<div class="hero-stats">
			<div class="stat-item">
				<span class="stat-number">All-in-One</span>
				<span class="stat-label">System</span>
			</div>
			<div class="stat-item">
				<span class="stat-number">Real-time</span>
				<span class="stat-label">Analytics</span>
			</div>
			<div class="stat-item">
				<span class="stat-number">Secure</span>
				<span class="stat-label">Access</span>
			</div>
		</div>
	</aside>

	<!-- ══ RIGHT FORM PANEL ══ -->
	<main class="auth-form-panel">
		<div class="form-box fade-in-up">

			<!-- School Logo & Title -->
			<div class="form-header">
				<div class="school-logo-wrap">
					<img src="<?php echo cache_busted_url('uploads/app_image/logo-small.png');?>"
					     onerror="this.style.display='none'; this.parentNode.innerHTML='<span style=&quot;font-size:28px;&quot;>🏫</span>'"
					     alt="School Logo">
				</div>
				<h2 class="form-title"><?php echo html_escape($global_config['institute_name']); ?></h2>
				<p class="form-subtitle">Sign in to your account to continue</p>
			</div>

			<!-- Role Selection Quick-Hints -->
			<div class="role-selector-hints">
				<span class="hint-label"><i class="fas fa-info-circle"></i> Portal Roles:</span>
				<div class="hints-pills">
					<span class="pill"><i class="fas fa-user-shield"></i> Admin</span>
					<span class="pill"><i class="fas fa-chalkboard-teacher"></i> Teacher</span>
					<span class="pill"><i class="fas fa-user-graduate"></i> Student</span>
				</div>
			</div>

			<!-- Alerts -->
			<?php if (form_error('email') || form_error('password')): ?>
			<div class="alert-banner error">
				<i class="fas fa-exclamation-circle"></i>
				Invalid credentials. Please check your email and password.
			</div>
			<?php endif; ?>

			<!-- Login Form -->
			<?php echo form_open($this->uri->uri_string(), ['id' => 'login-form']); ?>

				<div class="form-group">
					<label class="form-label" for="email">Email Address</label>
					<div class="input-wrap">
						<input
							type="email"
							class="form-control"
							id="email"
							name="email"
							value="<?php echo set_value('email'); ?>"
							placeholder="username@school.edu"
							autocomplete="email"
							required
						/>
						<i class="far fa-envelope input-icon"></i>
					</div>
					<span class="error"><?php echo form_error('email'); ?></span>
				</div>

				<div class="form-group">
					<label class="form-label" for="password"><?php echo translate('password'); ?></label>
					<div class="input-wrap">
						<input
							type="password"
							class="form-control"
							id="password"
							name="password"
							placeholder="••••••••"
							autocomplete="current-password"
							required
						/>
						<i class="fas fa-lock input-icon"></i>
						<button type="button" class="pwd-toggle" id="toggle-pwd" title="Show/hide password">
							<i class="far fa-eye" id="toggle-pwd-icon"></i>
						</button>
					</div>
					<span class="error"><?php echo form_error('password'); ?></span>
				</div>

				<div class="form-options">
					<label class="remember-label">
						<input type="checkbox" name="remember" id="remember">
						Remember me
					</label>
					<a href="<?php echo base_url('authentication/forgot'); ?>" class="forgot-link">
						Forgot password?
					</a>
				</div>

				<button type="submit" id="btn-login" class="btn-login">
					<i class="fas fa-sign-in-alt"></i>
					<?php echo translate('login'); ?>
				</button>

				<!-- Customized decorative colored bottom band -->
				<div class="flag-stripe">
					<span class="s1"></span>
					<span class="s2"></span>
					<span class="s3"></span>
				</div>

			<?php echo form_close(); ?>

			<p class="form-footer">
				<?php echo $global_config['footer_text']; ?>
			</p>
		</div>
	</main>

</div><!-- /.auth-wrapper -->

<script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.js');?>"></script>

<script>
$(function () {

	// ── Password visibility toggle ──
	$('#toggle-pwd').on('click', function () {
		var $input = $('#password');
		var $icon  = $('#toggle-pwd-icon');
		if ($input.attr('type') === 'password') {
			$input.attr('type', 'text');
			$icon.removeClass('fa-eye').addClass('fa-eye-slash');
		} else {
			$input.attr('type', 'password');
			$icon.removeClass('fa-eye-slash').addClass('fa-eye');
		}
	});

	// ── Login button loading state ──
	$('#login-form').on('submit', function () {
		var $btn = $('#btn-login');
		$btn.html('<i class="fas fa-circle-notch fa-spin"></i> Signing in…').prop('disabled', true);
	});

	// ── Generate floating particles ──
	var $particles = $('#particles');
	var colors = ['rgba(96,165,250,0.15)', 'rgba(16,185,129,0.1)', 'rgba(255,255,255,0.05)'];
	for (var i = 0; i < 18; i++) {
		var size = Math.floor(Math.random() * 50) + 10;
		var left = Math.random() * 100;
		var delay = Math.random() * 15;
		var duration = Math.random() * 15 + 15;
		var color = colors[Math.floor(Math.random() * colors.length)];
		$particles.append(
			'<div class="particle" style="'
			+ 'width:' + size + 'px;'
			+ 'height:' + size + 'px;'
			+ 'left:' + left + '%;'
			+ 'bottom:-' + size + 'px;'
			+ 'background:' + color + ';'
			+ 'animation-delay:' + delay + 's;'
			+ 'animation-duration:' + duration + 's;'
			+ '"></div>'
		);
	}

});
</script>

<?php
$alertclass = "";
if ($this->session->flashdata('alert-message-success')) {
	$alertclass = "success";
} elseif ($this->session->flashdata('alert-message-error')) {
	$alertclass = "error";
} elseif ($this->session->flashdata('alert-message-info')) {
	$alertclass = "info";
}
if ($alertclass != ''):
	$alert_message = $this->session->flashdata('alert-message-' . $alertclass);
	$alert_id = md5($alert_message);
?>
<script>
	(function() {
		var alertId = 'swal_alert_<?php echo $alert_id; ?>';
		if (!sessionStorage.getItem(alertId)) {
			sessionStorage.setItem(alertId, 'true');
			swal({
				toast: true,
				position: 'top-end',
				type: '<?php echo $alertclass; ?>',
				title: '<?php echo addslashes($alert_message); ?>',
				confirmButtonClass: 'btn btn-default',
				buttonsStyling: false,
				timer: 5000
			});
		}
	})();
</script>
<?php endif; ?>

</body>
</html>
