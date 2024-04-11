class Credit
{
    constructor() {
        if (!document.querySelector(".wc_payment_method .payment_method_wc_yapay_intermediador_cc"))
        return;

        if (typeof IMask !== 'function') return;
    
        this.setMasksEvents();
        this.setBrandEvents();
    }

    setBrandEvents() {
        this.handleCardBrand();
    }

    setMasksEvents() {
        this.setCardMask();
        this.setDateMask();
        this.setCvvMask();
        this.setOwnerMask();
        this.setCpfMask();
    }
    
    setCardMask() {
        const card = document.querySelector("#wc-yapay_intermediador-cc-card-number");

        if (card) {
            var mask = {
            mask: '0000 0000 0000 0000'
            };
            IMask(card, mask);
        }
    }
    
    setDateMask() {
        const date = document.querySelector("#wc-yapay_intermediador-cc-card-expiry");
    
        if (date) {
          var mask = {
            mask: '00/00'
          };
          IMask(date, mask);
        }
    }

    setCpfMask() {
        const fields = document.querySelectorAll(".yapay_cpf");
        fields.forEach(element => {
            var mask = {
                mask: '000.000.000-00'
            };

            IMask(element, mask);
        });
    }
    
    setCvvMask() {
        const cod = document.querySelector("#wc-yapay_intermediador-cc-card-cvc");
    
        if (cod) {
          var mask = {
            mask: '0000'
          };
          IMask(cod, mask);
        }
    }
    
    setOwnerMask() {
        const owner = document.querySelector("#wc-yapay_intermediador-cc-card-holder-name");
    
        if (owner) {
          var mask = {
            mask: /^[A-Za-z\s]*$/
          };
          IMask(owner, mask);
        }

        owner.addEventListener('change', () => {
            let text = owner.value;
            owner.value = text.toUpperCase();
        })
    }

    
    handleCardBrand() {
        const card = document.querySelector("#wc-yapay_intermediador-cc-card-number");

        if (!card) return;

        card.addEventListener('keyup', () => {
            this.setCardBrand(card);
        });

        card.addEventListener('change', () => {
            this.setCardBrand(card);
            this.getSplits();
        });
    }

    setCardBrand(card) {
        const number = card.value.replace(/\s/g, "");

        const paymentMethod = document.getElementById("tcPaymentMethod");
        const brands = this.getBrands();

        paymentMethod.value = '';

        for (const key in brands) {
            const brandCode = this.getBrandCode(key);
            const brandElement = document.getElementById(`tcPaymentFlag${brandCode}`);

            if (!brandElement) return;

            if (brands[key].test(number)) {
                paymentMethod.value = brandCode;
                brandElement.classList.add('tcPaymentFlagSelected');
            } else {
                brandElement.classList.remove('tcPaymentFlagSelected');
            }
        }
    }

    getBrands() {
        return {
            elo: /^((((636368)|(438935)|(504175)|(451416)|(636297))d{0,10})|((5067)|(4576)|(4011))d{0,12})/,
            visa: /^4[0-9]{12}(?:[0-9]{3})?$/,
            master: /^((5[1-5][0-9]{14})$|^(2(2(?=([2-9]{1}[1-9]{1}))|7(?=[0-2]{1}0)|[3-6](?=[0-9])))[0-9]{14})$/,
            amex: /^3[47][0-9]{13}$/,
            discover: /^6(?:011|5[0-9]{2})[0-9]{12}$/,
            hiper: /^(606282\d{10}(\d{3})?)|^(3841\d{15})$/,
            hiperItau: /^(637095)\d{0,10}$/,
            diners: /^((30(1|5))|(36|38)\d{1})\d{11}/,
            jcb: /^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}|5[0-9]{14}))$/,
            aura: /^50[0-9]{17}$/
        };
    }

    getBrandCode(brand) {
        let code;

        switch (brand) {
        case 'elo':
            code = 16;
        break;
        case 'visa':
            code = 3;
        break;
        case 'master':
            code = 4;
        break;
        case 'amex':
            code = 5;
        break;
        case 'discover':
            code = 15;
        break;
        case 'hiper':
            code = 20;
        break;
        case 'hiperItau':
            code = 25;
        break;
        case 'diners':
            code = 2;
        break;
        case 'jcb':
            code = 19;
        break;
        case 'aura':
            code = 18;
        break;
        default:
            code = 0;
        break;
        }

        return code;
    }

    getSplits() {
        const card = document.querySelector("#wc-yapay_intermediador-cc-card-number");
        const payment_method = document.getElementById("tcPaymentMethod");

        this.setCardBrand(card);

        if (!card || !payment_method.value) return;

        jQuery("#wc-yapay_intermediador-cc-card-installments").html(
            "<option value='0'>--</option>"
        );

        jQuery("form.checkout")
            .addClass("processing")
            .block({
            message: null,
            overlayCSS: {
                background: "#fff",
                opacity: 0.6,
            },
        });

        const data = new FormData();

        data.append('action', 'tc_get_splits');
        data.append('payment_method', payment_method.value);
        data.append('price', jQuery("#wc-yapay_intermediador-cc-cart-total").val());

        fetch(ajaxurl, {
            method: "POST",
            body: data
        })
        .then((response) => response.json())
        .then((data) => {
            this.renderSplits(data);
            jQuery("form.checkout").removeClass("processing").unblock();
        });
    }

    renderSplits(response) {
        let aditional_text;

        if (typeof response.splitting[0] == "object") {
            jQuery.each(response.splitting, function (i, splitData) {
                const show_fee = response.fees;
                const formatter = new Intl.NumberFormat("pt-BR", {
                    style: "currency",
                    currency: "BRL",
                });

                let rate = parseFloat(splitData.split_rate);
                let formatterPrice = formatter.format(splitData.value_transaction);

                switch (show_fee) {
                    case "show_fee_price":
                        aditional_text = ` (${formatterPrice})`;
                        break;
                    case "show_fee_text":
                        aditional_text =
                        rate === 0 ? " sem juros" : (aditional_text = " com juros");
                        break;
                    case "show_fee_text_price":
                        aditional_text =
                        rate === 0
                            ? ` (${formatterPrice}) sem juros`
                            : (aditional_text = ` (${formatterPrice}) com juros`);
                        break;
                    default:
                        aditional_text = "";
                        break;
                }
                jQuery("#wc-yapay_intermediador-cc-card-installments").append(
                    jQuery("<option>", {
                        value: splitData.split,
                        text: splitData.split + " x " + formatter.format(splitData.value_split) + aditional_text,
                    })
                );
            });
        } else {
            let splitting = response.splitting;
            let split_rate = parseFloat(splitting.split_rate);
            aditional_text = split_rate == 0 ? ' sem juros' : ' com juros';

            jQuery("#wc-yapay_intermediador-cc-card-installments").append(
                jQuery("<option>", {
                    value: splitting.split,
                    text: splitting.split + " x " + splitting.value_split + aditional_text,
                })
            );
        }

        jQuery("#wc-yapay_intermediador-cc-card-installments").removeAttr("disabled");
    }
}


(function($) {
    $(document).on("ready updated_checkout", () => {
        new Credit;
    });
}(jQuery));