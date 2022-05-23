jQuery(function($){

    $(document).on("click", ".catItem", function(e) {
        e.preventDefault();
        var ele = $(this);
        var cat_id = $(this).data('id');
       
        if ( !ele.hasClass('active') ) {
            $('.catItem').removeClass('active');
            ele.addClass('active');
            category_init(cat_id);
        }

        if ( ele.hasClass('hasSubItem') ) {
            $('.sub_cat_'+cat_id).toggleClass('show_sub');
        }
        
    });
        
        var min_price =  ( parseInt(wpf_ajax.min_price) ) ? parseInt(wpf_ajax.min_price) : parseInt(wpf_ajax.price_range.min_price); 
        var max_price =  ( parseInt(wpf_ajax.max_price) ) ? parseInt(wpf_ajax.max_price) : parseInt(wpf_ajax.price_range.max_price); 
        
        $( "#wpfSliderRange" ).slider({
              range: true,
              min: parseInt(wpf_ajax.price_range.min_price),
              max: parseInt(wpf_ajax.price_range.max_price),
              values: [ min_price,  max_price],
              slide: function( event, ui ) {
                $( "#wpfMinPrice" ).val( ui.values[ 0 ] );
                $( "#wpfMaxPrice" ).val( ui.values[ 1 ] );
              }
        });

        //console.log(wpf_ajax.price_range.min_price);
        $( "#wpfMinPrice" ).val( $( "#wpfSliderRange" ).slider( "values", 0 ) );
        $( "#wpfMaxPrice" ).val( $( "#wpfSliderRange" ).slider( "values", 1 ) );

    $("body").on("click", ".catItemMob", function(e) {
        e.preventDefault();
        var cat_id = $(this).data('id');
        var parent_id = $(this).data('parent_id');
        var step = $(this).data('step');

        wpf_data={
            'action': 'wpf_cat_filter',
            'cat_id': cat_id,
            'parent_id': parent_id,
            'step': step
        };
        $.ajax({
            url: wpf_ajax.ajaxurl,
            type: "POST",
            data: wpf_data,
            //dataType: "json",
            beforeSend:function(){
                //$(".mapi_import").removeClass("button-primary").addClass("button");
                //$(".mapi_import span.alax_loader").show(); 
            }, 
            success: function(resp) {
                //console.log(resp);
                var res = JSON.parse(resp);
                if(res.success == 'true'){
                    $('.productCategoryMobList').html(res.html_list);
                    $('.mobBack').html(res.back_btn);

                }
            }, error: function () {
                
            }
        });
        category_init(cat_id);
    });

    /* range slider dropdown js starts */
    $("body").on("click", ".wpfPriceBtn", function(e) {
        $('#wpfPriceDropdown').toggleClass('open_dropdown');
    });

    $(document).ready(function() {
        $('.wpfSortFilter').select2();
        $(".wpfBrand").select2({
            placeholder: "Brand"
        });
        $(".wpfTaxonomy").select2({
            placeholder: $(this).attr('data-placeholder')
        });

    });

    // Close the dropdown if the user clicks outside of it
    // window.onclick = function(event) {
    //   if (!event.target.matches('.wpfPriceBtn')) {
    //     var dropdowns = document.getElementsByClassName("wpf-dropdown-content");
    //     var i;
    //     for (i = 0; i < dropdowns.length; i++) {
    //         var openDropdown = dropdowns[i];
    //         if (openDropdown.classList.contains('open_dropdown')) {
    //             openDropdown.classList.remove('open_dropdown');
    //         }
    //     }
    //   }
    // }
    /* range slider dropdown js ends */

    function category_init(cat_id){
        $('input[name=productCatFilter]').val(cat_id);
        filter_products( 'cat');
    }

    $("body").on("change", ".wpfFilter", function(e) {
        var filter_type = null; 

        if ( $(this).hasClass('wpf_fltr') ) {
            filter_type = $(this).data('slug');
        }

        filter_products(filter_type);
    });

    $("body").on('submit','form#wpfPriceForm',function(e){
        e.preventDefault();
        //$('#wpfPriceActive').prop('checked', true);
        filter_products();
    });

    // $("body").on('click','#wpfPriceResetBtn',function(e){
    //     $('#wpfPriceActive').prop('checked', false);
    //     filter_products();
    // });

    // $("body").on('change','#wpfPriceActive',function(e){
    //     filter_products();
    // });

    //pagination function
    $("body").on("click", ".woocommerce-pagination ul.page-numbers li", function(e) {
        e.preventDefault();

        if ($(this).children('a').length) {
            var url = $(this).children('a').attr('href'),
            parts = url.split("/");
           // console.log(parts);
            page = parts[parts.length-2];
            
            filter_products(null,page);
        }

    });

    function filter_products( filter_type=null, page=null ) {


        wpf_data={
            'action': 'wpf_filter_products',
            'cat_id': $("[name='productCatFilter']").val(),
            'sort_by': $("[name='wpfSortFilter']").val(),
            'brands': $("[name='wpfBrand']").val(),
        };

        jQuery('.wpfTaxonomy').each(function() {
            let select = jQuery(this);
            //let name = select.attr('name');
            let name = select.data('slug');
            let opt = select.val();
            wpf_data['attr['+ name + ']'] = opt;
            //console.log('++' + name + '++');
        });


        if( jQuery("#wpfPriceActive").is(':checked') ) {
            //wpf_data['min_price'] = $('#wpfMinPrice').val();
            //wpf_data['max_price'] = $('#wpfMaxPrice').val();
            wpf_data['price_range'] = [ $('#wpfMinPrice').val(), $('#wpfMaxPrice').val() ];
        }

        //pagination
        if( page != null ){
            wpf_data['paged'] = page;
        }

        if( filter_type != null ) {
            wpf_data['filter_type'] = filter_type;
        }

        $.ajax({
            url: wpf_ajax.ajaxurl,
            type: "POST",
            data: wpf_data,
            //dataType: "json",
            beforeSend:function(){
                $(".wpf_loading").remove();
                $("body").append("<div class='wpf_loading'></div>");
            }, 
            success: function(resp) {
                var res = JSON.parse(resp);
                if(res.success == 'true') {
                    $('ul.products').html(res.products_html);
                    window.history.pushState(null, '', res.wpf_url);
                    document.title=res.wpf_page_title;
                    $('h1.page-title').html(res.wpf_page_title);
                    $('.woocommerce-breadcrumb').html(res.breadcrumbs);

                    //brands update
                    if( res.brands_list  != undefined ) {
                        $('.attr_brand_wrap').html(res.brands_list);
                        $(".wpfBrand").select2({
                                placeholder: "Brand"
                        });
                    }

                    //attributes update
                    if( res.attr_filter  != undefined ) {
                        $.each(res.attr_filter, function(attr_slug,attr_html){
                            $('.attr_'+attr_slug+'_wrap').html(attr_html);
                            //alert(key + "/" + valueObj );
                            //console.log(attr_slug);
                            $(".attr_"+attr_slug+"_wrap .wpfTaxonomy").select2({
                                placeholder: $(this).attr('data-placeholder')
                            });
                        });
                    }

                    //pagination 
                    $('.woocommerce-pagination').remove();
                    if(res.pagination != '') {
                        $('.storefront-sorting').append(res.pagination);
                    }

                }
            }, complete: function () {
               $(".wpf_loading").remove();
            }
        });
    }



    // function filter_products() {

    //     filter_arr = {};

    //     var sort_by = $("[name='wpfSortFilter']").children("option:selected").val(),
    //         brands = $("[name='wpfBrand']").children("option:selected").val();

    //     if (sort_by != '') {
    //         filter_arr['orderby'] = sort_by;
    //         //filter_arr.push({'orderby': sort_by}); 
    //     }

    //     if (brands != '') {
    //         filter_arr['pwb-brand-filter'] = brands;
    //         //filter_arr.push({'pwb-brand-filter': brands}); 
    //     }

    //     jQuery('.wpfTaxonomy').each(function() {
    //         let select = jQuery(this);
    //         let name = select.attr('name');
    //         let opt = select.children("option:selected").val();
    //         if( opt != '' ) filter_arr['filter_'+name] = opt;
         
    //         //console.log('++' + name + '++');
    //     });

    //     if( jQuery("#wpfPriceActive").is(':checked') ) {
    //         filter_arr['min_price'] = $('#wpfMinPrice').val();
    //         filter_arr['max_price'] = $('#wpfMaxPrice').val();
    //     }

    //     var str = jQuery.param( filter_arr );
    //     var url = wpf_ajax.current_url + '?' + str;
    //     window.location.replace(url);

    // }


});






