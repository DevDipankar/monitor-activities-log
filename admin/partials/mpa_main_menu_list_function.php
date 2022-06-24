<?php
    require_once  MPA_PATH . 'classes/class-mpa-list.php' ;
    global $wpdb;
	
    $table = new MPA_List_Log_Table();
    $table->prepare_items();

    ?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>

        <?php 
            if('edit' === $table->current_action()){
                
                //require_once(BDR_PLUGIN_DIR.'/admin-template/template-edit-birthday.php');
                
            } else if('view' === $table->current_action()){
                
                //require_once(BDR_PLUGIN_DIR.'/admin-templates/view-maintenance.php');
                
            }
            else{
        ?>
        <input type="hidden" class="date_day" value="<?php echo date('d');?>">
        <h2 class="main_head"><span><?php _e('Plugin Activities')?></span></h2> 
        <?php 
            if ('delete' === $table->current_action()) {
                echo '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'monitor_activities_log'), count(rest_sanitize_array($_REQUEST['id']))) . '</p></div>';
            }
        ?>
        <form id="form_mpa_list" method="GET" class="form_mpa_list">
            <input type="hidden" name="page" value="mpa_main_menu"/>
            <?php $table->display(); ?>
        </form>
    </div>

<!-- modal start -->
<div class="mpa_modal" id="mpa_plugin_preview_popup">
    <div class="box">
        <div class="close" close-modal>
            <span>&times;</span>
        </div>
        <h1 class="modal-list-title">Plugin Preview</h1>
        <h2 class="modal-list-description"><b></b></h2>
        <hr class="modal-separator" />
        <div class="modal-list-wrapper">
            <div class="iframecontent"></div>
        </div>
        
        <div class="sps_outer_popup_overlay">
            <div class="outer_popup_div">
                <div class="inline-loader"></div>
                <div class="inline-loader-text">Loading..</div>
            </div>
        </div>
    </div>
</div>
<!-- modal end -->

<script>
jQuery('.mpa_plugin_iframe').on('click', function(){
    jQuery('.iframecontent').html('');
    jQuery('.modal-list-description').html('');
    jQuery('.sps_outer_popup_overlay').show();
    jQuery('#mpa_plugin_preview_popup').addClass('active');
    var src = jQuery(this).attr('data-link');
    var html = '<iframe width="800px" height="550px" src="'+src+'" title="description"></iframe>';
    jQuery('.modal-list-description').html(jQuery(this).data('title'));
    jQuery('.iframecontent').html(html);
    setTimeout(() => {
        jQuery('.sps_outer_popup_overlay').hide();
    }, 3000);
});


jQuery('[close-modal]').on('click', function(){
        jQuery(this).parents('.mpa_modal').removeClass('active');
});

jQuery(function() {

var start = moment();
var end = moment();

// var start = catOBJ.start_event_date;
// var end = catOBJ.end_event_date;

function cb(start, end) {
   jQuery('#mpa_fil_date').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
   jQuery('#mpa_start_log_date').val(start.format('YYYY-MM-DD'));
   jQuery('#mpa_end_log_date').val(end.format('YYYY-MM-DD'));
   jQuery('#c_req_dt').show();
}

jQuery('#mpa_fil_date').daterangepicker({
   //startDate: start,
  // endDate: end,
   showCustomRangeLabel: true,
   autoUpdateInput: false,
   applyButtonClasses : 'cate_eve_date_apply btn-dark',
   ranges: {
       'Today': [moment(), moment()],
       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
       'This Month': [moment().startOf('month'), moment().endOf('month')],
       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
       'Last Three Months': [moment().subtract(3, 'month').startOf(moment()), moment() ],
       'Last Six Months': [moment().subtract(6, 'month').startOf(moment()), moment() ],
       'Last One Year': [moment().subtract(12, 'month').startOf(moment()), moment() ]
   }
}, cb);

//cb(start, end);

});

jQuery('#mpa_fil_date').on('keyup' , function(){
    if(jQuery(this).val() == ''){
        jQuery('#mpa_start_log_date').val('');
        jQuery('#mpa_end_log_date').val('');
    }
});

jQuery(document).on('change' , '#mpa_fil_date' , function(){
    
    if(jQuery(this).val() != ''){
        jQuery('#c_req_dt').prop('display' , 'inline-block');
    }
});

jQuery('#c_req_dt').on('click' , function(){
    jQuery('#mpa_fil_date').val('');
    jQuery('#mpa_start_log_date').val('');
    jQuery('#mpa_end_log_date').val('');
    jQuery('#c_req_dt').hide();
});

jQuery('.mpa_removefilter').on('click',function(){
    var $thisid = jQuery(this).attr('filkey');
    jQuery('#'+$thisid).val('');
    if($thisid === 'mpa_fil_date'){
        jQuery(document).find('#mpa_start_log_date').val('');
        jQuery(document).find('#mpa_end_log_date').val('');
    }
    jQuery('#form_mpa_list').submit();
});



</script>
<?php }?>
