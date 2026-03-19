var gnpub_pro_checkbox1=document.getElementById('gnpub_enable_google_revenue_manager');
if(gnpub_pro_checkbox1)
{
  check_for_copy_checkbox_rev();
  gnpub_pro_checkbox1.addEventListener('click', event => {
    check_for_copy_checkbox_rev();
  });
}

function check_for_copy_checkbox_rev()
{
  if(gnpub_pro_checkbox1)
  {
  if(gnpub_pro_checkbox1.checked) {
    document.getElementById('gnpub_val_tr_revenue').style.display="table-row";
    document.getElementById('gnpub_val_tr_revenue_snippname').style.display="table-row";
}
else{
  document.getElementById('gnpub_val_tr_revenue').style.display="none";
  document.getElementById('gnpub_val_tr_revenue_snippname').style.display="none";
}
  }
}

jQuery('.gnpub-show-upgrd-toprem-btn-fch').click(function(){
    jQuery(this).closest('td').find('a.gn-publisher-pro-btn, span').toggle();
});

function gn_copy(id) {
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    var tooltip = document.getElementById(id+"-tooltip");
    tooltip.innerHTML = "URL Copied";
  }
  
  function gn_out(id) {
    var tooltip = document.getElementById(id+"-tooltip");
    tooltip.innerHTML = "Copy URL";
  }

  function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("gn-tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("gn-tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
    
    /**
     * Set tab param to current url
     * @since 1.5.16
     * */
    var url = new URL(window.location.href);
    url.searchParams.set('tab', tabName);
    window.history.replaceState(null, null, url.toString());

  }
  
  // Get the element with id="defaultOpen" and click on it
  //document.getElementById("defaultOpen").click();
  jQuery('.gn-publisher-pro-btn').click(function(){
    jQuery('.gn-tablinks.gn-license-btn').addClass('active');
  });

  jQuery(document).ready(function($) {

    jQuery(".gn-service-card.first").on("click", function(evt) {
      if(!$(evt.target).is('.gn-service-card-right.first a')) {
        window.open('https://gnpublisher.com/services/google-news-setup-audit-service', '_blank');
      }    
    });
    jQuery(".gn-service-card.second").on("click", function(evt) {     
      if(!$(evt.target).is('.gn-service-card-right a')) {
        window.open('https://gnpublisher.com/services/dedicated-developer-for-website-search-console-maintenance-service/', '_blank');
      }  
          
    });
    jQuery(".gn-service-card.third").on("click", function(evt) {  
       
      if(!$(evt.target).is('.gn-service-card-right a')) {
        window.open('https://gnpublisher.com/services/search-console-maintenance-service/', '_blank');
      }     
    });

    var btn_click=false;
    $('.gn-service-card-left,.gn-service-heading,.gn-service-card-right p  ').click(function(){
      var url=$(this).parent().parent().data('url');
      if(url)
      {
          window.open(url, '_blank');
      }
  });  
 
 
  $(".gn-send-query").on("click", function(e){
    e.preventDefault();   
    var message     = $("#gn_query_message").val();  
    var email       = $("#gn_query_email").val();  
    
    if($.trim(message) !='' && $.trim(email) !='' && gnIsEmail(email) == true){
     $.ajax({
                    type: "POST",    
                    url:ajaxurl,                    
                    dataType: "json",
                    data:{action:"gn_send_query_message",message:message,email:email,gn_security_nonce:gn_script_vars.nonce},
                    success:function(response){                       
                      if(response['status'] =='t'){
                        $(".gn-query-success").show();
                        $(".gn-query-error").hide();
                      }else{                                  
                        $(".gn-query-success").hide();  
                        $(".gn-query-error").show();
                      }
                    },
                    error: function(response){                    
                    console.log(response);
                    }
                    });   
    }else{
        
        if($.trim(message) =='' && $.trim(email) ==''){
            alert('Please enter the message, email and select customer type');
        }else{
        
        if($.trim(message) == ''){
            alert('Please enter the message');
        }
        if($.trim(email) == ''){
            alert('Please enter the email');
        }
        if(gnIsEmail(email) == false){
            alert('Please enter a valid email');
        }
            
        }
        
    }                        

});

  $(document).on('change', '#gnpub_enable_google_news_follow', function(e){
    if( $(this).is(':checked') ) {
      $('.gnpub-google-news-button-opts').show();
    }else{
      $('.gnpub-google-news-button-opts').hide();
    }
  });

  $(document).on('keyup', '#gnpub_enable_google_news_follow_text', function(e){
    let newsText = $(this).val();
    $('#gnpub-gnfollow-shortcode-follow-text').text(newsText);
  });

  $(document).on('keyup', '#gnpub_enable_google_news_follow_link', function(e){
    let newsLink = $(this).val();
    if ( newsLink.length == 0 ){
      newsLink   = '#';  
    }
    $('#gnpub-gnfollow-shortcode-wrapper a').attr('href', newsLink);
  });

  $(document).on('click', '.gnpub_compatibility_options, .gnpub_compatibility_options_disable', function(e){
    
    let isChecked = false;
    let id = $(this).attr('data-id');

    if ( $('#'+id).is(':checked') ) {
      isChecked = true;  
      $('#'+id).prop('checked', true);
    }

    let currentId = $(this).prop('id');
    if ( currentId === id + '_link' ) {
        $(this).text('Please wait...');
    }

    $.ajax({
      type: "POST",    
      url:ajaxurl,                    
      dataType: "json",
      data:{
        action:"gnpub_save_com_options_ajax",
        option_name:id,
        option_value:isChecked,
        security_nonce:gn_script_vars.nonce
      },
      success:function(response){                       
        let inputId = '#' + id;
        if ( isChecked === true ) {
          $(inputId).attr('type', 'hidden');
          $(inputId + '_label').html($(inputId + '_label').attr('data-checked'));
          $(inputId + '_config').show();
          $(inputId + '_link').show();
          $(inputId + '_link').text('Disable');
        }else{
          $(inputId).show();
          $(inputId).attr('type', 'checkbox');
          $(inputId).removeAttr('checked');
          $(inputId + '_label').html($(inputId + '_label').attr('data-unchecked'));
          $(inputId + '_config').hide();
          $(inputId + '_link').hide();
          $(this).text('Disable');
          
        }
      },
      error: function(response){                    
      console.log(response);
      }
    }); 

  });

  });

  function gnIsEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

// Hide upgrade to premium button after 3 seconds
// @since 1.5.12
function gnpubDisplayProBtn() {
  let proBtn = document.getElementsByClassName('gn-publisher-pro-btn-f-setup')[0];
  if(proBtn){
    proBtn.style.display = "inline";

    setTimeout(() => {
      proBtn.style.display = "none";  
    }, 3000);
  }
}

function gnpub_model_init(model_id=null, button_id=null,close_id=null)
{
    if(!model_id || !button_id || !close_id)
    {
      return '';
    }
    console.log(button_id);
  // Get the modal
  var modal = document.getElementById(model_id);

  // Get the button that opens the modal
  var btn = document.getElementById(button_id);

  // Get the <span> element that closes the modal
  var span = document.getElementById(close_id);

  // When the user clicks the button, open the modal
  if(btn) {
  btn.onclick = function() {
    modal.style.display = "block";
  }}
  if(span) {
  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }}

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
}
gnpub_model_init('gnpub_modal_yandex_turbo','gnpub_yandex_turbo_config','gnpub_yandex_turbo_close');