=== WooCommerce Yapay Intermediador ===
Contributors: Integração Yapay
Tags: woocommerce, yapay, intermediador, yapay intermediador, payment
Requires at least: 3.5
Tested up to: 5.2.2
Stable tag: 0.5.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

O [Yapay Intermediador](https://www.yapay.com.br/) é um facilitador de pagamento que oferece benefícios aos lojistas e aos compradores.

== Description ==

### Plugin de Integração Yapay Intermediador - WooCommerce ###

Yapay Intermediador is a payment facilitator that offers benefits to merchants and buyers. Focused on practicality and conversion, enables virtual stores offer many payments possibilites, no need contracts with financial operators. 
 
This module is able to do the integration with the Payment API of the Yapay Intermediador. In this case, the consumer isn't redirect to Yapay Intermediador environment. Every steps of the payment are made in the WooCommerce Checkout. 

### Descrição em Português: ###
 
O Yapay Intermediador é um facilitador de pagamento que oferece benefícios aos lojistas e aos compradores. Focado em praticidade e conversão, possibilita que as lojas virtuais ofereçam diversas formas de pagamento, sem burocracia ou necessidade de contrato com operadoras financeiras. 
 
Este módulo é capaz de fazer a integração com a API do pagamento do Yapay Intermediador. Neste caso, o consumidor não é redirecionar para ambiente Yapay Intermediador. Todos os passos de o pagamento são feito no Checkout WooCommerce.


= Compatibilidade =

O Yapay Intermediador é compatível desde a versão 2.x até 3.x do WooCommerce.


= Dependência =

Este plugin depende dos campos do plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/), desta forma é possível enviar os campos de "CPF", "número do endereço" e "bairro" (para o Checkout Transparente é obrigatório o uso deste plugin).


== Installation ==

* Baixe o plugin e coloque os arquivos do plugin para a pasta wp-content/plugins. Ou você pode realizar a instalação diretamente pelo instalador de plugins do WordPress.
* É necessário ativar o plugin.

Para mais informações sobre a instalação e configuração do módulo acesse [Instalação WooCommerce Módulo Yapay Intermediador](http://dev.yapay.com.br/intermediador/modulos-integracao-intermediador/#woocommerce).

= Dúvidas =

Para dúvidas envie um e-mail para nosso time de Integração: integracao@yapay.com.br

== Screenshots ==

1. Página dos plugins no Wordpress, onde clicar para configurar as formas de pagamento
2. Página de configuração do plugin

== Changelog ==

= 0.1.0 = 20/11/2017

* Versão incial do plugin Yapay Intermediador.

= 0.2.0 = 01/02/2018

Correção bug combo box de parcelamento. 


= 0.2.1 = 20/12/2018

Correção bug combo box de parcelamento devido ao retorno da API de Parcelamento .


= 0.2.2 = 14/02/2019

Ajuste no frontend do campo de Data de Vencimento do Cartão de crédito.

= 0.2.3 = 19/02/2019

Ajuste no frontend do campo de Data de Vencimento do Cartão de crédito.

= 0.2.4 = 19/02/2019

Retirada botão Configurar Yapay.

= 0.2.5 = 11/03/2019

Adicionado fingerprint.

= 0.2.6 = 12/03/2019

Novos ajustes fingerprint.

= 0.2.7 = 12/03/2019

Ajuste no parâmetro de criação da transação customer_ip.

= 0.2.7 = 26/03/2019

Ajuste no layout quebrado do checkout.

= 0.4.1 = 21/06/2019

Adicionada a opção de enviar para a Yapay o código de rastreio do pedido. Essa informaço é importante para liberação do saque do vendedor.

Corrigido a variável de desconto enviada na transação para a Yapay.

= 0.4.2 = 17/07/2019

Adicionada a opção de realiar compras com CNPJ.

Adicionado o campo CPF nas formas de pagamento.

= 0.4.3 = 19/07/2019

Adicionado type no input CPF.

= 0.4.4 = 25/07/2019

Bug fix CPF não é valido no checkout.

= 0.4.5 = 15/08/2019

Bug fix CPF nÃ£o Ã© valido no checkout, somente CNPJ.

Ajuste front do input CPF

Ajuste variavel discount

= 0.4.8 = 16/08/2019

Bug fix get_item_subtotal desconto produto.

= 0.4.9 = 16/09/2019

Será exigido o campo CPF na forma de pagamento somente quando o tipo de pessoa for igual a PESSOA JURÍDICA

Atualização REGEX dos BIN de Cartão de Crédito.

Adicionada mensagem do retorno do pagamento na Tela de Sucesso do pedido, agora vai informar qual foi a mensagem da Adquirente.

= 0.5.0 = 19/09/2019

Correção do bug de CPF em branco, quando estava configurado apenas vendas para CPF

= 0.5.1 = 29/09/2019

Ajuste no javascript


= 0.5.2 = 09/10/2019

Ajuste no css das bandeiras do cartão de crédito.


= 0.5.3 = 24/02/2020

Alterar página de finalização de compra para order-received.

Ajustar input do ano do cartão de crédito para dois digitos, ficando MM / YY.

Ajuste no checkout, não será permitido digitar letras no campo de NÚMERO DO CARTÃO.

Ajuste no checkout, não será permitido digitar números no campo de NOME DO CARTÃO.


= 0.5.4 = 24/02/2020

Correção Envio de Código de Rastreio do pedido


= 0.5.5 = 29/02/2020

Bug fix função receitp_page

= 0.5.6 = 04/03/2020

Bug fix URL Boleto.

Correção das imagens das Bandeiras selecionadas na opção de cartão de crédito.

= 0.5.7 = 13/03/2020

Correção bug de envio de Código de Rastreio para Yapay com PHP >7.x 