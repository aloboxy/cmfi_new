<style>
    /* Ultra-Premium Glassmorphic Designer Styles */
    .designer-container {
        display: flex;
        gap: 24px;
        background: transparent;
        padding: 0;
        margin-top: 15px;
    }
    
    .designer-toolbox {
        width: 280px;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 20px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    html.dark .designer-toolbox {
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    .designer-canvas-wrapper {
        flex-grow: 1;
        background-color: #f1f5f9;
        /* Subtle blueprint grid */
        background-image: linear-gradient(rgba(148, 163, 184, 0.1) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(148, 163, 184, 0.1) 1px, transparent 1px);
        background-size: 20px 20px;
        padding: 40px 20px;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow: auto;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.02);
        border: 1px solid rgba(0,0,0,0.03);
    }

    html.dark .designer-canvas-wrapper {
        background-color: #0f172a;
        background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        box-shadow: inset 0 2px 15px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.05);
    }
    
    .canvas-container {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        background: #fff;
        margin: 0 auto;
        border-radius: 4px;
        transition: transform 0.3s ease;
    }
    
    .toolbox-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        cursor: grab;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        font-size: 0.9rem;
        color: #475569;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    html.dark .toolbox-item {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        color: #cbd5e1;
    }
    
    .toolbox-item:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 20px rgba(29, 78, 216, 0.12);
        border-color: var(--primary-light, #1d4ed8);
        color: var(--primary-light, #1d4ed8);
        z-index: 10;
    }

    html.dark .toolbox-item:hover {
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
        border-color: var(--primary-light, #3b82f6);
        color: #fff;
        background: rgba(59, 130, 246, 0.1);
    }
    
    .toolbox-item i {
        color: var(--primary-light, #3b82f6);
        font-size: 1.15em;
        width: 20px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .toolbox-item:hover i {
        transform: scale(1.15);
    }
    
    .designer-toolbar {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0;
        padding: 15px 24px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid rgba(255,255,255,0.5);
    }

    html.dark .designer-toolbar {
        background: rgba(30, 41, 59, 0.85);
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 4px 25px rgba(0,0,0,0.2);
    }
    
    .toolbar-actions {
        display: flex;
        gap: 12px;
    }

    /* Beautiful Buttons */
    .designer-toolbar .btn {
        border-radius: 8px !important;
        font-weight: 500;
        padding: 8px 16px;
        transition: all 0.3s ease;
        border: none !important;
    }

    .designer-toolbar .btn-group .btn {
        margin: 0;
    }

    .designer-toolbar .btn-default {
        background: #f1f5f9 !important;
        color: #475569 !important;
        box-shadow: none !important;
    }

    html.dark .designer-toolbar .btn-default {
        background: rgba(255, 255, 255, 0.05) !important;
        color: #94a3b8 !important;
    }

    .designer-toolbar .btn-primary {
        background: linear-gradient(135deg, var(--primary-light, #3b82f6) 0%, #1d4ed8 100%) !important;
        box-shadow: 0 4px 15px rgba(29, 78, 216, 0.25) !important;
        color: #fff !important;
    }

    .designer-toolbar .btn:hover {
        transform: translateY(-1px);
    }

    hr.toolbox-divider {
        border-top: 1px solid rgba(0,0,0,0.06);
        margin: 5px 0;
    }
    html.dark hr.toolbox-divider {
        border-top: 1px solid rgba(255,255,255,0.06);
    }

    .toolbox-heading {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        font-weight: 700;
        margin-top: 10px;
        margin-bottom: 5px;
        padding-left: 5px;
    }
</style>

<section class="panel appear-animation" data-appear-animation="<?= $global_config['animations'] ?? 'fadeInUp'; ?>" data-appear-animation-delay="100">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fas fa-paint-brush"></i> Report Card Canvas Designer</h4>
    </div>
    <div class="panel-body">
        
        <div class="designer-toolbar">
            <div class="page-selector">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-default active" id="btn-front-page">Front Page</button>
                    <button type="button" class="btn btn-default" id="btn-back-page">Back Page</button>
                </div>
            </div>
            
            <div class="toolbar-actions">
                <div class="form-group mb-0" style="margin-right: 15px; width: 200px;">
                    <select id="template-type-selector" class="form-control" style="height: 38px; border-radius: 8px;">
                        <option value="semester">Semester Template</option>
                        <option value="period">Period Template</option>
                        <option value="end_of_year">End of Year Template</option>
                    </select>
                </div>
                
                <div class="btn-group" style="margin-right: 15px;">
                    <button type="button" class="btn btn-default active" id="btn-portrait" title="Portrait">
                        <i class="fas fa-portrait"></i> Portrait
                    </button>
                    <button type="button" class="btn btn-default" id="btn-landscape" title="Landscape">
                        <i class="fas fa-image"></i> Landscape
                    </button>
                </div>
                
                <div class="btn-group" style="margin-right: 15px;">
                    <button type="button" class="btn btn-default" id="btn-zoom-out" title="Zoom Out">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button type="button" class="btn btn-default" id="btn-zoom-reset" title="Reset Zoom">
                        100%
                    </button>
                    <button type="button" class="btn btn-default" id="btn-zoom-in" title="Zoom In">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
                
                <button type="button" id="btn-delete" class="btn btn-danger btn-sm" disabled>
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
                <button type="button" id="btn-clear" class="btn btn-warning btn-sm">
                    <i class="fas fa-eraser"></i> Clear Canvas
                </button>
                <button type="button" id="btn-save" class="btn btn-primary btn-sm">
                    <i class="fas fa-save"></i> Save All Designs
                </button>
            </div>
        </div>
        
        <div id="text-properties-toolbar" style="display: none; padding: 10px 20px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05); align-items: center; gap: 15px; flex-wrap: wrap;">
            <div style="font-weight: 600; font-size: 13px; color: #64748b; margin-right: 10px;">TEXT PROPERTIES</div>
            
            <select id="text-font-family" class="form-control input-sm" style="width: 150px; display: inline-block;">
                <option value="Arial">Arial</option>
                <option value="Times New Roman">Times New Roman</option>
                <option value="Courier New">Courier New</option>
                <option value="Georgia">Georgia</option>
                <option value="Verdana">Verdana</option>
                <option value="Tahoma">Tahoma</option>
                <option value="Trebuchet MS">Trebuchet MS</option>
                <option value="Comic Sans MS">Comic Sans MS</option>
            </select>
            
            <div class="input-group input-group-sm" style="width: 100px;">
                <input type="number" id="text-font-size" class="form-control" value="20" min="8" max="120">
                <span class="input-group-addon">px</span>
            </div>
            
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm" id="btn-text-bold" title="Bold"><i class="fas fa-bold"></i></button>
                <button type="button" class="btn btn-default btn-sm" id="btn-text-italic" title="Italic"><i class="fas fa-italic"></i></button>
                <button type="button" class="btn btn-default btn-sm" id="btn-text-underline" title="Underline"><i class="fas fa-underline"></i></button>
            </div>
            
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm" id="btn-text-align-left" title="Align Left"><i class="fas fa-align-left"></i></button>
                <button type="button" class="btn btn-default btn-sm" id="btn-text-align-center" title="Align Center"><i class="fas fa-align-center"></i></button>
                <button type="button" class="btn btn-default btn-sm" id="btn-text-align-right" title="Align Right"><i class="fas fa-align-right"></i></button>
            </div>
            
            <input type="color" id="text-color" value="#000000" title="Text Color" style="height: 30px; width: 40px; padding: 0; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">
        </div>

        <div class="designer-container">
            <div class="designer-toolbox">
                <div class="toolbox-heading">Components</div>
                <p class="text-muted text-sm mb-md" style="padding-left: 5px;">Drag & drop to canvas</p>
                
                <div class="toolbox-item" data-type="text" data-text="[Student Name]">
                    <i class="fas fa-user"></i> Student Name
                </div>
                <div class="toolbox-item" data-type="text" data-text="[Class & Section]">
                    <i class="fas fa-users"></i> Class & Section
                </div>
                <div class="toolbox-item" data-type="text" data-text="[Roll Number]">
                    <i class="fas fa-hashtag"></i> Roll Number
                </div>
                <div class="toolbox-item" data-type="text" data-text="[Academic Year]">
                    <i class="fas fa-calendar"></i> Academic Year
                </div>
                <div class="toolbox-item" data-type="text" data-text="[Promoted To Class]">
                    <i class="fas fa-level-up-alt"></i> Promoted To Class
                </div>
                
                <hr class="toolbox-divider">
                <div class="toolbox-heading">Academic (Grades)</div>
                
                <div class="toolbox-item" data-type="grades_table" data-label="Grades Table">
                    <i class="fas fa-table"></i> Grades Table
                </div>
                <div class="toolbox-item" data-type="attendance_table" data-label="Attendance Table">
                    <i class="fas fa-calendar-check"></i> Attendance Table
                </div>
                <div class="toolbox-item" data-type="text" data-text="[GPA / Final Grade]">
                    <i class="fas fa-star"></i> GPA / Final Grade
                </div>
                
                <hr class="toolbox-divider">
                <div class="toolbox-heading">Elements</div>
                
                <div class="toolbox-item" data-type="logo">
                    <i class="fas fa-image"></i> School Logo
                </div>
                <div class="toolbox-item" data-type="signature" data-text="Principal Signature">
                    <i class="fas fa-pen-nib"></i> Principal Signature
                </div>
                <div class="toolbox-item" data-type="signature" data-text="Teacher Signature">
                    <i class="fas fa-pen-nib"></i> Teacher Signature
                </div>
                <div class="toolbox-item" data-type="text-custom">
                    <i class="fas fa-font"></i> Custom Text
                </div>
            </div>
            
            <div class="designer-canvas-wrapper">
                <!-- A4 Size at 96 DPI: 794 x 1123 -->
                <canvas id="gradeSheetCanvas" width="794" height="1123"></canvas>
            </div>
        </div>
        
    </div>
</section>

<!-- Fabric.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<script>
(function($) {
    $(function() {
        var canvas = new fabric.Canvas('gradeSheetCanvas', {
            backgroundColor: '#ffffff'
        });
        
        <?php 
            $front_settings = array();
            if (isset($template['front_layout_settings']) && !empty($template['front_layout_settings'])) {
                $front_settings = json_decode($template['front_layout_settings'], true);
            }
            $saved_orientation = isset($front_settings['orientation']) ? $front_settings['orientation'] : 'portrait';
            
            // Decode layouts
            $front_layout_raw = isset($canvas_layouts['front_canvas_layout']) && $canvas_layouts['front_canvas_layout'] ? $canvas_layouts['front_canvas_layout'] : null;
            $back_layout_raw = isset($canvas_layouts['back_canvas_layout']) && $canvas_layouts['back_canvas_layout'] ? $canvas_layouts['back_canvas_layout'] : null;
            
            // Get branch_id for AJAX calls (works for both superadmin and branch admin)
            $CI_inst = get_instance();
            $js_branch_id = intval(get_loggedin_branch_id());
            if (!$js_branch_id) {
                // Superadmin with no branch in session: use first branch
                $branch_row = $CI_inst->db->select('id')->order_by('id', 'ASC')->limit(1)->get('branch')->row();
                $js_branch_id = $branch_row ? intval($branch_row->id) : 1;
            }
        ?>
        
        var branchId = <?= $js_branch_id ?>;
        
        var currentPage = 'front';
        var currentTemplateType = 'semester';
        var currentOrientation = '<?= $saved_orientation ?>';
        
        // Parse the raw layout data. If it has 'semester', it's the new format. Otherwise, migrate it to 'semester' as a fallback.
        var frontRaw = <?= $front_layout_raw ? json_encode($front_layout_raw) : 'null' ?>;
        var backRaw = <?= $back_layout_raw ? json_encode($back_layout_raw) : 'null' ?>;
        
        var pageData = {
            semester: { front: null, back: null },
            period: { front: null, back: null },
            end_of_year: { front: null, back: null }
        };

        function parseLayouts(raw, side) {
            if (raw) {
                var parsed = typeof raw === 'string' ? JSON.parse(raw) : raw;
                if (parsed && (parsed.semester || parsed.period || parsed.end_of_year)) {
                    if (parsed.semester) pageData.semester[side] = parsed.semester;
                    if (parsed.period) pageData.period[side] = parsed.period;
                    if (parsed.end_of_year) pageData.end_of_year[side] = parsed.end_of_year;
                } else if (parsed && Object.keys(parsed).length > 0) {
                    // Old format (single template), map it to semester by default
                    pageData.semester[side] = parsed;
                }
            }
        }
        
        parseLayouts(frontRaw, 'front');
        parseLayouts(backRaw, 'back');
        
        // Setup orientation dimensions
        var dimensions = {
            portrait: { width: 794, height: 1123 },
            landscape: { width: 1123, height: 794 }
        };
        
        function applyOrientation(orientation) {
            currentOrientation = orientation;
            canvas.setWidth(dimensions[orientation].width);
            canvas.setHeight(dimensions[orientation].height);
            canvas.renderAll();
            
            $('#btn-portrait, #btn-landscape').removeClass('btn-primary').addClass('btn-default');
            $('#btn-' + orientation).removeClass('btn-default').addClass('btn-primary');
        }
        
        // Initial setup
        applyOrientation(currentOrientation);
        
        // Ensure standard object scaling behaves intuitively
        fabric.Object.prototype.set({
            transparentCorners: false,
            cornerColor: 'rgba(29, 78, 216, 0.8)',
            cornerStrokeColor: '#1d4ed8',
            borderColor: 'rgba(29, 78, 216, 0.8)',
            cornerSize: 10,
            padding: 5
        });

        // Zoom functionality
        var currentZoom = 1;
        
        function setZoom(zoomValue) {
            currentZoom = zoomValue;
            if (currentZoom < 0.2) currentZoom = 0.2;
            if (currentZoom > 3) currentZoom = 3;
            
            canvas.setZoom(currentZoom);
            canvas.setWidth(dimensions[currentOrientation].width * currentZoom);
            canvas.setHeight(dimensions[currentOrientation].height * currentZoom);
            $('#btn-zoom-reset').text(Math.round(currentZoom * 100) + '%');
        }

        $('#btn-zoom-in').on('click', function() {
            setZoom(currentZoom + 0.1);
        });
        
        $('#btn-zoom-out').on('click', function() {
            setZoom(currentZoom - 0.1);
        });
        
        $('#btn-zoom-reset').on('click', function() {
            setZoom(1);
        });
        
        // Mouse wheel zoom
        canvas.on('mouse:wheel', function(opt) {
            if (opt.e.ctrlKey || opt.e.metaKey) {
                var delta = opt.e.deltaY;
                var zoom = currentZoom;
                zoom *= 0.999 ** delta;
                setZoom(zoom);
                opt.e.preventDefault();
                opt.e.stopPropagation();
            }
        });

        // Initialize Canvas with existing data
        function loadPageData() {
            canvas.clear();
            canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
            
            var data = pageData[currentTemplateType][currentPage];
            if (data) {
                var jsonStr = data;
                if (typeof jsonStr === 'string') {
                    try {
                        jsonStr = JSON.parse(jsonStr);
                    } catch(e) {}
                }
                canvas.loadFromJSON(jsonStr, function() {
                    setZoom(1); // Reset zoom on load to ensure proper rendering bounds
                    canvas.renderAll();
                });
            }
        }
        
        // Load initially
        loadPageData();
        
        // Page Switching
        $('#btn-front-page, #btn-back-page').on('click', function() {
            var targetPage = $(this).attr('id') === 'btn-front-page' ? 'front' : 'back';
            if (targetPage === currentPage) return;
            
            // Save current state
            pageData[currentTemplateType][currentPage] = canvas.toJSON();
            
            // Toggle active class
            $('.page-selector .btn').removeClass('btn-primary').addClass('btn-default');
            $(this).removeClass('btn-default').addClass('btn-primary');
            
            // Load new page
            currentPage = targetPage;
            loadPageData();
        });
        
        // Template Type Switching
        $('#template-type-selector').on('change', function() {
            var targetTemplate = $(this).val();
            if (targetTemplate === currentTemplateType) return;
            
            // Save current state
            pageData[currentTemplateType][currentPage] = canvas.toJSON();
            
            // Load new template type
            currentTemplateType = targetTemplate;
            loadPageData();
        });
        
        // Initially set the front button as primary
        $('#btn-front-page').removeClass('btn-default').addClass('btn-primary');

        // Orientation Switching
        $('#btn-portrait, #btn-landscape').on('click', function() {
            var targetOrientation = $(this).attr('id') === 'btn-portrait' ? 'portrait' : 'landscape';
            if (targetOrientation === currentOrientation) return;
            
            if(confirm('Changing orientation will resize the canvas. Continue?')) {
                applyOrientation(targetOrientation);
            }
        });

        // Delete Object
        function deleteSelected() {
            var activeObjects = canvas.getActiveObjects();
            if (activeObjects.length) {
                canvas.discardActiveObject();
                activeObjects.forEach(function(object) {
                    canvas.remove(object);
                });
            }
        }
        
        $('#btn-delete').on('click', deleteSelected);
        
        // Keyboard support for Delete
        $(document).on('keydown', function(e) {
            if (e.keyCode === 46 || e.keyCode === 8) { // Delete or Backspace
                // Check if we are not editing text
                if (canvas.getActiveObject() && !canvas.getActiveObject().isEditing) {
                    deleteSelected();
                    e.preventDefault();
                }
            }
        });

        // Enable/Disable delete button
        canvas.on('selection:created', function() { $('#btn-delete').prop('disabled', false); });
        canvas.on('selection:updated', function() { $('#btn-delete').prop('disabled', false); });
        canvas.on('selection:cleared', function() { $('#btn-delete').prop('disabled', true); });

        // Clear Canvas
        $('#btn-clear').on('click', function() {
            if (confirm('Are you sure you want to clear this page?')) {
                canvas.clear();
                canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
            }
        });

        // Add Items from Toolbox
        $('.toolbox-item').on('click', function() {
            var type = $(this).data('type');
            var text = $(this).data('text');
            var center = canvas.getCenter();
            
            if (type === 'text' || type === 'text-custom') {
                var content = text || 'Double click to edit';
                var textObj = new fabric.IText(content, {
                    left: center.left,
                    top: center.top,
                    fontFamily: 'Arial',
                    fontSize: 20,
                    fill: '#333333',
                    originX: 'center',
                    originY: 'center'
                });
                canvas.add(textObj);
                canvas.setActiveObject(textObj);
            } else if (type === 'grades_table' || type === 'attendance_table') {
                var specificLabel = $(this).data('label') || 'Grades Table Placeholder';
                var label = '[' + specificLabel + ']';
                var rect = new fabric.Rect({
                    fill: '#f1f5f9',
                    stroke: '#94a3b8',
                    strokeWidth: 2,
                    strokeDashArray: [5, 5],
                    width: 600,
                    height: 250,
                    originX: 'center',
                    originY: 'center'
                });
                var tObj = new fabric.Text(label, {
                    fontSize: 24,
                    fill: '#64748b',
                    fontFamily: 'Arial',
                    originX: 'center',
                    originY: 'center'
                });
                var group = new fabric.Group([rect, tObj], {
                    left: center.left,
                    top: center.top,
                    originX: 'center',
                    originY: 'center',
                    customType: type // custom property to identify it later
                });
                // Group is selectable but children aren't directly editable
                canvas.add(group);
                canvas.setActiveObject(group);
                
            } else if (type === 'signature') {
                var line = new fabric.Line([-100, 0, 100, 0], {
                    stroke: '#000',
                    strokeWidth: 2
                });
                var tObj = new fabric.Text(text, {
                    fontSize: 16,
                    fill: '#000',
                    fontFamily: 'Arial',
                    originX: 'center',
                    originY: 'top',
                    top: 10
                });
                var group = new fabric.Group([line, tObj], {
                    left: center.left,
                    top: center.top,
                    originX: 'center',
                    originY: 'center'
                });
                canvas.add(group);
                canvas.setActiveObject(group);
                
            } else if (type === 'logo') {
                fabric.Image.fromURL('<?= cache_busted_url("uploads/app_image/report-card-logo.png") ?>', function(img) {
                    img.set({
                        left: center.left,
                        top: center.top,
                        originX: 'center',
                        originY: 'center'
                    });
                    // scale down if too large
                    if (img.width > 150) {
                        img.scaleToWidth(150);
                    }
                    canvas.add(img);
                    canvas.setActiveObject(img);
                });
            }
        });

        // Text Properties Toolbar Logic
        function updateTextToolbar() {
            var activeObj = canvas.getActiveObject();
            if (activeObj && (activeObj.type === 'i-text' || activeObj.type === 'text')) {
                $('#text-properties-toolbar').css('display', 'flex');
                
                // Populate current values
                $('#text-font-family').val(activeObj.fontFamily || 'Arial');
                $('#text-font-size').val(activeObj.fontSize || 20);
                $('#text-color').val(activeObj.fill || '#000000');
                
                // Toggle active states
                $('#btn-text-bold').toggleClass('active btn-primary', activeObj.fontWeight === 'bold');
                $('#btn-text-italic').toggleClass('active btn-primary', activeObj.fontStyle === 'italic');
                $('#btn-text-underline').toggleClass('active btn-primary', activeObj.underline === true);
                
                // Alignment
                $('#btn-text-align-left, #btn-text-align-center, #btn-text-align-right').removeClass('active btn-primary');
                if (activeObj.textAlign === 'center') {
                    $('#btn-text-align-center').addClass('active btn-primary');
                } else if (activeObj.textAlign === 'right') {
                    $('#btn-text-align-right').addClass('active btn-primary');
                } else {
                    $('#btn-text-align-left').addClass('active btn-primary');
                }
            } else {
                $('#text-properties-toolbar').hide();
            }
        }
        
        // Canvas Selection Events
        canvas.on('selection:created', updateTextToolbar);
        canvas.on('selection:updated', updateTextToolbar);
        canvas.on('selection:cleared', updateTextToolbar);
        
        // Property Changes
        $('#text-font-family').on('change', function() {
            var obj = canvas.getActiveObject();
            if (obj && (obj.type === 'i-text' || obj.type === 'text')) {
                obj.set('fontFamily', $(this).val());
                canvas.renderAll();
            }
        });
        
        $('#text-font-size').on('input', function() {
            var obj = canvas.getActiveObject();
            if (obj && (obj.type === 'i-text' || obj.type === 'text')) {
                obj.set('fontSize', parseInt($(this).val(), 10));
                canvas.renderAll();
            }
        });
        
        $('#text-color').on('input', function() {
            var obj = canvas.getActiveObject();
            if (obj && (obj.type === 'i-text' || obj.type === 'text')) {
                obj.set('fill', $(this).val());
                canvas.renderAll();
            }
        });
        
        $('#btn-text-bold').on('click', function() {
            var obj = canvas.getActiveObject();
            if (obj && (obj.type === 'i-text' || obj.type === 'text')) {
                var isBold = obj.fontWeight === 'bold';
                obj.set('fontWeight', isBold ? 'normal' : 'bold');
                updateTextToolbar();
                canvas.renderAll();
            }
        });
        
        $('#btn-text-italic').on('click', function() {
            var obj = canvas.getActiveObject();
            if (obj && (obj.type === 'i-text' || obj.type === 'text')) {
                var isItalic = obj.fontStyle === 'italic';
                obj.set('fontStyle', isItalic ? 'normal' : 'italic');
                updateTextToolbar();
                canvas.renderAll();
            }
        });
        
        $('#btn-text-underline').on('click', function() {
            var obj = canvas.getActiveObject();
            if (obj && (obj.type === 'i-text' || obj.type === 'text')) {
                obj.set('underline', !obj.underline);
                updateTextToolbar();
                canvas.renderAll();
            }
        });
        
        $('#btn-text-align-left').on('click', function() {
            var obj = canvas.getActiveObject();
            if (obj && (obj.type === 'i-text' || obj.type === 'text')) {
                obj.set('textAlign', 'left');
                updateTextToolbar();
                canvas.renderAll();
            }
        });
        
        $('#btn-text-align-center').on('click', function() {
            var obj = canvas.getActiveObject();
            if (obj && (obj.type === 'i-text' || obj.type === 'text')) {
                obj.set('textAlign', 'center');
                updateTextToolbar();
                canvas.renderAll();
            }
        });
        
        $('#btn-text-align-right').on('click', function() {
            var obj = canvas.getActiveObject();
            if (obj && (obj.type === 'i-text' || obj.type === 'text')) {
                obj.set('textAlign', 'right');
                updateTextToolbar();
                canvas.renderAll();
            }
        });

        // Save Design
        $('#btn-save').on('click', function() {
            // Temporarily reset zoom to 1 to ensure raw JSON coordinates are saved at 100% scale
            var previousZoom = currentZoom;
            setZoom(1);
            
            // Update current page data
            pageData[currentTemplateType][currentPage] = canvas.toJSON();
            
            // Restore previous zoom
            setZoom(previousZoom);
            
            var btn = $(this);
            var originalText = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
            
            var designData = {
                front: {
                    semester: pageData.semester.front,
                    period: pageData.period.front,
                    end_of_year: pageData.end_of_year.front
                },
                back: {
                    semester: pageData.semester.back,
                    period: pageData.period.back,
                    end_of_year: pageData.end_of_year.back
                }
            };
            
            // Build CSRF-safe POST data
            var postData = $.extend({}, csrfData, {
                branch_id: branchId,
                design_data: JSON.stringify(designData),
                orientation: currentOrientation
            });

            $.ajax({
                url: '<?= base_url('settings/save_grapejs_design'); ?>',
                type: 'POST',
                dataType: 'json',
                data: postData,
                success: function(response) {
                    btn.html(originalText).prop('disabled', false);
                    if (response.status === 'success') {
                        swal({
                            toast: true,
                            position: 'top-end',
                            type: 'success',
                            title: 'Design saved successfully',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        swal('Error', response.message || 'Unknown error occurred.', 'error');
                    }
                },
                error: function(xhr) {
                    btn.html(originalText).prop('disabled', false);
                    var msg = 'Failed to save. ';
                    try {
                        var resp = JSON.parse(xhr.responseText);
                        msg += resp.message || '';
                    } catch(e) {
                        if (xhr.status === 403) msg = 'Session expired. Please refresh the page.';
                        else msg += 'HTTP ' + xhr.status + '. Check console for details.';
                        console.error('Save error:', xhr.responseText);
                    }
                    swal('Error', msg, 'error');
                }
            });
        });
        
    });
})(jQuery);
</script>
