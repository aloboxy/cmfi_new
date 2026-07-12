<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width,initial-scale=1" name="viewport">
	<meta name="description" content="CMFI School Management System - Monrovia, Liberia">
	<meta name="author" content="CMFI School">
	<title><?php echo translate('login'); ?> — CMFI School</title>
	<link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.png');?>">

	<!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

	<!-- Bootstrap (for compatibility with alerts, etc.) -->
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.css');?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/font-awesome/css/all.min.css'); ?>">

	<!-- SweetAlert -->
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/sweetalert/sweetalert-custom.css');?>">
	<script src="<?php echo base_url('assets/vendor/sweetalert/sweetalert.min.js');?>"></script>

	<!-- jQuery (loaded early for CSRF) -->
	<script src="<?php echo base_url('assets/vendor/jquery/jquery.js');?>"></script>

	<!-- Our modern login CSS -->
	<link rel="stylesheet" href="<?php echo base_url('assets/login_page/css/style.css');?>">

	<script>
		var base_url = '<?php echo base_url() ?>';
	</script>
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
				<div class="hero-brand-name">CMFI School</div>
				<div class="hero-brand-sub">Monrovia, Liberia</div>
			</div>
		</div>

		<!-- Main content -->
		<div class="hero-content">
			<h1 class="hero-headline">
				Empowering<br>
				<span>Education</span><br>
				in Liberia
			</h1>
			<p class="hero-description">
				A complete school management platform built for Liberian educators — from student records to fees, exams, and staff payroll.
			</p>
			<ul class="hero-features">
				<li>
					<span class="feat-icon"><i class="fas fa-users"></i></span>
					Student &amp; Parent Management
				</li>
				<li>
					<span class="feat-icon"><i class="fas fa-file-alt"></i></span>
					Exam Results &amp; Report Cards
				</li>
				<li>
					<span class="feat-icon"><i class="fas fa-dollar-sign"></i></span>
					Fees Collection in LRD &amp; USD
				</li>
				<li>
					<span class="feat-icon"><i class="fas fa-chart-bar"></i></span>
					Attendance &amp; Analytics
				</li>
			</ul>
		</div>

		<!-- Stats footer -->
		<div class="hero-stats">
			<div class="stat-item">
				<span class="stat-number">500+</span>
				<span class="stat-label">Students</span>
			</div>
			<div class="stat-item">
				<span class="stat-number">30+</span>
				<span class="stat-label">Teachers</span>
			</div>
			<div class="stat-item">
				<span class="stat-number">12</span>
				<span class="stat-label">Grade Levels</span>
			</div>
		</div>
	</aside>

	<!-- ══ RIGHT FORM PANEL ══ -->
	<main class="auth-form-panel">
		<div class="form-box fade-in-up">

			<!-- School Logo & Title -->
			<div class="form-header">
				<div class="school-logo-wrap">
					<img src="<?php echo base_url('uploads/app_image/logo.png');?>"
					     onerror="this.style.display='none'; this.parentNode.innerHTML='<span style=\"font-size:28px;\">🏫</span>'"
					     alt="School Logo">
				</div>
				<h2 class="form-title"><?php echo $global_config['institute_name']; ?></h2>
				<p class="form-subtitle">Sign in to your account to continue</p>
			</div>

			<!-- Role Tabs -->
			<!-- <div class="role-tabs">
				<button class="role-tab active" type="button" id="tab-admin">
					<i class="fas fa-user-shield"></i> Admin
				</button>
				<button class="role-tab" type="button" id="tab-teacher">
					<i class="fas fa-chalkboard-teacher"></i> Teacher
				</button>
				<button class="role-tab" type="button" id="tab-student">
					<i class="fas fa-user-graduate"></i> Student
				</button>
			</div> -->

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
							placeholder="you@school.edu.lr"
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

				<!-- Liberia flag stripe -->
				<div class="flag-stripe">
					<span class="s1"></span>
					<span class="s2"></span>
					<span class="s3"></span>
					<span class="s4"></span>
					<span class="s5"></span>
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

	// ── Role tabs (visual only — all go to same login endpoint) ──
	$('.role-tab').on('click', function () {
		$('.role-tab').removeClass('active');
		$(this).addClass('active');
	});

	// ── Login button loading state ──
	$('#login-form').on('submit', function () {
		var $btn = $('#btn-login');
		$btn.html('<i class="fas fa-circle-notch fa-spin"></i> Signing in…').prop('disabled', true);
	});

	// ── Generate floating particles ──
	var $particles = $('#particles');
	var colors = ['rgba(59,130,246,0.15)', 'rgba(244,166,31,0.12)', 'rgba(255,255,255,0.06)'];
	for (var i = 0; i < 18; i++) {
		var size = Math.floor(Math.random() * 60) + 10;
		var left = Math.random() * 100;
		var delay = Math.random() * 20;
		var duration = Math.random() * 20 + 15;
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
