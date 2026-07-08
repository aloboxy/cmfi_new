<?php
$div = 0;
if (get_permission('employee_count_widget', 'is_view')) $div++;
if (get_permission('student_count_widget', 'is_view'))  $div++;
if (get_permission('parent_count_widget', 'is_view'))   $div++;
if (get_permission('teacher_count_widget', 'is_view'))  $div++;
$widget_col = ($div > 0) ? (12 / $div) : 0;

$div2 = 0;
if (get_permission('admission_count_widget', 'is_view'))  $div2++;
if (get_permission('voucher_count_widget', 'is_view'))    $div2++;
if (get_permission('transport_count_widget', 'is_view'))  $div2++;
if (get_permission('hostel_count_widget', 'is_view'))     $div2++;
$widget_col2 = ($div2 > 0) ? (12 / $div2) : 0;
?>

<!-- ══ STAT CARDS ROW 1 ══ -->
<?php if ($widget_col > 0): ?>
<div class="row stat-row" style="margin-bottom:1.5rem;">

    <?php if (get_permission('student_count_widget', 'is_view')): ?>
    <div class="col-lg-<?= $widget_col ?> col-sm-6" style="margin-bottom:1rem;">
        <div class="stat-card stat-card--blue">
            <div class="stat-card__icon"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?= $get_total_student ?></div>
                <div class="stat-card__label"><?= translate('students') ?></div>
                <div class="stat-card__sub"><?= translate('total_strength') ?></div>
            </div>
            <div class="stat-card__bg-icon"><i class="fas fa-user-graduate"></i></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (get_permission('teacher_count_widget', 'is_view')):
        $staff_t = $this->dashboard_model->getstaffcounter(3, $school_id);
    ?>
    <div class="col-lg-<?= $widget_col ?> col-sm-6" style="margin-bottom:1rem;">
        <div class="stat-card stat-card--green">
            <div class="stat-card__icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?= $staff_t['snumber'] ?></div>
                <div class="stat-card__label"><?= translate('teachers') ?></div>
                <div class="stat-card__sub"><?= translate('total_strength') ?></div>
            </div>
            <div class="stat-card__bg-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (get_permission('employee_count_widget', 'is_view')):
        $staff_e = $this->dashboard_model->getstaffcounter('', $school_id);
    ?>
    <div class="col-lg-<?= $widget_col ?> col-sm-6" style="margin-bottom:1rem;">
        <div class="stat-card stat-card--purple">
            <div class="stat-card__icon"><i class="fas fa-users"></i></div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?= $staff_e['snumber'] ?></div>
                <div class="stat-card__label"><?= translate('employee') ?></div>
                <div class="stat-card__sub"><?= translate('total_strength') ?></div>
            </div>
            <div class="stat-card__bg-icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (get_permission('parent_count_widget', 'is_view')):
        if (!empty($school_id)) $this->db->where('branch_id', $school_id);
        $parent_count = $this->db->select('id')->get('parent')->num_rows();
    ?>
    <div class="col-lg-<?= $widget_col ?> col-sm-6" style="margin-bottom:1rem;">
        <div class="stat-card stat-card--gold">
            <div class="stat-card__icon"><i class="fas fa-user-friends"></i></div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?= $parent_count ?></div>
                <div class="stat-card__label"><?= translate('parents') ?></div>
                <div class="stat-card__sub"><?= translate('total_strength') ?></div>
            </div>
            <div class="stat-card__bg-icon"><i class="fas fa-user-friends"></i></div>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /.row stat-row -->
<?php endif; ?>

<!-- ══ STAT CARDS ROW 2 ══ -->
<?php if ($widget_col2 > 0): ?>
<div class="row stat-row" style="margin-bottom:1.5rem;">

    <?php if (get_permission('admission_count_widget', 'is_view')): ?>
    <div class="col-lg-<?= $widget_col2 ?> col-sm-6" style="margin-bottom:1rem;">
        <div class="stat-card stat-card--teal">
            <div class="stat-card__icon"><i class="far fa-address-card"></i></div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?= $get_monthly_admission ?></div>
                <div class="stat-card__label"><?= translate('admission') ?></div>
                <div class="stat-card__sub"><?= translate('interval_month') ?></div>
            </div>
            <div class="stat-card__bg-icon"><i class="far fa-address-card"></i></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (get_permission('voucher_count_widget', 'is_view')): ?>
    <div class="col-lg-<?= $widget_col2 ?> col-sm-6" style="margin-bottom:1rem;">
        <div class="stat-card stat-card--red">
            <div class="stat-card__icon"><i class="fas fa-money-check-alt"></i></div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?= $get_voucher ?></div>
                <div class="stat-card__label"><?= translate('voucher') ?></div>
                <div class="stat-card__sub"><?= translate('total_number') ?></div>
            </div>
            <div class="stat-card__bg-icon"><i class="fas fa-money-check-alt"></i></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (get_permission('transport_count_widget', 'is_view')): ?>
    <div class="col-lg-<?= $widget_col2 ?> col-sm-6" style="margin-bottom:1rem;">
        <div class="stat-card stat-card--orange">
            <div class="stat-card__icon"><i class="fas fa-bus-alt"></i></div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?= $get_transport_route ?></div>
                <div class="stat-card__label"><?= translate('transport') ?></div>
                <div class="stat-card__sub"><?= translate('total_route') ?></div>
            </div>
            <div class="stat-card__bg-icon"><i class="fas fa-bus-alt"></i></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (get_permission('hostel_count_widget', 'is_view')):
        if (!empty($school_id)) $this->db->where('branch_id', $school_id);
        $hostel_room = $this->db->select('id')->get('hostel_room')->num_rows();
    ?>
    <div class="col-lg-<?= $widget_col2 ?> col-sm-6" style="margin-bottom:1rem;">
        <div class="stat-card stat-card--indigo">
            <div class="stat-card__icon"><i class="fas fa-building"></i></div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?= $hostel_room ?></div>
                <div class="stat-card__label"><?= translate('hostel') ?></div>
                <div class="stat-card__sub"><?= translate('total_room') ?></div>
            </div>
            <div class="stat-card__bg-icon"><i class="fas fa-building"></i></div>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php endif; ?>

<!-- ══ CHARTS ROW ══ -->
<div class="row">
    <?php if (get_permission('monthly_income_vs_expense_chart', 'is_view')): ?>
    <div class="<?= get_permission('annual_student_fees_summary_chart', 'is_view') ? 'col-md-12 col-lg-4 col-xl-3' : 'col-md-12' ?>">
        <section class="panel pg-fw">
            <div class="panel-heading">
                <i class="fas fa-chart-pie" style="color:var(--liberia-red,#BF0A30);margin-right:6px;"></i>
                <?= translate('income_vs_expense_of') . ' ' . date('F') ?>
            </div>
            <div class="panel-body">
                <div id="cash_book_transaction" style="min-height:260px;"></div>
                <div class="text-center" style="margin-top:8px;">
                    <span class="label label-danger" style="margin-right:4px;"><?= translate('expense') ?></span>
                    <span class="label label-primary"><?= translate('income') ?></span>
                </div>
            </div>
        </section>
    </div>
    <?php endif; ?>

    <?php if (get_permission('annual_student_fees_summary_chart', 'is_view')): ?>
    <div class="<?= get_permission('monthly_income_vs_expense_chart', 'is_view') ? 'col-md-12 col-lg-8 col-xl-9' : 'col-md-12' ?>">
        <section class="panel">
            <div class="panel-heading">
                <i class="fas fa-chart-bar" style="color:#1D4ED8;margin-right:6px;"></i>
                <?= translate('annual_fee_summary') ?>
            </div>
            <div class="panel-body">
                <div class="chart-container" style="position: relative; height: 280px; width: 100%;">
                    <canvas id="fees_graph"></canvas>
                </div>
            </div>
        </section>
    </div>
    <?php endif; ?>
</div>

<!-- ══ STUDENT CHARTS ROW ══ -->
<div class="row">
    <?php if (get_permission('student_quantity_pie_chart', 'is_view')): ?>
    <div class="<?= get_permission('weekend_attendance_inspection_chart', 'is_view') ? 'col-md-12 col-lg-4 col-xl-3' : 'col-md-12' ?>">
        <section class="panel pg-fw">
            <div class="panel-heading">
                <i class="fas fa-school" style="color:#10B981;margin-right:6px;"></i>
                <?= translate('student_quantity') ?>
            </div>
            <div class="panel-body">
                <div id="student_strength" style="min-height:260px;"></div>
            </div>
        </section>
    </div>
    <?php endif; ?>

    <?php if (get_permission('weekend_attendance_inspection_chart', 'is_view')): ?>
    <div class="<?= get_permission('student_quantity_pie_chart', 'is_view') ? 'col-md-12 col-lg-8 col-xl-9' : 'col-md-12' ?>">
        <section class="panel">
            <div class="panel-heading">
                <i class="fas fa-calendar-check" style="color:#F59E0B;margin-right:6px;"></i>
                <?= translate('weekend_attendance_inspection') ?>
            </div>
            <div class="panel-body">
                <div class="chart-container" style="position: relative; height: 280px; width: 100%;">
                    <canvas id="weekend_attendance"></canvas>
                </div>
            </div>
        </section>
    </div>
    <?php endif; ?>
</div>

<!-- ══ EVENT CALENDAR ══ -->
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <div class="panel-heading">
                <i class="far fa-calendar-alt" style="margin-right:6px;"></i>
                <?= translate('events') ?>
            </div>
            <div class="panel-body">
                <div id="event_calendar"></div>
            </div>
        </section>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="zoom-anim-dialog modal-block modal-block-primary mfp-hide" id="modal">
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-btn">
                <button onclick="fn_printElem('printResult')" class="btn btn-default btn-circle icon"><i class="fas fa-print"></i></button>
            </div>
            <h4 class="panel-title"><i class="fas fa-info-circle"></i> <?= translate('event_details') ?></h4>
        </header>
        <div class="panel-body">
            <div id="printResult" class="pt-sm pb-sm">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed text-dark tbr-top" id="ev_table"></table>
                </div>
            </div>
        </div>
        <footer class="panel-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-default modal-dismiss"><?= translate('close') ?></button>
                </div>
            </div>
        </footer>
    </section>
</div>

<script type="application/javascript">
(function($) {
    // ── Full Calendar ──
    $('#event_calendar').fullCalendar({
        header: { left: 'prev,next,today', center: 'title', right: 'month,agendaWeek,agendaDay,listWeek' },
        firstDay: 1, height: 680, droppable: false, editable: true,
        events: { url: "<?= base_url('event/get_events_list/' . $school_id) ?>" },
        buttonText: { today:'Today', month:'Month', week:'Week', day:'Day', list:'List' },
        eventRender: function(event, element) {
            $(element).on("click", function() { viewEvent(event.id); });
            if (event.icon) { element.find(".fc-title").prepend("<i class='fas fa-" + event.icon + "'></i> "); }
        }
    });

    // ── Fees Summary Chart ──
    var total_fees = <?= json_encode($fees_summary["total_fee"]) ?>;
    var total_paid = <?= json_encode($fees_summary["total_paid"]) ?>;
    var total_due  = <?= json_encode($fees_summary["total_due"]) ?>;

    <?php if (get_permission('annual_student_fees_summary_chart', 'is_view')): ?>
    var ctx = document.getElementById('fees_graph').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [
                { label: '<?= translate("total") ?>', data: total_fees,      backgroundColor: 'rgba(0,40,104,0.65)',   borderRadius: 4 },
                { label: '<?= translate("collected") ?>', data: total_paid,  backgroundColor: 'rgba(16,185,129,0.65)', borderRadius: 4 },
                { label: '<?= translate("remaining") ?>', data: total_due,   backgroundColor: 'rgba(239,68,68,0.55)',  borderRadius: 4 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            tooltips: { mode: 'index', bodySpacing: 4 },
            legend: { position: 'bottom', labels: { boxWidth: 12 } },
            scales: { xAxes: [{ gridLines: { display: false } }], yAxes: [{ stacked: false }] }
        }
    });
    <?php endif; ?>

    // ── Weekend Attendance Chart ──
    var days = <?= json_encode($weekend_attendance["days"]) ?>;
    var employees_att = <?= json_encode($weekend_attendance["employee_att"]) ?>;
    var student_att   = <?= json_encode($weekend_attendance["student_att"]) ?>;

    <?php if (get_permission('weekend_attendance_inspection_chart', 'is_view')): ?>
    var ctx2 = document.getElementById('weekend_attendance').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: days,
            datasets: [
                { label: '<?= translate("employee") ?>', data: employees_att, borderColor: '#1D4ED8', backgroundColor: 'rgba(29,78,216,0.08)', fill: true, tension: 0.4, pointRadius: 4 },
                { label: '<?= translate("student") ?>',  data: student_att,   borderColor: '#10B981', backgroundColor: 'rgba(16,185,129,0.08)', fill: true, tension: 0.4, pointRadius: 4 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            legend: { position: 'bottom', labels: { boxWidth: 12 } },
            scales: { xAxes: [{ gridLines: { display: false } }] }
        }
    });
    <?php endif; ?>

    // ── Income vs Expense Donut ──
    <?php if (get_permission('monthly_income_vs_expense_chart', 'is_view')): ?>
    var cashbookchart = echarts.init(document.getElementById("cash_book_transaction"));
    cashbookchart.setOption({
        tooltip: { trigger: 'item', formatter: "{a}<br/>{b}: <?= $global_config['currency_symbol'] ?>{c} ({d}%)" },
        legend: { show: false },
        color: ["#BF0A30", "#002868"],
        series: [{
            name: 'Transaction', type: 'pie', radius: ['72%', '88%'],
            itemStyle: { normal: { label: { show: false }, labelLine: { show: false } } },
            data: <?= json_encode($income_vs_expense) ?>
        }]
    });
    <?php endif; ?>

    // ── Student Quantity Pie ──
    <?php if (get_permission('student_quantity_pie_chart', 'is_view')): ?>
    var studentchart = echarts.init(document.getElementById("student_strength"));
    studentchart.setOption({
        tooltip: { trigger: 'item', formatter: "{a}<br/>{b}: {c} ({d}%)" },
        color: ['#002868','#1D4ED8','#3B82F6','#93C5FD','#BF0A30','#F4A61F','#10B981','#6EE7B7'],
        legend: { type: 'scroll', x: 'center', y: 'bottom', itemWidth: 12 },
        series: [{
            name: 'Students', type: 'pie', radius: ['70%', '85%'], center: ['50%', '42%'],
            itemStyle: { normal: { label: { show: false }, labelLine: { show: false } } },
            data: <?= json_encode($student_by_class) ?>
        }]
    });
    <?php endif; ?>

    // Charts resize on sidebar toggle
    $(".sidebar-toggle").on("click", function() { echartsResize(); });
    $(window).on("resize", echartsResize);
    function echartsResize() {
        setTimeout(function() {
            if ($("#student_strength").length) studentchart.resize();
            if ($("#cash_book_transaction").length) cashbookchart.resize();
        }, 350);
    }

})(jQuery);
</script>