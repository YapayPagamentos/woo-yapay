class Credit {
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

        owner.addEventListener('input', () => {
            let text = owner.value;
            owner.value = text.toUpperCase();
        })
    }


    handleCardBrand() {
        const card = document.querySelector("#wc-yapay_intermediador-cc-card-number");

        if (!card) return;

        card.addEventListener('input', () => {
            this.setCardBrand(card);
            this.getSplits();
        });
    }

    setCardBrand(card) {
        const number = card.value.replace(/\s/g, "");
        jQuery('.form-error-message-cc').hide();
        const paymentMethod = document.getElementById("tcPaymentMethod");
        const brands = this.getBrands();
        const brandElements = document.querySelectorAll('.tcPaymentMethod > img');
    
        paymentMethod.value = '';
    
        for (const brand of Object.keys(brands)) {
            const brandCode = this.getBrandCode(brand);
            const brandElement = document.getElementById(`tcPaymentFlag${brandCode}`);
    
            brandElements.forEach((element) => {
                element.classList.remove('tcPaymentFlagSelected');
            });
    
            if (!brandElement) return;
    
            if (brands[brand].test(number)) {
                paymentMethod.value = brandCode;
                brandElement.classList.add('tcPaymentFlagSelected');
                break;
            }
        }
    }
    

    getBrands() {
        return {
            elo: /(4011|431274|438935|451416|457393|4576|457631|457632|504175|627780|636297|636368|636369|(6503[1-3])|(6500(3[5-9]|4[0-9]|5[0-1]))|(6504(0[5-9]|1[0-9]|2[0-9]|3[0-9]))|(650(48[5-9]|49[0-9]|50[0-9]|51[1-9]|52[0-9]|53[0-7]))|(6505(4[0-9]|5[0-9]|6[0-9]|7[0-9]|8[0-9]|9[0-8]))|(6507(0[0-9]|1[0-8]))|(6507(2[0-7]))|(650(90[1-9]|91[0-9]|920))|(6516(5[2-9]|6[0-9]|7[0-9]))|(6550(0[0-9]|1[1-9]))|(6550(2[1-9]|3[0-9]|4[0-9]|5[0-8]))|(506(699|77[0-8]|7[1-6][0-9))|(509([0-9][0-9][0-9])))/,
            master: /^((5[1-5][0-9]{14})$|^(2(2(?=([2-9]{1}[1-9]{1}))|7(?=[0-2]{1}0)|[3-6](?=[0-9])))[0-9]{14})$/,
            amex: /^3[47][0-9]{13}$/,
            hiper: /^(606282\d{10}(\d{3})?)|^(3841\d{15})$/,
            hiperItau: /^(637095)\d{0,10}$/,
            jcb: /^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}|5[0-9]{14}))$/,
            visa: /^(?!504175|506699|5067|509|6500|6501|4011(78|79)|43(1274|8935)|45(1416|7393|763(1|2))|50(4175|6699|67[0-6][0-9]|677[0-8]|9[0-8][0-9]{2}|99[0-8][0-9]|999[0-9])|627780|63(6297|6368|6369)|65(0(0(3([1-3]|[5-9])|4([0-9])|5[0-1])|4(0[5-9]|[1-3][0-9]|8[5-9]|9[0-9])|5([0-2][0-9]|3[0-8]|4[1-9]|[5-8][0-9]|9[0-8])|7(0[0-9]|1[0-8]|2[0-7])|9(0[1-9]|[1-6][0-9]|7[0-8]))|16(5[2-9]|[6-7][0-9])|50(0[0-9]|1[0-9]|2[1-9]|[3-4][0-9]|5[0-8])))4[0-9]{12}(?:[0-9]{3})?$/
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
            case 'hiper':
                code = 20;
                break;
            case 'hiperItau':
                code = 25;
                break;
            case 'jcb':
                code = 19;
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
        jQuery('.form-error-message-cc').hide();

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
                jQuery("#wc-yapay_intermediador-cc-card-installments").empty()
                this.renderSplits(data);
                jQuery("form.checkout").removeClass("processing").unblock();
            })
            .catch((error) => {
                jQuery('.form-error-message-cc').show();
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


(function ($) {
    $(document).on("ready updated_checkout", () => {
        new Credit;
    });
}(jQuery));