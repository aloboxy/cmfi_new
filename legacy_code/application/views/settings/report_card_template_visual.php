<?php
/**
 * Report Card Template Visual Block Editor
 * Allows drag-and-drop block positioning on front/back canvases
 * Uses GridStack for layout and Summernote for HTML editing
 */
?>
<style>
    .editor-wrapper {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }
    .block-palette {
        flex: 0 0 200px;
        background: #f5f5f5;
        padding: 15px;
        border-radius: 5px;
        max-height: 600px;
        overflow-y: auto;
    }
    .block-palette h5 {
        margin-top: 0;
        font-size: 13px;
        text-transform: uppercase;
        color: #666;
        font-weight: bold;
    }
    .draggable-block {
        background: white;
        padding: 10px;
        margin-bottom: 10px;
        border: 2px dashed #3498db;
        border-radius: 4px;
        cursor: grab;
        user-select: none;
        font-size: 12px;
        text-align: center;
        transition: all 0.3s ease;
        draggable: true;
    }
    .draggable-block:active {
        cursor: grabbing;
    }
    .draggable-block:hover {
        background: #e3f2fd;
        border-color: #2196f3;
    }
    .draggable-block.dragging {
        opacity: 0.5;
        background: #bbdefb;
    }
    .grid-stack.drag-over {
        background: #2196f3 !important;
        border-color: #1976d2 !important;
    }
    .canvases-wrapper {
        flex: 1;
        display: flex;
        gap: 20px;
    }
    .canvas-container {
        flex: 1;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
    }
    .canvas-header {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #3498db;
        color: #2c3e50;
    }
    .grid-stack {
        background: #fafafa;
        min-height: 700px;
        border: 1px dashed #ccc;
        border-radius: 3px;
    }
    .grid-stack-item {
        background: white;
        border: 2px solid #3498db;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: move;
    }
    .grid-stack-item-content {
        padding: 15px;
        height: 100%;
        overflow: hidden;
        position: relative;
    }
    .grid-stack-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e0e0e0;
    }
    .grid-stack-item-title {
        font-weight: bold;
        font-size: 12px;
        color: #2c3e50;
    }
    .grid-stack-item-remove {
        background: #e74c3c;
        color: white;
        border: none;
        padding: 3px 8px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 11px;
    }
    .grid-stack-item-remove:hover {
        background: #c0392b;
    }
    .block-preview {
        font-size: 11px;
        color: #7f8c8d;
        min-height: 40px;
    }
    .ui-state-highlight {
        background: #2196f3;
        opacity: 0.3;
        border: 2px dashed #2196f3;
    }
</style>

<section class="panel appear-animation" data-appear-animation="<?= $global_config['animations']; ?>" data-appear-animation-delay="100">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fas fa-palette"></i> Report Card Template Visual Designer</h4>
    </div>
    <div class="panel-body">
        <!-- Instructions -->
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i> <strong>How to use:</strong> Drag blocks from the palette on the left onto either the Front Page or Back Page canvas to design your report card layout. Drag within a canvas to reorder blocks. Click "Remove" to delete a block.
        </div>

        <!-- Block Palette -->
        <div class="editor-wrapper">
            <div class="block-palette">
                <h5>📦 Blocks</h5>
                <div class="draggable-block" data-block-type="student_info" draggable="true">
                    <i class="fas fa-user"></i><br/>Student Info
                </div>
                <div class="draggable-block" data-block-type="subjects_table" draggable="true">
                    <i class="fas fa-table"></i><br/>Marks Table
                </div>
                <div class="draggable-block" data-block-type="custom_html" draggable="true">
                    <i class="fas fa-code"></i><br/>HTML Block
                </div>
                <div class="draggable-block" data-block-type="image_logo" draggable="true">
                    <i class="fas fa-image"></i><br/>Image/Logo
                </div>
            </div>

            <!-- Two Canvases -->
            <div class="canvases-wrapper">
                <!-- Front Page Canvas -->
                <div class="canvas-container">
                    <div class="canvas-header">📄 Front Page</div>
                    <div class="grid-stack" id="front-canvas" data-gs-width="24" data-gs-cellheight="40"></div>
                </div>

                <!-- Back Page Canvas -->
                <div class="canvas-container">
                    <div class="canvas-header">📋 Back Page</div>
                    <div class="grid-stack" id="back-canvas" data-gs-width="24" data-gs-cellheight="40"></div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div style="margin-top: 20px; text-align: center;">
            <button type="button" id="save-layout-btn" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Layout
            </button>
            <button type="button" id="preview-layout-btn" class="btn btn-info ml-md">
                <i class="fas fa-eye"></i> Preview
            </button>
        </div>
    </div>
</section>

<!-- Include GridStack CSS/JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@8.4.0/dist/gridstack.min.css" />
<script src="https://cdn.jsdelivr.net/npm/gridstack@8.4.0/dist/gridstack-h5.umd.min.js"></script>

<!-- Include Summernote for HTML editing -->
<link href="<?= base_url('assets/vendor/summernote/summernote.css'); ?>" rel="stylesheet">
<script src="<?= base_url('assets/vendor/summernote/summernote.js'); ?>"></script>

<script>
(function($) {
    $(function() {
        // Initialize GridStack for both canvases
        var options = {
            float: true,
            animate: true,
            disableOneColumnMode: false,
        };

        var frontGrid = GridStack.init(options, '#front-canvas');
        var backGrid = GridStack.init(options, '#back-canvas');

        // Load existing layouts if available
        var frontLayout = <?= isset($canvas_layouts['front_canvas_layout']) ? json_encode($canvas_layouts['front_canvas_layout']) : '[]' ?>;
        var backLayout = <?= isset($canvas_layouts['back_canvas_layout']) ? json_encode($canvas_layouts['back_canvas_layout']) : '[]' ?>;

        if (frontLayout.length > 0) {
            frontGrid.load(frontLayout);
            attachBlockHandlers(frontGrid);
        }

        if (backLayout.length > 0) {
            backGrid.load(backLayout);
            attachBlockHandlers(backGrid);
        }

        // HTML5 Drag-and-Drop implementation
        var blockCounter = {front: 0, back: 0};
        var draggedBlockType = null;

        // Palette block drag handlers
        $(document).on('dragstart', '.draggable-block', function(e) {
            draggedBlockType = $(this).data('block-type');
            $(this).addClass('dragging');
            e.originalEvent.dataTransfer.effectAllowed = 'copy';
            e.originalEvent.dataTransfer.setData('blockType', draggedBlockType);
        });

        $(document).on('dragend', '.draggable-block', function(e) {
            $(this).removeClass('dragging');
            $('.grid-stack').removeClass('drag-over');
        });

        // Canvas drag-over handlers
        $(document).on('dragover', '.grid-stack', function(e) {
            e.preventDefault();
            e.originalEvent.dataTransfer.dropEffect = 'copy';
            $(this).addClass('drag-over');
        });

        $(document).on('dragleave', '.grid-stack', function(e) {
            $(this).removeClass('drag-over');
        });

        // Canvas drop handler
        $(document).on('drop', '.grid-stack', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var blockType = e.originalEvent.dataTransfer.getData('blockType');
            var canvas = $(this);
            var canvasId = canvas.attr('id'); // 'front-canvas' or 'back-canvas'
            var canvasName = canvasId.replace('-canvas', ''); // 'front' or 'back'
            
            // Get the GridStack instance
            var grid = canvas[0].gridstack;
            
            if (grid && blockType) {
                blockCounter[canvasName]++;
                var blockId = canvasName + '_' + blockType + '_' + blockCounter[canvasName];
                var blockHTML = createBlockHTML(blockType, blockId);
                
                grid.addWidget({
                    content: blockHTML,
                    h: 4,
                    w: 6,
                });
                
                attachBlockHandlers(grid);
            }
            
            canvas.removeClass('drag-over');
        });

        function createBlockHTML(blockType, blockId) {
            var title = '';
            var content = '';

            switch(blockType) {
                case 'student_info':
                    title = 'Student Information';
                    content = '<div class="block-preview">Student Name, Roll, Class, DOB, etc.</div>';
                    break;
                case 'subjects_table':
                    title = 'Marks Table';
                    content = '<div class="block-preview">Subject | Marks | Grade</div>';
                    break;
                case 'custom_html':
                    title = 'HTML Content';
                    content = '<textarea class="summernote-block" style="width:100%; height:80px;"></textarea>';
                    break;
                case 'image_logo':
                    title = 'Image/Logo';
                    content = '<div class="block-preview" style="text-align:center; padding:20px; background:#f0f0f0; border:1px dashed #ccc;">[Image Placeholder]</div>';
                    break;
            }

            var html = '<div class="grid-stack-item-content">' +
                '<div class="grid-stack-item-header">' +
                    '<span class="grid-stack-item-title">' + title + '</span>' +
                    '<button type="button" class="grid-stack-item-remove" data-block-id="' + blockId + '">Remove</button>' +
                '</div>' +
                '<div class="grid-stack-item-body" data-block-type="' + blockType + '" data-block-id="' + blockId + '">' +
                    content +
                '</div>' +
            '</div>';
            return html;
        }

        function attachBlockHandlers(grid) {
            // Remove button
            grid.el.find('.grid-stack-item-remove').off('click').on('click', function(e) {
                e.preventDefault();
                var item = $(this).closest('.grid-stack-item');
                grid.removeWidget(item, true);
            });

            // Initialize Summernote for custom HTML blocks
            grid.el.find('.summernote-block').each(function() {
                if (!$(this).data('summernote-initialized')) {
                    $(this).summernote({
                        height: 100,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link']],
                        ]
                    });
                    $(this).data('summernote-initialized', true);
                }
            });
        }

        // Save Layout
        $('#save-layout-btn').on('click', function() {
            var frontLayoutData = frontGrid.save(true);
            var backLayoutData = backGrid.save(true);

            // Serialize Summernote editors
            frontGrid.el.find('.summernote-block').each(function() {
                var blockId = $(this).closest('[data-block-id]').data('block-id');
                var content = $(this).summernote('code');
                // Store in data attribute for submission
                $(this).data('saved-content', content);
            });

            backGrid.el.find('.summernote-block').each(function() {
                var blockId = $(this).closest('[data-block-id]').data('block-id');
                var content = $(this).summernote('code');
                $(this).data('saved-content', content);
            });

            // Send to server
            $.ajax({
                url: '<?= base_url('settings/save_canvas_layout'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    front_canvas_layout: JSON.stringify(frontLayoutData),
                    back_canvas_layout: JSON.stringify(backLayoutData),
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Layout saved successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function() {
                    alert('Error saving layout');
                }
            });
        });

        // Preview
        $('#preview-layout-btn').on('click', function() {
            window.open('<?= base_url('exam/reportCardPreview'); ?>', 'preview', 'width=1000,height=800');
        });
    });
})(jQuery);
</script>
