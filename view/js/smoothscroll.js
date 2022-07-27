// Pas besoin d'utiliser la fonction : $(".nav a").smoothscroll();

$(function(){
	$('a[href*=#]:not([href=#])').click(function(){
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname){
			var target = $(this.hash);
			target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
			var marge = 72;
			if($("body").width() <= 710)
				marge = 132;
			if(target.length){
				$('html,body').animate({
					scrollTop: target.offset().top - marge
				}, 500);
				return false;
			}
		}
	});
});