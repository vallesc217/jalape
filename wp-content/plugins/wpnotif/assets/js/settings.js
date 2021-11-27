jQuery(function () {
    "use strict";

    function init_select($elems) {
        if ($elems === undefined || !$elems.length) {
            return;
        }
        $elems.each(function () {
            var $elem = jQuery(this);

            var data = {
                dir: wpnotifobj.direction,
                minimumResultsForSearch: 8,
                dropdownCssClass: "wpnotif-select-dropdown",
                theme: "default wpnotif-select",
            };
            if ($elem.data('source')) {
                data['minimumInputLength'] = 0;
                data['ajax'] = {
                    url: wpnotifobj.ajax_url,
                    dataType: 'json',
                    delay: 1000,
                    cache: true,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            action: jQuery(this).data('source'),
                            nonce: jQuery(this).data('nonce'),
                        };
                        return query;
                    }
                };
            }

            $elem.untselect(data);

        });
    }

    init_select(jQuery(".wpnotif_admin_fields").find("select").not('.wpnotif_multiselect_dynamic_enable'));

    jQuery(".wpnotif_multiselect_dynamic_enable").untselect({
        dir: wpnotifobj.direction,
        tags: true,
        tokenSeparators: [',', ' '],
        dropdownCssClass: "wpnotif-select-dropdown",
        theme: "default wpnotif-select"
    });
    var gatewayInp = jQuery(".wpnotif_gateways");

    var gatewayBoxTemplate = jQuery("#gateway_template").html();
    var apisettingsTab = jQuery(".wpnotif_gateway_settings");
    var gateway_table_list = jQuery(".gateway_table");


    var allowUpdate = false;
    var gatewayList = [];
    if (gatewayInp.length) {
        gatewayList = jQuery.parseJSON(gatewayInp.val());
    }
    var updateNotificationBasedGateways = false;
    jQuery(document).on("change", ".wpnotif_gateway_box input,.wpnotif-gs-trigger select", function () {
        var thisGatewayBox = jQuery(this).closest('.wpnotif_gateway_box');
        var gatewaySelect = thisGatewayBox.find('.wpnotif_gateway');
        var gateway = gatewaySelect.val();
        var countriesBox = thisGatewayBox.find('.gateway_countries select');

        var countries;
        if (!gateway || gateway == undefined) return;

        var values = {};
        values['ccodes'] = 0;
        values['countries'] = '';
        if (thisGatewayBox.attr('all')) {
            values['countries'] = 'all';
            values['all'] = 1;
            values['ccodes'] = 0;
            updateNotificationBasedGateways = true;
        } else {
            values['all'] = 0;
            if (countriesBox.val() != null) {
                var countriesBoxValue = countriesBox.val();
                values['ccodes'] = countriesBoxValue.join();
                var countryList = [];
                countriesBox.find("option:selected").each(function () {
                    countryList.push(jQuery(this).attr('data-country'));
                });
                var countryListString = countryList.join();
                values['countries'] = countryListString;
                thisGatewayBox.find('.wpnotif_ctr_list').text(countryListString);
            }
        }


        var prevGateway;
        if (jQuery(this).hasClass('wpnotif_gateway') && thisGatewayBox.attr('wpnotif-gateway')) {
            prevGateway = thisGatewayBox.attr('wpnotif-gateway');
            enableGateway(prevGateway);
        } else {
            prevGateway = gateway;
        }
        values['gateway'] = gateway;
        if (thisGatewayBox.find('.input-switch input').prop("checked") == true) {
            values['enable'] = 'on';
        } else {
            values['enable'] = 'off';
        }

        thisGatewayBox.attr('enable', values['enable']);

        if (gateway == 1001) {
            values['data-label'] = 'Whatsapp';
            values['class'] = 'hideGatewayList';

        }


        disableGateway(gatewaySelect, gateway);


        var dataString;
        if (gatewayList.hasOwnProperty('gc_' + prevGateway)) {
            gatewayList['gc_' + prevGateway] = values;
            dataString = JSON.stringify(gatewayList);
            dataString = dataString.replace('"gc_' + prevGateway + '":{', '"gc_' + gateway + '":{');
            gatewayList = JSON.parse(dataString);

        } else {
            gatewayList['gc_' + gateway] = values;
            dataString = JSON.stringify(gatewayList);
        }


        gatewayInp.val(dataString);


        var $this = jQuery(this);
        setTimeout(function () {
            if ($this.hasClass('wpnotif_gateway')) {
                updateDigGatewayFields($this);
            }
            if (updateNotificationBasedGateways) {
                jQuery(".gateway_based_notifications .wpnotif_gate_allcountries").first().attr('wpnotif-gateway', gateway).find('input').first().trigger('change');
                updateNotificationBasedGateways = false;
            }
        });


        if (allowUpdate) {
            allowUpdateSettings();
        }
    });

    var otpSettings = jQuery(".disotp");

    function updateDigGatewayFields($this) {


        var selected = $this.find("option:selected");
        var han = selected.attr('han');

        var gatewayName = selected.text();
        var val = $this.val();
        var thisGatewayBox = $this.closest('.wpnotif_gateway_box');
        var table = thisGatewayBox.find('.selected_gateway_conf');
        table.find('.gateway_conf').appendTo(gateway_table_list);
        gateway_table_list.find('.' + han + 'cred').show().appendTo(table);

        if (selected.data('addon') == 1) {
            thisGatewayBox.find(".require_addon_text").show();
        } else {
            thisGatewayBox.find(".require_addon_text").hide();
        }

        if (selected.data('document_link') == 1) {
            jQuery(".wpn_gateway_document_link").show().closest('td').addClass('wpn_gateway_document_visible');
        }


        if (!thisGatewayBox.attr('all')) thisGatewayBox.find('.wpnotif_gateway_target_head').text(gatewayName);
        else if (thisGatewayBox.attr('data-label'))
            thisGatewayBox.find('.wpnotif_gateway_target_head').text(thisGatewayBox.attr('data-label'));

        if (thisGatewayBox.attr('enable') != 'on') {
            thisGatewayBox.find('.input-switch input').prop('checked', false).trigger('change');
        } else {
            thisGatewayBox.find('.input-switch input').prop('checked', true).trigger('change');
        }

        thisGatewayBox.attr('wpnotif-gateway', val);


        var size = Object.keys(gatewayList).length;
        if (
            (size == 1 && (gatewayList.hasOwnProperty('gc_1') || gatewayList.hasOwnProperty('gc_13'))) ||
            (size == 2 && gatewayList.hasOwnProperty('gc_1') && gatewayList.hasOwnProperty('gc_13'))) {
            otpSettings.hide();
        } else {
            otpSettings.show();
        }
    };
//thisGatewayBox.find(".disotp").hide();
    jQuery.each(gatewayList, function (gatewayCode, values) {

        allowUpdate = false;
        addUpdateGatewayBox(values['gateway'], values);
        jQuery(".wpnotif_admin_conf .active").removeClass('active').find(".wpnotif_gateway_configuation_expand_box").hide();
        jQuery(".wpnotif_admin_conf .wpnotif_gateway_box").first().addClass('active').find(".wpnotif_gateway_configuation_expand_box").show();

        allowUpdate = true;

    });

    jQuery(".apisettingstab .wpnotif_gateway_settings").find(".hideGatewayList").after('<div class="country_specific"><div class="wpnotif_admin_head"><span>Country Specific</span></div></div>');

    jQuery(".add_gateway_group_button").on('click', function () {
        allowUpdate = true;
        addUpdateGatewayBox(-1, null);
    })

    function addUpdateGatewayBox(gatewayCode, values) {
        var gatewayBox = jQuery(gatewayBoxTemplate).clone();
        apisettingsTab.append(gatewayBox);


        var gatewaySelectbox = gatewayBox.find('.wpnotif_gateway');
        var gatewayCountries = gatewayBox.find('.gateway_countries select');

        var expand = false;
        if (values != null) {

            if (values['all'] == 1) {
                gatewayBox.attr('all', '1').addClass("wpnotif_gate_allcountries active");
                expandGateway(gatewayBox, false, false);


                if (values['data-label'])
                    gatewayBox.attr('data-label', values['data-label']);

                if (values['class'])
                    gatewayBox.addClass(values['class']);


            } else {
                gatewayBox.removeAttr('all');
                var countriesString = values['countries'];
                gatewayBox.find('.wpnotif_ctr_list').text(countriesString);

                gatewayBox.find('.wpnotif_gateway_configuation_expand_box').hide();

                var countriesArray = countriesString.split(',');
                jQuery.each(countriesArray, function (key, country) {
                    gatewayCountries.find('[data-country="' + country + '"]').attr('selected', 'selected');
                })


            }

            gatewaySelectbox.val(gatewayCode);
        } else {
            collapseGateway();


            expandGateway(gatewayBox, true, true);

            jQuery.each(gatewayList, function (gc, values) {
                var gatewayCode = values['gateway'];
                gatewaySelectbox.find('[data-value=' + gatewayCode + ']').addClass('disabled').attr('disabled', 'disabled');
            });
            var selNotDisabled = gatewaySelectbox.find('option:not([disabled]):first').val();
            gatewaySelectbox.val(selNotDisabled);


        }

        var input_switch = gatewayBox.find('.input-switch');
        var random = Math.random();
        input_switch.find('input').attr('id', random);
        input_switch.find('label').attr('for', random);


        if (values == null || values['enable'] != 'on') {
            input_switch.find('input').prop('checked', false).trigger('change');
        } else {
            input_switch.find('input').prop('checked', true).trigger('change');
        }
        //gatewayBox.attr('enable',values['enable']);

        if (!gatewaySelectbox.data('untselect-id')) {
            init_select(gatewaySelectbox);
            init_select(gatewayCountries);
        }
        gatewaySelectbox.trigger('change');
        gatewayCountries.trigger('change');


    }

    jQuery(document).on("click", ".wpnotif_gateway_box_close,.wpnotif_gateway_settings .inactive,.wpnotif_gateway_settings .wpnotif_gateay_conf_expand", function (e) {
        if (jQuery(e.target).hasClass('wpnotif_gateay_conf_delete')) return;


        var showGatewayBox = jQuery(this).closest('.wpnotif_gateway_box');
        if (!showGatewayBox.hasClass('active')) {
            collapseGateway();

            showGatewayBox.addClass('active');
            expandGateway(showGatewayBox, false, true);
        } else {
            collapseGateway();
        }


    });


    jQuery(".whatsapp_whatsapp_gateway").on('change', function () {
        var value = jQuery(this).val();
        var name = jQuery(this).attr('name');

        if(value>2){
            jQuery(".whatsapp_additional_addon p").show();
        }else{
            jQuery(".whatsapp_additional_addon p").hide();
        }

        if(value == 3){
            jQuery('.wpn_gateway_document_link').show().closest('td').addClass('wpn_gateway_document_visible');
        }

        jQuery('.' + name + '_active').hide();
        jQuery('.whatsapp_gateway_' + value).addClass(name + '_active').show();
    });
    setTimeout(function () {
        jQuery(".wpnotif-hide-elem").hide();
        jQuery(".whatsapp_whatsapp_gateway").trigger('change');
    })

    function enableGateway(gateway) {
        jQuery(".wpnotif-gs-gatway-select-td").find('[data-value=' + gateway + ']').removeClass('disabled').removeAttr('disabled');
    }

    function disableGateway(gatewaySelect, gateway) {
        jQuery(".wpnotif-gs-gatway-select-td").find('[data-value=' + gateway + ']').addClass('disabled').attr('disabled', 'disabled');
        gatewaySelect.find('[data-value=' + gateway + ']').removeClass('disabled').removeAttr('disabled');
    }

    jQuery(document).on("click", ".wpnotif_gateay_conf_delete", function (e) {

        var thisGatewayBox = jQuery(this).closest('.wpnotif_gateway_box');
        var gc = thisGatewayBox.attr('wpnotif-gateway');
        if (thisGatewayBox.attr('all')) {
            jQuery(this).remove();
            return;
        }

        enableGateway(gc);
        delete gatewayList['gc_' + gc];
        gatewayInp.val(JSON.stringify(gatewayList));


        var table = thisGatewayBox.find('.selected_gateway_conf');
        table.find('.gateway_conf').appendTo(gateway_table_list);

        thisGatewayBox.slideUp('fast').remove();

        allowUpdateSettings();
    });


    function collapseGateway() {
        jQuery(".wpnotif_admin_conf .active").removeClass('active').find(".wpnotif_gateway_configuation_expand_box").slideUp('fast');


    }

    var activeGb;

    function expandGateway(gatewayBox, isNew, autoScroll) {

        activeGb = gatewayBox;
        gatewayBox.addClass('active').find(".wpnotif_gateway_configuation_expand_box").stop().slideDown('fast', function () {
            if (!autoScroll) return;
            if (!isNew) {
                gatewayScroll();
            } else {
                setTimeout(function () {
                    gatewayScroll();
                }, 190)
            }
        });


    }

    function gatewayScroll() {
        jQuery('html, body').stop().animate({
            scrollTop: activeGb.offset().top - 100
        }, 300);
    }

    function allowUpdateSettings() {
        jQuery(".wpnotif_admin_submit").removeAttr("disabled");

    }


    var wpnotif_sort_fields = jQuery(".wpnotif-reg-fields").find('tbody');

    if (wpnotif_sort_fields.length) {
        var wpnotif_sortorder = jQuery("#wpnotif_sortorder");


        var sortorder = wpnotif_sortorder.val().split(',');

        wpnotif_sort_fields.find('tr').sort(function (a, b) {
            var ap = jQuery.inArray(a.id, sortorder);
            var bp = jQuery.inArray(b.id, sortorder);
            return (ap < bp) ? -1 : (ap > bp) ? 1 : 0;


        }).appendTo(wpnotif_sort_fields);


        wpnotif_sort_fields.sortable({
            update: function (event, ui) {
                var sortOrder = jQuery(this).sortable('toArray').toString();
                wpnotif_sortorder.val(sortOrder);

                allowUpdateSettings();
            }
        });

    }

    var dpc = jQuery('#wpnotif_purchasecode');


    var wpnotif_tab_wrapper = jQuery(".wpnotif-tab-wrapper");
    if (wpnotif_tab_wrapper.length) {
        var wpnotif_admin_submit = jQuery(".wpnotif_admin_submit");
        var width_wpnotif_admin_submit = wpnotif_admin_submit.outerWidth(true) + 24;
        var wpnotif_left_side = jQuery(".wpnotif_admin_left_side");
        jQuery(window).on('load', function () {
            update_tab_width();
        });
        jQuery(window).on('resize', function () {
            update_tab_width();
            update_tab_sticky();
            update_tb_line();

        });

        var respon_win = 822;
        var tb_top = wpnotif_tab_wrapper.offset().top;
        var ad_bar_height = jQuery("#wpadminbar").outerHeight(true);
        jQuery(window).on('scroll', function () {
            update_tab_sticky();
        });


        jQuery(window).trigger('scroll');

    }

    function update_tab_sticky() {
        var w_top = jQuery(window).scrollTop();
        var sb = tb_top - w_top;
        if (sb <= ad_bar_height && jQuery(window).width() >= respon_win) {
            wpnotif_tab_wrapper.addClass("wpnotif-tab-wrapper-fixed").css({'top': ad_bar_height});
        } else {
            wpnotif_tab_wrapper.removeClass("wpnotif-tab-wrapper-fixed");
        }
    }

    function update_tab_width() {
        var w = wpnotif_left_side.width();
        wpnotif_tab_wrapper.outerWidth(w);
        wpnotif_admin_submit.css({'left': wpnotif_left_side.offset().left + w - 168});

    }

    var $mainNav = jQuery(".wpnotif-tab-ul");

    jQuery(document).on("click", ".wpnotif_popmessage", function () {

        jQuery(this).closest('.wpnotif_popmessage').slideUp('fast', function () {
            jQuery(this).remove();
        });
    })

    var $el, leftPos, newWidth;

    $mainNav.append("<li id='wpnotif-tab-magic-line' style='display:none;'></li>");
    var $magicLine = jQuery("#wpnotif-tab-magic-line");

    setTimeout(function () {
        $magicLine.show();
        update_tb_line();
    })

    function update_tb_line() {
        var wpnotif_active_tab = jQuery(".wpnotif-nav-tab-active");

        if (!wpnotif_active_tab.length) {
            $magicLine.hide();
            return;
        }

        var wpnotif_active_tab_par_pos = wpnotif_active_tab.parent().position();
        $magicLine
            .width(wpnotif_active_tab.parent().width())
            .css({
                "left": wpnotif_active_tab_par_pos.left,
                "top": wpnotif_active_tab_par_pos.top + 21
            })
            .data("origLeft", $magicLine.position().left)
            .data("origWidth", $magicLine.width());
        if (wpnotif_active_tab.hasClass("wpnotif_ngmc") && !wpnotif_active_tab.hasClass("customfieldsNavTab")) {

            $magicLine.hide().css({'top': 45});
        }
    }

    jQuery(".wpnotif_admin_conf .updatetabview").on('click', function () {


        var c = jQuery(this).attr('tab');

        var acr = jQuery(this).attr('acr');

        var refresh = jQuery(this).attr('refresh');

        if (typeof refresh !== typeof undefined && refresh !== false) {
            location.reload();
            return true;
        }

        if (typeof acr !== typeof undefined && acr !== false) {
            var inv = dpc.attr('invalid');
            if (dpc.val().length != 36 || inv == 1) {

                wpnotif_showMessage(wpnotifobj.plsActMessage);
                if (jQuery("#wpnotif_activatetab").length) {
                    jQuery("#wpnotif_activatetab").click();
                    dpc.focus();
                }
                return false;
            }
        }

        var tab = jQuery("." + c);

        if (tab.hasClass('digcurrentactive')) return false;

        var $this = jQuery(this);

        if (tab.data('attach')) {
            $this = jQuery('.' + tab.data('attach'));
        }

        if (!$this.hasClass("wpnotif_ngmc")) {
            $magicLine.show();
            $el = $this.parent();
            leftPos = $el.position().left;
            newWidth = $el.width();
            $magicLine.stop().animate({
                left: leftPos,
                width: newWidth,
                top: $el.position().top + 21
            }, 'fast');
        } else {
            $magicLine.hide();
        }

        jQuery(".digcurrentactive").removeClass("digcurrentactive").hide();

        tab.fadeIn(150).addClass("digcurrentactive");


        if (jQuery(".wpnotif-tab-wrapper-fixed").length)
            jQuery('html, body').animate({scrollTop: tab.offset().top - 90}, 220);


        jQuery(".wpnotif-nav-tab-active").removeClass("wpnotif-nav-tab-active");
        jQuery(this).addClass("wpnotif-nav-tab-active");


        updateURL("tab", c.slice(0, -3));

        return false;
    });

    function updateURL(key, val) {
        var url = window.location.href;
        var reExp = new RegExp("[\?|\&]" + key + "=[0-9a-zA-Z\_\+\-\|\.\,\;]*");

        if (reExp.test(url)) {
            // update
            var reExp = new RegExp("[\?&]" + key + "=([^&#]*)");
            var delimiter = reExp.exec(url)[0].charAt(0);
            url = url.replace(reExp, delimiter + key + "=" + val);
        } else {
            // add
            var newParam = key + "=" + val;
            if (!url.indexOf('?')) {
                url += '?';
            }

            if (url.indexOf('#') > -1) {
                var urlparts = url.split('#');
                url = urlparts[0] + "&" + newParam + (urlparts[1] ? "#" + urlparts[1] : '');
            } else {
                url += "&" + newParam;
            }
        }
        window.history.pushState(null, document.title, url);
    }


    var chn = false;
    jQuery(".wpnotif_admin_conf textarea,.wpnotif_admin_conf input").on('keyup', function () {
        if (!jQuery(this).attr("readonly") && !jQuery(this).attr('wpnotif-save')) {
            var pcheck = jQuery(this).closest('.digcon');
            if (!pcheck.length) enableSave();
        }

    });
    jQuery(".wpnotif_admin_conf input,.wpnotif_admin_conf select,.wpnotif_activation_form input").on('change', function () {

        if (!jQuery(this).attr("readonly") && !jQuery(this).attr('wpnotif-save')) enableSave();
    });


    var wpnotif_pc = jQuery("#wpnotif_purchasecode");

    wpnotif_pc.on('change', function () {
        if (jQuery(this).attr('readonly')) return;
        jQuery(".customfieldsNavTab").attr('refresh', 1);
    });
    wpnotif_pc.on('keyup', function () {

        if (jQuery(this).attr('readonly')) return;


        if (jQuery(this).val().length == 36 || jQuery(this).val().length == 0) {
            jQuery(".wpnotif_prc_ver").hide();
            jQuery(".wpnotif_prc_nover").hide();
        } else {
            invPC(-1);
        }
    });

    function enableSave() {
        if (!chn) {
            chn = true;
            allowUpdateSettings();
        }
    }

    function allowUpdateSettings() {
        jQuery(".wpnotif_admin_submit").removeAttr("disabled");

    }

    jQuery(document).on('click', '.wpnotif_copy_shortcode', function (e) {
        var a = jQuery(this).parent();
        var i = a.find("input");
        copyShortcode(i);
    });

    function copyShortcode(i) {
        if (i.attr("nocop")) return;
        i.attr('nocop', 1);
        i.select();
        document.execCommand("copy");
        var v = i.val();
        i.val(wpnotifobj.Copiedtoclipboard);
        setTimeout(
            function () {
                i.val(v);
                i.removeAttr('nocop');
            }, 800);
    }

    if (jQuery.fn.mask) {
        dpc.mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA');
    }

    var wpnotif_tapp = jQuery("#wpnotif_tapp");

    var sgs = jQuery(".wpnotif_load_overlay_gs");

    var se = sgs.length;


    var wpnotif_test_api_status = 0;

    jQuery(".wpnotif_request_server_addition").on('click', function () {
        var hr = jQuery(this).attr('href');
        window.open(hr, '_target');
    })
    var refreshCode = 0;
    jQuery(".wpnotif_domain_type").find('button').on('click', function () {
        var value = jQuery(this).attr('val');
        jQuery("input[name='wpnotif_license_type']").val(value);
        if (refreshCode != 1) {
            refreshCode = 0;
            jQuery("#wpnotif_purchasecode").val('').removeAttr('readonly');
        }
        jQuery(".wpnotif_prchcde").fadeIn('fast');
        jQuery(".wpnotif_domain_type").hide();
        jQuery(".wpnotif_btn_unregister").hide();

        if (value != 1) {
            jQuery(".request_live_server_addition").show();
            jQuery(".request_testing_server_addition").hide();
        } else {
            jQuery(".request_live_server_addition").hide();
            jQuery(".request_testing_server_addition").show();
        }
    })
    jQuery(".wpnotif_btn_unregister").on('click', function () {

        sgs.find('.circle-loader').removeClass('load-complete');
        sgs.find('.checkmark').hide();
        sgs.fadeIn();


        var code = dpc.val();
        jQuery.post('https://bridge.unitedover.com/updates/verify.php',
            {
                code: code,
                slug: 'wpnotif',
                request_site: encodeURIComponent(jQuery("input[name='wpnotif_domain']").val()),
                license_type: jQuery("input[name='wpnotif_license_type']").val(),
                unregister: 1,
                version: jQuery("input[name='wpnotif_version']").val(),
                settings: 1,
            }, function (data, status) {
                if (data == 1) {
                    jQuery(".wpnotif_domain_type").fadeIn('fast');
                    jQuery(".wpnotif_prchcde").fadeOut();
                    jQuery(".wpnotif_prc_ver").fadeOut();
                    jQuery(".wpnotif_prc_nover").hide();
                    jQuery("#wpnotif_purchasecode").val('').removeAttr('readonly').trigger('change');
                } else {
                    wpnotif_showMessage(data);
                }
                jQuery(".wpnotif_activation_form").submit();

                return false;
            }
        );

    })

    jQuery(".wpnotif_btn_unregister").on('click', function () {

        sgs.find('.circle-loader').removeClass('load-complete');
        sgs.find('.checkmark').hide();
        sgs.fadeIn();


        var code = dpc.val();
        jQuery.post('https://bridge.unitedover.com/updates/verify.php',
            {
                code: code,
                slug: 'wpnotif',
                request_site: encodeURIComponent(jQuery("input[name='wpnotif_domain']").val()),
                license_type: jQuery("input[name='wpnotif_license_type']").val(),
                addons: jQuery("input[name='wpnotif_addons_list']").val(),
                unregister: 1,
                version: jQuery("input[name='wpnotif_version']").val(),
                settings: 1,
            }, function (data, status) {
                jQuery(".wpnotif_btn_unregister").hide();

                jQuery("#wpnotif_purchasecode").val('').removeAttr('readonly').trigger('change');
                jQuery(".wpnotif_activation_form").submit();

                return false;
            }
        );

    })


    var dac;
    jQuery('.wpnotif_admin_submit').on('click', function () {
        jQuery(this).closest('form').submit();
    })

    jQuery('.wpnotif_admin_form').on("submit", function (e) {
        e.preventDefault();
        loader.show();
        var form_data = jQuery(this).serialize();
        jQuery.ajax({
            type: "POST",
            url: wpnotifobj.ajax_url,
            data: form_data,
            success: function (data) {
                loader.hide();
            },
            error: function () {
                showWPNotifErrorMessage(wpnotifobj.Error);
            }
        });

        return false;
    });

    var refreshCode = 0;
    jQuery("#wpnotif_setting_update").on("submit", function () {
        dac = jQuery(this);


        hideWPNotifMessage();

        var isOpt = false;
        var fd = dac.serialize();

        if (wpnotif_test_api_status != 1) {
            sgs.find('.circle-loader').removeClass('load-complete');
            sgs.find('.checkmark').hide();
            sgs.fadeIn();
        }

        var code = dpc.val();
        if (code.length == 0) {

            jQuery(".wpnotif_prc_ver").hide();
            jQuery(".wpnotif_prc_nover").hide();

            updateSettings(fd, -1);
            return false;
        } else if (code.length != 36) {
            showWPNotifErrorMessage(wpnotifobj.invalidpurchasecode);

            jQuery(".wpnotif_prc_ver").hide();
            jQuery(".wpnotif_prc_nover").show();
            updateSettings(fd, -1);
            return false;
        }

        if (isBusy) return false;
        isBusy = true;

        jQuery.post('https://bridge.unitedover.com/updates/verify.php',
            {
                json: 1,
                code: code,
                slug: 'wpnotif',
                request_site: encodeURIComponent(jQuery("input[name='wpnotif_domain']").val()),
                settings: 1,
                license_type: jQuery("input[name='wpnotif_license_type']").val(),
                version: jQuery("input[name='wpnotif_version']").val(),
            }, function (response, status) {
                isBusy = false;
                var data = response.code;


                var type = response.type;
                refreshCode = 1;
                jQuery(".wpnotif_domain_type").find('button[val=' + type + ']').trigger('click');
                fd = dac.serialize();

                if (data != 1) {
                    invPC(se);
                    dpc.attr('invalid', 1);


                } else {
                    jQuery(".wpnotif_prc_ver").show();
                    jQuery(".wpnotif_prc_nover").hide();
                    dpc.attr('invalid', 0);

                }

                if (data == 0) {
                    showWPNotifErrorMessage(wpnotifobj.invalidpurchasecode);
                    if (!sgs.attr("ajxsu")) {
                        updateSettings(fd, -1);
                    }

                } else if (data == 1) {

                    jQuery(".wpnotif_btn_unregister").show();

                    if (sgs.attr("ajxsu")) {
                        jQuery(".wpnotif_activation_form").unbind("submit").submit();
                    } else {
                        updateSettings(fd, 1);
                        jQuery(".wpnotif_pc_notice").hide();
                    }
                } else {
                    if (data == -1) {
                        showWPNotifErrorMessage("This purchase code is already being used on another site.");
                    } else showWPNotifErrorMessage(response.msg);


                    if (!sgs.attr("ajxsu")) {
                        updateSettings(fd, -1);
                    }
                }


            }
        );


        return false;
    });


    function invPC(se) {
        jQuery("#wpnotif_purchasecode").removeAttr('readonly');
        jQuery(".wpnotif_prc_ver").hide();
        jQuery(".wpnotif_prc_nover").show();
        if (se > 0) sgs.hide();
    }

    var isBusy = false;

    function updateSettings(fd, activate) {
        if (isBusy) return;
        isBusy = true;
        jQuery.ajax({
            type: "POST",
            url: wpnotifobj.ajax_url,
            data: fd + '&action=wpnotif_save_settings&pca=' + activate,
            success: function (data) {
                isBusy = false;
                sgs.find('.circle-loader').addClass('load-complete');
                sgs.find('.checkmark').show();
                setTimeout(
                    function () {
                        sgs.fadeOut();
                        chn = false;
                        jQuery(".wpnotif_admin_submit").attr("disabled", "disabled");
                        if (wpnotif_test_api_status == 1) {
                            digCallTestApi();
                        }
                    }, 1500);


            },
            error: function () {
                isBusy = false;
                invPC();
                showWPNotifErrorMessage(wpnotifobj.Error);
            }
        });

    }


    var wpnotif_api_test;

    var loader = jQuery(".wpnotif_load_overlay");

    jQuery(document).on("click", ".wpnotif_call_test_api_btn", function () {


        wpnotif_api_test = jQuery(this).closest(".wpnotif_api_test");

        var wpnotif_test_cont = wpnotif_api_test.find(".digcon");
        var mobile = wpnotif_test_cont.find(".mobile").val();
        var countrycode = wpnotif_test_cont.find(".wpnotif_countrycode").val();

        if (mobile.length == 0 || !jQuery.isNumeric(mobile) || countrycode.length == 0 || !jQuery.isNumeric(countrycode)) {
            showWPNotifErrorMessage(wpnotifobj.validnumber);
            return false;
        }

        wpnotif_test_api_status = 1;

        loader.show();

        if (jQuery(".wpnotif_admin_submit").attr("disabled")) {
            digCallTestApi();
        } else jQuery(".wpnotif_activation_form").trigger("submit");


    });

    var sms_processing = false;
    var sms_processing_elem = jQuery(".wpnotif_sms_processing");
    var hide_message_timeout;
    jQuery(".send_quick_sms").on('click', function (e) {

        e.preventDefault();
        if (sms_processing) return;
        clearTimeout(hide_message_timeout);
        var send_button = jQuery(this);
        send_button.addClass('processing').find('span').text(send_button.attr('data-processing')).parent().find('div').addClass('wpnotif_sms_processing');
        sms_processing = true;
        sms_processing_elem.show();
        var box = jQuery(this).closest('.quick_sms_grid');
        var mobile = box.find(".mobile").val();
        wpnotif_test_api_status = 0;
        var response_box = jQuery(".quick_sms_response_box");
        var message = box.find("#quick_message").val();
        var trigger_order_status = 0;
        var order_id = jQuery('.wpnotif_order_id').val();
        if (jQuery(this).hasClass('trigger_order_status')) {
            trigger_order_status = 1;
        }
        var wpnotif_nonce = jQuery(".wpnotif_nonce").val();

        var post_notification = 0;
        if (jQuery('.wpnotif_post_notification').length) {
            post_notification = jQuery('.wpnotif_post_notification').val();
        }

        response_box.hide();
        jQuery.ajax({
            type: 'post',
            async: true,
            url: wpnotifobj.ajax_url,
            data: {
                action: 'wpnotif_send_quick_sms',
                mobile: mobile,
                quick_message: message,
                trigger_order_status: trigger_order_status,
                post_id: order_id,
                wpnotif_nonce: wpnotif_nonce,
                post_notification: post_notification
            },
            success: function (res) {
                box.find("#quick_message").val('');
                send_button.removeClass('processing').find('span').text(send_button.attr('data-send')).parent().find('div').removeClass('wpnotif_sms_processing');
                sms_processing = false;
                sms_processing_elem.hide();

                if (res.success == 0) {
                    response_box.addClass('msg_send_failed').removeClass('msg_send_success');
                } else {
                    response_box.addClass('msg_send_success').removeClass('msg_send_failed');

                    if (res.data.data != null && wpnotifobj.isWhatsappWebEnabled) {
                        try {
                            var data = res.data.data;
                            jQuery.each(data, function (no, msg) {
                                sendWhatsWebMessage(no, msg);
                            });
                            if (Object.keys(data).length > 1) {
                                update_message(0);
                            }

                        } catch (e) {

                        }
                    }

                }
                response_box.find('.msg').text(res.data.msg).parent().stop().fadeIn('fast');
                hide_message_timeout = setTimeout(function () {
                    response_box.fadeOut('fast')
                }, 3000);
            },
            error: function (res) {
                send_button.removeClass('processing').find('span').text(send_button.attr('data-send')).parent().find('div').removeClass('wpnotif_sms_processing');
                sms_processing = false;
                sms_processing_elem.hide();
                response_box.addClass('msg_send_failed').removeClass('msg_send_success').find('.msg').text(wpnotifobj.Error).parent().stop().fadeIn('fast');

                hide_message_timeout = setTimeout(function () {
                    response_box.fadeOut('fast')
                }, 3000);
            }
        });
        return false;
    })


    function digCallTestApi() {
        if (wpnotif_test_api_status != 1) return;
        loader.show();
        var wpnotif_test_cont = wpnotif_api_test.find(".digcon");
        var mobile = wpnotif_test_cont.find(".mobile").val();
        var countrycode = wpnotif_test_cont.find(".wpnotif_countrycode").val();

        var gatewayBox = wpnotif_api_test.closest('.wpnotif_gateway_box');

        var gateway;
        if (gatewayBox.length) {
            gateway = gatewayBox.find(".wpnotif_gateway").val();
        } else {
            gateway = jQuery(".wpnotif_gateway").val();
        }
        wpnotif_test_api_status = 0;
        jQuery.ajax({
            type: 'post',
            async: true,
            url: wpnotifobj.ajax_url,
            data: {
                action: 'wpnotif_test_api',
                digt_mobile: mobile,
                gateway: gateway,
                digt_countrycode: countrycode

            },
            success: function (res) {
                showTestResponse(res);
            },
            error: function (res) {
                showTestResponse(res);
            }
        });
    }


    function showTestResponse(msg) {
        wpnotif_test_api_status = 0;
        wpnotif_api_test.find(".wpnotif_call_test_response").show();
        wpnotif_api_test.find(".wpnotif_call_test_response_msg").text(msg);
        loader.hide();

    }

    wpnotif_tapp.on('change', function () {
        var val = jQuery(this).val();

        var te = wpnotif_tapp.find("option:selected").attr('han');

        te = te.replace(".", "_");

        jQuery('.wpnotif_call_test_response').hide();
        if (val == 1 || val == 13) {

            jQuery(".wpnotif_api_test").hide();
            jQuery(".disotp").hide();
            jQuery(".wpnotif_current_gateway").hide();
        } else {
            jQuery(".wpnotif_api_test").show();
            jQuery(".disotp").show();
            jQuery(".wpnotif_current_gateway").show().find("span").text(wpnotif_tapp.find("option:selected").text());
        }


        wpnotif_tapp.find('option').each(function (index, element) {
            var hanc = jQuery(this).attr("han");
            if (hanc != te) {
                jQuery("." + hanc + "cred").each(function () {
                    jQuery(this).hide().find("input").removeAttr("required");
                });

            }
        });
        jQuery("." + te + "cred").each(function () {
            var input = jQuery(this).show().find("input");
            var optional = input.attr('wpnotif-optional');
            if (optional && optional == 1) return;

            input.attr("required", "required");
        });

    });

    jQuery(document).on("change update", ".input-switch input", function () {
        if (jQuery(this).prop("checked") == true) {
            jQuery(this).parent().addClass('checked');
        } else {
            jQuery(this).parent().removeClass('checked');
        }
    });
    var manual_call = false;
    jQuery(document).on("change", ".notification_toggle", function () {
        var table = jQuery(this).closest('table');
        var msg = table.next();
        if (jQuery(this).prop("checked") == true) {
            if (manual_call) {
                msg.show();
            } else {
                msg.stop().slideDown('fast', function () {
                    jQuery(this).show();
                });
            }
        } else {
            if (manual_call) {
                msg.hide();
            } else {
                msg.stop().slideUp('fast');
            }
        }
    });


    var country_based = jQuery("#country_based");
    var gateway_based_notifications = jQuery(".gateway_based_notifications");
    var simple_user_notifications = jQuery(".simple_user_notifications");
    var user_notifications_temp = jQuery(".user_notifications");
    country_based.on('change', function () {
        updateCountryBasedNotifications();
    })
    setTimeout(function () {
        updateCountryBasedNotifications();
    });

    function updateCountryBasedNotifications() {

        if (country_based.prop("checked") == true) {
            var apiSettingsClone = apisettingsTab.clone();
            apiSettingsClone.find('.country_specific').remove();

            apiSettingsClone.find('.wpnotif_gateway_configuation_expand_box_contents').empty().each(function () {
                var clone = user_notifications_temp.clone();

                clone.find('tr').each(function () {
                    var id = "a_" + Math.random();
                    var $this = jQuery(this);
                    $this.find('input, textarea').attr('id', id);
                    $this.find('label').attr('for', id);

                });
                jQuery(this).append('<div class="wpnotif_gateway_sep_line"></div>').append(clone);
            });

            gateway_based_notifications.empty().append(apiSettingsClone);
            gateway_based_notifications.stop().slideDown('fast');
            simple_user_notifications.hide();
            updateCountryBasedNotifications_ui();

        } else {
            gateway_based_notifications.hide();
            simple_user_notifications.show();
        }
    }

    var notifications_json;
    var notifications = jQuery("#wpnotif_gateway_customer_notifications");
    setTimeout(function () {

        jQuery(document).on("change", ".gateway_based_notifications input,.gateway_based_notifications textarea", function (e) {
            updateCountryBasedNotifications_data();
        });

        updateCountryBasedNotifications_ui();

    }, 10);

    function updateCountryBasedNotifications_data() {
        var json = {};
        gateway_based_notifications.find('.wpnotif_gateway_box').each(function () {
            var gateway = jQuery(this).attr('wpnotif-gateway');
            var values = {};
            jQuery(this).find('.form-switch').each(function () {
                var input = jQuery(this).find('input');
                var key = input.attr('name');
                var checked = '';
                if (input.prop("checked") == true) {
                    checked = 'on';

                }
                var message = jQuery(this).next().find('textarea').val();
                values[key] = {'enable': checked, 'msg': message};
            });
            json['wpnotif_' + gateway] = values;

        });
        notifications.val(JSON.stringify(json));
        enableSave();
    }




    function stripslashes (str) {
        return (str + '').replace(/\\(.?)/g, function (s, n1) {
            switch (n1) {
                case '\\':
                    return '\\';
                case '0':
                    return '\u0000';
                case '':
                    return '';
                default:
                    return n1;
            }
        });
    }

    function addslashes(string) {
        return string.replace(/\\/g, '\\\\').
        replace(/\u0008/g, '\\b').
        replace(/\t/g, '\\t').
        replace(/\n/g, '\\n').
        replace(/\f/g, '\\f').
        replace(/\r/g, '\\r').
        replace(/'/g, '\\\'').
        replace(/"/g, '\\"');
    }

    function updateCountryBasedNotifications_ui() {
        try {

            notifications_json = notifications.val();
            notifications_json = stripslashes(notifications_json);
            notifications_json = JSON.parse(notifications_json);
            jQuery.each(notifications_json, function (gateway_key, data) {
                var gateway = gateway_key.replace(/\D/g, '');

                var gatewaybox = gateway_based_notifications.find('div[wpnotif-gateway="' + gateway + '"]');
                if (gatewaybox.length) {
                    jQuery.each(data, function (key, values) {
                        var input = gatewaybox.find('input[name="' + key + '"]');
                        if (values['enable'] == 'on') {
                            input.prop("checked", true).parent().addClass('checked');
                            gatewaybox.find('textarea[name="' + key + '_msg"]').val(values['msg']).closest('.notification_message').show();
                        } else {
                            input.prop("checked", false).parent().removeClass('checked');
                            gatewaybox.find('textarea[name="' + key + '_msg"]').val(values['msg']).closest('.notification_message').hide();
                        }

                    });
                }
            })

           notifications.val(addslashes(JSON.stringify(notifications_json)));

        } catch (e) {

        }

    }

    chn = true;
    manual_call = true;
    wpnotif_tapp.trigger('change');
    jQuery(".input-switch input").trigger('change');

    chn = false;

    jQuery("#wpnotif_loading_container").fadeOut('fast');

    setTimeout(function () {
        manual_call = false;
    })
    jQuery("#wp-admin-bar-wpnotif-pending-messages").on('click', function () {
        jQuery(".wpnotif-box").fadeToggle('fast');
        return false;
    });
    jQuery(document).on('click', '.wpnotif-whatsapp-send', function (e) {
        e.preventDefault();
        var container = jQuery(this).closest('.wpnotif-list');
        var mobile = container.attr('data-mobile');
        var msg = container.find('.wpnotif-message').text();
        var id = container.attr('data-id');

        sendWhatsWebMessage(mobile, msg);

        container.fadeOut('fast', function () {
            jQuery(this).remove();
        });

        update_message(id);
        return false;
    });

    function sendWhatsWebMessage(phone, msg) {
        phone = phone.replace('+','');
        msg = msg.replace(/[\u200B-\u200D\uFEFF]/g, '');
        msg = encodeURIComponent(msg);
        var url = 'https://web.whatsapp.com/send?phone=' + phone + '&text='+msg;
        if(wpnotifobj.usingWhatsAppDesktop || isMobile.any()){
            url = 'https://api.whatsapp.com/send?phone=' + phone + '&text='+msg;
        }
        window.open(url);
    }

    function update_message(id) {


        var data_pending = jQuery(".wp-notif-total_messages");

        jQuery.ajax({
            type: 'post',
            async: true,
            url: wpnotifobj.ajax_url,
            data: {
                action: 'wpnotif_update_message',
                id: id,
                wpnotif_nonce: jQuery(".wpnotif_del_nonce").val(),

            },
            success: function (res) {
                loader.hide();
                res = jQuery.parseHTML(res);
                var notif_box = jQuery(".wpnotif-box");
                notif_box.replaceWith(res);
                var count = jQuery(".wpnotif-box").find(".wpnotif-pending-whatsapp-messages").length;


                if (id == -1) {
                    jQuery(".wpnotif-box").fadeOut('fast');
                } else {
                    jQuery(".wpnotif-box").show();
                }
                data_pending.text(count);
                showTime();

            }, error: function () {
                loader.hide();
            }
        });
    }

    var notify_user_sms = jQuery("#user_notify_user");
    var notify_user_whatsapp_message = jQuery("#user_whatsapp_message");
    var notify_both = jQuery("#user_combine_both");
    jQuery("#user_notify_user,#user_whatsapp_message").on('change', function () {
        updateUserConsent();
    })
    notify_both.on('change', function () {

        if (notify_both.prop("checked") == true) {
            if (notify_user_sms.prop("checked") == true) notify_user_sms.closest('table').next().slideUp();
            if (notify_user_whatsapp_message.prop("checked") == true) notify_user_whatsapp_message.closest('table').next().slideUp();
        } else {
            if (notify_user_sms.prop("checked") == true) notify_user_sms.closest('table').next().slideDown();
            if (notify_user_whatsapp_message.prop("checked") == true) notify_user_whatsapp_message.closest('table').next().slideDown();
        }
    })
    notify_both.trigger('change');
    updateUserConsent();

    function updateUserConsent() {
        if (notify_user_sms.prop("checked") == true &&
            notify_user_whatsapp_message.prop("checked") == true) {
            notify_both.closest('tr').show();
        } else {
            notify_both.prop("checked", false).trigger('change');
            notify_both.closest('tr').hide();
        }
    }

    jQuery(document).on('click', '.clear_message', function (e) {
        var parent = jQuery(this).closest('.wpnotif-list');
        var id = parent.attr('data-id');
        update_message(id);
        parent.slideUp('fast');

    })

    jQuery(document).on('click', '.whatsapp_clear_all', function (e) {
        loader.show();
        update_message(-1);
    })

    jQuery(".wpnotif_field_floating_text").find("input").on('keyup change', function () {
        var inp = jQuery(this).val();
        var size = inp.length;
        var spn_lbl = jQuery(this).parent().find("span");

        spn_lbl.stop().animate({'left': Math.max(size * 9 + 33, jQuery(this).attr('dig-min'))}, 'fast');
    }).trigger('keyup');

    jQuery('.wpnotif_show_image').on('click', function () {

        var src = jQuery(this).data('src');
        var p = jQuery(".wpnotif_preview_overlay");

        p.find('img').attr('src', '').attr('src', src);
        p.fadeIn('fast');
        return false;
    });

    jQuery('.wpnotif_preview_overlay').on('click', function () {
        jQuery(this).fadeOut('fast');
    });


    var wpnotif_post_container = jQuery(".wpnotif-post_notifications");
    var wpnotif_post_msg_row = wpnotif_post_container.find(".wpnotif_post_msg_row");
    var wpnotif_post_label = wpnotif_post_container.find(".wpnotif-notification_post_name");
    var wpnotif_post_type = jQuery("#wpnotif_post_type");
    var enable_post_notifications = jQuery("#enable_post_notifications");
    var wpnotif_post_msg = jQuery("#wpnotif_post_msg");
    var wpnotif_post_route = wpnotif_post_container.find('#wpnotif_post_route');
    var post_details_selector = jQuery("#wpnotif_post_msg_details");
    var post_usergroup = wpnotif_post_container.find('.wpnotif-user_group');

    var post_details = {};
    try {
        post_details = JSON.parse(post_details_selector.val());
    } catch (e) {
        post_details = {};
    }
    if (post_details == null) {
        post_details = {};
    }
    wpnotif_post_type.on('change update', function () {
        var message = '';
        var type = jQuery(this).val();
        var group = '';
        var route = 1;
        if (post_details.hasOwnProperty(type)) {
            var data = post_details[type];
            message = data['message'];
            group = data['group'];
            route = data['route'];

            if (data['enable'] == 'on') {
                wpnotif_post_msg_row.removeClass('wpnotif-hide');
                enable_post_notifications.prop('checked', true).trigger('update');
            } else {
                disable_post_notification();
            }
        } else {
            disable_post_notification();
        }

        wpnotif_post_label.text(jQuery(this).find("option:selected").text());

        wpnotif_post_msg.val(message);
        wpnotif_post_route.val(route).trigger('change');

        if (group && group.length > 0) {
            group = group.split(",");
            jQuery.each(group, function (i, e) {
                post_usergroup.untselect().val(e);
            });
            post_usergroup.trigger('change');
        }
    }).trigger('update');


    wpnotif_post_route.on('change', function () {
        update_post_notifications();
    });

    enable_post_notifications.on('change', function () {
        update_post_notifications();
    });

    wpnotif_post_msg.on('change', function (e) {
        update_post_notifications();
    });
    post_usergroup.on('change', function () {
        update_post_notifications();
    })

    function disable_post_notification() {
        wpnotif_post_msg_row.addClass('wpnotif-hide');
        enable_post_notifications.prop('checked', false).trigger('update');
        post_usergroup.untselect('val', []);
    }

    function update_post_notifications() {
        var type = wpnotif_post_type.val();
        var message = wpnotif_post_msg.val();
        var group = post_usergroup.val();
        var route = wpnotif_post_route.val();
        if (group && group.length > 0) {
            group = group.join(',');
        }
        var data = {};


        if (enable_post_notifications.is(":checked")) {
            wpnotif_post_msg_row.removeClass('wpnotif-hide');
            data['enable'] = 'on';
        } else {
            wpnotif_post_msg_row.addClass('wpnotif-hide');
            data['enable'] = '';
        }
        data['group'] = group;
        data['message'] = message;
        data['route'] = route;

        post_details[type] = data;
        post_details_selector.val(JSON.stringify(post_details));
    }

    jQuery(".wpnotif_use_as_newsletter_inp").on('change', function () {
        var value = jQuery(this).val();
        var form = jQuery(this).closest('form');

        if (value == 1) {
            form.find('.wpnotif_use_newsletter').closest('tr').removeClass('wpnotif-hide');
        } else {
            form.find('.wpnotif_use_newsletter').closest('tr').addClass('wpnotif-hide');
        }
    }).trigger('change');


    jQuery(document).on("keyup update", ".wpnotif_textarea, .wpnotif_admin_conf textarea,.wpnotif_admin_conf textarea,#quick_message", function () {
        var arabic = /[\u0600-\u06FF]/;

        if (arabic.test(jQuery(this).val()) === true) {
            jQuery(this).addClass('wpnotif_rtl').removeClass('wpnotif_ltr');
        } else {
            jQuery(this).removeClass('wpnotif_rtl').addClass('wpnotif_ltr');
        }
    }).trigger("update");

    jQuery("#wpnotif_cron_method").on('change update', function () {
        var val = jQuery(this).val();
        jQuery('.wpnotif_cron_desc').hide();
        jQuery('.wpnotif_' + val).show();
    }).trigger('update');


    function showTime() {

        var date_options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
        };

        jQuery(".wpnotif-time").each(function () {
            var $this = jQuery(this);

            $this.removeClass(".wpnotif-time");
            var time = $this.data('time');
            var date_time = new Date(time * 1000);
            $this.text(date_time.toLocaleDateString("en-US", date_options));

        });
    }

    showTime();

    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };
});