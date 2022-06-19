jQuery('#mpa_start_log_date').on('change' , function(){
    alert('');
    if(jQuery(this).val() != ''){
        jQuery('#c_req_dt').prop('display' , 'inline-block');
    }
});