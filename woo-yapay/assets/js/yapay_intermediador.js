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

function generateRegexRange(start, end){
    var regexResult = '';
    var or = '|';
    start = parseInt(start, 10);
    end = parseInt(end, 10);

    if (!(start >= 0 && end > 0)) {
        return false;
    }

    for (var i = start; i <= end; i++) {
        regexResult += '(' + i + ')';
        if (i < end) {
            regexResult += or;
        }
    }

    return regexResult;
}

function getEloPattern(ccNumber){
    var regexPattern = '^(' + this.generateRegexRange(457631, 457632) + '|';
    regexPattern += this.generateRegexRange(506699, 506778) + '|';
    regexPattern += this.generateRegexRange(509000, 509999) + '|';
    regexPattern += this.generateRegexRange(650031, 650033) + '|';
    regexPattern += this.generateRegexRange(650035, 650051) + '|';
    regexPattern += this.generateRegexRange(650405, 650439) + '|';
    regexPattern += this.generateRegexRange(650485, 650538) + '|';
    regexPattern += this.generateRegexRange(650541, 650598) + '|';
    regexPattern += this.generateRegexRange(650700, 650718) + '|';
    regexPattern += this.generateRegexRange(650720, 650727) + '|';
    regexPattern += this.generateRegexRange(650901, 650978) + '|';
    regexPattern += this.generateRegexRange(651652, 651679) + '|';
    regexPattern += this.generateRegexRange(655000, 655019) + '|';
    regexPattern += this.generateRegexRange(655021, 655058) + ')';
    return new RegExp(regexPattern);
}


function identifyCreditCardTc(ccNumber){
    
    ccNumber = ccNumber.replace(/ /g, "");
    
    regexElo = getEloPattern(ccNumber);

    var eloRE = /^((((457393)|(431274)|(627780)|(636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})$/;
    var elo2RE = /^(4011(78|79)|43(1274|8935)|45(1416|7393|763(1|2))|50(4175|6699|67[0-7][0-9]|9000)|50(9[0-9][0-9][0-9])|627780|63(6297|6368)|650(03([^4])|04([0-9])|05(0|1)|05([7-9])|06([0-9])|07([0-9])|08([0-9])|4([0-3][0-9]|8[5-9]|9[0-9])|5([0-9][0-9]|3[0-8])|9([0-6][0-9]|7[0-8])|7([0-2][0-9])|541|700|720|727|901)|65165([2-9])|6516([6-7][0-9])|65500([0-9])|6550([0-5][0-9])|655021|65505([6-7])|6516([8-9][0-9])|65170([0-4]))$/;
    var elo3RE = regexElo;
    var visaRE = /^4[0-9]{12}(?:[0-9]{3})?$/;
    var masterRE = /^((5[1-5][0-9]{14})$|^(2(2(?=([2-9]{1}[1-9]{1}))|7(?=[0-2]{1}0)|[3-6](?=[0-9])))[0-9]{14})$/;
    var amexRE = /^3[47][0-9]{13}$/;
    var discoverRE = /^6(?:011|5[0-9]{2})[0-9]{12}$/;    
    var hiperRE = /^(606282\d{10}(\d{3})?)|^(3841\d{15})$/;
    var hiperItauRE = /^(637095)\d{0,10}$/;
    var dinersRE = /^((30(1|5))|(36|38)\d{1})\d{11}/;
    var jcbRE = /^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}|5[0-9]{14}))$/;
    var auraRE = /^50[0-9]{17}$/; 
    
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
    }if(elo2RE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '16';
        document.getElementById('tcPaymentFlag16').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }if(elo3RE.test(ccNumber)){
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
    }else if(hiperItauRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '25';
        document.getElementById('tcPaymentFlag25').className = 'tcPaymentFlag tcPaymentFlagSelected';
    }else if(dinersRE.test(ccNumber)){
        document.getElementById('tcPaymentMethod').value = '2';
        document.getElementById('tcPaymentFlag2').className = 'tcPaymentFlag tcPaymentFlagSelected';
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
    inputCPFYapay();


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
                url: location.origin + ajaxurl, 
                type: 'POST',
                dataType: 'json',
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
};

function somenteNumeros(num) {
    var er = /[^0-9]/;
    er.lastIndex = 0;
    var campo = num;
    if (er.test(campo.value)) {
      campo.value = "";
    }
};


function inputCPFYapay() {
    if ((document.getElementById('billing_persontype') == null) && (document.getElementById('billing_cnpj') != null ) ) {
        if (document.getElementById('cpf_yapayB') != null) {
            document.getElementById('cpf_yapayB').style.display = 'block';
        }
        if (document.getElementById('cpf_yapayT') != null) {
            document.getElementById('cpf_yapayT').style.display = 'block';
        }
        if (document.getElementById('cpf_yapayC') != null) {
            document.getElementById('cpf_yapayC').style.display = 'block';
        }
    } else {
        if ((document.getElementById('billing_persontype') == null) && (document.getElementById('billing_cpf') != null ) ) {
            if (document.getElementById('cpf_yapayB') != null) {
                document.getElementById('cpf_yapayB').style.display = 'none';
            }
            if (document.getElementById('cpf_yapayT') != null) {
                document.getElementById('cpf_yapayT').style.display = 'none';
            }
            if (document.getElementById('cpf_yapayC') != null) {
                document.getElementById('cpf_yapayC').style.display = 'none';
            }
        }
    }

    if ((document.getElementById('billing_persontype') != null) && (document.getElementById('billing_cpf') != null )
        && (document.getElementById('billing_cpf') != null )) {
            personType = document.getElementById('billing_persontype').value;
    
            if (personType == 2) {
                if (document.getElementById('cpf_yapayB') != null) {
                    document.getElementById('cpf_yapayB').style.display = 'block';
                }
                if (document.getElementById('cpf_yapayT') != null) {
                    document.getElementById('cpf_yapayT').style.display = 'block';
                }
                if (document.getElementById('cpf_yapayC') != null) {
                    document.getElementById('cpf_yapayC').style.display = 'block';
                }
            } else {
                if (document.getElementById('cpf_yapayB') != null) {
                    document.getElementById('cpf_yapayB').style.display = 'none';
                }
                if (document.getElementById('cpf_yapayT') != null) {
                    document.getElementById('cpf_yapayT').style.display = 'none';
                }
                if (document.getElementById('cpf_yapayC') != null) {
                    document.getElementById('cpf_yapayC').style.display = 'none';
                } 
              }
    }

}
setInterval(inputCPFYapay, 100);
jQuery(document).ready(function() {
    var interval;

    jQuery(document).on('change', '#billing_persontype', function(){
        if(this.value == '2'){
            interval = setInterval(inputCPFYapay, 100);

        } else {
            clearInterval(interval);
  
        }
    });     

});


function apenasLetrasCartao(e, t) {
    try {
        if (window.event && window.event.keyCode) {
            var charCode = window.event.keyCode;
        } else if (e) {
            var charCode = e.which;
        } else {
            return true;
        }
        if (
            (charCode > 64 && charCode < 91) ||
            (charCode > 96 && charCode < 123) ||
            (charCode > 191 && charCode <= 255) ||
            (charCode == 32) // letras   com acentos
        ) {
            return true;

        } else {
            return false;
        }
    } catch (err) {
        console.log(err.Description);
    }
}


function somenteNumerosCartao(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46 || charCode > 31 && (charCode < 48 || charCode > 57)){
        evt.preventDefault();
        return false;
    }
    return true;
}
