import $ from 'jquery';
import mdlcfg from 'core/config';
import ls from 'local_th_ecommercelib/localstorage-slim';
import {exception as displayException} from 'core/notification';

export const ttl = 50000;
export const cart_count_selector = '.th_lblCartCount';
const key_cart = 'th_cart';

export const ajax_check_payment_methods = ()=>{

    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${mdlcfg.wwwroot}/local/th_edumy_cart/ajax_update_cart.php`,
            method: "POST",
            dataType: 'json',
            data: {
                function: "check_method",
            },

            success:function(response){
                resolve(response);
            },

            error: function (xhr, ajaxOptions, thrownError) {
                reject(xhr,ajaxOptions,thrownError)
            }
        });
    });
}

export const ajax_checkprice = ($request_param)=>{

    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${mdlcfg.wwwroot}/local/th_edumy_cart/ajax_update_cart.php`,
            method: "POST",
            dataType: 'json',
            data: {
                function: "checkprice",
                request_param:$request_param
            },

            success:function(response){
                resolve(response);
            },

            error: function (xhr, ajaxOptions, thrownError) {
                reject(xhr,ajaxOptions,thrownError)
            }
        });
    });
}


export const ajax_add_to_cart = ($courseid)=>{
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${mdlcfg.wwwroot}/local/th_edumy_cart/ajax_update_cart.php`,
            method: "POST",
            dataType: 'json',
            data: {
                function: "add_to_cart",
                courseid: $courseid
            },

            success:function(response){
                resolve(response);
            },

            error: function (xhr, ajaxOptions, thrownError) {
                reject(xhr,ajaxOptions,thrownError)
            }
        });
    });
}


export const ajax_get_card_number = ()=>{

    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${mdlcfg.wwwroot}/local/th_edumy_cart/ajax_update_cart.php`,
            method: "POST",
            dataType: 'json',
            data: {
                function: "get_card_number",
            },

            success:function(response){
                resolve(response);
            },

            error: function (xhr, ajaxOptions, thrownError) {
                reject(xhr,ajaxOptions,thrownError)
            }
        });
    });
}


export const update_cart_number = (number_product=null)=>{
    if(number_product){
        $(cart_count_selector).text(number_product);
    }
    else{
        ajax_get_card_number().then((response)=>{
            if(response.isloggedin){
                $(cart_count_selector).text(response.num_product)
            }
            else
            {
                ls_cart = ls.get(key_cart,{ decrypt: true });
                if(ls_cart){
                    newcart = new Cart();
                    Object.assign(newcart,ls_cart);
                    num_product = ls_cart.products.length;
                    $(cart_count_selector).text(num_product);
                }
                else
                {
                    $(cart_count_selector).text(0);
                }
            }
        })
    }
}



export const add_to_cart = (selector)=>{
    $(document).ready(function(){

        $(document).on("click",selector,()=>{
            $btn_muangay = $(selector);
            $courseid = $btn_muangay.attr('data-courseid');
            ajax_add_to_cart($courseid).then((response)=>{
                
                if(response.isloggedin){
                    if(response.duplicate){
                        showCustomToast("Sản phẩm đã có trong giỏ hàng!");
                        
                    } else {
                        showCustomToast("Sản phẩm đã được thêm vào giỏ hàng!");
                    }
                    num_product = response.num_product;
                    update_cart_number(num_product);
                }
                else
                {
                    product = response.product;
                    newcart = new Cart();

                    ls_cart = ls.get(key_cart,{ decrypt: true });
                    if(ls_cart){
                        Object.assign(newcart,ls_cart);
                    }
                    var existingProduct = newcart.products.find(function(item) {
                        return item.id === product.id;
                    });

                    if (existingProduct) {
                        showCustomToast("Sản phẩm đã có trong giỏ hàng!");
                    } else {
                        showCustomToast("Sản phẩm đã được thêm vào giỏ hàng!");
                        newcart.add_product(product);
                        ls.set(key_cart, newcart, { ttl: ttl, encrypt: true });
                    }

                    num_product = newcart.products.length;
                    update_cart_number(num_product);
                }
            })/*.catch((xhr, ajaxOptions, thrownError)=>{
              
              
              
            })*/
            })

    });
};

export const showError = (errormessage)=>
{
    displayException(new Error(errormessage));
}

export const showCustomToast = (message)=>{
    require(['core/toast'], Toast => {
        Toast.add(message,{
            closeButton: true,
        });
    })
};