<section class="panel appear-animation" data-appear-animation="<?= $global_config['animations']; ?>" data-appear-animation-delay="100">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fas fa-file-alt"></i> Report Card Template Builder</h4>
    </div>
    <div class="panel-body">
        <?php echo form_open('settings/report_card_template', array('class' => 'form-horizontal form-bordered', 'id' => 'report_card_template_form')); ?>

        <?php
        $front_settings = json_decode($template['front_layout_settings'], true);
        if (empty($front_settings)) {
            $front_settings = array();
        }
        $order_values = array();
        if (!empty($front_settings['order']) && is_array($front_settings['order'])) {
            foreach ($front_settings['order'] as $index => $section) {
                $order_values[$section] = $index + 1;
            }
        }
        ?>

        <div class="form-group">
            <label class="col-md-3 control-label">Front Page Controls</label>
            <div class="col-md-9">
                <div class="checkbox-replace mt-xs">
                    <label class="i-checks">
                        <input type="checkbox" name="show_gpa" <?= isset($front_settings['show_gpa']) && $front_settings['show_gpa'] ? 'checked' : ''; ?>>
                        <i></i> Show GPA
                    </label>
                </div>
                <div class="checkbox-replace mt-xs">
                    <label class="i-checks">
                        <input type="checkbox" name="show_class_rank" <?= isset($front_settings['show_class_rank']) && $front_settings['show_class_rank'] ? 'checked' : ''; ?>>
                        <i></i> Show Class Rank
                    </label>
                </div>
                <div class="checkbox-replace mt-xs">
                    <label class="i-checks">
                        <input type="checkbox" name="show_attendance_summary" <?= isset($front_settings['show_attendance_summary']) && $front_settings['show_attendance_summary'] ? 'checked' : ''; ?>>
                        <i></i> Show Attendance Summary
                    </label>
                </div>
                <div class="checkbox-replace mt-xs">
                    <label class="i-checks">
                        <input type="checkbox" name="show_vpa_signature_line" <?= isset($front_settings['show_vpa_signature_line']) && $front_settings['show_vpa_signature_line'] ? 'checked' : ''; ?>>
                        <i></i> Show VPA Signature Line
                    </label>
                </div>
                <div class="checkbox-replace mt-xs">
                    <label class="i-checks">
                        <input type="checkbox" name="show_principal_signature_line" <?= isset($front_settings['show_principal_signature_line']) && $front_settings['show_principal_signature_line'] ? 'checked' : ''; ?>>
                        <i></i> Show Principal Signature Line
                    </label>
                </div>
                <div class="checkbox-replace mt-xs">
                    <label class="i-checks">
                        <input type="checkbox" name="grading_scale_legend" <?= isset($front_settings['grading_scale_legend']) && $front_settings['grading_scale_legend'] ? 'checked' : ''; ?>>
                        <i></i> Show Grading Scale Legend
                    </label>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Section Ordering</h5>
            </div>
            <div class="panel-body">
                <?php
                $sections = array(
                    'student_info' => 'Student Information',
                    'subjects_table' => 'Subjects / Marks Table',
                    'attendance_summary' => 'Attendance Summary',
                    'gpa' => 'GPA',
                    'class_rank' => 'Class Rank',
                    'grade_scale_legend' => 'Grading Scale Legend',
                    'signature_lines' => 'Signature Lines',
                );
                ?>
                <p class="help-block">Drag and drop sections to reorder the front page. The numeric order inputs are kept hidden for compatibility.</p>
                <ul id="rc-sections" class="list-group mb-md">
                    <?php foreach ($sections as $key => $label) {
                        $order_value = isset($order_values[$key]) ? $order_values[$key] : '';
                    ?>
                        <li class="list-group-item" data-key="<?= $key; ?>" style="cursor:move;">
                            <i class="fas fa-arrows-alt-v mr-sm"></i> <?= $label; ?>
                            <input type="hidden" name="order_<?= $key; ?>" value="<?= set_value('order_' . $key, $order_value); ?>" />
                        </li>
                    <?php } ?>
                </ul>
                <style>
                    #rc-sections { list-style:none; padding:0; }
                    #rc-sections .list-group-item { display:flex; align-items:center; justify-content:space-between; }
                </style>
                <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
                <script>
                    (function($){
                        $(function(){
                            var $list = $('#rc-sections');
                            $list.sortable({
                                placeholder: 'ui-state-highlight'
                            });

                            function syncOrderInputs(){
                                $list.children('li').each(function(i){
                                    var key = $(this).data('key');
                                    $(this).find('input[type=hidden][name^="order_"]').val(i+1);
                                });
                            }

                            // Initialize order values on page load
                            syncOrderInputs();

                            // Update hidden inputs before submit
                            $('#report_card_template_form').on('submit', function(){
                                syncOrderInputs();
                            });
                        });
                    })(jQuery);
                </script>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Section Titles</h5>
            </div>
            <div class="panel-body">
                <?php
                $titles = isset($front_settings['titles']) ? $front_settings['titles'] : array();
                foreach ($sections as $key => $label) {
                    $title_value = isset($titles[$key]) ? $titles[$key] : $label;
                ?>
                    <div class="form-group">
                        <label class="col-md-4 control-label"><?= $label; ?> Title</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="title_<?= $key; ?>" value="<?= set_value('title_' . $key, $title_value); ?>" />
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label">Enable Back Page</label>
            <div class="col-md-9">
                <div class="checkbox-replace mt-xs">
                    <label class="i-checks">
                        <input type="checkbox" name="enable_back_page" <?= isset($template['enable_back_page']) && $template['enable_back_page'] ? 'checked' : ''; ?>>
                        <i></i> Enable Back Page Content for Two-Sided Printing
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label">Back Page Content</label>
            <div class="col-md-9">
                <textarea name="back_page_content" class="form-control summernote"><?= set_value('back_page_content', $template['back_page_content']); ?></textarea>
                <p class="help-block">Use this area to define school policies, promotion criteria, or custom announcements that print on the back of the report card.</p>
            </div>
        </div>

        <footer class="panel-footer mt-lg">
            <div class="row">
                <div class="col-md-3 col-sm-offset-3">
                    <button type="submit" class="btn btn-default btn-block" name="submit" value="save">
                        <i class="fas fa-save"></i> Save Template
                    </button>
                </div>
            </div>
        </footer>

        <?php echo form_close(); ?>
    </div>
</section>
