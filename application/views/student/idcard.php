<?php $widget = (is_superadmin_loggedin() ? 4 : 6);
$this->db->select('b.*');
$this->db->from('branch as b');
$this->db->limit(1);
$school = $this->db->get();
if ($school->num_rows() > 0) {
	$school = $school->row();
}

$card_bg_image = '';
if (file_exists('uploads/app_image/idcard_bg.png')) {
    $card_bg_image = 'background-image: url(' . base_url('uploads/app_image/idcard_bg.png') . ') !important; background-size: cover !important; background-position: center !important;';
}
?>

<style type="text/css">
.dynamic-id-card-holder {
	position: relative;
	margin-bottom: 20px;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
	background-color: <?= !empty($theme_config['idcard_bg_color']) ? $theme_config['idcard_bg_color'] : '#ffffff' ?> !important;
	border: 1px solid <?= !empty($theme_config['idcard_border_color']) ? $theme_config['idcard_border_color'] : '#E5E7EB' ?> !important;
	color: <?= !empty($theme_config['idcard_text_color']) ? $theme_config['idcard_text_color'] : '#333333' ?> !important;
	<?= $card_bg_image ?>
}

.dynamic-id-card-holder td, 
.dynamic-id-card-holder h5,
.dynamic-id-card-holder hr {
	color: <?= !empty($theme_config['idcard_text_color']) ? $theme_config['idcard_text_color'] : '#333333' ?> !important;
	border-color: <?= !empty($theme_config['idcard_border_color']) ? $theme_config['idcard_border_color'] : '#E5E7EB' ?> !important;
}

/* Vertical Layout */
.layout-vertical {
	width: 260px;
	border-radius: 12px;
	padding: 15px;
	text-align: center;
}
.layout-vertical .id-card-heading {
	border-bottom: 1px solid <?= !empty($theme_config['idcard_border_color']) ? $theme_config['idcard_border_color'] : '#E5E7EB' ?>;
	padding-bottom: 8px;
	margin-bottom: 12px;
}
.layout-vertical .photo img {
	border: 2px solid <?= !empty($theme_config['idcard_border_color']) ? $theme_config['idcard_border_color'] : '#c8c8c8' ?>;
	border-radius: 6px;
	width: 90px;
	height: 90px;
	object-fit: cover;
	margin: 0 auto;
}
.layout-vertical .idcard_info {
	margin-top: 10px;
	text-align: left;
}
.layout-vertical .idcard_info table {
	width: 100%;
}
.layout-vertical .qr-code {
	margin-top: 10px;
}
.layout-vertical .qr-code img {
	width: 60px;
	height: 60px;
}

/* Horizontal Layout */
.layout-horizontal {
	width: 380px;
	height: 220px;
	border-radius: 12px;
	padding: 12px;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}
.layout-horizontal .id-card-heading {
	border-bottom: 1px solid <?= !empty($theme_config['idcard_border_color']) ? $theme_config['idcard_border_color'] : '#E5E7EB' ?>;
	padding-bottom: 5px;
	margin-bottom: 8px;
	text-align: center;
}
.layout-horizontal .card-main-body {
	display: flex;
	flex-direction: row;
	align-items: center;
	gap: 12px;
	height: 100%;
}
.layout-horizontal .photo {
	flex: 0 0 80px;
}
.layout-horizontal .photo img {
	border: 2px solid <?= !empty($theme_config['idcard_border_color']) ? $theme_config['idcard_border_color'] : '#c8c8c8' ?>;
	border-radius: 6px;
	width: 80px;
	height: 80px;
	object-fit: cover;
}
.layout-horizontal .idcard_info {
	flex: 1;
	font-size: 0.85rem;
	text-align: left;
}
.layout-horizontal .idcard_info table {
	width: 100%;
}
.layout-horizontal .qr-code {
	flex: 0 0 60px;
	text-align: center;
}
.layout-horizontal .qr-code img {
	width: 60px;
	height: 60px;
}

/* Print Overrides */
@media print {
	.dynamic-id-card-holder {
		-webkit-print-color-adjust: exact !important;
		print-color-adjust: exact !important;
		box-shadow: none !important;
	}
	.idcard-col {
		float: left;
		page-break-inside: avoid;
	}
}
</style>

<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('select_ground')?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
			<div class="panel-body">
				<div class="row mb-sm">
				<?php if (is_superadmin_loggedin() ): ?>
					<div class="col-md-<?php echo $widget; ?>">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
						</div>
					</div>
				<?php endif; ?>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('class')?> <span class="required">*</span></label>
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,1)'
								required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
						</div>
					</div>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('section')?> <span class="required">*</span></label>
							<?php
								$arraySection = $this->app_lib->getSections(set_value('class_id'), true);
								echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' required
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
						</div>
					</div>
				</div>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<button type="submit" name="search" value="1" class="btn btn-default btn-block"> <i class="fas fa-filter"></i> <?=translate('filter')?></button>
					</div>
				</div>
			</footer>
			<?php echo form_close();?>
		</section>

		<?php if ( isset($query) && !empty($query)):?>
		<section class="panel appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
			<header class="panel-heading">
				<div class="checkbox-replace pull-right">
					<label class="i-checks"><input type="checkbox" name="chkall" id="selectAllchkbox" checked><i></i> Checked All</label>
				</div>
				<h5 class="panel-title"><i class="fas fa-id-card-alt"></i> <?=translate('student_list')?></h5>
			</header>
			<div class="panel-body">
				<?php
				if($query->num_rows() != 0){
				$students = $query->result();
				?>
				<div class="row checked-area" id="id_card_print">
					<?php foreach($students as $row):?>
					<div class="col-md-6 col-lg-4 col-xl-3 idcard-col" style="margin-bottom: 20px;">
						<div class="checkbox-replace hidden-print mb-sm">
							<label class="i-checks"><input type="checkbox" name="chk_idcard" id="student_<?=html_escape($row->student_id)?>" checked><i></i> ID Card Print</label>
						</div>
						
						<?php if ($theme_config['idcard_layout'] == 'horizontal'): ?>
							<!-- Horizontal ID Card Layout -->
							<div class="dynamic-id-card-holder layout-horizontal">
								<header class="id-card-heading">
									<img class="img-fs" style="max-height: 25px;" src="<?=base_url('uploads/app_image/printing-logo.png')?>" alt="School Logo" />
								</header>
								<div class="card-main-body">
									<div class="photo">
										<img src="<?=get_image_url('student', html_escape($row->photo))?>">
									</div>
									<div class="idcard_info">
										<h5 style="margin: 0 0 5px 0; font-weight: bold;"><?php echo html_escape($row->fullname); ?></h5>
										<table>
											<tbody>
												<?php if ($theme_config['idcard_show_father'] == 1): ?>
												<tr>
													<td width="70">Father </td>
													<td width="8">:</td>
													<td><?=empty($row->parent_id) ? 'N/A' : get_type_name_by_id('parent', $row->parent_id);?></td>
												</tr>
												<?php endif; ?>
												<?php if ($theme_config['idcard_show_class'] == 1): ?>
												<tr>
													<td width="70">Class</td>
													<td width="8">:</td>
													<td><?php echo get_type_name_by_id('class', $row->class_id);?></td>
												</tr>
												<?php endif; ?>
												<?php if ($theme_config['idcard_show_section'] == 1): ?>
												<tr>
													<td width="70">Section</td>
													<td width="8">:</td>
													<td><?php echo get_type_name_by_id('section', $row->section_id);?></td>
												</tr>
												<?php endif; ?>
												<?php if ($theme_config['idcard_show_blood'] == 1): ?>
												<tr>
													<td width="70">Blood</td>
													<td width="8">:</td>
													<td><?=empty($row->blood_group) ? 'N/A' : $row->blood_group;?></td>
												</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
									<?php if ($theme_config['idcard_show_qrcode'] == 1): ?>
									<div class="qr-code">
										<img src="<?php echo base_url('uploads/qrcode_cache/'.$row->qr_code); ?>" alt="QR Code">
									</div>
									<?php endif; ?>
								</div>
							</div>
						<?php else: ?>
							<!-- Vertical ID Card Layout -->
							<div class="dynamic-id-card-holder layout-vertical">
								<header class="id-card-heading">
									<img class="img-fs" style="max-height: 35px;" src="<?=base_url('uploads/app_image/printing-logo.png')?>" alt="School Logo" />
								</header>
								<div class="photo">
									<img src="<?=get_image_url('student', html_escape($row->photo))?>">
								</div>
								<h5 class="mt-sm" style="font-weight: bold;"><?php echo html_escape($row->fullname); ?></h5>
								<div class="idcard_info">
									<table>
										<tbody>
											<?php if ($theme_config['idcard_show_father'] == 1): ?>
											<tr>
												<td width="80">Father Name </td>
												<td width="8">:</td>
												<td><?=empty($row->parent_id) ? 'N/A' : get_type_name_by_id('parent', $row->parent_id);?></td>
											</tr>
											<?php endif; ?>
											<?php if ($theme_config['idcard_show_class'] == 1): ?>
											<tr>
												<td width="80">Class</td>
												<td width="8">:</td>
												<td><?php echo get_type_name_by_id('class', $row->class_id);?></td>
											</tr>
											<?php endif; ?>
											<?php if ($theme_config['idcard_show_section'] == 1): ?>
											<tr>
												<td width="80">Section</td>
												<td width="8">:</td>
												<td><?php echo get_type_name_by_id('section', $row->section_id);?></td>
											</tr>
											<?php endif; ?>
											<?php if ($theme_config['idcard_show_blood'] == 1): ?>
											<tr>
												<td width="80">Blood Group</td>
												<td width="8">:</td>
												<td><?=empty($row->blood_group) ? 'N/A' : $row->blood_group;?></td>
											</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
								<?php if ($theme_config['idcard_show_qrcode'] == 1): ?>
								<hr style="margin: 8px 0;">
								<div class="qr-code">
									<img src="<?php echo base_url('uploads/qrcode_cache/'.$row->qr_code); ?>" alt="QR Code">
								</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

					</div>
					<?php endforeach;?>
				</div>
				<?php
				}else{
					echo '<div class="alert alert-subl mt-md text-center"><strong>Opps!</strong> ' . translate('no_information_available') . '!</div>'; 
				}
				?>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<button onClick="printElem('id_card_print')" class="btn btn-default btn-block"><i class="fas fa-print"></i> <?=translate('print')?></button>
					</div>
				</div>
			</div>
		</section>
		<?php endif;?>
	</div>
</div>

<script type="text/javascript">
	// print function
	function printElem(elem)
	{
	    var oContent = document.getElementById(elem).innerHTML;
	    var frame1 = document.createElement('iframe');
	    frame1.name = "frame1";
	    frame1.style.position = "absolute";
	    frame1.style.top = "-1000000px";
	    document.body.appendChild(frame1);
	    var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
	    frameDoc.document.open();
	    //Create a new HTML document.
	    frameDoc.document.write('<html><head><title></title>');
	    frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'assets/vendor/bootstrap/css/bootstrap.min.css">');
	    frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'assets/css/custom-style.css">');
	    frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'assets/css/ramom.css">');
	    frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'assets/css/idcard.css">');
	    
	    // Inject dynamic template builder style tag for the printing document
	    var styleTag = document.getElementsByTagName("style")[0].outerHTML;
	    frameDoc.document.write(styleTag);
	    
	    frameDoc.document.write('</head><body>');
	    frameDoc.document.write(oContent);
	    frameDoc.document.write('</body></html>');
	    frameDoc.document.close();
	    setTimeout(function () {
	        window.frames["frame1"].focus();
	        window.frames["frame1"].print();
	        frame1.remove();
	    }, 500);
	    return true;
	}
</script>