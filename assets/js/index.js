/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function onkeyupYapay() {
  var input = document.getElementById("inputYapayRastreio");
  var inputURL = document.getElementById("inputYapayRastreioURL");

  if (input.value == "" && input.value.length == 0) {
    document.getElementById("btnYapayRastreio").disabled = true;
  } else {
    document.getElementById("btnYapayRastreio").disabled = false;
  }
}

function sendRastreio(order_id, code, url) {
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
        type: "POST",
        dataType: "json",
        data: {
          action: "sendRastreioYapay",
          order_id: order_id,
          code: jQuery("#inputYapayRastreio").val(),
          url: jQuery("#inputYapayRastreioURL").val(),
        },
        success: function (response) { },
      });
      document.getElementById("inputYapayRastreio").value = "";
      document.getElementById("inputYapayRastreioURL").value = "";
      document.getElementById("btnYapayRastreio").disabled = true;
    }
  }
}

function inputCPFYapay() {
  if (
    document.getElementById("billing_persontype") == null &&
    document.getElementById("billing_cnpj") != null
  ) {
    if (document.getElementById("cpf_yapayB") != null) {
      document.getElementById("cpf_yapayB").style.display = "block";
    }
    if (document.getElementById("cpf_yapayT") != null) {
      document.getElementById("cpf_yapayT").style.display = "block";
    }
    if (document.getElementById("cpf_yapayC") != null) {
      document.getElementById("cpf_yapayC").style.display = "block";
    }
    if (document.getElementById("cpf_yapayP") != null) {
      document.getElementById("cpf_yapayP").style.display = "block";
    }
  } else {
    if (
      document.getElementById("billing_persontype") == null &&
      document.getElementById("billing_cpf") != null
    ) {
      if (document.getElementById("cpf_yapayB") != null) {
        document.getElementById("cpf_yapayB").style.display = "none";
      }
      if (document.getElementById("cpf_yapayT") != null) {
        document.getElementById("cpf_yapayT").style.display = "none";
      }
      if (document.getElementById("cpf_yapayC") != null) {
        document.getElementById("cpf_yapayC").style.display = "none";
      }
      if (document.getElementById("cpf_yapayP") != null) {
        document.getElementById("cpf_yapayP").style.display = "none";
      }
    }
  }

  if (
    document.getElementById("billing_persontype") != null &&
    document.getElementById("billing_cpf") != null &&
    document.getElementById("billing_cpf") != null
  ) {
    personType = document.getElementById("billing_persontype").value;

    if (personType == 2) {
      if (document.getElementById("cpf_yapayB") != null) {
        document.getElementById("cpf_yapayB").style.display = "block";
      }
      if (document.getElementById("cpf_yapayT") != null) {
        document.getElementById("cpf_yapayT").style.display = "block";
      }
      if (document.getElementById("cpf_yapayC") != null) {
        document.getElementById("cpf_yapayC").style.display = "block";
      }
      if (document.getElementById("cpf_yapayP") != null) {
        document.getElementById("cpf_yapayP").style.display = "block";
      }
    } else {
      if (document.getElementById("cpf_yapayB") != null) {
        document.getElementById("cpf_yapayB").style.display = "none";
      }
      if (document.getElementById("cpf_yapayT") != null) {
        document.getElementById("cpf_yapayT").style.display = "none";
      }
      if (document.getElementById("cpf_yapayC") != null) {
        document.getElementById("cpf_yapayC").style.display = "none";
      }
      if (document.getElementById("cpf_yapayP") != null) {
        document.getElementById("cpf_yapayP").style.display = "none";
      }
    }
  }
}
setInterval(inputCPFYapay, 100);
jQuery(document).ready(function () {
  var interval;

  jQuery(document).on("change", "#billing_persontype", function () {
    if (this.value == "2") {
      interval = setInterval(inputCPFYapay, 100);
    } else {
      clearInterval(interval);
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".copiaCola");
  buttons.forEach((button) => {
    button.addEventListener('click', (event) => {
      event.preventDefault();
      const input = button.previousElementSibling;
      if (input) {
        const textToCopy = input.value;
        const textarea = document.createElement('textarea');
        textarea.value = textToCopy;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);

        const originalText = button.innerHTML;
        button.innerHTML = 'Copiado!';
        setTimeout(() => {
          button.innerHTML = originalText;
        }, 1000);
      }
    });
  });
});