<?php
/**
 * Report Card Template Editor - GrapeJS Visual Builder
 * Advanced WYSIWYG editor with:
 * - Full drag-and-drop block system
 * - CSS editor and HTML inspector
 * - Page orientation support (portrait/landscape)
 * - Responsive block library
 */
?>
<style>
    #gjs {
        width: 100%;
        height: 900px;
        border: 1px solid #ccc;
        margin-bottom: 20px;
    }
    .editor-toolbar {
        background: #f5f5f5;
        padding: 15px;
        border-bottom: 1px solid #ddd;
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }
    .editor-toolbar label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        margin: 0;
    }
    .editor-toolbar select,
    .editor-toolbar input {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 13px;
    }
    .page-preview {
        width: 100%;
        aspect-ratio: 8.5/11;
        margin: 20px auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .page-preview.landscape {
        aspect-ratio: 11/8.5;
    }
</style>

<section class="panel appear-animation" data-appear-animation="<?= $global_config['animations']; ?>" data-appear-animation-delay="100">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fas fa-paint-brush"></i> Report Card Designer (GrapeJS)</h4>
    </div>
    <div class="panel-body">
        <!-- Toolbar -->
        <div class="editor-toolbar">
            <div>
                <label for="page-orientation">Page Orientation:</label>
                <select id="page-orientation">
                    <option value="portrait">Portrait (8.5" × 11")</option>
                    <option value="landscape">Landscape (11" × 8.5")</option>
                </select>
            </div>
            <div>
                <label for="current-page">Current Page:</label>
                <select id="current-page">
                    <option value="front">Front Page</option>
                    <option value="back">Back Page</option>
                </select>
            </div>
            <button type="button" id="save-design-btn" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Design
            </button>
            <button type="button" id="preview-design-btn" class="btn btn-info">
                <i class="fas fa-eye"></i> Preview Report Card
            </button>
            <button type="button" id="export-design-btn" class="btn btn-secondary">
                <i class="fas fa-download"></i> Export HTML
            </button>
        </div>

        <!-- GrapeJS Editor -->
        <div id="gjs"></div>

        <!-- Hidden storage for both pages -->
        <input type="hidden" id="front-page-html" value="">
        <input type="hidden" id="back-page-html" value="">
        <input type="hidden" id="page-orientation-value" value="portrait">
    </div>
</section>

<!-- GrapeJS Libraries -->
<link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-blocks-basic"></script>
<script src="https://unpkg.com/grapesjs-component-countdown"></script>

<script>
(function($) {
    $(function() {
        // Initialize GrapeJS editor
        var editor = grapesjs.init({
            container: '#gjs',
            fromElement: false,
            height: '900px',
            storageManager: false, // We'll handle storage ourselves
            blocks: [
                // ===== REPORT CARD FIELDS =====
                { label: 'Student Name', attributes: { class: 'fa fa-user', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#e8f4f8; border:2px dashed #3498db; border-radius:4px; font-weight:500; color:#2980b9; text-align:center; min-height:24px;">[Student Name]</div>' },
                { label: 'Student Class', attributes: { class: 'fa fa-graduation-cap', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#e8f4f8; border:2px dashed #3498db; border-radius:4px; font-weight:500; color:#2980b9; text-align:center; min-height:24px;">[Class]</div>' },
                { label: 'Roll Number', attributes: { class: 'fa fa-hashtag', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#e8f4f8; border:2px dashed #3498db; border-radius:4px; font-weight:500; color:#2980b9; text-align:center; min-height:24px;">[Roll Number]</div>' },
                { label: 'School Name', attributes: { class: 'fa fa-institution', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#fef5e7; border:2px dashed #f39c12; border-radius:4px; font-weight:600; color:#d68910; text-align:center; min-height:24px;">[School Name]</div>' },
                { label: 'School Logo', attributes: { class: 'fa fa-image', 'data-category': 'fields' }, content: '<div style="padding:15px; background:#f0f0f0; border:2px dashed #95a5a6; border-radius:4px; text-align:center; min-height:60px;"><img src="' + '<?= base_url('uploads/app_image/report-card-logo.png') ?>' + '" style="max-height:60px; max-width:100%;" /></div>' },
                { label: 'Student Photo', attributes: { class: 'fa fa-camera', 'data-category': 'fields' }, content: '<div style="padding:15px; background:#f0f0f0; border:2px dashed #95a5a6; border-radius:4px; text-align:center; width:100px; height:120px; margin:auto;"><p style="color:#7f8c8d; font-size:11px; padding-top:40px;">Student Photo</p></div>' },
                { label: 'Academic Year', attributes: { class: 'fa fa-calendar', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#e8f4f8; border:2px dashed #3498db; border-radius:4px; font-weight:500; color:#2980b9; text-align:center; min-height:24px;">[Academic Year]</div>' },
                { label: 'Report Date', attributes: { class: 'fa fa-calendar-check-o', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#e8f4f8; border:2px dashed #3498db; border-radius:4px; font-weight:500; color:#2980b9; text-align:center; min-height:24px;">[Date]</div>' },
                { label: 'Sponsor/Donor', attributes: { class: 'fa fa-heart', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#fadbd8; border:2px dashed #e74c3c; border-radius:4px; font-weight:500; color:#c0392b; text-align:center; min-height:24px;">[Sponsor Name]</div>' },
                { label: 'Principal Signature', attributes: { class: 'fa fa-pen-square', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#d5f4e6; border:2px dashed #27ae60; border-radius:4px; text-align:center; min-height:40px;"><p style="font-size:11px; color:#27ae60; margin-bottom:4px;">Principal Signature</p><div style="border-top:1px solid #27ae60; height:20px;"></div></div>' },
                { label: 'Teacher Signature', attributes: { class: 'fa fa-pen-square', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#d5f4e6; border:2px dashed #27ae60; border-radius:4px; text-align:center; min-height:40px;"><p style="font-size:11px; color:#27ae60; margin-bottom:4px;">Teacher Signature</p><div style="border-top:1px solid #27ae60; height:20px;"></div></div>' },
                { label: 'Parent Signature', attributes: { class: 'fa fa-pen-square', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#d5f4e6; border:2px dashed #27ae60; border-radius:4px; text-align:center; min-height:40px;"><p style="font-size:11px; color:#27ae60; margin-bottom:4px;">Parent Signature</p><div style="border-top:1px solid #27ae60; height:20px;"></div></div>' },
                { label: 'Performance Grade', attributes: { class: 'fa fa-star', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#f4ecf7; border:2px dashed #8e44ad; border-radius:4px; font-weight:600; color:#8e44ad; text-align:center; font-size:18px; min-height:24px;">[Grade]</div>' },
                { label: 'Remarks', attributes: { class: 'fa fa-sticky-note', 'data-category': 'fields' }, content: '<div style="padding:8px 12px; background:#fffacd; border:2px dashed #f1c40f; border-radius:4px; color:#f39c12; min-height:40px; font-size:12px;"><p>Teacher Remarks / Comments</p></div>' },
                
                // ===== BASIC BLOCKS =====
                { label: 'Section', attributes: { class: 'fa fa-square-o' }, content: '<section style="padding: 20px; border: 1px solid #ddd;"><h2>Section</h2><p>Content here...</p></section>' },
                { label: 'Text', attributes: { class: 'fa fa-font' }, content: '<p>Your text here</p>' },
                { label: 'Heading', attributes: { class: 'fa fa-heading' }, content: '<h2>Heading</h2>' },
                { label: 'Paragraph', attributes: { class: 'fa fa-paragraph' }, content: '<p>Paragraph content</p>' },
                { label: 'Table', attributes: { class: 'fa fa-table' }, content: '<table style="width:100%; border-collapse:collapse;"><tr><td style="border:1px solid #ccc; padding:8px;">Cell 1</td><td style="border:1px solid #ccc; padding:8px;">Cell 2</td></tr></table>' },
                { label: 'Image', attributes: { class: 'fa fa-image' }, content: '<img src="' + '<?= base_url('uploads/app_image/report-card-logo.png') ?>' + '" style="max-width:100%; height:auto;" />' },
                { label: 'Button', attributes: { class: 'fa fa-mouse-pointer' }, content: '<button style="padding:10px 20px; background:#3498db; color:white; border:none; border-radius:4px; cursor:pointer;">Button</button>' },
                { label: 'HTML Block', attributes: { class: 'fa fa-code' }, content: '<div style="padding:15px; background:#f5f5f5; border:1px dashed #999; border-radius:4px;"><p style="color:#666; font-size:12px;">Custom HTML Block - Edit in Code View</p></div>' },
            ],
            panels: {
                defaults: [
                    {
                        id: 'layers',
                        el: '.gjs-panel-left',
                        resizable: {
                            maxWidth: 350,
                            minWidth: 200,
                        },
                    },
                    {
                        id: 'settings',
                        el: '.gjs-panel-right',
                        resizable: {
                            maxWidth: 350,
                            minWidth: 200,
                        },
                    },
                    {
                        id: 'styles',
                        el: '.gjs-panel-right',
                    },
                ],
            },
            deviceManager: {
                devices: [
                    {
                        name: 'Desktop',
                        width: '',
                    },
                    {
                        name: 'Mobile',
                        width: '320px',
                        widthMedia: '480px',
                    },
                ],
            },
            undoManager: { trackChanges: true },
            canvas: {
                // Canvas settings can be customized here
            },
        });

        // Add custom CSS for report card styling
        var css = editor.getCss();
        editor.setStyle(`
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; color: #333; }
            h2, h3, h4 { margin-bottom: 10px; }
            p { margin-bottom: 8px; line-height: 1.5; }
            table { width: 100%; border-collapse: collapse; }
            table td, table th { border: 1px solid #ddd; padding: 8px; text-align: left; }
            table th { background: #f5f5f5; font-weight: bold; }
        `);

        // Load existing design if available
        var existingFront = <?= isset($canvas_layouts['front_canvas_layout']) ? json_encode($canvas_layouts['front_canvas_layout']) : 'null' ?>;
        var existingBack = <?= isset($canvas_layouts['back_canvas_layout']) ? json_encode($canvas_layouts['back_canvas_layout']) : 'null' ?>;
        
        if (existingFront) {
            // Parse and load existing front page design
            try {
                if (typeof existingFront === 'string') {
                    existingFront = JSON.parse(existingFront);
                }
                if (existingFront.html) {
                    editor.setComponents(existingFront.html);
                    $('#front-page-html').val(existingFront.html);
                }
            } catch(e) {
                console.log('Could not load existing front page design');
            }
        }

        // Page orientation handler
        $('#page-orientation').on('change', function() {
            var orient = $(this).val();
            $('#page-orientation-value').val(orient);
            
            // Update canvas aspect ratio
            var canvas = editor.Canvas.getElement();
            if (orient === 'landscape') {
                canvas.style.aspectRatio = '11/8.5';
            } else {
                canvas.style.aspectRatio = '8.5/11';
            }
        });

        // Switch between front and back pages
        var currentPage = 'front';
        
        $('#current-page').on('change', function() {
            currentPage = $(this).val();
            
            // Save current page content
            var content = editor.getHtml();
            if (currentPage === 'front') {
                $('#back-page-html').val(content);
                // Load front page if exists
                var frontHtml = $('#front-page-html').val();
                if (frontHtml) {
                    editor.setComponents(frontHtml);
                }
            } else {
                $('#front-page-html').val(content);
                // Load back page if exists
                var backHtml = $('#back-page-html').val();
                if (backHtml) {
                    editor.setComponents(backHtml);
                }
            }
        });

        // Save design
        $('#save-design-btn').on('click', function() {
            // Capture current page
            var content = editor.getHtml();
            if (currentPage === 'front') {
                $('#front-page-html').val(content);
            } else {
                $('#back-page-html').val(content);
            }

            var frontHtml = $('#front-page-html').val();
            var backHtml = $('#back-page-html').val();
            var orientation = $('#page-orientation-value').val();

            // Prepare design data
            var designData = {
                front: {
                    html: frontHtml,
                    orientation: orientation,
                },
                back: {
                    html: backHtml,
                    orientation: orientation,
                },
            };

            // Send to server
            $.ajax({
                url: '<?= base_url('settings/save_grapejs_design'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    design_data: JSON.stringify(designData),
                    orientation: orientation,
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Design saved successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    console.log('Error response:', xhr);
                    alert('Error saving design');
                }
            });
        });

        // Preview design
        $('#preview-design-btn').on('click', function() {
            var frontHtml = $('#front-page-html').val();
            var backHtml = $('#back-page-html').val();
            
            var previewContent = '<html><head><style>body{font-family:Arial,sans-serif; margin:40px;} .page{page-break-after:always; padding:20px; border:1px solid #ddd; min-height:11in;}</style></head><body>';
            previewContent += '<div class="page">' + (frontHtml || '<p>Front page is empty</p>') + '</div>';
            previewContent += '<div class="page">' + (backHtml || '<p>Back page is empty</p>') + '</div>';
            previewContent += '</body></html>';
            
            var win = window.open('', 'preview', 'width=1000,height=800');
            win.document.write(previewContent);
            win.document.close();
        });

        // Export HTML
        $('#export-design-btn').on('click', function() {
            var frontHtml = $('#front-page-html').val();
            var backHtml = $('#back-page-html').val();
            
            var html = '<html><head><meta charset="utf-8"><title>Report Card Design</title>';
            html += '<style>body{font-family:Arial,sans-serif; margin:0;} .page{page-break-after:always; padding:40px; min-height:11in;}</style>';
            html += '</head><body>';
            html += '<div class="page">' + frontHtml + '</div>';
            html += '<div class="page">' + backHtml + '</div>';
            html += '</body></html>';
            
            var blob = new Blob([html], {type: 'text/html'});
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'report-card-design.html';
            a.click();
            URL.revokeObjectURL(url);
        });
    });
})(jQuery);
</script>
