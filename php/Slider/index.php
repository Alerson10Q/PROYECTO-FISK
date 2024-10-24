<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider</title>
    <link rel="stylesheet" href="styles.css">
</head>
<Script>
    var slideIndex = 0;
        const totalSlides = document.querySelectorAll('.slide').length;

        function showSlides(index) {
            if (index < 0) {
                slideIndex = totalSlides - 1;
            } else if (index >= totalSlides) {
                slideIndex = 0;
            } else {
                slideIndex = index;
            }

            const slides = document.querySelectorAll('.slide');
            slides.forEach((slide, i) => {
                slide.style.transform = `translateX(${(i - slideIndex) * 100}%)`;
            });
        }

        function prevSlide() {
            showSlides(slideIndex - 1);
        }

        function nextSlide() {
            showSlides(slideIndex + 1);
        }

        showSlides(slideIndex);
</Script>
<body>
    <div class="slider">
        <div class="slides">
            <div class="slide"><img src="./img/imagen1.jpg" alt="Image 1"></div>
            <div class="slide"><img src="./img/imagen2.jpg" alt="Image 2"></div>
            <div class="slide"><img src="./img/imagen3.jpg" alt="Image 3"></div>
        </div>
        <button class="prev" onclick="prevSlide()">&#10094;</button>
        <button class="next" onclick="nextSlide()">&#10095;</button>
    </div>
    
</body>

</html>