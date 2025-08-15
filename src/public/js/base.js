(function($) {

	//mostra menu mobile
	$(".header__icon-bar").click(function(e){
		 $(".header__menu").toggleClass('show-menu');
		 e.preventDefault();
	});

	/*Smooth scrolling when clicking an anchor link*/
	document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

})( jQuery );
