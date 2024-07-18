=== WooCommerce Vindi Pagamento ===
Contributors: Integração Vindi, aguiart0, apiki
Tags: woocommerce, vindi, intermediador, Vindi Pagamento, payment
Requires at least: 3.5
Tested up to: 6.4
Stable tag: 0.7.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

O [Vindi Pagamento](https://vindi.com.br/formas-de-pagamentos/) é um facilitador de pagamento que oferece benefícios aos lojistas e aos compradores.

== Description ==

### Plugin de Integração Vindi Pagamento - WooCommerce ###

Vindi Pagamento is a payment facilitator that offers benefits to merchants and buyers. Focused on practicality and conversion, enables virtual stores offer many payments possibilites, no need contracts with financial operators.

This module is able to do the integration with the Payment API of the Vindi Pagamento. In this case, the consumer isn't redirect to Vindi Pagamento environment. Every steps of the payment are made in the WooCommerce Checkout.

### Descrição em Português: ###

O Vindi Pagamento é um facilitador de pagamento que oferece benefícios aos lojistas e aos compradores. Focado em praticidade e conversão, possibilita que as lojas virtuais ofereçam diversas formas de pagamento, sem burocracia ou necessidade de contrato com operadoras financeiras.

Este módulo é capaz de fazer a integração com a API do pagamento do Vindi Pagamento. Neste caso, o consumidor não é redirecionar para ambiente Vindi Pagamento. Todos os passos de o pagamento são feito no Checkout WooCommerce.


= Compatibilidade =

O Vindi Pagamento é compatível desde a versão 2.x até 3.x do WooCommerce.


= Dependência =

Este plugin depende dos campos do plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/), desta forma é possível enviar os campos de "CPF", "número do endereço" e "bairro" (para o Checkout Transparente é obrigatório o uso deste plugin).


== Installation ==

* Baixe o plugin e coloque os arquivos do plugin para a pasta wp-content/plugins. Ou você pode realizar a instalação diretamente pelo instalador de plugins do WordPress.
* É necessário ativar o plugin.

Para mais informações sobre a instalação e configuração do módulo acesse [Instalação WooCommerce Módulo Vindi Pagamento](http://dev.yapay.com.br/intermediador/modulos-integracao-intermediador/#woocommerce).

= Dúvidas =

Para dúvidas envie um e-mail para nosso time de Integração: integracao@yapay.com.br

== Screenshots ==

1. Página dos plugins no Wordpress, onde clicar para configurar as formas de pagamento
2. Página de configuração do plugin

== Changelog ==
= 0.7.6 = 18/07/2024
* Fix: Correção de no código de envio

= 0.7.5 = 01/07/2024
* Fix: Botão copia e cola corrigidos
* Fix: Código de rastreio sendo salvo no banco de dados

= 0.7.5 = 01/07/2024
* Fix: Botão copia e cola corrigidos
* Fix: Código de rastreio sendo salvo no banco de dados

= 0.7.4 = 09/05/2024
* Feat: Adição do Fingerprint ao sistema
* Feat: Alteração de change para input nos eventos do credit.js

= 0.7.3 = 30/04/2024
* Fix: Remoção de bandeiras não utilizadas pela plataforma Vindi
* Fix: Melhoria no regex das bandeiras
* Fix: Melhoria na remoção da classe da bandeira selecionada
* Fix: Adição do catch na requisição das parcerlas para evitar loop
* Fix: Adição de mensagem de erro caso a bandeira não seja permetida

= 0.7.2 = 20/03/2024
* Fix: Nomenclatura de yapay para vindi
* Fix: Diminuição de caracteres permitidos de 500 para 50

= 0.7.1 = 15/03/2024
* Feat: Adicionando o método de pagamento 'BolePix'

= 0.7.0 = 04/03/2024
* Feat: Adicionando a configuração de reseller token para os métodos de pagamentos
* Fix: Alterando a utilização da propriedade order_discount do objeto de pedido para a utilização da função get_total_discount()
* Fix: Alterando a utilização da propriedade order_shipping do objeto de pedido para a utilização da função get_shipping_total()

= 0.6.9 = 21/02/2024
* Fix: Corrigindo a utilização da função get_meta na classe de gateway de cartão de crédito
* Fix: Corrigindo salvamento de dados de transação na classe de gateway de cartão de crédito

= 0.6.8 = 07/12/2023

* Feat: Adaptação do plugin para o nova estrutura de banco de dados do WooCommerce(HPOS)
* Fix: Bug de CPF não preenchido para compras com Cartão de Crédito
* Fix: Erro de Javascript no console e interferência na aplicação das máscaras de campos

= 0.6.7 = 13/09/2023

* Fix: Utilização das mascaras e identificação de bandeiras em dispositivos mobile
* Feat: Foi adicionado a opção do lojista exigir o CPF no checkout para compras com CNPJ.
* Fix: Exibição do texto de parcelas do checkout.

= 0.6.6 = 21/07/2023

* Fix:  Ícones gigantes no checkout
* Fix:  Correção da funcionalidade de Código de Rastreio
* Fix:  Remoção de estilização genéria do elemento 'ul'
* Feat: Criação de opção de remoção de ícones no checkout
* Feat: Melhorando visualização do formulário de cartão

= 0.6.5 = 20/01/2023

* Fix:  Envio de taxas e descontos em pedidos
* Fix:  Pagamento com PIX para Pessoa Jurídica
* Feat: Padronizando o padrão visual do plugin
* Feat: Criando visualização de boleto e pix na página do pedido
* Feat: Refatoração do sistema de log e remoção do sistema antigo
* Feat: Controlador do formato de visualização das parcelas durante o checkout

= 0.6.4 = 03/11/2021

* Ajuste token_transaction Notificação

= 0.5.2 = 09/10/2019

* Ajuste no css das bandeiras do cartão de crédito.

= 0.6.3 = 01/11/2021

* Ajuste token_transaction Notificação

= 0.6.2 = 27/10/2021

* Adicionada forma de pagamento PIX

= 0.6.1 = 11/02/2021

* Ajuste variáveis de desconto.

= 0.6.0 = 08/10/2020

* Ajustes de segurança nos logs

= 0.5.9 = 08/09/2020

* Ajustes de segurança e funções depreciadas

* Ajustes no envio da palavra CORREIOS na URL de envio do código de rastreio.
* Adicionado o parâmetro max_split_transaction, no reprocessamento do pagamento permite apenas o que foi setado no primeiro processamento.
* Adicionado o envio do número do celular (billing_celphone) caso exista.
* Correção no input do nome do cartão de crédito permitindo APENAS letras e espaço.

= 0.5.8 = 11/05/2020

* Correção bug thankyou_page com informações do pagamento do pedido.
* Correção bug de alteração do status dos pedidos.
* Erro na criação da parcela, PHP >= 7.3

= 0.5.7 = 13/03/2020

* Correção bug de envio de Código de Rastreio para Vindi com PHP >7.x

= 0.5.6 = 04/03/2020

* Bug fix URL Boleto.
* Correção das imagens das Bandeiras selecionadas na opção de cartão de crédito.

= 0.5.5 = 29/02/2020

* Bug fix função receitp_page

= 0.5.4 = 24/02/2020

* Correção Envio de Código de Rastreio do pedido

= 0.5.3 = 24/02/2020

* Alterar página de finalização de compra para order-received.
* Ajustar input do ano do cartão de crédito para dois digitos, ficando MM / YY.
* Ajuste no checkout, não será permitido digitar letras no campo de NÚMERO DO CARTÃO.
* Ajuste no checkout, não será permitido digitar números no campo de NOME DO CARTÃO.
























