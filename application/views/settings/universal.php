<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations']; ?>" data-appear-animation-delay="100">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li <?=(empty($this->session->flashdata('active')) ? 'class="active"' : '');?>>
				<a href="#setting" data-toggle="tab">
					<i class="fas fa-chalkboard-teacher"></i> 
				   <span class="hidden-xs"> <?=translate('general_settings')?></span>
				</a>
			
			</li>
			<li <?=($this->session->flashdata('active') == 2 ? 'class="active"' : '');?>>
				<a href="#theme" data-toggle="tab">
				   <i class="fas fa-paint-roller"></i>
				   <span class="hidden-xs"> <?=translate('theme_settings')?></span>
				</a>
			
			</li>
			<li <?=($this->session->flashdata('active') == 3 ? 'class="active"' : '');?>>
				<a href="#upload" data-toggle="tab">
				   <i class="fab fa-uikit"></i>
				   <span class="hidden-xs"> <?=translate('logo')?></span>
				</a>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane box <?=(empty($this->session->flashdata('active')) ? 'active' : '');?>" id="setting">
				<?php echo form_open($this->uri->uri_string(), array( 'class' 	=> 'validate form-horizontal form-bordered' )); ?>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('institute_name')?></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="institute_name" value="<?=set_value('institute_name', $global_config['institute_name'])?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('institution_code')?></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="institution_code" value="<?=set_value('institution_code', $global_config['institution_code'])?>" />
						<div class="checkbox-replace mt-md">
							<label class="i-checks">
								<input type="checkbox" name="reg_prefix" id="reg_prefix" <?=($global_config['reg_prefix'] == 'on' ? 'checked' : '');?>>
								<i></i> The Institute Code will be used as the prefix for Student (Registration No).
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('mobile_no');?></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="mobileno" value="<?=set_value('mobileno', $global_config['mobileno'])?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('address');?></label>
					<div class="col-md-6">
						<textarea name="address" rows="2" class="form-control" aria-required="true"><?=set_value('address', $global_config['address'])?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">
						<?=translate('currency');?>
					</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="currency" value="<?=set_value('currency', $global_config['currency'])?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('currency_symbol');?></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="currency_symbol" value="<?=set_value('currency_symbol', $global_config['currency_symbol'])?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('language');?></label>
					<div class="col-md-6">
						<?php
						$languages = $this->db->select('id,lang_field,name')->where('status', 1)->get('language_list')->result();
						foreach ($languages as $lang) {
							$array[$lang->lang_field] = ucfirst($lang->name);
						}
						echo form_dropdown("translation", $array, set_value('translation', $global_config['translation']), "class='form-control' data-plugin-selectTwo 
							data-width='100%' data-minimum-results-for-search='Infinity' ");
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('academic_session');?></label>
					<div class="col-md-6">
						<?php
						$arrayYear = array("" => translate('select'));
						$years = $this->db->get('schoolyear')->result();
						foreach ($years as $year) {
							$arrayYear[$year->id] = $year->school_year;
						}
						echo form_dropdown("session_id", $arrayYear, set_value('session_id', $global_config['session_id']), "class='form-control' required
						data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('timezone');?></label>
					<div class="col-md-6">
						<?php
						$timezones = $this->app_lib->timezone_list();
						echo form_dropdown("timezone", $timezones, set_value('timezone', $global_config['timezone']), "class='form-control populate' required id='timezones' 
						data-plugin-selectTwo data-width='100%'");
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">
						<?=translate('animations');?>
					</label>
					<div class="col-md-6">
						<?php
						$getAnimationslist = $this->app_lib->getAnimationslist();
						echo form_dropdown("animations", $getAnimationslist, set_value('animations', $global_config['animations']), "class='form-control populate' required
						id='timezones' data-plugin-selectTwo data-width='100%'");
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('date_format')?></label>
					<div class="col-md-6">
						<?php
						$getDateformat = $this->app_lib->getDateformat();
						echo form_dropdown("date_format", $getDateformat, set_value('date_format', $global_config['date_format']), "class='form-control' id='date_format' 
						data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=translate('footer_text');?></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="footer_text" value="<?=set_value('footer_text', $global_config['footer_text'])?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Facebook URL</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="facebook_url" value="<?=set_value('facebook_url', $global_config['facebook_url'])?>" />
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-md-3 control-label">Email Address</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="twitter_url" value="<?=set_value('twitter_url', $global_config['twitter_url'])?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Instagram URL</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="linkedin_url" value="<?=set_value('linkedin_url', $global_config['linkedin_url'])?>" />
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-md-3 control-label">Youtube URL</label>
					<div class="col-md-6 mb-md">
						<input type="text" class="form-control" name="youtube_url" value="<?=set_value('youtube_url', $global_config['youtube_url'])?>" />
					</div>
				</div>

				<footer class="panel-footer mt-lg">
					<div class="row">
						<div class="col-md-2 col-sm-offset-3">
							<button type="submit" class="btn btn btn-default btn-block" name="submit" value="setting">
								<i class="fas fa-plus-circle"></i> <?=translate('save');?>
							</button>
						</div>
					</div>
				</footer>
				<?php echo form_close(); ?>
			</div>

			<div class="tab-pane box <?=($this->session->flashdata('active') == 2 ? 'active' : '');?>" id="theme">
				<?php
					echo form_open($this->uri->uri_string(), array(
						'method'	=> 'post',
						'class' 	=> 'validate form-horizontal form-bordered'
					));
				?>
				<div class="form-group">
					<label class="col-md-2 control-label" for="zoomcontrol">Theme</label>
					<div class="col-md-8">
						<ul class="list-unstyled thememenu-sy">
							<li>
								<div class="theme-box">
									<label> 
										<input name="dark_skin" value="false" type="radio" <?=($theme_config['dark_skin'] == 'false' ? 'checked' : '');?>>
										<div class="theme-img">
											<img src="<?=base_url('assets/images/theme/light.png')?>">
										</div>
									</label>
								</div>
							</li>
							<li>
								<div class="theme-box">
									<label >
										<input name="dark_skin" value="true" type="radio" <?=($theme_config['dark_skin'] == 'true' ? 'checked' : '');?> >
										<div class="theme-img">
											<img src="<?=base_url('assets/images/theme/dark.png')?>">
										</div>
									</label>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label" for="zoomcontrol">Border</label>
					<div class="col-md-8 mb-md">
						<ul class="list-unstyled thememenu-sy">
							<li>
								<div class="theme-box">
									<label> 
										<input name="border_mode" value="true" type="radio" <?=($theme_config['border_mode'] == 'true' ? 'checked' : '')?> >
										<div class="theme-img">
											<img src="<?=base_url('assets/images/theme/rounded.png')?>">
										</div>
									</label>
								</div>
							</li>
							<li>
								<div class="theme-box">
									<label >
										<input name="border_mode" value="false" type="radio" <?=($theme_config['border_mode'] == 'false' ? 'checked' : '')?> >
										<div class="theme-img">
											<img src="<?=base_url('assets/images/theme/square.png')?>">
										</div>
									</label>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">System Colors</label>
					<div class="col-md-8 mb-md">
						<div class="row">
							<div class="col-md-4 mb-sm">
								<label class="control-label">Primary/Brand Color</label>
								<input type="color" class="form-control" name="primary_color" value="<?=set_value('primary_color', !empty($theme_config['primary_color']) ? $theme_config['primary_color'] : '#1D4ED8')?>" />
							</div>
							<div class="col-md-4 mb-sm">
								<label class="control-label">Sidebar Background</label>
								<input type="color" class="form-control" name="sidebar_bg" value="<?=set_value('sidebar_bg', !empty($theme_config['sidebar_bg']) ? $theme_config['sidebar_bg'] : '#0D1B3E')?>" />
							</div>
							<div class="col-md-4 mb-sm">
								<label class="control-label">Sidebar Text</label>
								<input type="color" class="form-control" name="sidebar_text" value="<?=set_value('sidebar_text', !empty($theme_config['sidebar_text']) ? $theme_config['sidebar_text'] : '#C5D0E8')?>" />
							</div>
							<div class="col-md-4 mb-sm">
								<label class="control-label">Sidebar Icons</label>
								<input type="color" class="form-control" name="sidebar_icon" value="<?=set_value('sidebar_icon', !empty($theme_config['sidebar_icon']) ? $theme_config['sidebar_icon'] : '#C5D0E8')?>" />
							</div>
							<div class="col-md-4 mb-sm">
								<label class="control-label">Sidebar Active Item</label>
								<input type="color" class="form-control" name="sidebar_active" value="<?=set_value('sidebar_active', !empty($theme_config['sidebar_active']) ? $theme_config['sidebar_active'] : '#F4A61F')?>" />
							</div>
							<div class="col-md-4 mb-sm">
								<label class="control-label">Dark Text Color</label>
								<input type="color" class="form-control" name="text_dark" value="<?=set_value('text_dark', !empty($theme_config['text_dark']) ? $theme_config['text_dark'] : '#111827')?>" />
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">ID Card Designer</label>
					<div class="col-md-8 mb-md">
						<div class="row">
							<div class="col-md-4 mb-sm">
								<label class="control-label">ID Card Layout</label>
								<select name="idcard_layout" class="form-control">
									<option value="vertical" <?=($theme_config['idcard_layout'] == 'vertical' ? 'selected' : '')?>>Vertical</option>
									<option value="horizontal" <?=($theme_config['idcard_layout'] == 'horizontal' ? 'selected' : '')?>>Horizontal</option>
								</select>
							</div>
							<div class="col-md-4 mb-sm">
								<label class="control-label">Card Background</label>
								<input type="color" class="form-control" name="idcard_bg_color" value="<?=set_value('idcard_bg_color', !empty($theme_config['idcard_bg_color']) ? $theme_config['idcard_bg_color'] : '#ffffff')?>" />
							</div>
							<div class="col-md-4 mb-sm">
								<label class="control-label">Card Text Color</label>
								<input type="color" class="form-control" name="idcard_text_color" value="<?=set_value('idcard_text_color', !empty($theme_config['idcard_text_color']) ? $theme_config['idcard_text_color'] : '#333333')?>" />
							</div>
							<div class="col-md-4 mb-sm">
								<label class="control-label">Card Border Color</label>
								<input type="color" class="form-control" name="idcard_border_color" value="<?=set_value('idcard_border_color', !empty($theme_config['idcard_border_color']) ? $theme_config['idcard_border_color'] : '#E5E7EB')?>" />
							</div>
						</div>
						<div class="row mt-md">
							<div class="col-md-12">
								<label class="control-label" style="display: block; font-weight: bold; margin-bottom: 8px;">Visible Fields on ID Card</label>
								<label class="checkbox-inline mr-md">
									<input type="hidden" name="idcard_show_father" value="0">
									<input type="checkbox" name="idcard_show_father" value="1" <?=($theme_config['idcard_show_father'] == 1 ? 'checked' : '')?>> Father's Name
								</label>
								<label class="checkbox-inline mr-md">
									<input type="hidden" name="idcard_show_class" value="0">
									<input type="checkbox" name="idcard_show_class" value="1" <?=($theme_config['idcard_show_class'] == 1 ? 'checked' : '')?>> Class
								</label>
								<label class="checkbox-inline mr-md">
									<input type="hidden" name="idcard_show_section" value="0">
									<input type="checkbox" name="idcard_show_section" value="1" <?=($theme_config['idcard_show_section'] == 1 ? 'checked' : '')?>> Section
								</label>
								<label class="checkbox-inline mr-md">
									<input type="hidden" name="idcard_show_blood" value="0">
									<input type="checkbox" name="idcard_show_blood" value="1" <?=($theme_config['idcard_show_blood'] == 1 ? 'checked' : '')?>> Blood Group
								</label>
								<label class="checkbox-inline mr-md">
									<input type="hidden" name="idcard_show_qrcode" value="0">
									<input type="checkbox" name="idcard_show_qrcode" value="1" <?=($theme_config['idcard_show_qrcode'] == 1 ? 'checked' : '')?>> QR Code
								</label>
							</div>
						</div>
					</div>
				</div>
				<footer class="panel-footer">
					<div class="row">
						<div class="col-md-2 col-sm-offset-3">
							<button type="submit" class="btn btn btn-default btn-block" name="submit" value="theme">
								<i class="fas fa-plus-circle"></i> <?=translate('save');?>
							</button>
						</div>
					</div>
				</footer>
				<?php echo form_close(); ?>
			</div>
			<div class="tab-pane box <?=($this->session->flashdata('active') == 3 ? 'active' : '');?>" id="upload">
				<?php
					echo form_open_multipart($this->uri->uri_string(), array('class' 	=> 'validate'));
				?>

				<!-- all logo -->
				<div class="headers-line">
					<i class="fab fa-envira"></i> <?=translate('logo');?>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label"><?=translate('system_logo');?></label>
							<input type="file" name="logo_file" class="dropify" data-allowed-file-extensions="png" data-default-file="<?=cache_busted_url('uploads/app_image/logo.png')?>" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label"><?=translate('text_logo');?></label>
							<input type="file" name="text_logo" class="dropify" data-allowed-file-extensions="png" data-default-file="<?=cache_busted_url('uploads/app_image/logo-small.png')?>" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label"><?=translate('printing_logo');?></label>
							<input type="file" name="print_file" class="dropify" data-allowed-file-extensions="png" data-default-file="<?=cache_busted_url('uploads/app_image/printing-logo.png')?>" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label"><?=translate('report_card');?></label>
							<input type="file" name="report_card" class="dropify" data-allowed-file-extensions="png" data-default-file="<?=cache_busted_url('uploads/app_image/report-card-logo.png')?>" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">ID Card Background</label>
							<input type="file" name="idcard_bg" class="dropify" data-allowed-file-extensions="png" data-default-file="<?=file_exists('uploads/app_image/idcard_bg.png') ? cache_busted_url('uploads/app_image/idcard_bg.png') : ''?>" />
						</div>
					</div>
				</div>

				<!-- login background -->
				<div class="headers-line mt-lg">
					<i class="fas fa-sign-out-alt"></i> Login Background
				</div>
				<div class="row mb-ld">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Slider 1</label>
							<input type="file" name="slider_1" class="dropify" data-allowed-file-extensions="jpg" data-default-file="<?=cache_busted_url('uploads/login_image/slider_1.jpg')?>" />
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Slider 2</label>
							<input type="file" name="slider_2" class="dropify" data-allowed-file-extensions="jpg" data-default-file="<?=cache_busted_url('uploads/login_image/slider_2.jpg')?>" />
						</div>
					</div>
					<div class="col-md-4 mb-lg">
						<div class="form-group">
							<label class="control-label">Slider 3</label>
							<input type="file" name="slider_3" class="dropify" data-allowed-file-extensions="jpg" data-default-file="<?=cache_busted_url('uploads/login_image/slider_3.jpg')?>" />
						</div>
					</div>
				</div>
				
				<footer class="panel-footer">
					<div class="row">
						<div class="col-md-2 col-sm-offset-10">
							<button type="submit" class="btn btn btn-default btn-block" name="submit" value="logo">
								<i class="fas fa-upload"></i> <?=translate('upload')?>
							</button>
						</div>
					</div>
				</footer>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</section>