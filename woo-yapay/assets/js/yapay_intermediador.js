/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function getSplits(payment_method,ta,ev){
    jQuery('#wc-yapay_intermediador-cc-card-installments').html("<option value='0'>--</option>");
    jQuery.ajax({
        url: ajaxurl, 
        type: 'POST',
        data: {
            'action': 'tc_get_splits', 
            'payment_method': payment_method,
            'ta': ta,
            'ev': ev,
            'price': jQuery('#wc-yapay_intermediador-cc-cart-total').val()
        },
        success: function( response ){
            jQuery('form.checkout').removeClass( 'processing' ).unblock();
            var json_response = JSON.parse(response);
            

            if (typeof json_response.splitting[0] == "object"){
                jQuery.each(json_response.splitting, function (i, splitData) {
                    
                    // if(parseFloat(splitData.split_rate) == 0){
                    if(splitData.split_rate == "0" ||  splitData.split_rate == "0.0"){
                        aditional_text = " sem juros";
                    }else{
                        aditional_text = " com juros";
                    }
                    jQuery('#wc-yapay_intermediador-cc-card-installments').append(jQuery('<option>', { 
                        value: splitData.split,
                        text : splitData.split + " x " + splitData.value_split + aditional_text
                    }));
                });
            }else{
                // if(parseFloat(json_response.splitting.split_rate) == 0){
                 if (json_response.splitting.split_rate == "0" || json_response.splitting.split_rate == "0.0") {
                    aditional_text = " sem juros";
                }else{
                    aditional_text = " com juros";
                }
                jQuery('#wc-yapay_intermediador-cc-card-installments').append(jQuery('<option>', {  
                    value: json_response.splitting.split,
                    text : json_response.splitting.split + " x " + json_response.splitting.value_split + aditional_text
                }));
            }
            jQuery('#wc-yapay_intermediador-cc-card-installments').removeAttr( "disabled");
        },
        beforeSend: function (xhr){
            jQuery('form.checkout').addClass( 'processing' ).block({
                    message: null,
                    overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                    }
            });
        }
    });
}

function identifyCreditCardTc(ccNumber){
    
    ccNumber = ccNumber.replace(/ /g, "");
    
    var eloRE = /^(636368|438935|504175|451416|(6362|5067|4576|4011)\d{2})\d{10}/;
    var visaRE = /^4\d{12,15}/;
    var masterRE = /^5[1-5]{1}\d{14}/;
    var amexRE = /^(34|37)\d{13}/;
    var discoverRE = /^(6011|622\d{1}|(64|65)\d{2})\d{12}/;
    var hiperRE = /^(60\d{2}|3841)\d{9,15}/;
    // var dinersRE = /^((30(1|5))|(36|38)\d{1})\d{11}/;
    var jcbRE = /^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}|5[0-9]{14}))$/;
    var auraRE = /^50\d{14}/; 
    
    document.getElementById('tcPaymentMethod').value = "";
    
    try { document.getElementById('tcPaymentFlag3').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag4').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag2').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag5').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag16').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag15').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag20').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag18').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag19').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag25').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}

    if(eloRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '16';
        document.getElementById('tcPaymentFlag16').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }else if(visaRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '3';
        document.getElementById('tcPaymentFlag3').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }else if(masterRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '4';
        document.getElementById('tcPaymentFlag4').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }else if(amexRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '5';
        document.getElementById('tcPaymentFlag5').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }else if(discoverRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '15';
        document.getElementById('tcPaymentFlag15').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }else if(hiperRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '20';
        document.getElementById('tcPaymentFlag20').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }else if(jcbRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '19';
        document.getElementById('tcPaymentFlag19').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }else if(auraRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '18';
        document.getElementById('tcPaymentFlag18').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }
        
    var maskCcNumber = ccNumber.substr(0,4);
    maskCcNumber += (ccNumber.length > 4)? " " + ccNumber.substr(4,4) : "";
    maskCcNumber += (ccNumber.length > 8)? " " + ccNumber.substr(8,4) : "";
    maskCcNumber += (ccNumber.length > 12)? " " + ccNumber.substr(12,4) : "";
    maskCcNumber += (ccNumber.length > 16)? " " + ccNumber.substr(16,4) : "";
    
    document.getElementById('wc-yapay_intermediador-cc-card-number').value =  maskCcNumber;

}



function selectCreditCardTc(idPaymentTC,ta,ev){
    getSplits(idPaymentTC,ta,ev);
    document.getElementById('tcPaymentMethod').value = "";
    
    try { document.getElementById('tcPaymentFlag3').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag4').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag2').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag5').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag16').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag15').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag20').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag18').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag19').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tcPaymentFlag25').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}

    document.getElementById('tcPaymentMethod').value = idPaymentTC;
    document.getElementById('tcPaymentFlag'+idPaymentTC).className = 'tcPaymentFlag tcPaymentFlagSelected';
}

function selectTefTc(idPaymentTC){
    
    document.getElementById('tctPaymentMethod').value = "";
    
    try { document.getElementById('tctPaymentFlag7').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tctPaymentFlag14').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tctPaymentFlag22').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tctPaymentFlag23').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}
    try { document.getElementById('tctPaymentFlag21').className = 'tcPaymentFlag';} catch(err) { console.debug(err.message);}

    document.getElementById('tctPaymentMethod').value = idPaymentTC;
    document.getElementById('tctPaymentFlag'+idPaymentTC).className = 'tcPaymentFlag tcPaymentFlagSelected';
}



function onkeyupYapay() {
    var input = document.getElementById("inputYapayRastreio");
    var inputURL = document.getElementById("inputYapayRastreioURL"); 

    if (input.value == "" && input.value.length == 0) {
        document.getElementById("btnYapayRastreio").disabled = true;        
    }
    else {
        document.getElementById("btnYapayRastreio").disabled = false;
    }
}


function sendRastreio(order_id, code, url){
    if (document.getElementById("inputYapayRastreio").value == "") {
        window.alert("O campo não pode estar em branco.");
        document.getElementById("inputYapayRastreio").focus();

    } else {
        if (window.confirm("Deseja enviar o código de rastreio para Yapay?")) {
            var text = document.getElementById("inputYapayRastreio").value; 
            // var li = "<li>" + text + "<a href='#'<span class='dashicons dashicons-dismiss' onclick='remove(this)'></span></a></li>";
            var li = "<li>" + text + "</li>";
            document.getElementById("list").innerHTML = li;

            jQuery.ajax({
                url: ajaxurl, 
                type: 'POST',
                data: {
                    'action': 'sendRastreioYapay',
                    'order_id': order_id,
                    'code': jQuery('#inputYapayRastreio').val(),
                    'url': jQuery('#inputYapayRastreioURL').val()
                },
                success: function( response ){
                },
            });
            document.getElementById("inputYapayRastreio").value = '';
            document.getElementById("inputYapayRastreioURL").value = ''; 
            document.getElementById("btnYapayRastreio").disabled = true;           
        }        

      }
}


// Remove Rastreio
// function remove(link) { 

// jQuery.ajax({
//         url: ajaxurl, 
//         type: 'POST',
//         data: {
//             'action': 'removeRastreioYapay'
//         },
//     });

//     link.parentNode.parentNode.removeChild(link.parentNode);
// }