require([
    'jquery'
], function ($) {
    'use strict';
    jQuery(document).ready(function(){
		console.log("gram1 ");
        jQuery(document).ajaxStop(function () {
			//$('.admin__field-note').text("الثمن النهائي لهذا المنتج هو: ");
			//$('admin__field-note').text("الثمن النهائي لهذا المنتج هو: ");
		//console.log("gram2 ");
            /*$("input[name='product[gramme]'").change('input', function() {
				console.log("gram: ".$("input[name='product[gramme]'").val());
				console.log("gram: ".$("input[name='gramme'").val());
			});*/
			/*console.log("grami1 ");
			$('input[name="product[gramme]"]').val("z0");
			$('input[name=gramme]').val("z1");
			$('input[name="gramme"]').val("z2");
			var val1 = $('input[name="product[gramme]"]').val();
			var val2 = document.querySelectorAll('input[name="product[gramme]"]')[0].value;
			console.log("gramiio "+val1+val2);*/
			/*$('input[name="product[gramme]"]').change(function() {
					console.log("grami1 ");
			});*/
			$('input[name="product[gramme]"]').on('input',function(e){
			//notice-PWAHLSA
				var price = document.querySelectorAll('input[name="product[price]"]')[0].value;
				var gramme = document.querySelectorAll('input[name="product[gramme]"]')[0].value;
				//var gramme = $('input[name="product[gramme]"]').val();
				///console.log("price "+price+gramme);
				if(price){
					var res = gramme * price;
					//var res = 1222;
					console.log("res "+res);
					$('.admin__field-note').text("الثمن النهائي الذي سيعرض للعميل هو: "+res);
					//$('admin__field-note').text("يجب ادخال ثمن الغرام الواحد أولا قبل التمكن من حساب ثمن المنتج");
					//$( "span" ).last().html( "الثمن النهائي لهذا المنتج هو: ".res);
					//$('input[name="product[gramme]"]').after( "الثمن النهائي لهذا المنتج هو: ".res);
				}
				else{
					$('.admin__field-note').text("يجب ادخال ثمن الغرام الواحد أولا قبل التمكن من حساب ثمن المنتج");
					
				}

			});
        });
    });
});