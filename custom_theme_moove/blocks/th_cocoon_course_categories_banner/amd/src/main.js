define([
	'jquery'
	], function($) {
	return {
		   	main: function() {
                $(document).ready(function(){
                    var carousel = document.getElementById('carouselExampleIndicators');
                    var interval = 5000;
            
                    function nextSlide() {
                        var slides = carousel.querySelectorAll('.slide');

                        for (var i = 0; i < slides.length; i++) {
                            if (slides[i].classList.contains('active')) {
                                currentSlide = i;
                                break;
                            }
                        }
            
                        slides[currentSlide].classList.remove('active');
                        
                        currentSlide = currentSlide + 1;
                        if(currentSlide == slides.length){
                            currentSlide = 0;
                        }

                        slides[currentSlide].classList.add('active');
                    }

                    function prevSlide() {
                        var slides = carousel.querySelectorAll('.slide');

                        for (var i = 0; i < slides.length; i++) {
                            if (slides[i].classList.contains('active')) {
                                currentSlide = i;
                            }
                        }
            
                        slides[currentSlide].classList.remove('active');
                        
                        if(currentSlide == 0){
                            currentSlide = slides.length - 1;
                        } else {
                            currentSlide = currentSlide - 1;
                        }

                        slides[currentSlide].classList.add('active');
                    }
            
                    var slideInterval = setInterval(nextSlide, interval);

                    $(".prev-btn").click(function() {
                        prevSlide();
                    });

                    $(".next-btn").click(function() {
                        nextSlide();
                    });
            
                    carousel.addEventListener('mouseenter', function () {
                        clearInterval(slideInterval);
                    });
            
                    carousel.addEventListener('mouseleave', function () {
                        slideInterval = setInterval(nextSlide, interval);
                    });
                })
            }
	   };
   });